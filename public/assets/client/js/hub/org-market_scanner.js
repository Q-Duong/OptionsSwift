const SYMBOLS = [
    "SPY",
    "QQQ",
    "SPX",
    "AAPL",
    "MSFT",
    "NVDA",
    "AMZN",
    "META",
    "GOOGL",
    "TSLA",
];

let activeSide = "bull";
let stateFilter = "all";
let gradeFilter = "all";
let vwapFilter = "all";
let ipsMin = 0;
let scanData = {};
let vwapData = {};
let allEntries = [];
let selectedSym = null;
let countdownVal = 30;
let countdownTmr = null;
let scanning = false;
let lastScanTime = null;

function todayET() {
    return new Intl.DateTimeFormat("en-CA", {
        timeZone: "America/New_York",
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
    }).format(new Date());
}

function selectedTradeDate() {
    return document.getElementById("tradeDate")?.value || todayET();
}

function initTradeDate() {
    const el = document.getElementById("tradeDate");
    if (el && !el.value) el.value = todayET();
}

function setTradeDateToday() {
    const el = document.getElementById("tradeDate");
    if (el) el.value = todayET();
    onTradeDateChange();
}

function onTradeDateChange() {
    scanData = {};
    vwapData = {};
    allEntries = [];
    selectedSym = null;
    closeDrawer();
    renderTable();
    runScan();
}

function setStatus(msg, err = false) {
    const el = document.getElementById("status-bar");
    el.textContent = msg;
    el.className = err ? "error" : "";
}

// ── FETCH CHAIN ───────────────────────────────────────────────────────────
async function fetchChain(sym, expDate) {
    // Cập nhật URL theo route web mới
    const res = await fetch(`/scanner/chain?symbol=${sym}&expDate=${expDate}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': window.AppConfig.csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    if (!res.ok) throw new Error(`${sym} Chain API Error: ${res.status}`);

    const data = await res.json();
    return {
        calls: data.calls || [],
        puts: data.puts || []
    };
}

// ── FETCH VWAP ────────────────────────────────────────────────────────────
async function fetchVwap(sym) {
    const date = selectedTradeDate();
    const res = await fetch(`/scanner/vwap?symbol=${sym}&date=${date}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': window.AppConfig.csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    });

    if (!res.ok) throw new Error(`VWAP ${sym}: ${res.status}`);
    
    const data = await res.json();
    const bars = data.results || [];
    if (!bars.length) return null;

    let sumTP = 0,
        sumVol = 0;
    for (const b of bars) {
        const tp = (b.h + b.l + b.c) / 3;
        sumTP += tp * b.v;
        sumVol += b.v;
    }
    if (!sumVol) return null;

    const vwap = sumTP / sumVol,
        price = bars[bars.length - 1].c,
        diff = price - vwap,
        pct = (diff / vwap) * 100;
    const pos = Math.abs(pct) < 0.05 ? "flat" : pct > 0 ? "above" : "below";

    const avgVol = sumVol / bars.length;
    const lastVol = bars[bars.length - 1].v;
    const rvol = avgVol > 0 ? lastVol / avgVol : 1;

    return {
        price,
        vwap,
        diff,
        pct,
        pos,
        rvol,
        barCount: bars.length,
    };
}

// ── COMPUTE IMBALANCE ─────────────────────────────────────────────────────
function computeImbalance(sym, calls, puts) {
    // UPGRADED ENGINE:
    // 40% Net Delta Hedge Shares + 30% Call/Put Premium + 20% Volume Pressure + 10% Persistence.
    // This avoids the old problem where large bid_size / ask_size quotes could look like real flow.
    let bullPrem = 0,
        bearPrem = 0,
        bullHedge = 0,
        bearHedge = 0;
    let callVol = 0,
        putVol = 0,
        oi0dte = 0;
    let callDollarVol = 0,
        putDollarVol = 0;
    let callContracts = 0,
        putContracts = 0;
    let quoteBullPrem = 0,
        quoteBearPrem = 0; // kept only as a small tie-breaker, not main signal
    const expDate = todayET();

    function midPrice(c) {
        const bid = Number(c.last_quote?.bid || 0),
            ask = Number(c.last_quote?.ask || 0);
        const last = Number(
            c.day?.close || c.day?.last_price || c.last_trade?.price || 0,
        );
        if (bid > 0 && ask > 0) return (bid + ask) / 2;
        if (ask > 0) return ask;
        if (bid > 0) return bid;
        return last;
    }

    function proc(c, type) {
        const bid = Number(c.last_quote?.bid || 0),
            ask = Number(c.last_quote?.ask || 0);
        const bidsz = Number(
            c.last_quote?.bid_size || c.last_quote?.bidSize || 0,
        );
        const asksz = Number(
            c.last_quote?.ask_size || c.last_quote?.askSize || 0,
        );
        const delta = Math.abs(Number(c.greeks?.delta || 0));
        const vol = Number(c.day?.volume || 0);
        const mark = midPrice(c);
        const prem = vol * mark * 100;
        const hedgeShares = vol * delta * 100;
        const exp = c.details?.expiration_date || "";

        if (type === "call") {
            // Calls are treated as upside pressure when volume is present.
            bullPrem += prem;
            bullHedge += hedgeShares;
            callVol += vol;
            callDollarVol += prem;
            callContracts += vol;
            quoteBullPrem += ask * asksz * 100;
            quoteBearPrem += bid * bidsz * 100;
        } else {
            // Puts are treated as downside pressure when volume is present.
            bearPrem += prem;
            bearHedge += hedgeShares;
            putVol += vol;
            putDollarVol += prem;
            putContracts += vol;
            quoteBullPrem += bid * bidsz * 100;
            quoteBearPrem += ask * asksz * 100;
        }
        if (exp === expDate) oi0dte += Number(c.open_interest || 0);
    }
    calls.forEach((c) => proc(c, "call"));
    puts.forEach((c) => proc(c, "put"));

    // If volume is missing/zero, fall back lightly to quote liquidity so the table does not go blank.
    const hasRealVol = callVol + putVol > 0;
    if (!hasRealVol) {
        bullPrem = quoteBullPrem;
        bearPrem = quoteBearPrem;
        bullHedge = quoteBullPrem / 100;
        bearHedge = quoteBearPrem / 100;
    }

    const side = bullPrem >= bearPrem ? "bull" : "bear";
    const domPrem = side === "bull" ? bullPrem : bearPrem;
    const subPrem = side === "bull" ? bearPrem : bullPrem;
    const domHedge = side === "bull" ? bullHedge : bearHedge;
    const subHedge = side === "bull" ? bearHedge : bullHedge;
    // Best strike to pass into the chain page when this scanner row is opened.
    // This finds the strike with the strongest same-day premium pressure for this symbol.
    const strikeMap = new Map();
    for (const c of calls) {
        const strike = Number(c.details?.strike_price);
        if (!Number.isFinite(strike)) continue;
        const prem = Number(c.day?.volume || 0) * midPrice(c) * 100;
        strikeMap.set(strike, (strikeMap.get(strike) || 0) + prem);
    }
    for (const p of puts) {
        const strike = Number(p.details?.strike_price);
        if (!Number.isFinite(strike)) continue;
        const prem = Number(p.day?.volume || 0) * midPrice(p) * 100;
        strikeMap.set(strike, (strikeMap.get(strike) || 0) + prem);
    }
    const dominantStrike =
        [...strikeMap.entries()].sort((a, b) => b[1] - a[1])[0]?.[0] || "";

    const prev = scanData[sym] || {};

    const ratio = domPrem / Math.max(subPrem, 1);
    const ratioDelta = ratio - (prev.ratio || 0);
    const netPct = (
        ((domPrem - subPrem) / Math.max(domPrem + subPrem, 1)) *
        100
    ).toFixed(0);

    let persist = prev.persist || 0;
    if (prev.side === side && ratio > 1.1) persist = Math.min(persist + 1, 10);
    else if (prev.side !== side) persist = 0;
    else persist = Math.max(persist - 1, 0);

    // Component scores, 0-100.
    const hedgeScore = Math.min(
        Math.max(
            ((domHedge - subHedge) / Math.max(domHedge + subHedge, 1)) * 100,
            0,
        ),
        100,
    );
    const premiumScore = Math.min(
        Math.max(
            ((domPrem - subPrem) / Math.max(domPrem + subPrem, 1)) * 100,
            0,
        ),
        100,
    );
    const volDom = side === "bull" ? callVol : putVol;
    const volSub = side === "bull" ? putVol : callVol;
    const volumeScore = Math.min(
        Math.max(((volDom - volSub) / Math.max(volDom + volSub, 1)) * 100, 0),
        100,
    );
    const persistScore = Math.min((persist / 5) * 100, 100);

    // Small quote confirmation only: helps detect stacked ask/bid liquidity but cannot dominate the score.
    const quoteSide = quoteBullPrem >= quoteBearPrem ? "bull" : "bear";
    const quoteConfirm = quoteSide === side ? 5 : -5;

    const baseIPS = Math.max(
        0,
        Math.min(
            100,
            hedgeScore * 0.4 +
                premiumScore * 0.3 +
                volumeScore * 0.2 +
                persistScore * 0.1 +
                quoteConfirm,
        ),
    );

    // CF now means combined pressure confidence instead of pure ratio math.
    const cf = Math.min(Math.max(baseIPS / 100, 0), 1);

    // Sub-scores mapped into the existing UI labels.
    const subImbalance = premiumScore;
    const subUrgency = Math.min((Math.abs(ratioDelta) / 3) * 100, 100);
    const subPersist = persistScore;
    const subQuality = hedgeScore;
    const subConfirm = Math.min(
        hedgeScore * 0.55 + premiumScore * 0.35 + persistScore * 0.1,
        100,
    );

    const state =
        baseIPS >= 70 && persist >= 3
            ? "CONFIRMED"
            : baseIPS >= 45 && persist >= 1
              ? "BUILDING"
              : "EARLY";
    const quality = baseIPS >= 65 ? "HQ" : baseIPS >= 40 ? "MQ" : "LQ";
    const signalType =
        ratioDelta > 2.5
            ? "SPIKE"
            : persist >= 4
              ? "TRIPLE"
              : persist >= 2
                ? "DUAL"
                : ratioDelta < 0
                  ? "RELOAD"
                  : "NEW";

    return {
        sym,
        side,
        ratio,
        ratioDelta,
        netPct,
        bullPrem,
        bearPrem,
        bullHedge,
        bearHedge,
        callVol,
        putVol,
        oi0dte,
        cf,
        baseIPS,
        state,
        quality,
        signalType,
        persist,
        wins: Math.min(persist, 5),
        subImbalance,
        subUrgency,
        subPersist,
        subQuality,
        subConfirm,
        hedgeScore,
        premiumScore,
        volumeScore,
        persistScore,
        callDollarVol,
        putDollarVol,
        hasRealVol,
        dominantStrike,
    };
}

// ── COMPOSITE SCORE ───────────────────────────────────────────────────────
function compositeScore(d, vwap) {
    let vwapBonus = 0;
    if (vwap) {
        const aligned =
            (d.side === "bull" && vwap.pos === "above") ||
            (d.side === "bear" && vwap.pos === "below");
        const opposed =
            (d.side === "bull" && vwap.pos === "below") ||
            (d.side === "bear" && vwap.pos === "above");
        if (aligned) vwapBonus = Math.min(Math.abs(vwap.pct) * 5, 10);
        if (opposed) vwapBonus = -Math.min(Math.abs(vwap.pct) * 3, 5);
    }
    const score = Math.round(Math.max(0, Math.min(100, d.baseIPS + vwapBonus)));
    const grade =
        score >= 88
            ? "A+"
            : score >= 74
              ? "A"
              : score >= 60
                ? "B+"
                : score >= 46
                  ? "B"
                  : "C";
    // Updated subConfirm with VWAP
    const subConfirmFinal = Math.min(
        d.subConfirm + (vwapBonus > 0 ? vwapBonus * 2 : 0),
        100,
    );
    return {
        score,
        grade,
        subConfirmFinal,
    };
}

// ── MAIN SCAN ─────────────────────────────────────────────────────────────
async function runScan() {
    if (scanning) return;
    scanning = true;
    setStatus("SCANNING…");
    const expDate = selectedTradeDate();
    let done = 0;

    // Bắn toàn bộ request cùng một lúc, không có delay
    await Promise.allSettled(
        SYMBOLS.map(async (sym) => {
            try {
                const [{ calls, puts }, vwap] = await Promise.all([
                    fetchChain(sym, expDate),
                    fetchVwap(sym).catch(() => null),
                ]);
                scanData[sym] = computeImbalance(sym, calls, puts);
                vwapData[sym] = vwap;
            } catch (e) {
                console.warn(sym, e.message);
            }
            done++;
            setStatus(`SCANNING… ${done}/${SYMBOLS.length}`);
        }),
    );

    // Tính toán áp lực
    let bull = 0,
        bear = 0;
    SYMBOLS.forEach((s) => {
        if (scanData[s]) {
            bull += scanData[s].bullPrem;
            bear += scanData[s].bearPrem;
        }
    });

    const pressureFill = document.getElementById("pressure-fill");
    if (pressureFill) {
        pressureFill.style.width =
            ((bull / Math.max(bull + bear, 1)) * 100).toFixed(1) + "%";
    }

    lastScanTime = new Date();
    const now = lastScanTime.toLocaleTimeString("en-US", {
        hour12: false,
    });
    setStatus(
        `Last scan: ${now} · ${done}/${SYMBOLS.length} loaded · Expiry: ${expDate}`,
    );

    const statAge = document.getElementById("stat-age");
    if (statAge) {
        statAge.textContent = now + " ago ⟳";
    }

    buildEntries();
    updateStatCounters();
    updateWatchlistBuckets();
    renderTable();

    if (selectedSym && scanData[selectedSym]) openDrawer(selectedSym);

    resetCountdown();
    scanning = false;
}

// ── BUILD ENTRIES ─────────────────────────────────────────────────────────
function buildEntries() {
    allEntries = SYMBOLS.map((sym) => {
        const d = scanData[sym],
            vwap = vwapData[sym] || null;
        if (!d || d.ratio === 0) return null;
        const { score, grade, subConfirmFinal } = compositeScore(d, vwap);
        return {
            ...d,
            vwap,
            score,
            grade,
            subConfirmFinal,
        };
    }).filter(Boolean);
}

// ── STAT COUNTERS ─────────────────────────────────────────────────────────
function updateStatCounters() {
    document.getElementById("stat-dual").textContent = allEntries.filter(
        (d) => d.signalType === "DUAL" || d.signalType === "TRIPLE",
    ).length;
    document.getElementById("stat-conf").textContent = allEntries.filter(
        (d) => d.state === "CONFIRMED",
    ).length;
    document.getElementById("stat-reload").textContent = allEntries.filter(
        (d) => d.signalType === "RELOAD",
    ).length;
}

// ── WATCHLIST BUCKETS ─────────────────────────────────────────────────────
function updateWatchlistBuckets() {
    const confirmed =
        allEntries
            .filter((d) => d.state === "CONFIRMED")
            .map((d) => d.sym)
            .join(", ") || "—";
    const early =
        allEntries
            .filter((d) => d.state === "EARLY" || d.state === "BUILDING")
            .map((d) => d.sym)
            .join(", ") || "—";
    const reload =
        allEntries
            .filter((d) => d.signalType === "RELOAD")
            .map((d) => d.sym)
            .join(", ") || "—";
    // Fade: bear side with CONFIRMED, or LQ quality
    const fade =
        allEntries
            .filter((d) => d.quality === "LQ" || d.side === "bear")
            .map((d) => d.sym)
            .join(", ") || "—";

    document.getElementById("bucket-confirmed").textContent = confirmed;
    document.getElementById("bucket-early").textContent = early;
    document.getElementById("bucket-reload").textContent = reload;
    document.getElementById("bucket-fade").textContent = fade;
}

// ── RENDER TABLE ──────────────────────────────────────────────────────────
function renderTable() {
    const tbody = document.getElementById("imb-tbody");

    let entries = [...allEntries].filter((d) => d.side === activeSide);
    if (stateFilter !== "all")
        entries = entries.filter((d) => d.state === stateFilter);
    if (gradeFilter === "A")
        entries = entries.filter((d) => d.grade === "A+" || d.grade === "A");
    if (gradeFilter === "B")
        entries = entries.filter((d) => d.grade === "B+" || d.grade === "B");
    if (vwapFilter === "above")
        entries = entries.filter((d) => d.vwap?.pos === "above");
    if (vwapFilter === "below")
        entries = entries.filter((d) => d.vwap?.pos === "below");
    if (ipsMin > 0) entries = entries.filter((d) => d.score >= ipsMin);

    const stateOrd = {
        CONFIRMED: 0,
        BUILDING: 1,
        EARLY: 2,
    };
    entries.sort(
        (a, b) => stateOrd[a.state] - stateOrd[b.state] || b.score - a.score,
    );

    document.getElementById("flow-label").textContent =
        activeSide === "bull" ? "BULLISH FLOW" : "BEARISH FLOW";
    document.getElementById("flow-label").style.color =
        activeSide === "bull" ? "var(--green)" : "var(--red)";
    document.getElementById("count-label").textContent = entries.length
        ? `${entries.length} name${entries.length > 1 ? "s" : ""} · sorted by IPS + state`
        : "";

    if (!entries.length) {
        tbody.innerHTML = `<tr id="empty-row"><td colspan="7">NO ${activeSide.toUpperCase()} FLOW DETECTED</td></tr>`;
        return;
    }

    const gradeKey = {
        "A+": "Aplus",
        A: "A",
        "B+": "Bplus",
        B: "B",
        C: "C",
    };
    const rowCls = {
        CONFIRMED: "confirmed-row",
        BUILDING: "building-row",
        EARLY: "early-row",
    };

    tbody.innerHTML = entries
        .map((d) => {
            const ratioStr =
                (d.side === "bull" ? "+" : "-") + d.ratio.toFixed(2);
            const dSign = d.ratioDelta >= 0 ? "+" : "";
            const cfBarW = Math.min(d.cf * 100, 100).toFixed(1) + "%";
            const sCls = d.score >= 72 ? "hi" : d.score >= 44 ? "md" : "lo";
            const gKey = gradeKey[d.grade] || "C";
            const rCls = rowCls[d.state] || "";
            const pips = Array.from(
                {
                    length: 5,
                },
                (_, i) =>
                    `<div class="streak-pip ${i < Math.min(d.wins, 5) ? "on-" + d.side : ""}"></div>`,
            ).join("");
            let vwapHTML = d.vwap
                ? `<div class="vwap-price ${d.vwap.pos}">${d.vwap.pos === "above" ? "▲" : d.vwap.pos === "below" ? "▼" : "─"} $${d.vwap.price.toFixed(2)}</div><div class="vwap-diff">${(d.vwap.pct >= 0 ? "+" : "") + d.vwap.pct.toFixed(2)}%</div>`
                : `<span class="vwap-tag loading">—</span>`;
            const sel = selectedSym === d.sym ? " selected" : "";
            return `<tr class="${rCls} new-flash${sel}" onclick="openDrawer('${d.sym}')">
<td><div class="sym-name">${d.sym}</div><div class="sig-tag">${d.signalType}</div></td>
<td><div class="ratio-val ${d.side}">${ratioStr}</div><div class="ratio-delta">${dSign + d.ratioDelta.toFixed(2)}</div></td>
<td><span class="qual-badge ${d.quality}">${d.quality}</span><div class="persist-streak">${pips}</div><div class="persist-label">${d.wins}/5 scans</div></td>
<td><span class="state-badge ${d.state}">${d.state}</span><div class="cf-wrap"><span class="cf-label">cf ${d.cf.toFixed(2)}</span><div class="cf-track"><div class="cf-fill" style="width:${cfBarW}"></div></div></div></td>
<td style="text-align:center">${vwapHTML}</td>
<td style="text-align:right"><span class="composite-score ${sCls}">${d.score}</span></td>
<td style="text-align:right"><span class="grade-badge ${gKey}">${d.grade}</span></td>
</tr>`;
        })
        .join("");
}

// ── DRAWER ────────────────────────────────────────────────────────────────
function openDrawer(sym) {
    selectedSym = sym;
    const d = allEntries.find((e) => e.sym === sym);
    if (!d) return;

    document.getElementById("detail-pane").classList.add("open");
    document.getElementById("drawer-sym").textContent = sym;
    const ctaSym = document.getElementById("drawer-cta-sym");
    if (ctaSym) ctaSym.textContent = sym;

    const date = selectedTradeDate();
    const vwapPos = d.vwap ? d.vwap.pos.toUpperCase() : "NO VWAP";
    document.getElementById("drawer-meta").textContent =
        `${date} · ${d.quality} · ${d.score}IPS${d.dominantStrike ? " · STRIKE $" + d.dominantStrike : ""}`;

    // Big score
    const bigEl = document.getElementById("drawer-ips-big");
    bigEl.textContent = d.score;
    bigEl.className = d.score >= 72 ? "" : "md";
    if (d.score < 44) bigEl.className = "lo";
    document.getElementById("drawer-ips-bar-fill").style.width = d.score + "%";

    // Grade
    const gKey = {
        "A+": "Aplus",
        A: "A",
        "B+": "Bplus",
        B: "B",
        C: "C",
    };
    document.getElementById("drawer-grade-big").innerHTML =
        `<span class="grade-badge ${gKey[d.grade] || "C"}" style="font-size:14px;padding:6px 12px">${d.grade}</span>`;

    // State badge
    const sb = document.getElementById("drawer-state-badge");
    sb.textContent = d.state;
    sb.className = d.state;

    // Sub-score bars
    const subScores = [
        {
            label: "Premium",
            val: Math.round(d.subImbalance),
            color: "var(--green)",
        },
        {
            label: "Urgency",
            val: Math.round(d.subUrgency),
            color: "var(--blue)",
        },
        {
            label: "Persistence",
            val: Math.round(d.subPersist),
            color: "var(--purple)",
        },
        {
            label: "Delta Hedge",
            val: Math.round(d.subQuality),
            color: "var(--amber)",
        },
        {
            label: "Confirm",
            val: Math.round(d.subConfirmFinal || d.subConfirm),
            color: "var(--orange)",
        },
    ];
    document.getElementById("drawer-sub-scores").innerHTML = subScores
        .map(
            (s) => `
<div class="sub-score-row">
<div class="sub-score-lbl">${s.label}</div>
<div class="sub-score-bar"><div class="sub-score-fill" style="width:${s.val}%;background:${s.color}"></div></div>
<div class="sub-score-val">${s.val}</div>
</div>`,
        )
        .join("");

    // Detail rows
    const vwapLine = d.vwap
        ? `$${d.vwap.price.toFixed(2)} / VWAP $${d.vwap.vwap.toFixed(2)} (${(d.vwap.pct >= 0 ? "+" : "") + d.vwap.pct.toFixed(2)}%)`
        : "No data";
    const rvolLine =
        d.vwap?.rvol != null ? `RVOL ${d.vwap.rvol.toFixed(1)}x` : "—";
    const netDir = d.side === "bull" ? "+" : "-";
    document.getElementById("drawer-details").innerHTML = `
<div class="drawer-detail-row"><span class="drawer-detail-lbl">Imbalance</span><span class="drawer-detail-val">R ${d.ratio.toFixed(2)} · Net ${d.netPct}% · Δ${d.ratioDelta >= 0 ? "+" : ""}${d.ratioDelta.toFixed(2)} ${d.ratioDelta >= 0 ? "↑" : "↓"}</span></div>
<div class="drawer-detail-row"><span class="drawer-detail-lbl">Urgency</span><span class="drawer-detail-val">Spike ${Math.abs(d.ratioDelta).toFixed(1)}x · ${d.state.toLowerCase()}</span></div>
<div class="drawer-detail-row"><span class="drawer-detail-lbl">Persist</span><span class="drawer-detail-val">${d.wins}/5 win · ${d.persist} streak</span></div>
<div class="drawer-detail-row"><span class="drawer-detail-lbl">Engine</span><span class="drawer-detail-val">40% Hedge · 30% Premium · 20% Vol · 10% Persist</span></div>
<div class="drawer-detail-row"><span class="drawer-detail-lbl">Flow Vol</span><span class="drawer-detail-val">C.Vol ${fmtK(d.callVol)} / P.Vol ${fmtK(d.putVol)}</span></div>
<div class="drawer-detail-row"><span class="drawer-detail-lbl">Hedge Score</span><span class="drawer-detail-val">${Math.round(d.hedgeScore || 0)} · Premium ${Math.round(d.premiumScore || 0)} · Vol ${Math.round(d.volumeScore || 0)}</span></div>
<div class="drawer-detail-row"><span class="drawer-detail-lbl">VWAP</span><span class="drawer-detail-val">${vwapLine}</span></div>
<div class="drawer-detail-row"><span class="drawer-detail-lbl">Confirm</span><span class="drawer-detail-val">${d.vwap?.pos === "above" || d.vwap?.pos === "below" ? "PX " + (d.vwap.pos === "above" ? "✓" : "✗") + " · " : ""} ${rvolLine}</span></div>`;

    // highlight row
    document.querySelectorAll("#imb-tbody tr").forEach((tr) => {
        tr.classList.toggle(
            "selected",
            tr.querySelector(".sym-name")?.textContent === sym,
        );
    });
}

function closeDrawer() {
    selectedSym = null;
    document.getElementById("detail-pane").classList.remove("open");
    document
        .querySelectorAll("#imb-tbody tr")
        .forEach((tr) => tr.classList.remove("selected"));
}

function drawerViewChain() {
    if (!selectedSym) return;

    const symbol = selectedSym;
    const expiry = selectedTradeDate();

    // Bắn tín hiệu PostMessage ra ngoài Dashboard Laravel để gọi Pop-up
    window.parent.postMessage(
        {
            action: "openFlowPopup",
            ticker: symbol,
            expiry: expiry,
        },
        "*",
    );
}

function openOptionChainModal(ticker, expiry) {
    const modal = document.getElementById("optionChainModal");
    // Bác có thể truyền ticker/expiry vào một hàm xử lý data trong option_chain
    // Ví dụ: updateOptionChainData(ticker, expiry);
    modal.style.display = "block"; // Hiển thị modal
}

function fmtK(n) {
    return n >= 1000 ? (n / 1000).toFixed(0) + "K" : String(n || 0);
}

// ── CONTROLS ──────────────────────────────────────────────────────────────
function setSide(s) {
    activeSide = s;
    document.getElementById("btn-bull").className =
        "side-btn" + (s === "bull" ? " bull-active" : "");
    document.getElementById("btn-bear").className =
        "side-btn" + (s === "bear" ? " bear-active" : "");
    renderTable();
}

function setStateFilter(f) {
    stateFilter = f;
    ["all", "confirmed", "building", "early"].forEach((k) =>
        document.getElementById("fs-" + k)?.classList.remove("active"),
    );
    document.getElementById("fs-" + f.toLowerCase())?.classList.add("active");
    renderTable();
}

function setGradeFilter(f) {
    gradeFilter = f;
    ["all", "a", "b"].forEach((k) =>
        document
            .getElementById("fg-" + k)
            ?.classList.remove("active", "grade-active"),
    );
    if (f === "all") document.getElementById("fg-all")?.classList.add("active");
    if (f === "A")
        document.getElementById("fg-a")?.classList.add("grade-active");
    if (f === "B")
        document.getElementById("fg-b")?.classList.add("grade-active");
    renderTable();
}

function setVwapFilter(f) {
    vwapFilter = f;
    ["all", "above", "below"].forEach((k) =>
        document
            .getElementById("fv-" + k)
            ?.classList.remove("active", "grade-active"),
    );
    document
        .getElementById("fv-" + f)
        ?.classList.add(f === "all" ? "active" : "grade-active");
    renderTable();
}

function onIpsSlider(v) {
    ipsMin = Number(v);
    document.getElementById("ips-val").textContent = v;
    renderTable();
}

function resetCountdown() {
    clearInterval(countdownTmr);
    countdownVal = 30;
    countdownTmr = setInterval(() => {
        countdownVal--;
        document.getElementById("countdown").textContent =
            `NEXT SCAN: ${countdownVal}s`;
        if (countdownVal <= 0) {
            clearInterval(countdownTmr);
            runScan();
        }
    }, 1000);
    document.getElementById("countdown").textContent = "NEXT SCAN: 30s";
}

initTradeDate();
runScan();