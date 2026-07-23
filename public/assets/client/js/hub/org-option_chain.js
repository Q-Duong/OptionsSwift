// ── MULTI-SYMBOL ROUTE: scanner + chain share symbol/expiry/strike ────
const CHAIN_SYMBOLS = [
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
const MAG7_SYMBOLS = ["AAPL", "MSFT", "NVDA", "AMZN", "META", "GOOGL"];
function normalizeChainSymbol(value) {
    const raw = String(value || "")
        .trim()
        .toUpperCase();
    if (raw === "GOOG") return "GOOGL";
    return CHAIN_SYMBOLS.includes(raw) ? raw : "SPY";
}
function symbolLabel(sym) {
    return MAG7_SYMBOLS.includes(sym) ? sym + " · MAG 7" : sym;
}
(function () {
    const qs = new URLSearchParams(window.location.search || "");
    const urlSym = qs.get("symbol") || qs.get("ticker") || qs.get("sym");
    const urlExp = qs.get("expiry") || qs.get("expiration") || qs.get("date");
    const urlStrike =
        qs.get("strike") || qs.get("selectedStrike") || qs.get("s");
    const storeSym =
        localStorage.getItem("chain_symbol") ||
        localStorage.getItem("scanner_symbol") ||
        localStorage.getItem("selected_symbol");
    const storeExp =
        localStorage.getItem("chain_expiry") ||
        localStorage.getItem("scanner_expiry") ||
        localStorage.getItem("selected_expiry");
    const storeStrike =
        localStorage.getItem("chain_strike") ||
        localStorage.getItem("selected_strike");

    const sym = normalizeChainSymbol(urlSym || storeSym || "SPY");
    const requestedExp = urlExp || storeExp || null;
    const earliestActiveExp = defaultExpirationET();
    // A 0DTE snapshot can be empty after the contract expires. After 4:00 PM ET
    // (and on weekends), automatically advance stale saved dates to the next weekday.
    const exp =
        requestedExp && requestedExp >= earliestActiveExp
            ? requestedExp
            : earliestActiveExp;
    const strike = urlStrike || storeStrike || "";

    window.__CHAIN_SYMBOL = sym;
    window.__CHAIN_EXPIRY = exp;
    window.__CHAIN_STRIKE = strike;
    localStorage.setItem("chain_symbol", sym);
    if (exp) localStorage.setItem("chain_expiry", exp);
    if (strike) localStorage.setItem("chain_strike", String(strike));

    function applySymbolRoute() {
        const b = document.getElementById("chain-symbol-display");
        if (b) b.textContent = symbolLabel(sym);
        const select = document.getElementById("chainSymbol");
        if (select) select.value = sym;
        const banner = document.getElementById("chain-symbol-banner");
        if (banner && strike) {
            let note = document.getElementById("scannerRouteNote");
            if (!note) {
                note = document.createElement("span");
                note.id = "scannerRouteNote";
                note.style.color = "#4a6660";
                note.style.fontSize = "11px";
                banner.appendChild(note);
            }
            note.textContent = "Scanner strike: $" + strike;
        }
        document.title = sym + " 0DTE Pro Gamma System";
        const heroTitle = document.querySelector(".hero-title");
        if (heroTitle)
            heroTitle.textContent = sym + " INSTITUTIONAL FLOW TERMINAL";
        const expEl = document.getElementById("expiration");
        if (expEl) expEl.value = exp || todayET();
        patchSymbolText(sym);
    }
    if (document.readyState === "loading")
        document.addEventListener("DOMContentLoaded", applySymbolRoute);
    else applySymbolRoute();
})();

function changeChainSymbol(sym) {
    sym = normalizeChainSymbol(sym);
    const exp =
        document.getElementById("expiration")?.value ||
        window.__CHAIN_EXPIRY ||
        todayET();
    window.__CHAIN_SYMBOL = sym;
    window.__CHAIN_EXPIRY = exp;
    localStorage.setItem("chain_symbol", sym);
    localStorage.setItem("chain_expiry", exp);
    const b = document.getElementById("chain-symbol-display");
    if (b) b.textContent = symbolLabel(sym);
    document.title = sym + " 0DTE Pro Gamma System";
    const heroTitle = document.querySelector(".hero-title");
    if (heroTitle) heroTitle.textContent = sym + " INSTITUTIONAL FLOW TERMINAL";
    patchSymbolText(sym);
    if (
        typeof connectLiveUnderlyingStream === "function" &&
        typeof MASSIVE_STOCKS_API_KEY !== "undefined"
    ) {
        connectLiveUnderlyingStream(MASSIVE_STOCKS_API_KEY);
    }
    if (typeof loadGammaSystem === "function") loadGammaSystem();
}

function patchSymbolText(sym) {
    const label = sym || window.__CHAIN_SYMBOL || "SPY";
    const generic = [
        "largestOrderBlockInfo",
        "orderSideRangeInfo",
        "blockImpactReason",
        "spyImbalanceReason",
        "spyImbalanceExplain",
    ];
    generic.forEach((id) => {
        const el = document.getElementById(id);
        if (el && el.dataset.originalText == null)
            el.dataset.originalText = el.textContent;
        if (el && el.dataset.originalText)
            el.textContent = el.dataset.originalText.replace(/SPY/g, label);
    });
}

function todayET() {
    return new Intl.DateTimeFormat("en-CA", {
        timeZone: "America/New_York",
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
    }).format(new Date());
}

function defaultExpirationET() {
    const parts = Object.fromEntries(
        new Intl.DateTimeFormat("en-US", {
            timeZone: "America/New_York",
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
            hour: "2-digit",
            minute: "2-digit",
            hourCycle: "h23",
            weekday: "short",
        })
            .formatToParts(new Date())
            .filter((p) => p.type !== "literal")
            .map((p) => [p.type, p.value]),
    );
    let d = new Date(
        Date.UTC(
            Number(parts.year),
            Number(parts.month) - 1,
            Number(parts.day),
        ),
    );
    const afterClose =
        Number(parts.hour || 0) * 60 + Number(parts.minute || 0) >= 16 * 60;
    if (afterClose || parts.weekday === "Sat" || parts.weekday === "Sun")
        d.setUTCDate(d.getUTCDate() + 1);
    while (d.getUTCDay() === 0 || d.getUTCDay() === 6)
        d.setUTCDate(d.getUTCDate() + 1);
    return d.toISOString().slice(0, 10);
}
if (!document.getElementById("expiration").value)
    document.getElementById("expiration").value =
        window.__CHAIN_EXPIRY || defaultExpirationET();

function fmt(n, decimals = 0) {
    if (!Number.isFinite(n)) return "--";
    return n.toLocaleString(undefined, {
        maximumFractionDigits: decimals,
        minimumFractionDigits: decimals,
    });
}

function money(n) {
    return "$" + fmt(n, 0);
}

function fmtGex(n) {
    if (!Number.isFinite(n)) return "--";
    const sign = n < 0 ? "-" : "";
    const abs = Math.abs(n);
    if (abs >= 1e9) return sign + "$" + fmt(abs / 1e9, 2) + "B";
    if (abs >= 1e6) return sign + "$" + fmt(abs / 1e6, 2) + "M";
    if (abs >= 1e3) return sign + "$" + fmt(abs / 1e3, 2) + "K";
    return sign + "$" + fmt(abs, 0);
}
function fmtPct(n, decimals = 1) {
    if (!Number.isFinite(n)) return "--";
    const sign = n > 0 ? "+" : "";
    return sign + fmt(n, decimals) + "%";
}

// Polygon's options snapshot endpoint requires index underlyings (SPX, and any other
// index ticker added later) to be prefixed with "I:". Equity/ETF tickers (SPY, QQQ, AAPL...)
// are passed through unchanged. Without this, SPX requests return no/incomplete data,
// which shows up as an empty chain and a Net Gamma Exposure stuck at 0.
const INDEX_UNDERLYINGS = ["SPX"];
// Order Side Flow tracks exactly 14 strike levels: 7 at/below live spot and 7 above.
// Keep subscriptions and every table renderer tied to these shared settings.
const ORDER_SIDE_STRIKES_PER_SIDE = 7;
const ORDER_SIDE_TOTAL_STRIKES = ORDER_SIDE_STRIKES_PER_SIDE * 2;
const MASSIVE_API_KEY = "XhRptfyaWuxZ3WprKhAZsMPynpTJfS5k";
const MASSIVE_STOCKS_API_KEY = "w5peuTPuApf_LHDtfnDsJhHF0gqUc397";
// Massive is the Polygon.io rebrand. Keep both official-compatible hosts so
// local HTML, Hostinger, DNS filters, and older accounts can connect reliably.
const MASSIVE_STOCKS_SOCKETS = [
    "wss://socket.massive.com/stocks",
    "wss://socket.polygon.io/stocks",
];
const MASSIVE_STOCKS_APIS = [
    "https://api.polygon.io",
    "https://api.massive.com",
];

let liveUnderlyingSocket = null;
let liveUnderlyingReconnectTimer = null;
let liveUnderlyingConnectionId = 0;
const liveUnderlyingState = {
    symbol: null,
    bid: 0,
    ask: 0,
    last: 0,
    aggregateClose: 0,
    calculationPrice: 0,
    calculationTimestamp: 0,
    calculationSource: "WAITING",
    quoteTimestamp: 0,
    tradeTimestamp: 0,
    timestamp: 0,
    source: "WAITING",
};
const LIVE_DISPLAY_MIN_INTERVAL_MS = 200;
let liveUnderlyingDisplayTimer = null;
let liveUnderlyingDisplayFrame = null;
let liveUnderlyingLastPaint = 0;

function liveDisplayClock() {
    return window.performance && typeof window.performance.now === "function"
        ? window.performance.now()
        : Date.now();
}

function scheduleLiveUnderlyingDisplay() {
    if (
        liveUnderlyingDisplayTimer !== null ||
        liveUnderlyingDisplayFrame !== null
    )
        return;
    const wait = Math.max(
        0,
        LIVE_DISPLAY_MIN_INTERVAL_MS -
            (liveDisplayClock() - liveUnderlyingLastPaint),
    );
    liveUnderlyingDisplayTimer = window.setTimeout(() => {
        liveUnderlyingDisplayTimer = null;
        const requestFrame =
            window.requestAnimationFrame ||
            ((callback) => window.setTimeout(callback, 0));
        liveUnderlyingDisplayFrame = requestFrame(() => {
            liveUnderlyingDisplayFrame = null;
            liveUnderlyingLastPaint = liveDisplayClock();
            updateLiveUnderlyingDisplay();
        });
    }, wait);
}

function showMarketDataError(message) {
    const el = document.getElementById("marketDataError");
    if (!el) return;
    el.textContent =
        "MARKET DATA ERROR: " + String(message || "Unknown connection error");
    el.style.display = "block";
}

function clearMarketDataError() {
    const el = document.getElementById("marketDataError");
    if (!el) return;
    el.textContent = "";
    el.style.display = "none";
}

function polygonUnderlyingTicker(sym) {
    const s = String(sym || "SPY").toUpperCase();
    return INDEX_UNDERLYINGS.includes(s) ? "I:" + s : s;
}

function massiveEventTimeMs(value) {
    const n = Number(value || 0);
    if (!Number.isFinite(n) || n <= 0) return Date.now();
    if (n > 1e17) return Math.floor(n / 1e6); // nanoseconds
    if (n > 1e14) return Math.floor(n / 1e3); // microseconds
    return n;
}

async function fetchWithTimeout(url, options = {}, timeoutMs = 10000) {
    const controller = new AbortController();
    const timer = setTimeout(() => controller.abort(), timeoutMs);
    try {
        return await fetch(url, { ...options, signal: controller.signal });
    } catch (err) {
        if (err?.name === "AbortError")
            throw new Error(
                "Market-data request timed out after " +
                    Math.round(timeoutMs / 1000) +
                    " seconds.",
            );
        throw err;
    } finally {
        clearTimeout(timer);
    }
}

function getFreshStreamedUnderlyingPrice(symbol, maxAgeMs = 15000) {
    const sym = String(symbol || window.__CHAIN_SYMBOL || "SPY").toUpperCase();
    if (liveUnderlyingState.symbol !== sym) return null;
    if (!(liveUnderlyingState.calculationPrice > 0)) return null;
    if (
        Date.now() - Number(liveUnderlyingState.calculationTimestamp || 0) >
        maxAgeMs
    )
        return null;
    return {
        calculationPrice: liveUnderlyingState.calculationPrice,
        displayPrice:
            liveUnderlyingState.last || liveUnderlyingState.calculationPrice,
        bid: liveUnderlyingState.bid,
        ask: liveUnderlyingState.ask,
        timestamp: liveUnderlyingState.calculationTimestamp,
        source: liveUnderlyingState.calculationSource,
    };
}

function waitForFreshStreamedUnderlyingPrice(symbol, timeoutMs = 3000) {
    const started = Date.now();
    return new Promise((resolve) => {
        const check = () => {
            const fresh = getFreshStreamedUnderlyingPrice(symbol);
            if (fresh) return resolve(fresh);
            if (Date.now() - started >= timeoutMs) return resolve(null);
            setTimeout(check, 100);
        };
        check();
    });
}

function updateLiveUnderlyingDisplay() {
    const symbol = liveUnderlyingState.symbol || window.__CHAIN_SYMBOL || "SPY";
    const midpoint =
        liveUnderlyingState.bid > 0 &&
        liveUnderlyingState.ask >= liveUnderlyingState.bid
            ? (liveUnderlyingState.bid + liveUnderlyingState.ask) / 2
            : 0;
    const displayPrice = Number(
        liveUnderlyingState.last ||
            midpoint ||
            liveUnderlyingState.calculationPrice ||
            0,
    );
    const calculationPrice = Number(liveUnderlyingState.calculationPrice || 0);
    const priceEl = document.getElementById("liveUnderlyingPrice");
    const feedEl = document.getElementById("liveUnderlyingFeed");

    const setPriceText = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value > 0 ? "$" + fmt(value, 2) : "$--";
    };
    const lastLabel = document.getElementById("sipLastLabel");
    if (lastLabel) lastLabel.textContent = symbol + " SIP LAST";
    setPriceText("sipLast", liveUnderlyingState.last);
    setPriceText("sipBid", liveUnderlyingState.bid);
    setPriceText("sipAsk", liveUnderlyingState.ask);
    setPriceText("sipGexMid", midpoint);
    setPriceText(
        "sipSpread",
        liveUnderlyingState.bid > 0 &&
            liveUnderlyingState.ask >= liveUnderlyingState.bid
            ? liveUnderlyingState.ask - liveUnderlyingState.bid
            : 0,
    );

    const calcAgeMs =
        Date.now() - Number(liveUnderlyingState.calculationTimestamp || 0);
    const calcAge =
        liveUnderlyingState.calculationTimestamp > 0
            ? Math.max(0, Math.round(calcAgeMs / 1000))
            : null;
    const sipStatusEl = document.getElementById("sipFeedStatus");
    if (sipStatusEl) {
        const status = String(liveUnderlyingState.source || "WAITING").replace(
            /^MASSIVE SIP\s*/,
            "",
        );
        sipStatusEl.textContent =
            status + (calcAge == null ? "" : " · " + calcAge + "s");
        sipStatusEl.style.color = /ERROR|RECONNECT|WAIT|CONNECT/i.test(status)
            ? "var(--yellow)"
            : "var(--green)";
    }

    if (priceEl)
        priceEl.textContent =
            calculationPrice > 0
                ? symbol + " GEX MID $" + fmt(calculationPrice, 2)
                : symbol + " GEX MID $--";
    if (feedEl) {
        const sourceLabel = String(
            liveUnderlyingState.calculationSource || "WAITING",
        ).replace(/^MASSIVE SIP\s*/, "");
        feedEl.textContent =
            "MASSIVE SIP: " +
            sourceLabel +
            (calcAge == null ? "" : " • " + calcAge + "s");
    }

    // The option-chain Greeks refresh every 12 seconds. Between chain refreshes,
    // keep the headline dollar GEX synchronized with the current SIP midpoint.
    const basis = window.__liveGexBasis;
    if (
        basis &&
        basis.symbol === symbol &&
        Number.isFinite(basis.netGammaShares) &&
        calculationPrice > 0
    ) {
        const liveNetGamma =
            basis.netGammaShares * calculationPrice * calculationPrice * 0.01;
        const netEl = document.getElementById("netGamma");
        if (netEl) netEl.textContent = fmtGex(liveNetGamma);
    }
}

function setLiveUnderlyingState(symbol, patch) {
    const sym = String(symbol || "SPY").toUpperCase();
    if (liveUnderlyingState.symbol !== sym) {
        liveUnderlyingState.symbol = sym;
        liveUnderlyingState.bid = 0;
        liveUnderlyingState.ask = 0;
        liveUnderlyingState.last = 0;
        liveUnderlyingState.aggregateClose = 0;
        liveUnderlyingState.calculationPrice = 0;
        liveUnderlyingState.calculationTimestamp = 0;
        liveUnderlyingState.calculationSource = "WAITING";
        liveUnderlyingState.quoteTimestamp = 0;
        liveUnderlyingState.tradeTimestamp = 0;
        liveUnderlyingState.timestamp = 0;
    }
    Object.assign(liveUnderlyingState, patch || {});
    // GEX must use the consolidated SIP NBBO midpoint. The latest SIP trade is
    // displayed separately and is only a brief startup fallback before a quote arrives.
    if (
        liveUnderlyingState.bid > 0 &&
        liveUnderlyingState.ask >= liveUnderlyingState.bid
    ) {
        liveUnderlyingState.calculationPrice =
            (liveUnderlyingState.bid + liveUnderlyingState.ask) / 2;
        liveUnderlyingState.calculationTimestamp =
            liveUnderlyingState.quoteTimestamp ||
            liveUnderlyingState.timestamp ||
            Date.now();
        liveUnderlyingState.calculationSource = "MASSIVE SIP NBBO MIDPOINT";
    } else if (liveUnderlyingState.last > 0) {
        liveUnderlyingState.calculationPrice = liveUnderlyingState.last;
        liveUnderlyingState.calculationTimestamp =
            liveUnderlyingState.tradeTimestamp ||
            liveUnderlyingState.timestamp ||
            Date.now();
        liveUnderlyingState.calculationSource = "MASSIVE SIP LAST FALLBACK";
    } else if (liveUnderlyingState.aggregateClose > 0) {
        liveUnderlyingState.calculationPrice =
            liveUnderlyingState.aggregateClose;
        liveUnderlyingState.calculationTimestamp =
            liveUnderlyingState.timestamp || Date.now();
        liveUnderlyingState.calculationSource =
            "MASSIVE SIP AGGREGATE FALLBACK";
    }
    // Keep every feed event in state, but batch DOM work to at most five paints/second.
    scheduleLiveUnderlyingDisplay();
}

async function fetchLiveUnderlyingPrice(symbol, apiKey) {
    const sym = String(symbol || "SPY").toUpperCase();
    if (INDEX_UNDERLYINGS.includes(sym)) return null;

    let data = null;
    let lastError = null;
    for (const apiBase of MASSIVE_STOCKS_APIS) {
        const url =
            apiBase +
            "/v2/snapshot/locale/us/markets/stocks/tickers/" +
            encodeURIComponent(sym) +
            "?apiKey=" +
            encodeURIComponent(apiKey);
        try {
            const res = await fetchWithTimeout(
                url,
                { cache: "no-store" },
                3500,
            );
            if (!res.ok) {
                const text = await res.text();
                throw new Error("HTTP " + res.status + ": " + text);
            }
            data = await res.json();
            break;
        } catch (err) {
            lastError = err;
        }
    }
    if (!data)
        throw new Error(
            "Massive live stock snapshot unavailable on both compatible endpoints: " +
                (lastError?.message || "connection failed"),
        );
    const ticker = data.ticker || {};
    const bid = Number(ticker.lastQuote?.p || 0);
    const ask = Number(ticker.lastQuote?.P || 0);
    const last = Number(ticker.lastTrade?.p || 0);
    const minuteClose = Number(ticker.min?.c || 0);
    const quoteTimestamp = massiveEventTimeMs(
        ticker.lastQuote?.t || ticker.updated,
    );
    const tradeTimestamp = massiveEventTimeMs(
        ticker.lastTrade?.t || ticker.updated,
    );
    // GEX uses the consolidated SIP NBBO midpoint. Last trade/minute close are
    // startup fallbacks only when a valid NBBO is temporarily unavailable.
    const calculationPrice =
        bid > 0 && ask >= bid ? (bid + ask) / 2 : last || minuteClose || 0;
    if (!(calculationPrice > 0))
        throw new Error(
            "Massive did not return a current " + sym + " stock price.",
        );

    const result = {
        calculationPrice,
        displayPrice: last || calculationPrice,
        bid,
        ask,
        quoteTimestamp,
        tradeTimestamp,
        timestamp: bid > 0 && ask >= bid ? quoteTimestamp : tradeTimestamp,
        source: "MASSIVE SIP SNAPSHOT",
    };
    setLiveUnderlyingState(sym, {
        bid: result.bid,
        ask: result.ask,
        last: result.displayPrice,
        aggregateClose: 0,
        quoteTimestamp: result.quoteTimestamp,
        tradeTimestamp: result.tradeTimestamp,
        timestamp: result.timestamp,
        source: result.source,
    });
    return getFreshStreamedUnderlyingPrice(sym, 60000) || result;
}

function connectLiveUnderlyingStream(apiKey, endpointIndex = 0) {
    const symbol = String(window.__CHAIN_SYMBOL || "SPY").toUpperCase();
    const connectionId = ++liveUnderlyingConnectionId;
    if (liveUnderlyingReconnectTimer)
        clearTimeout(liveUnderlyingReconnectTimer);
    if (liveUnderlyingSocket) {
        liveUnderlyingSocket.onclose = null;
        liveUnderlyingSocket.close();
        liveUnderlyingSocket = null;
    }

    if (INDEX_UNDERLYINGS.includes(symbol)) {
        setLiveUnderlyingState(symbol, { source: "INDEX SNAPSHOT" });
        return;
    }

    setLiveUnderlyingState(symbol, { source: "CONNECTING" });
    const socketUrl =
        MASSIVE_STOCKS_SOCKETS[endpointIndex] || MASSIVE_STOCKS_SOCKETS[0];
    const socket = new WebSocket(socketUrl);
    liveUnderlyingSocket = socket;

    socket.onmessage = (event) => {
        let messages;
        try {
            messages = JSON.parse(event.data);
        } catch (_) {
            return;
        }
        if (!Array.isArray(messages)) messages = [messages];

        for (const message of messages) {
            if (message.ev === "status" && message.status === "connected") {
                socket.send(JSON.stringify({ action: "auth", params: apiKey }));
            } else if (
                message.ev === "status" &&
                message.status === "auth_success"
            ) {
                socket.send(
                    JSON.stringify({
                        action: "subscribe",
                        params: "Q." + symbol + ",T." + symbol,
                    }),
                );
                setLiveUnderlyingState(symbol, { source: "SUBSCRIBED" });
            } else if (
                message.ev === "status" &&
                /error|failed/i.test(
                    String(message.status || "") +
                        " " +
                        String(message.message || ""),
                )
            ) {
                setLiveUnderlyingState(symbol, { source: "ERROR" });
                showMarketDataError(
                    "Massive stock WebSocket authentication failed. Close other dashboard tabs and confirm this API key has real-time Stocks access. Trying the compatible fallback now.",
                );
                try {
                    socket.close();
                } catch (_) {}
            } else if (message.ev === "Q" && message.sym === symbol) {
                const bid = Number(message.bp || 0);
                const ask = Number(message.ap || 0);
                const quoteTimestamp = massiveEventTimeMs(message.t);
                setLiveUnderlyingState(symbol, {
                    bid,
                    ask,
                    quoteTimestamp,
                    timestamp: quoteTimestamp,
                    source: "MASSIVE SIP LIVE",
                });
            } else if (message.ev === "T" && message.sym === symbol) {
                const last = Number(message.p || 0);
                const tradeTimestamp = massiveEventTimeMs(message.t);
                setLiveUnderlyingState(symbol, {
                    last,
                    tradeTimestamp,
                    timestamp: tradeTimestamp,
                    source: "MASSIVE SIP LIVE",
                });
            } else if (message.ev === "A" && message.sym === symbol) {
                const aggregateClose = Number(message.c || 0);
                if (!(aggregateClose > 0)) continue;
                setLiveUnderlyingState(symbol, {
                    aggregateClose,
                    last: aggregateClose,
                    calculationPrice: aggregateClose,
                    timestamp: massiveEventTimeMs(message.e || message.s),
                    source: "MASSIVE SIP CHART",
                });
            }
        }
    };

    socket.onerror = () =>
        setLiveUnderlyingState(symbol, { source: "RECONNECTING" });
    socket.onclose = () => {
        if (connectionId !== liveUnderlyingConnectionId) return;
        setLiveUnderlyingState(symbol, { source: "RECONNECTING" });
        const nextEndpoint =
            (endpointIndex + 1) % MASSIVE_STOCKS_SOCKETS.length;
        liveUnderlyingReconnectTimer = setTimeout(
            () => connectLiveUnderlyingStream(apiKey, nextEndpoint),
            1200,
        );
    };
}

async function fetchAllOptions(contractType, expirationDate, apiKey) {
    let url =
        "https://api.polygon.io/v3/snapshot/options/" +
        polygonUnderlyingTicker(window.__CHAIN_SYMBOL) +
        "?expiration_date=" +
        encodeURIComponent(expirationDate) +
        "&contract_type=" +
        encodeURIComponent(contractType) +
        "&limit=250" +
        "&apiKey=" +
        encodeURIComponent(apiKey);
    let all = [];
    while (url) {
        const res = await fetchWithTimeout(url, { cache: "no-store" }, 12000);
        if (!res.ok) {
            const text = await res.text();
            throw new Error("Polygon error " + res.status + ": " + text);
        }
        const data = await res.json();
        all = all.concat(data.results || []);
        url = data.next_url
            ? data.next_url + "&apiKey=" + encodeURIComponent(apiKey)
            : null;
    }
    return all;
}

function buildWall(chain, type, full = false, spotPrice = null) {
    const map = new Map();
    const spotSq =
        Number.isFinite(spotPrice) && spotPrice > 0 ? spotPrice * spotPrice : 0;
    for (const c of chain) {
        const strike = Number(c.details?.strike_price);
        if (!Number.isFinite(strike)) continue;

        const oi = c.open_interest || 0;
        const volume = c.day?.volume || 0;
        const gamma = Math.abs(c.greeks?.gamma || 0);
        const delta = Math.abs(c.greeks?.delta || 0);
        const bid = c.last_quote?.bid || 0;
        const ask = c.last_quote?.ask || 0;
        const mid = bid && ask ? (bid + ask) / 2 : c.last_trade?.price || 0;
        // Dollar gamma exposure: gamma * OI * 100 shares/contract * spot^2 * 0.01 = $ exposure per 1% underlying move.
        // Falls back to the un-dollarized share-equivalent value if spot price isn't available yet.
        const gammaShares = gamma * oi * 100;
        const gammaExposure =
            spotSq > 0 ? gammaShares * spotSq * 0.01 : gammaShares;
        const signedGamma = type === "call" ? gammaExposure : -gammaExposure;
        const deltaExposure = delta * oi * 100;
        const premium = mid * volume * 100;
        const score =
            oi +
            volume * 2 +
            gammaShares * 12 +
            deltaExposure * 0.04 +
            premium * 0.001;

        if (!map.has(strike)) {
            map.set(strike, {
                strike,
                openInterest: 0,
                volume: 0,
                gammaExposure: 0,
                signedGamma: 0,
                deltaExposure: 0,
                premium: 0,
                score: 0,
            });
        }
        const row = map.get(strike);
        row.openInterest += oi;
        row.volume += volume;
        row.gammaExposure += gammaExposure;
        row.signedGamma += signedGamma;
        row.deltaExposure += deltaExposure;
        row.premium += premium;
        row.score += score;
    }
    const result = [...map.values()].sort((a, b) => b.score - a.score);
    return full
        ? result
        : result.slice(
              0,
              Number(document.getElementById("rowsToShow").value || 15),
          );
}

function totalPremium(chain) {
    return chain.reduce((total, c) => {
        const volume = c.day?.volume || 0;
        const bid = c.last_quote?.bid || 0;
        const ask = c.last_quote?.ask || 0;
        const mid = bid && ask ? (bid + ask) / 2 : c.last_trade?.price || 0;
        return total + mid * volume * 100;
    }, 0);
}

function optionPremium(c) {
    const volume = c.day?.volume || 0;
    const bid = c.last_quote?.bid || 0;
    const ask = c.last_quote?.ask || 0;
    const mid = bid && ask ? (bid + ask) / 2 : c.last_trade?.price || 0;
    return mid * volume * 100;
}

function buildPremiumByStrike(calls, puts) {
    const map = new Map();
    function ensure(strike) {
        if (!map.has(strike)) {
            map.set(strike, {
                strike,
                callPremium: 0,
                putPremium: 0,
                netPremium: 0,
                callVolume: 0,
                putVolume: 0,
                bias: "BALANCED",
            });
        }
        return map.get(strike);
    }
    for (const c of calls) {
        const strike = Number(c.details?.strike_price);
        if (!Number.isFinite(strike)) continue;
        const row = ensure(strike);
        row.callPremium += optionPremium(c);
        row.callVolume += c.day?.volume || 0;
    }
    for (const p of puts) {
        const strike = Number(p.details?.strike_price);
        if (!Number.isFinite(strike)) continue;
        const row = ensure(strike);
        row.putPremium += optionPremium(p);
        row.putVolume += p.day?.volume || 0;
    }
    for (const row of map.values()) {
        row.netPremium = row.callPremium - row.putPremium;
        if (row.netPremium > 0) row.bias = "CALL";
        else if (row.netPremium < 0) row.bias = "PUT";
    }
    return [...map.values()].sort(
        (a, b) => Math.abs(b.netPremium) - Math.abs(a.netPremium),
    );
}

function maxBy(rows, key) {
    return rows.length
        ? rows.reduce((max, r) => (r[key] > max[key] ? r : max), rows[0])
        : null;
}

function getUnderlyingSpyPrice(calls, puts) {
    const chain = [...calls, ...puts];
    for (const c of chain) {
        const u = c.underlying_asset || {};
        const price = Number(
            u.price ?? u.value ?? u.last_price ?? u.close ?? u.day?.close ?? 0,
        );
        if (Number.isFinite(price) && price > 0) return price;
    }
    return null;
}

function filterRowsNearSpyPrice(rows, spyPrice, range = 10) {
    if (!Number.isFinite(spyPrice) || spyPrice <= 0) return rows;
    return rows.filter((r) => Math.abs(Number(r.strike) - spyPrice) <= range);
}

function getOrderSideStrikeWindow(source, spyPrice) {
    if (!Number.isFinite(Number(spyPrice)) || Number(spyPrice) <= 0) return [];
    const strikes = [
        ...new Set(
            (source || [])
                .map((item) => {
                    if (typeof item === "number") return item;
                    return Number(item?.strike ?? item?.details?.strike_price);
                })
                .filter((strike) => Number.isFinite(strike) && strike > 0),
        ),
    ];
    const lower = strikes
        .filter((strike) => strike <= Number(spyPrice))
        .sort((a, b) => b - a)
        .slice(0, ORDER_SIDE_STRIKES_PER_SIDE);
    const upper = strikes
        .filter((strike) => strike > Number(spyPrice))
        .sort((a, b) => a - b)
        .slice(0, ORDER_SIDE_STRIKES_PER_SIDE);
    const selected = new Set([...lower, ...upper]);

    // If one side has fewer than seven listed strikes, fill the remaining slots
    // with the closest available strikes so the table still caps at 14 total.
    if (selected.size < ORDER_SIDE_TOTAL_STRIKES) {
        strikes
            .slice()
            .sort(
                (a, b) =>
                    Math.abs(a - Number(spyPrice)) -
                    Math.abs(b - Number(spyPrice)),
            )
            .forEach((strike) => {
                if (selected.size < ORDER_SIDE_TOTAL_STRIKES)
                    selected.add(strike);
            });
    }
    return [...selected]
        .sort((a, b) => a - b)
        .slice(0, ORDER_SIDE_TOTAL_STRIKES);
}

function filterOrderSideRows(rows, spyPrice, strikeReference = rows) {
    const allowed = new Set(
        getOrderSideStrikeWindow(strikeReference, spyPrice),
    );
    if (!allowed.size) return [];
    return (rows || []).filter((row) => allowed.has(Number(row.strike)));
}

function estimateGammaFlip(fullCalls, fullPuts) {
    const byStrike = new Map();
    for (const r of fullCalls)
        byStrike.set(r.strike, (byStrike.get(r.strike) || 0) + r.signedGamma);
    for (const r of fullPuts)
        byStrike.set(r.strike, (byStrike.get(r.strike) || 0) + r.signedGamma);
    const rows = [...byStrike.entries()]
        .map(([strike, signedGamma]) => ({ strike, signedGamma }))
        .sort((a, b) => a.strike - b.strike);
    if (!rows.length) return null;

    let best = rows[0];
    for (let i = 1; i < rows.length; i++) {
        if (
            (rows[i - 1].signedGamma <= 0 && rows[i].signedGamma >= 0) ||
            (rows[i - 1].signedGamma >= 0 && rows[i].signedGamma <= 0)
        ) {
            return rows[i];
        }
        if (Math.abs(rows[i].signedGamma) < Math.abs(best.signedGamma))
            best = rows[i];
    }
    return best;
}

function updateTbodyHTML(tbody, html) {
    if (!tbody) return;
    // Do not clear first. Only swap when the new table HTML is ready and different.
    // This keeps the 12-second auto-refresh running in the background without blinking.
    if (tbody.innerHTML !== html) tbody.innerHTML = html;
}

function renderTable(id, rows) {
    const tbody = document.getElementById(id);
    let html = "";
    if (!rows.length) {
        html = '<tr><td colspan="7" class="muted">No data found</td></tr>';
    } else {
        html = rows
            .map(
                (r) =>
                    `<tr><td>$${r.strike}</td><td>${fmt(r.openInterest)}</td><td>${fmt(r.volume)}</td><td>${fmt(r.gammaExposure, 2)}</td><td>${fmt(r.deltaExposure, 2)}</td><td>${money(r.premium)}</td><td>${fmt(r.score, 2)}</td></tr>`,
            )
            .join("");
    }
    updateTbodyHTML(tbody, html);
}

function quoteSize(q, side) {
    if (!q) return 0;
    const key = side === "ask" ? "ask_size" : "bid_size";
    const altKey = side === "ask" ? "askSize" : "bidSize";
    return Number(q[key] ?? q[altKey] ?? 0) || 0;
}

function buildWhaleBidAskByStrike(calls, puts) {
    const map = new Map();
    function ensure(strike) {
        if (!map.has(strike)) {
            map.set(strike, {
                strike,
                callBidVolume: 0,
                callAskVolume: 0,
                putBidVolume: 0,
                putAskVolume: 0,
                callBidValue: 0,
                callAskValue: 0,
                putBidValue: 0,
                putAskValue: 0,
                callBidPremium: 0,
                callAskPremium: 0,
                putBidPremium: 0,
                putAskPremium: 0,
                netWhale: 0,
                bias: "BALANCED",
            });
        }
        return map.get(strike);
    }

    for (const c of calls) {
        const strike = Number(c.details?.strike_price);
        if (!Number.isFinite(strike)) continue;
        const bid = Number(c.last_quote?.bid || 0);
        const ask = Number(c.last_quote?.ask || 0);
        const bidSize = quoteSize(c.last_quote, "bid");
        const askSize = quoteSize(c.last_quote, "ask");
        const row = ensure(strike);
        if (bidSize > 0) {
            row.callBidVolume += bidSize;
            row.callBidValue += bid * bidSize;
            row.callBidPremium += bid * bidSize * 100;
        }
        if (askSize > 0) {
            row.callAskVolume += askSize;
            row.callAskValue += ask * askSize;
            row.callAskPremium += ask * askSize * 100;
        }
    }

    for (const p of puts) {
        const strike = Number(p.details?.strike_price);
        if (!Number.isFinite(strike)) continue;
        const bid = Number(p.last_quote?.bid || 0);
        const ask = Number(p.last_quote?.ask || 0);
        const bidSize = quoteSize(p.last_quote, "bid");
        const askSize = quoteSize(p.last_quote, "ask");
        const row = ensure(strike);
        if (bidSize > 0) {
            row.putBidVolume += bidSize;
            row.putBidValue += bid * bidSize;
            row.putBidPremium += bid * bidSize * 100;
        }
        if (askSize > 0) {
            row.putAskVolume += askSize;
            row.putAskValue += ask * askSize;
            row.putAskPremium += ask * askSize * 100;
        }
    }

    for (const row of map.values()) {
        row.callBid =
            row.callBidVolume > 0 ? row.callBidValue / row.callBidVolume : 0;
        row.callAsk =
            row.callAskVolume > 0 ? row.callAskValue / row.callAskVolume : 0;
        row.putBid =
            row.putBidVolume > 0 ? row.putBidValue / row.putBidVolume : 0;
        row.putAsk =
            row.putAskVolume > 0 ? row.putAskValue / row.putAskVolume : 0;
        const bullishWhale = row.callAskPremium + row.putBidPremium;
        const bearishWhale = row.callBidPremium + row.putAskPremium;
        row.netWhale = bullishWhale - bearishWhale;
        if (row.netWhale > 0) row.bias = "BULL WHALE";
        else if (row.netWhale < 0) row.bias = "BEAR WHALE";
    }
    return [...map.values()].sort(
        (a, b) => Math.abs(b.netWhale) - Math.abs(a.netWhale),
    );
}

function quoteTimestampMs(q, fallbackMs = Date.now()) {
    if (!q) return fallbackMs;

    // Polygon may use different timestamp fields depending on the endpoint/data plan.
    // Try all known quote timestamp fields first, then fall back to the refresh time
    // so the Order Time column never stays blank.
    const raw =
        q.participant_timestamp ??
        q.sip_timestamp ??
        q.trf_timestamp ??
        q.last_updated ??
        q.updated ??
        q.timestamp ??
        q.time ??
        q.t ??
        null;

    if (raw === null || raw === undefined || raw === "") return fallbackMs;

    if (typeof raw === "string" && raw.includes("T")) {
        const parsed = Date.parse(raw);
        return Number.isFinite(parsed) ? parsed : fallbackMs;
    }

    const n = Number(raw);
    if (!Number.isFinite(n) || n <= 0) return fallbackMs;
    if (n > 1000000000000000000) return Math.floor(n / 1000000); // nanoseconds, very large
    if (n > 1000000000000000) return Math.floor(n / 1000000); // nanoseconds
    if (n > 1000000000000) return n; // milliseconds
    return n * 1000; // seconds
}

function fmtTime(ms) {
    const safeMs = ms || Date.now();
    return new Date(safeMs).toLocaleString("en-US", {
        timeZone: "America/New_York",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hour12: true,
    });
}

function fmtAge(ms) {
    if (!ms) return "refresh time";
    const diffSec = Math.max(0, Math.floor((Date.now() - ms) / 1000));
    if (diffSec < 60) return diffSec + "s ago";
    const diffMin = Math.floor(diffSec / 60);
    if (diffMin < 60) return diffMin + "m ago";
    const diffHr = Math.floor(diffMin / 60);
    return diffHr + "h " + (diffMin % 60) + "m ago";
}

// Flow controls support rolling windows up to six hours or the current New York regular session.
window.flowWindowMode = String(window.flowWindowMode || "rolling");
window.flowWindowSeconds = Number(window.flowWindowSeconds || 1800);
window.orderFlowMinPremium = Number(window.orderFlowMinPremium || 100000);
const MAX_ORDER_FLOW_WINDOW_MS = 24 * 60 * 60 * 1000;
const ORDER_SIDE_FLOW_MEMORY_MAX_ROWS = 25000;
const orderSideFlowMemory = new Map();

function zonedDateTimeToUtcMs(year, month, day, hour, minute, timeZone) {
    const target = Date.UTC(year, month - 1, day, hour, minute, 0);
    let guess = target;
    const formatter = new Intl.DateTimeFormat("en-US", {
        timeZone,
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
        hourCycle: "h23",
    });
    for (let i = 0; i < 3; i++) {
        const parts = Object.fromEntries(
            formatter
                .formatToParts(new Date(guess))
                .filter((p) => p.type !== "literal")
                .map((p) => [p.type, p.value]),
        );
        const represented = Date.UTC(
            Number(parts.year),
            Number(parts.month) - 1,
            Number(parts.day),
            Number(parts.hour),
            Number(parts.minute),
            Number(parts.second),
        );
        guess += target - represented;
    }
    return guess;
}

function getCurrentNyTradingSessionBoundsMs() {
    const parts = Object.fromEntries(
        new Intl.DateTimeFormat("en-US", {
            timeZone: "America/New_York",
            year: "numeric",
            month: "2-digit",
            day: "2-digit",
        })
            .formatToParts(new Date())
            .filter((p) => p.type !== "literal")
            .map((p) => [p.type, p.value]),
    );
    const year = Number(parts.year),
        month = Number(parts.month),
        day = Number(parts.day);
    return {
        start: zonedDateTimeToUtcMs(
            year,
            month,
            day,
            9,
            30,
            "America/New_York",
        ),
        end: zonedDateTimeToUtcMs(year, month, day, 16, 0, "America/New_York"),
    };
}

function getCurrent0DteSessionStartMs() {
    return getCurrentNyTradingSessionBoundsMs().start;
}

function isAllDay0DteFlowWindow() {
    return String(window.flowWindowMode || "") === "all_day";
}

function getOrderFlowMinPremium() {
    const selected = Number(
        window.orderFlowMinPremium ||
            document.getElementById("flowPremiumFilter")?.value ||
            100000,
    );
    return Math.max(0, selected);
}

function getOrderFlowWindowMs() {
    if (isAllDay0DteFlowWindow()) {
        const session = getCurrentNyTradingSessionBoundsMs();
        return Math.max(0, Math.min(Date.now(), session.end) - session.start);
    }
    const seconds = Number(window.flowWindowSeconds || 600);
    return Math.max(60, Math.min(21600, seconds)) * 1000;
}

function getOrderFlowWindowLabel() {
    if (isAllDay0DteFlowWindow())
        return "All NY Trading Hours (9:30 AM–4:00 PM ET)";
    const minutes = Math.round(getOrderFlowWindowMs() / 60000);
    return minutes >= 60
        ? minutes / 60 + (minutes === 60 ? " hour" : " hours")
        : minutes + " minutes";
}

function isWithinOrderFlowWindow(ms) {
    if (isAllDay0DteFlowWindow()) {
        const value = Number(ms);
        const session = getCurrentNyTradingSessionBoundsMs();
        return (
            Number.isFinite(value) &&
            value >= session.start &&
            value <= Math.min(Date.now() + 5000, session.end)
        );
    }
    const age = Date.now() - Number(ms);
    return (
        Number.isFinite(Number(ms)) && age <= getOrderFlowWindowMs() && age >= 0
    );
}

function isWithinMaxOrderFlowMemory(ms) {
    const age = Date.now() - Number(ms);
    return (
        Number.isFinite(Number(ms)) &&
        age <= MAX_ORDER_FLOW_WINDOW_MS &&
        age >= 0
    );
}

function orderMemoryKey(r) {
    // Live prints keep their exchange/trade identity so two orders at the same strike,
    // price, size, and millisecond never collapse into one position.
    const sourceId = String(r.tradeId || r.sequence || r.eventTimeRaw || "");
    if (sourceId) return ["LIVE", r.type, r.strike, sourceId].join("|");

    // Snapshot-derived rows have no exchange trade id. Bucket those refresh-time
    // observations by second to avoid cloning the same quote on every refresh.
    return [
        Math.floor(Number(r.timeMs || Date.now()) / 1000),
        r.strike,
        r.type,
        r.side,
        Number(r.bid || 0).toFixed(3),
        Number(r.ask || 0).toFixed(3),
        Number(r.delta || 0).toFixed(4),
        r.size,
    ].join("|");
}

function rememberRecentOrderRows(rows) {
    const now = Date.now();

    // Keep OLD filled premium, but refresh LIVE premium for older rows.
    // This fixes Gain/Loss: entryPrice = filled side price, currentMark = newest option mark.
    const liveByContract = new Map();
    for (const r of rows || []) {
        if (!r) continue;
        const k = [r.strike, String(r.type || "").toUpperCase()].join("|");
        const liveMark = Number(r.currentMark || r.mark || 0);
        if (liveMark > 0)
            liveByContract.set(k, {
                bid: Number(r.bid || 0),
                ask: Number(r.ask || 0),
                mark: liveMark,
                currentMark: liveMark,
            });
    }
    for (const [key, oldRow] of orderSideFlowMemory.entries()) {
        const k = [oldRow.strike, String(oldRow.type || "").toUpperCase()].join(
            "|",
        );
        const live = liveByContract.get(k);
        if (live) {
            oldRow.liveBid = live.bid;
            oldRow.liveAsk = live.ask;
            oldRow.currentMark = live.currentMark;
            oldRow.mark = live.mark;
        }
    }

    for (const r of rows || []) {
        if (!r || !Number.isFinite(Number(r.timeMs))) continue;
        if (!isWithinMaxOrderFlowMemory(r.timeMs)) continue;
        const entry = Number(
            r.entryPrice ||
                (String(r.side).toUpperCase() === "ASK" ? r.ask : r.bid) ||
                0,
        );
        orderSideFlowMemory.set(orderMemoryKey(r), {
            ...r,
            entryPrice: entry,
            filledPremium: entry,
            currentMark: Number(r.currentMark || r.mark || 0),
        });
    }
    for (const [key, r] of orderSideFlowMemory.entries()) {
        if (!isWithinMaxOrderFlowMemory(r.timeMs))
            orderSideFlowMemory.delete(key);
    }
    if (orderSideFlowMemory.size > ORDER_SIDE_FLOW_MEMORY_MAX_ROWS) {
        const newest = [...orderSideFlowMemory.entries()]
            .sort((a, b) => Number(b[1].timeMs || 0) - Number(a[1].timeMs || 0))
            .slice(0, ORDER_SIDE_FLOW_MEMORY_MAX_ROWS);
        orderSideFlowMemory.clear();
        for (const [key, value] of newest) orderSideFlowMemory.set(key, value);
    }
    return [...orderSideFlowMemory.values()].sort(
        (a, b) => (b.timeMs || 0) - (a.timeMs || 0) || b.premium - a.premium,
    );
}

function isWithinLastFiveMinutes(ms) {
    if (!Number.isFinite(ms) || ms <= 0) return false;
    const ageMs = Date.now() - ms;
    return ageMs >= 0 && ageMs <= 10 * 60 * 1000;
}

function buildOrderSideFlow(calls, puts) {
    const now = Date.now();
    const delayMs = 15 * 60 * 1000; // options snapshots can be delayed depending on the data plan
    const rows = [];
    const summary = { forced: 0, delayed: 0, call: 0, put: 0 };

    function addRow(c, type) {
        const strike = Number(c.details?.strike_price);
        if (!Number.isFinite(strike)) return;
        const q = c.last_quote || {};
        const bid = Number(q.bid || 0);
        const ask = Number(q.ask || 0);
        const lastTrade = Number(c.last_trade?.price || 0);
        const mark =
            bid > 0 && ask > 0
                ? (bid + ask) / 2
                : lastTrade > 0
                  ? lastTrade
                  : bid || ask || 0;
        const delta = Number(c.greeks?.delta || 0);
        const gamma = Math.abs(Number(c.greeks?.gamma || 0));
        const bidSize = quoteSize(q, "bid");
        const askSize = quoteSize(q, "ask");
        const ts = quoteTimestampMs(q, now);
        const hasRealTime = Boolean(
            q.participant_timestamp ||
            q.sip_timestamp ||
            q.trf_timestamp ||
            q.last_updated ||
            q.updated ||
            q.timestamp ||
            q.time ||
            q.t,
        );
        const delayed = hasRealTime ? now - ts > delayMs : false;
        const spread = ask > 0 && bid > 0 ? ask - bid : 0;

        const sides = [
            { side: "BID", size: bidSize, price: bid, oppositeSize: askSize },
            { side: "ASK", size: askSize, price: ask, oppositeSize: bidSize },
        ];

        for (const x of sides) {
            if (x.size <= 0 || x.price <= 0) continue;
            const forced =
                x.size >= Math.max(50, x.oppositeSize * 3) && spread >= 0;
            const status = delayed
                ? "DELAYED"
                : forced
                  ? "FORCED"
                  : hasRealTime
                    ? "NORMAL"
                    : "REFRESH TIME";
            const premium = x.price * x.size * 100;
            const signal = type.toUpperCase() + " " + x.side;
            const hedgeShares = Math.abs(delta) * x.size * 100;
            const dealerHedge =
                (type === "call" && x.side === "ASK") ||
                (type === "put" && x.side === "BID")
                    ? "BUY"
                    : "SELL";
            rows.push({
                timeMs: ts,
                hasRealTime,
                strike,
                type: type.toUpperCase(),
                side: x.side,
                bid,
                ask,
                mark,
                entryPrice: x.price,
                currentMark: mark,
                delta,
                gamma,
                size: x.size,
                premium,
                hedgeShares,
                dealerHedge,
                hedgeSharePct: 0,
                orders: 1,
                status,
                signal,
            });
            summary[type] += 1;
            if (forced) summary.forced += 1;
            if (delayed) summary.delayed += 1;
        }
    }

    calls.forEach((c) => addRow(c, "call"));
    puts.forEach((p) => addRow(p, "put"));
    rows.sort(
        (a, b) => (b.timeMs || 0) - (a.timeMs || 0) || b.premium - a.premium,
    );
    return { rows, summary };
}

// ---------------------------------------------------------------
// LIVE TRADE-PRINT FLOW
// Subscribes to Massive's options trades WebSocket for contracts
// currently in the selected 14-strike window around spot and converts each
// executed print into a row shaped exactly like buildOrderSideFlow's
// rows, so it plugs into the existing rememberRecentOrderRows /
// dealerFlowGex / largeOrderBlocks / scanner pipeline unchanged.
// Aggressor side is classified against the NBBO at print time
// (quote-rule / Lee-Ready style): trade at/near ask = buyer aggressor
// (BUY the option), trade at/near bid = seller aggressor.
// If the API key's plan doesn't include options trade-stream access,
// or markets are closed, this silently produces zero rows and the
// table falls back to the quote-imbalance rows exactly as before.
// ---------------------------------------------------------------
let tradeFlowSocket = null;
let tradeFlowSubscribed = new Set();
let tradeFlowContractInfo = new Map(); // O:ticker -> {strike, type, delta, gamma, bid, ask}
let tradeFlowChainByStrike = new Map(); // strike -> {CALL:{volume,oi,bid,mark,ask}, PUT:{...}}
window.tradeFlowChainByStrike = tradeFlowChainByStrike;
let liveTradeFlowRows = [];
const LIVE_TRADE_FLOW_MAX_ROWS = 25000;
const LIVE_TRADE_FLOW_RENDER_MS = 1500;
let liveTradeFlowRenderTimer = null;
const TRADE_FLOW_LOCK_KEY = "optionsSwift.optionsTradeSocketLeader.v1";
const TRADE_FLOW_LOCK_TTL_MS = 15000;
const TRADE_FLOW_HEARTBEAT_MS = 5000;
const TRADE_FLOW_TAB_ID = (() => {
    try {
        let id = sessionStorage.getItem("optionsSwift.tradeFlowTabId");
        if (!id) {
            id =
                window.crypto?.randomUUID?.() ||
                Date.now().toString(36) + Math.random().toString(36).slice(2);
            sessionStorage.setItem("optionsSwift.tradeFlowTabId", id);
        }
        return id;
    } catch (e) {
        return Date.now().toString(36) + Math.random().toString(36).slice(2);
    }
})();
let tradeFlowIsLeader = false;
let tradeFlowLockHeartbeat = null;
let tradeFlowReconnectTimer = null;
let tradeFlowLeadershipTimer = null;
let tradeFlowReconnectAttempt = 0;
let tradeFlowTerminalFailure = false;
let tradeFlowLastApiKey = "";
let tradeFlowManualClose = false;

function parseOptionTicker(ticker) {
    const m = /^O:([A-Z]+)(\d{6})([CP])(\d{8})$/.exec(String(ticker || ""));
    if (!m) return null;
    return {
        root: m[1],
        expiry: m[2],
        type: m[3] === "C" ? "CALL" : "PUT",
        strike: parseInt(m[4], 10) / 1000,
    };
}

function classifyTradeSide(price, bid, ask) {
    if (!(bid > 0) || !(ask > 0)) return null;
    if (price >= ask - 0.005) return "ASK";
    if (price <= bid + 0.005) return "BID";
    return price > (bid + ask) / 2 ? "ASK" : "BID";
}

function setTradeFlowStatus(label, cls, detail = "") {
    const el = document.getElementById("tradeFlowStatus");
    if (!el) return;
    el.textContent = label;
    el.className = cls;
    el.title = detail || label;
}

function readTradeFlowLock() {
    try {
        const value = localStorage.getItem(TRADE_FLOW_LOCK_KEY);
        return value ? JSON.parse(value) : null;
    } catch (e) {
        return null;
    }
}

function writeTradeFlowLock() {
    try {
        localStorage.setItem(
            TRADE_FLOW_LOCK_KEY,
            JSON.stringify({
                tabId: TRADE_FLOW_TAB_ID,
                expiresAt: Date.now() + TRADE_FLOW_LOCK_TTL_MS,
            }),
        );
        const confirmed = readTradeFlowLock();
        return Boolean(confirmed && confirmed.tabId === TRADE_FLOW_TAB_ID);
    } catch (e) {
        // If browser storage is unavailable, keep the page functional with an in-tab socket.
        return true;
    }
}

function releaseTradeFlowLock() {
    if (tradeFlowLockHeartbeat) clearInterval(tradeFlowLockHeartbeat);
    tradeFlowLockHeartbeat = null;
    try {
        const lock = readTradeFlowLock();
        if (lock && lock.tabId === TRADE_FLOW_TAB_ID)
            localStorage.removeItem(TRADE_FLOW_LOCK_KEY);
    } catch (e) {}
    tradeFlowIsLeader = false;
}

function stopTradeFlowSocket() {
    tradeFlowManualClose = true;
    if (liveTradeFlowRenderTimer) clearTimeout(liveTradeFlowRenderTimer);
    liveTradeFlowRenderTimer = null;
    if (tradeFlowReconnectTimer) clearTimeout(tradeFlowReconnectTimer);
    tradeFlowReconnectTimer = null;
    if (tradeFlowLeadershipTimer) clearTimeout(tradeFlowLeadershipTimer);
    tradeFlowLeadershipTimer = null;
    if (tradeFlowSocket) {
        const socket = tradeFlowSocket;
        tradeFlowSocket = null;
        socket.onclose = null;
        socket.onerror = null;
        try {
            socket.close(1000, "Options Swift connection handoff");
        } catch (e) {}
    }
    tradeFlowSubscribed = new Set();
}

function loseTradeFlowLeadership(label = "OTHER TAB LIVE") {
    stopTradeFlowSocket();
    releaseTradeFlowLock();
    setTradeFlowStatus(
        label,
        "connecting",
        "Another visible Options Swift tab owns the single Options WebSocket connection.",
    );
    scheduleTradeFlowLeadershipCheck(tradeFlowLastApiKey);
}

function startTradeFlowHeartbeat() {
    if (tradeFlowLockHeartbeat) clearInterval(tradeFlowLockHeartbeat);
    tradeFlowLockHeartbeat = setInterval(() => {
        if (!tradeFlowIsLeader) return;
        const lock = readTradeFlowLock();
        if (
            lock &&
            lock.tabId !== TRADE_FLOW_TAB_ID &&
            Number(lock.expiresAt || 0) > Date.now()
        ) {
            loseTradeFlowLeadership();
            return;
        }
        writeTradeFlowLock();
    }, TRADE_FLOW_HEARTBEAT_MS);
}

function acquireTradeFlowLeadership() {
    const lock = readTradeFlowLock();
    const otherTabHasLock =
        lock &&
        lock.tabId !== TRADE_FLOW_TAB_ID &&
        Number(lock.expiresAt || 0) > Date.now();
    if (otherTabHasLock) {
        tradeFlowIsLeader = false;
        return false;
    }
    tradeFlowIsLeader = writeTradeFlowLock();
    if (tradeFlowIsLeader) startTradeFlowHeartbeat();
    return tradeFlowIsLeader;
}

function scheduleTradeFlowReconnect(apiKey, reason = "Connection closed") {
    if (
        tradeFlowTerminalFailure ||
        !tradeFlowIsLeader ||
        document.visibilityState === "hidden"
    )
        return;
    if (tradeFlowReconnectTimer) clearTimeout(tradeFlowReconnectTimer);
    const baseDelay = Math.min(
        30000,
        1000 * Math.pow(2, Math.min(tradeFlowReconnectAttempt, 5)),
    );
    const delay = baseDelay + Math.floor(Math.random() * 750);
    tradeFlowReconnectAttempt += 1;
    setTradeFlowStatus(
        "RETRY " + Math.ceil(delay / 1000) + "s",
        "connecting",
        reason,
    );
    tradeFlowReconnectTimer = setTimeout(() => {
        tradeFlowReconnectTimer = null;
        connectOptionsTradeFlow(apiKey);
    }, delay);
}

function scheduleTradeFlowLeadershipCheck(apiKey) {
    if (tradeFlowLeadershipTimer || document.visibilityState === "hidden")
        return;
    tradeFlowLeadershipTimer = setTimeout(() => {
        tradeFlowLeadershipTimer = null;
        connectOptionsTradeFlow(apiKey);
    }, 3000);
}

function setTradeFlowTerminalFailure(label, detail) {
    tradeFlowTerminalFailure = true;
    stopTradeFlowSocket();
    setTradeFlowStatus(label, "offline", detail);
    console.warn("Options trade stream stopped:", detail);
}

function updateTradeFlowContractInfo(calls, puts, spyPrice) {
    tradeFlowContractInfo.clear();
    tradeFlowChainByStrike.clear();
    const allowedStrikes = new Set(
        getOrderSideStrikeWindow([...calls, ...puts], spyPrice),
    );
    const near = [...calls, ...puts].filter((c) => {
        const strike = Number(c.details?.strike_price);
        return Number.isFinite(strike) && allowedStrikes.has(strike);
    });
    for (const c of near) {
        const ticker = c.details?.ticker;
        const strike = Number(c.details?.strike_price);
        const type = (c.details?.contract_type || "").toUpperCase();
        if (!Number.isFinite(strike) || (type !== "CALL" && type !== "PUT"))
            continue;
        const q = c.last_quote || {};
        const bid = Number(q.bid || q.bid_price || 0);
        const ask = Number(q.ask || q.ask_price || 0);
        const lastTrade = Number(c.last_trade?.price || c.last_trade?.p || 0);
        const mark =
            bid > 0 && ask > 0 ? (bid + ask) / 2 : lastTrade || bid || ask || 0;
        if (!tradeFlowChainByStrike.has(strike))
            tradeFlowChainByStrike.set(strike, { CALL: null, PUT: null });
        tradeFlowChainByStrike.get(strike)[type] = {
            volume: Number(c.day?.volume || 0),
            oi: Number(c.open_interest || 0),
            bid,
            mark,
            ask,
        };
        if (!ticker) continue;
        tradeFlowContractInfo.set(ticker, {
            strike,
            type,
            delta: Number(c.greeks?.delta || 0),
            gamma: Math.abs(Number(c.greeks?.gamma || 0)),
            bid,
            ask,
        });
    }
    resyncTradeSubscriptions();
}

function resyncTradeSubscriptions() {
    if (
        !tradeFlowIsLeader ||
        !tradeFlowSocket ||
        tradeFlowSocket.readyState !== WebSocket.OPEN
    )
        return;
    const wanted = new Set(tradeFlowContractInfo.keys());
    const toAdd = [...wanted].filter((t) => !tradeFlowSubscribed.has(t));
    const toDrop = [...tradeFlowSubscribed].filter((t) => !wanted.has(t));
    try {
        if (toAdd.length)
            tradeFlowSocket.send(
                JSON.stringify({
                    action: "subscribe",
                    params: toAdd.map((t) => "T." + t).join(","),
                }),
            );
        if (toDrop.length)
            tradeFlowSocket.send(
                JSON.stringify({
                    action: "unsubscribe",
                    params: toDrop.map((t) => "T." + t).join(","),
                }),
            );
    } catch (e) {
        const failedSocket = tradeFlowSocket;
        tradeFlowSocket = null;
        if (failedSocket) {
            failedSocket.onclose = null;
            try {
                failedSocket.close();
            } catch (closeError) {}
        }
        scheduleTradeFlowReconnect(
            tradeFlowLastApiKey,
            "Subscription update failed: " + e.message,
        );
        return;
    }
    toAdd.forEach((t) => tradeFlowSubscribed.add(t));
    toDrop.forEach((t) => tradeFlowSubscribed.delete(t));
}

function handleLiveTradePrint(msg) {
    const info = tradeFlowContractInfo.get(msg.sym);
    const parsed = info || parseOptionTicker(msg.sym);
    if (!parsed) return;
    const bid = info ? info.bid : 0;
    const ask = info ? info.ask : 0;
    const side = classifyTradeSide(Number(msg.p), bid, ask);
    if (!side) return; // can't classify aggressor without a valid NBBO - skip rather than guess
    const size = Number(msg.s || 0);
    const price = Number(msg.p || 0);
    if (size <= 0 || price <= 0) return;
    const delta = info ? info.delta : 0;
    const gamma = info ? info.gamma : 0;
    const type = parsed.type;
    const premium = price * size * 100;
    const hedgeShares = Math.abs(delta) * size * 100;
    const dealerHedge =
        (type === "CALL" && side === "ASK") ||
        (type === "PUT" && side === "BID")
            ? "BUY"
            : "SELL";
    const mark = bid > 0 && ask > 0 ? (bid + ask) / 2 : price;
    const timeMs = Math.round(Number(msg.t || Date.now() * 1e6) / 1e6);
    const tradeId = String(msg.i || msg.id || msg.q || msg.t || "");
    liveTradeFlowRows.unshift({
        timeMs,
        strike: parsed.strike,
        type,
        side,
        bid,
        ask,
        mark,
        entryPrice: price,
        currentMark: mark,
        delta,
        gamma,
        size,
        premium,
        hedgeShares,
        dealerHedge,
        hedgeSharePct: 0,
        orders: 1,
        status: "LIVE PRINT",
        signal: type + " " + side,
        source: "TRADE",
        tradeId,
        sequence: msg.q || "",
        eventTimeRaw: String(msg.t || ""),
    });
    if (liveTradeFlowRows.length > LIVE_TRADE_FLOW_MAX_ROWS)
        liveTradeFlowRows.length = LIVE_TRADE_FLOW_MAX_ROWS;
    scheduleLiveTradeFlowRender();
}

function scheduleLiveTradeFlowRender() {
    if (
        liveTradeFlowRenderTimer !== null ||
        document.visibilityState === "hidden"
    )
        return;
    liveTradeFlowRenderTimer = window.setTimeout(() => {
        liveTradeFlowRenderTimer = null;
        if (gammaRefreshInProgress) {
            scheduleLiveTradeFlowRender();
            return;
        }
        const spot = Number(liveUnderlyingState.calculationPrice || 0);
        if (!(spot > 0) || typeof window.renderOrderSideTable !== "function")
            return;
        try {
            // Merge a bounded recent batch into all-day memory; the table itself still applies
            // the selected premium and New York flow-window filters.
            const rows = rememberRecentOrderRows(
                liveTradeFlowRows.slice(0, 1000),
            );
            window.renderOrderSideTable(rows, spot);
        } catch (error) {
            console.warn(
                "Lightweight live-flow render skipped:",
                error.message,
            );
        }
    }, LIVE_TRADE_FLOW_RENDER_MS);
}

function pruneLiveTradeFlowRows() {
    const cutoff = Date.now() - MAX_ORDER_FLOW_WINDOW_MS;
    liveTradeFlowRows = liveTradeFlowRows.filter((r) => r.timeMs >= cutoff);
}

function connectOptionsTradeFlow(apiKey) {
    tradeFlowLastApiKey = String(apiKey || "").trim();
    if (!tradeFlowLastApiKey) {
        setTradeFlowTerminalFailure(
            "API KEY MISSING",
            "The Options WebSocket API key is empty.",
        );
        return;
    }
    if (document.visibilityState === "hidden") {
        setTradeFlowStatus(
            "TAB PAUSED",
            "offline",
            "This tab is hidden. The visible dashboard tab can own the live trade connection.",
        );
        return;
    }
    if (!acquireTradeFlowLeadership()) {
        setTradeFlowStatus(
            "OTHER TAB LIVE",
            "connecting",
            "Close the other dashboard tab or bring this tab to the front.",
        );
        scheduleTradeFlowLeadershipCheck(tradeFlowLastApiKey);
        return;
    }
    if (tradeFlowLeadershipTimer) clearTimeout(tradeFlowLeadershipTimer);
    tradeFlowLeadershipTimer = null;
    if (tradeFlowTerminalFailure) return;
    if (
        tradeFlowSocket &&
        (tradeFlowSocket.readyState === WebSocket.OPEN ||
            tradeFlowSocket.readyState === WebSocket.CONNECTING)
    )
        return;

    stopTradeFlowSocket();
    tradeFlowManualClose = false;
    tradeFlowSubscribed = new Set();
    setTradeFlowStatus(
        "CONNECTING",
        "connecting",
        "Opening the Massive Options trade stream.",
    );
    const socket = new WebSocket("wss://socket.massive.com/options");
    tradeFlowSocket = socket;
    socket.onopen = () => {
        if (socket !== tradeFlowSocket || !tradeFlowIsLeader) return;
        setTradeFlowStatus(
            "AUTHENTICATING",
            "connecting",
            "Connected. Authenticating the Options WebSocket API key.",
        );
        socket.send(
            JSON.stringify({ action: "auth", params: tradeFlowLastApiKey }),
        );
    };
    socket.onmessage = (evt) => {
        if (socket !== tradeFlowSocket || !tradeFlowIsLeader) return;
        let msgs;
        try {
            msgs = JSON.parse(evt.data);
        } catch (e) {
            return;
        }
        for (const m of msgs) {
            if (m.ev === "status" && m.status === "auth_success") {
                tradeFlowReconnectAttempt = 0;
                setTradeFlowStatus(
                    "LIVE",
                    "live",
                    m.message || "Massive Options trades are live.",
                );
                resyncTradeSubscriptions();
            } else if (m.ev === "status") {
                const status = String(m.status || "").toLowerCase();
                const message = String(
                    m.message || status || "Options WebSocket status",
                );
                const combined = (status + " " + message).toLowerCase();
                if (status === "connected") {
                    setTradeFlowStatus("AUTHENTICATING", "connecting", message);
                } else if (
                    status === "auth_failed" ||
                    /auth.*fail|not authorized|not entitled|permission denied|does not include|plan.*required|upgrade.*plan/.test(
                        combined,
                    )
                ) {
                    setTradeFlowTerminalFailure("AUTH / PLAN ERROR", message);
                    return;
                } else if (
                    /max.*connection|connection.*limit|too many.*connection|concurrent connection/.test(
                        combined,
                    )
                ) {
                    setTradeFlowTerminalFailure(
                        "CONNECTION LIMIT",
                        message +
                            " Close other dashboard tabs/devices, then refresh this page.",
                    );
                    return;
                } else if (/error|failed/.test(combined)) {
                    setTradeFlowStatus("SOCKET ERROR", "offline", message);
                    console.warn("Options WebSocket status:", status, message);
                }
            } else if (m.ev === "T") {
                handleLiveTradePrint(m);
            }
        }
    };
    socket.onerror = () => {
        if (socket !== tradeFlowSocket || tradeFlowTerminalFailure) return;
        setTradeFlowStatus(
            "SOCKET ERROR",
            "offline",
            "The Options WebSocket reported a network or server error.",
        );
    };
    socket.onclose = (event) => {
        if (socket !== tradeFlowSocket) return;
        tradeFlowSocket = null;
        tradeFlowSubscribed = new Set();
        if (
            tradeFlowManualClose ||
            tradeFlowTerminalFailure ||
            !tradeFlowIsLeader
        )
            return;
        const reason = event.reason || "Socket closed with code " + event.code;
        scheduleTradeFlowReconnect(tradeFlowLastApiKey, reason);
    };
}

window.addEventListener("storage", (event) => {
    if (event.key !== TRADE_FLOW_LOCK_KEY) return;
    const lock = readTradeFlowLock();
    if (
        tradeFlowIsLeader &&
        lock &&
        lock.tabId !== TRADE_FLOW_TAB_ID &&
        Number(lock.expiresAt || 0) > Date.now()
    ) {
        loseTradeFlowLeadership();
    } else if (
        !tradeFlowIsLeader &&
        (!lock || Number(lock.expiresAt || 0) <= Date.now()) &&
        document.visibilityState !== "hidden"
    ) {
        window.setTimeout(
            () => connectOptionsTradeFlow(tradeFlowLastApiKey),
            250,
        );
    }
});

document.addEventListener("visibilitychange", () => {
    if (document.visibilityState === "hidden") {
        stopTradeFlowSocket();
        releaseTradeFlowLock();
        setTradeFlowStatus(
            "TAB PAUSED",
            "offline",
            "Hidden tab paused so another visible tab can use the Options connection.",
        );
    } else if (tradeFlowLastApiKey && !tradeFlowTerminalFailure) {
        window.setTimeout(
            () => connectOptionsTradeFlow(tradeFlowLastApiKey),
            150,
        );
        window.setTimeout(() => loadGammaSystem(true), 300);
    }
});

window.addEventListener("pagehide", () => {
    stopTradeFlowSocket();
    releaseTradeFlowLock();
});

function largeOrderDirection(type, side) {
    if (
        (type === "CALL" && side === "ASK") ||
        (type === "PUT" && side === "BID")
    )
        return "BULLISH";
    if (
        (type === "CALL" && side === "BID") ||
        (type === "PUT" && side === "ASK")
    )
        return "BEARISH";
    return "NEUTRAL";
}

function buildLargeOrderBlocks(rows, spyPrice = null) {
    const nearSpyRows = filterRowsNearSpyPrice(rows, spyPrice, 10);
    const bigRows = nearSpyRows.filter(
        (r) => r.premium >= 100000 || r.size >= 100,
    );
    const map = new Map();

    for (const r of bigRows) {
        const direction = largeOrderDirection(r.type, r.side);
        const key = [r.strike, r.type, r.side].join("|");
        if (!map.has(key)) {
            map.set(key, {
                strike: r.strike,
                type: r.type,
                side: r.side,
                direction,
                premium: 0,
                size: 0,
                orders: 0,
                entryValue: 0,
                markValue: 0,
                avgEntryPrice: 0,
                avgCurrentMark: 0,
                gainPct: 0,
                deltaSum: 0,
                hedgeShares: 0,
                dealerHedge: r.dealerHedge,
                impactScore: 0,
                signal: "WATCH",
                latestTimeMs: 0,
            });
        }
        const block = map.get(key);
        block.premium += r.premium;
        block.size += r.size;
        block.orders += r.orders || 1;
        block.entryValue += Number(r.entryPrice || 0) * Number(r.size || 0);
        const livePremForBlock =
            Number(r.currentMark) ||
            Number(r.mark) ||
            Number(r.last) ||
            (Number(r.bid) + Number(r.ask)) / 2 ||
            0;
        block.markValue += livePremForBlock * Number(r.size || 0);
        block.deltaSum += Math.abs(r.delta || 0) * (r.orders || 1);
        block.hedgeShares += Math.abs(r.hedgeShares || 0);
        block.latestTimeMs = Math.max(block.latestTimeMs || 0, r.timeMs || 0);
    }

    const blocks = [...map.values()]
        .map((b) => {
            const distance = Number.isFinite(spyPrice)
                ? Math.abs(Number(b.strike) - spyPrice)
                : 0;
            const proximityBoost = Number.isFinite(spyPrice)
                ? Math.max(0, 10 - distance) * 2500
                : 0;
            b.avgDelta = b.orders > 0 ? b.deltaSum / b.orders : 0;
            b.avgEntryPrice = b.size > 0 ? b.entryValue / b.size : 0;
            b.avgCurrentMark = b.size > 0 ? b.markValue / b.size : 0;
            // Robinhood-style P/L: compare live premium vs bought premium only.
            // Do NOT flip by dealer hedge / BUY / SELL.
            const rawGainPct =
                b.avgEntryPrice > 0
                    ? ((b.avgCurrentMark - b.avgEntryPrice) / b.avgEntryPrice) *
                      100
                    : 0;
            b.gainPct = rawGainPct;
            b.impactScore =
                b.premium * 0.001 +
                b.size * 8 +
                b.hedgeShares * 0.35 +
                proximityBoost;
            if (b.premium >= 1000000 || b.impactScore >= 25000)
                b.signal = "MARKET MOVE RISK";
            else if (b.premium >= 500000 || b.impactScore >= 12000)
                b.signal = "HIGH IMPACT";
            else if (b.premium >= 100000 || b.size >= 100)
                b.signal = "LARGE BLOCK";
            return b;
        })
        .sort((a, b) => b.impactScore - a.impactScore);

    const bullishPremium = blocks
        .filter((b) => b.direction === "BULLISH")
        .reduce((s, b) => s + b.premium, 0);
    const bearishPremium = blocks
        .filter((b) => b.direction === "BEARISH")
        .reduce((s, b) => s + b.premium, 0);
    return {
        blocks,
        bullishPremium,
        bearishPremium,
        netPremium: bullishPremium - bearishPremium,
    };
}

function renderLargeOrderBlocks(data, spyPrice = null) {
    const tbody = document.getElementById("largeOrderBlockTable");
    const limit = Math.max(
        30,
        Number(document.getElementById("rowsToShow").value || 30),
    );
    const blocks = (data?.blocks || []).slice(0, limit);
    const top = blocks[0];

    document.getElementById("bullishBlockPremium").textContent = money(
        data?.bullishPremium || 0,
    );
    document.getElementById("bearishBlockPremium").textContent = money(
        data?.bearishPremium || 0,
    );
    document.getElementById("largestOrderBlock").textContent = top
        ? "$" + top.strike
        : "--";
    document.getElementById("largestOrderBlockInfo").textContent = top
        ? top.direction +
          " " +
          top.type +
          " " +
          top.side +
          " | Premium: " +
          money(top.premium) +
          " | Score: " +
          fmt(top.impactScore, 0)
        : "No large block found near current SPY.";

    const net = data?.netPremium || 0;
    const biasEl = document.getElementById("blockImpactBias");
    if (net > 0) {
        biasEl.textContent = "BULLISH";
        setClass("blockImpactBias", "bull");
    } else if (net < 0) {
        biasEl.textContent = "BEARISH";
        setClass("blockImpactBias", "bear");
    } else {
        biasEl.textContent = "BALANCED";
        setClass("blockImpactBias", "neutral");
    }
    document.getElementById("blockImpactReason").textContent =
        "Net block premium: " +
        money(net) +
        ". Watch confirmation at VWAP, gamma wall, and current price acceptance.";

    const info = document.getElementById("largeOrderBlockInfo");
    if (info) {
        info.textContent =
            Number.isFinite(spyPrice) && spyPrice > 0
                ? "Large $100,000+ premium or 100+ size blocks within +/- 10 of current SPY ($" +
                  fmt(spyPrice, 2) +
                  "). Higher impact score = larger premium, size, hedge shares, and closer to spot. Bought Prem shows the average premium paid; Current Prem shows the live mark/mid premium. Est. Gain % compares current premium vs bought premium."
                : "Large $100,000+ premium or 100+ size blocks. Current SPY price was not available, so distance scoring may be less precise.";
    }

    let html = "";
    if (!blocks.length) {
        html =
            '<tr><td colspan="15" class="muted">No large $100,000+ premium or 100+ size order blocks found near current SPY price</td></tr>';
    } else {
        html = blocks
            .map((b) => {
                const dirClass =
                    b.direction === "BULLISH"
                        ? "call"
                        : b.direction === "BEARISH"
                          ? "put"
                          : "neutral";
                const hedgeClass = b.dealerHedge === "BUY" ? "pos" : "neg";
                const hotClass =
                    b.signal === "MARKET MOVE RISK" ||
                    b.signal === "HIGH IMPACT"
                        ? ' class="hot-block"'
                        : "";
                const gainClass =
                    b.gainPct > 0
                        ? "gain-pos"
                        : b.gainPct < 0
                          ? "gain-neg"
                          : "gain-flat";
                return `<tr${hotClass}><td>${fmtTime(b.latestTimeMs)}</td><td>$${b.strike}</td><td class="${dirClass}">${b.type}</td><td>${b.side}</td><td class="${dirClass}">${b.direction}</td><td>${money(b.premium)}</td><td>$${fmt(b.avgEntryPrice, 2)}</td><td>$${fmt(b.avgCurrentMark, 2)}</td><td class="${gainClass}" title="Avg entry: $${fmt(b.avgEntryPrice, 2)} | Current mark: $${fmt(b.avgCurrentMark, 2)}">${fmtPct(b.gainPct, 1)}</td><td>${fmt(b.size)}</td><td>${fmt(b.orders)}</td><td>${fmt(b.avgDelta, 3)}</td><td class="${hedgeClass}">${b.dealerHedge} ${fmt(b.hedgeShares, 0)}</td><td class="warning">${fmt(b.impactScore, 0)}</td><td><span class="block-signal">${b.signal}</span></td></tr>`;
            })
            .join("");
    }
    updateTbodyHTML(tbody, html);
}

function updateOrderSideFlowTotals(shown) {
    const rows = Array.isArray(shown) ? shown : [];
    const callRows = rows.filter((r) => r.type === "CALL");
    const putRows = rows.filter((r) => r.type === "PUT");
    const callTotal = callRows.reduce(
        (s, r) => s + (Number(r.premium) || 0),
        0,
    );
    const putTotal = putRows.reduce((s, r) => s + (Number(r.premium) || 0), 0);
    const grandTotal = callTotal + putTotal;
    const netTotal = callTotal - putTotal;
    const callPct = grandTotal > 0 ? (callTotal / grandTotal) * 100 : 0;
    const putPct = grandTotal > 0 ? (putTotal / grandTotal) * 100 : 0;
    const netPct = grandTotal > 0 ? (netTotal / grandTotal) * 100 : 0;

    const setText = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    };
    const setWidth = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.style.width = Math.max(0, Math.min(100, value)) + "%";
    };
    const setValueClass = (id, cls) => {
        const el = document.getElementById(id);
        if (el) el.className = "order-flow-total-value " + cls;
    };

    setText("orderFlowCallTotal", money(callTotal));
    setText("orderFlowPutTotal", money(putTotal));
    setText(
        "orderFlowNetTotal",
        (netTotal > 0 ? "+" : netTotal < 0 ? "-" : "") +
            money(Math.abs(netTotal)),
    );
    setText("orderFlowCallPct", fmt(callPct, 1) + "%");
    setText("orderFlowPutPct", fmt(putPct, 1) + "%");
    setText("orderFlowSplitCallPct", fmt(callPct, 1) + "%");
    setText("orderFlowSplitPutPct", fmt(putPct, 1) + "%");
    setText("orderFlowSplitCallTotal", money(callTotal));
    setText("orderFlowSplitPutTotal", money(putTotal));
    setText("orderFlowNetPct", (netPct > 0 ? "+" : "") + fmt(netPct, 1) + "%");
    setText("orderFlowCallCount", fmt(callRows.length) + " call rows");
    setText("orderFlowPutCount", fmt(putRows.length) + " put rows");
    setWidth("orderFlowCallBar", grandTotal > 0 ? callPct : 50);
    setWidth("orderFlowPutBar", grandTotal > 0 ? putPct : 50);

    let bias = "BALANCED";
    let biasClass = "neutral";
    let strength = "WAIT";
    let netLabel = "Balanced flow";
    const absNetPct = Math.abs(netPct);
    if (netTotal > 0) {
        bias = "CALL LEAN";
        biasClass = "call";
        netLabel = "Calls leading by " + money(Math.abs(netTotal));
    } else if (netTotal < 0) {
        bias = "PUT LEAN";
        biasClass = "put";
        netLabel = "Puts leading by " + money(Math.abs(netTotal));
    }
    if (grandTotal > 0) {
        if (absNetPct >= 65) strength = "EXTREME";
        else if (absNetPct >= 35) strength = "STRONG";
        else if (absNetPct >= 15) strength = "BUILDING";
        else strength = "LIGHT";
    }
    setText("orderFlowBias", bias);
    setText("orderFlowBiasStrength", strength);
    setText("orderFlowNetLabel", netLabel);
    setValueClass("orderFlowNetTotal", biasClass);
    setValueClass("orderFlowBias", biasClass);
}

function renderOrderSideTable(rows, spyPrice = null) {
    const tbody = document.getElementById("orderSideTable");
    const orderInfo = document.getElementById("orderSideRangeInfo");
    const nearSpyRows = filterOrderSideRows(rows, spyPrice);
    // Keep ALL order rows whose Order Time ET / Age are inside the selected rolling flow window.
    // No Top 30/50/75 limit is applied here because this table is meant to show every recent order.
    const recentRows = nearSpyRows.filter((r) =>
        isWithinOrderFlowWindow(r.timeMs),
    );
    const minPremium = getOrderFlowMinPremium();
    const premiumFiltered = recentRows.filter((r) => r.premium >= minPremium);
    const totalHedgeShares = premiumFiltered.reduce(
        (s, r) => s + (Math.abs(r.hedgeShares) || 0),
        0,
    );
    const shown = premiumFiltered.map((r) => ({
        ...r,
        hedgeSharePct:
            totalHedgeShares > 0
                ? (Math.abs(r.hedgeShares) / totalHedgeShares) * 100
                : 0,
    }));
    updateOrderSideFlowTotals(shown);

    if (orderInfo) {
        const windowLabel = getOrderFlowWindowLabel();
        orderInfo.textContent =
            Number.isFinite(spyPrice) && spyPrice > 0
                ? "Shows ALL rows inside the selected " +
                  windowLabel +
                  " flow window, premium " +
                  money(minPremium) +
                  "+, using 14 strike levels total (7 at/below and 7 above current SPY). Current SPY: $" +
                  fmt(spyPrice, 2) +
                  ". Dealer hedge share estimates each row's share of total nearby delta-hedging pressure."
                : "Shows ALL rows inside the selected " +
                  windowLabel +
                  " flow window with premium " +
                  money(minPremium) +
                  "+. Current SPY price was not available, so strike-window filtering could not be applied.";
    }

    let html = "";
    if (!shown.length) {
        html =
            '<tr><td colspan="17" class="muted">No ' +
            money(minPremium) +
            "+ bid/ask order-side rows found inside the selected flow window and nearest 14 strike levels</td></tr>";
    } else {
        html = shown
            .map((r) => {
                const selectedStrike = Number(window.__CHAIN_STRIKE || 0);
                const isSelectedStrike =
                    Number.isFinite(selectedStrike) &&
                    selectedStrike > 0 &&
                    Math.abs(Number(r.strike) - selectedStrike) < 0.001;
                const typeClass = r.type === "CALL" ? "call" : "put";
                const hedgeClass = r.dealerHedge === "BUY" ? "pos" : "neg";
                const statusClass =
                    r.status === "LIVE PRINT"
                        ? "live-print"
                        : r.status === "FORCED"
                          ? "warning"
                          : r.status === "DELAYED"
                            ? "neutral"
                            : r.status === "REFRESH TIME"
                              ? "muted"
                              : "muted";
                const blockTag =
                    r.premium >= 1000000 || r.size >= 250
                        ? " ⚠ MOVE BLOCK"
                        : r.premium >= 100000 || r.size >= 100
                          ? " LARGE BLOCK"
                          : "";
                const rowClasses = [
                    blockTag ? "hot-block" : "",
                    isSelectedStrike ? "scanner-highlight-strike" : "",
                ]
                    .filter(Boolean)
                    .join(" ");
                const hotClass = rowClasses
                    ? ' class="' +
                      rowClasses +
                      '" data-strike="' +
                      r.strike +
                      '"'
                    : "";
                const entryPrice = Number(
                    r.entryPrice ||
                        (r.side === "ASK"
                            ? Number(r.ask || 0)
                            : Number(r.bid || 0)),
                );
                const currentPrice =
                    Number(r.currentMark) ||
                    Number(r.mark) ||
                    Number(r.last) ||
                    (Number(r.bid) + Number(r.ask)) / 2 ||
                    0;
                // Robinhood-style P/L: live premium minus entry premium. Do not invert BID side.
                const estGainPct =
                    entryPrice > 0 && currentPrice > 0
                        ? ((currentPrice - entryPrice) / entryPrice) * 100
                        : 0;
                const gainClass =
                    estGainPct > 0
                        ? "gain-pos"
                        : estGainPct < 0
                          ? "gain-neg"
                          : "gain-flat";
                return `<tr${hotClass}><td>${fmtTime(r.timeMs)}</td><td>${fmtAge(r.timeMs)}</td><td>$${r.strike}</td><td class="${typeClass}">${r.type}</td><td>${r.side}</td><td>${optMoney(r.bid)}</td><td>${optMoney(entryPrice)}</td><td>${optMoney(currentPrice)}</td><td class="${typeClass}">${fmt(r.delta, 3)}</td><td>${fmt(r.size)}</td><td>${money(r.premium)}</td><td class="${gainClass}" title="Entry estimate: $${fmt(entryPrice, 2)} | Current mark: $${fmt(currentPrice, 2)}">${fmtPct(estGainPct, 1)}</td><td class="${hedgeClass}">${r.dealerHedge} ${fmt(r.hedgeShares, 0)}</td><td class="${hedgeClass}">${fmt(r.hedgeSharePct, 1)}%</td><td>${fmt(r.orders)}</td><td class="${statusClass}">${r.status}</td><td class="${typeClass}">${r.signal}${blockTag}</td></tr>`;
            })
            .join("");
    }
    updateTbodyHTML(tbody, html);
    scrollToScannerSelectedStrike();
}

function scrollToScannerSelectedStrike() {
    const strike = window.__CHAIN_STRIKE;
    if (!strike) return;
    window.setTimeout(() => {
        const row = document.querySelector(
            "#orderSideTable tr.scanner-highlight-strike",
        );
        if (!row) return;
        row.scrollIntoView({ behavior: "smooth", block: "center" });
    }, 80);
}

function renderWhaleBidAskTable(rows, spyPrice = null) {
    const tbody = document.getElementById("whaleBidAskTable");
    const limit = Number(document.getElementById("rowsToShow").value || 15);
    const nearSpyRows = filterRowsNearSpyPrice(rows, spyPrice, 10);
    const shown = nearSpyRows.slice(0, limit);
    const rangeInfo = document.getElementById("whaleRangeInfo");
    if (rangeInfo) {
        rangeInfo.textContent =
            Number.isFinite(spyPrice) && spyPrice > 0
                ? "Tracks quote-side pressure from Polygon snapshot quotes. Current SPY: $" +
                  fmt(spyPrice, 2) +
                  " | Showing strikes from $" +
                  fmt(spyPrice - 10, 2) +
                  " to $" +
                  fmt(spyPrice + 10, 2) +
                  "."
                : "Tracks quote-side pressure from Polygon snapshot quotes. Showing top rows because current SPY price was not available.";
    }
    let html = "";
    if (!shown.length) {
        html =
            '<tr><td colspan="15" class="muted">No whale bid/ask quote data found within +/- 10 strikes of current SPY price</td></tr>';
    } else {
        html = shown
            .map((r) => {
                const netClass = r.netWhale >= 0 ? "pos" : "neg";
                const biasClass =
                    r.bias === "BULL WHALE"
                        ? "call"
                        : r.bias === "BEAR WHALE"
                          ? "put"
                          : "neutral";
                return `<tr><td>$${r.strike}</td><td class="call">${fmt(r.callBidVolume)}</td><td class="call">${money(r.callBid)}</td><td class="call">${money(r.callBidPremium)}</td><td class="call">${fmt(r.callAskVolume)}</td><td class="call">${money(r.callAsk)}</td><td class="call">${money(r.callAskPremium)}</td><td class="put">${fmt(r.putBidVolume)}</td><td class="put">${money(r.putBid)}</td><td class="put">${money(r.putBidPremium)}</td><td class="put">${fmt(r.putAskVolume)}</td><td class="put">${money(r.putAsk)}</td><td class="put">${money(r.putAskPremium)}</td><td class="${netClass}">${money(r.netWhale)}</td><td class="${biasClass}">${r.bias}</td></tr>`;
            })
            .join("");
    }
    updateTbodyHTML(tbody, html);
}

function renderPremiumStrikeTable(rows) {
    const tbody = document.getElementById("premiumStrikeTable");
    const limit = Number(document.getElementById("rowsToShow").value || 15);
    const shown = rows.slice(0, limit);
    let html = "";
    if (!shown.length) {
        html =
            '<tr><td colspan="7" class="muted">No premium flow found</td></tr>';
    } else {
        html = shown
            .map((r) => {
                const netClass = r.netPremium >= 0 ? "pos" : "neg";
                const biasClass =
                    r.bias === "CALL"
                        ? "call"
                        : r.bias === "PUT"
                          ? "put"
                          : "neutral";
                return `<tr><td>$${r.strike}</td><td class="call">${money(r.callPremium)}</td><td class="put">${money(r.putPremium)}</td><td class="${netClass}">${money(r.netPremium)}</td><td>${fmt(r.callVolume)}</td><td>${fmt(r.putVolume)}</td><td class="${biasClass}">${r.bias}</td></tr>`;
            })
            .join("");
    }
    updateTbodyHTML(tbody, html);
}

function resetUI() {
    for (const id of [
        "topCall",
        "topPut",
        "gammaCallWall",
        "gammaPutWall",
        "gammaFlip",
        "topCallPremiumStrike",
        "topPutPremiumStrike",
        "dominantPremiumStrike",
        "topCallAskWhaleStrike",
        "topCallBidWhaleStrike",
        "topPutBidWhaleStrike",
        "topPutAskWhaleStrike",
        "largestOrderBlock",
    ])
        document.getElementById(id).textContent = "--";
    document.getElementById("totalCallPremium").textContent = "$0";
    document.getElementById("totalPutPremium").textContent = "$0";
    document.getElementById("netPremiumFlow").textContent = "$0";
    document.getElementById("totalCallAskWhalePremium").textContent = "$0";
    document.getElementById("totalCallBidWhalePremium").textContent = "$0";
    document.getElementById("totalPutBidWhalePremium").textContent = "$0";
    document.getElementById("totalPutAskWhalePremium").textContent = "$0";
    document.getElementById("bullishBlockPremium").textContent = "$0";
    document.getElementById("bearishBlockPremium").textContent = "$0";
    document.getElementById("blockImpactBias").textContent = "WAIT";
    document.getElementById("largestOrderBlockInfo").textContent =
        "Highest premium cluster near current SPY.";
    document.getElementById("blockImpactReason").textContent =
        "Net large-block pressure.";
    document.getElementById("forcedOrderCount").textContent = "0";
    document.getElementById("delayedOrderCount").textContent = "0";
    document.getElementById("callOrderCount").textContent = "0";
    document.getElementById("putOrderCount").textContent = "0";
    document.getElementById("netGamma").textContent = "0";
    document.getElementById("dealerRegime").textContent = "WAIT";
    document.getElementById("premiumBias").textContent = "WAIT";
    document.getElementById("tradeMap").textContent = "--";
    document.getElementById("dealerReason").textContent = "Loading data...";
    document.getElementById("premiumReason").textContent = "Loading data...";
    document.getElementById("tradeMapReason").textContent = "Loading data...";
    document.getElementById("premiumBar").style.width = "0%";
    renderTable("callTable", []);
    renderTable("putTable", []);
    renderPremiumStrikeTable([]);
    renderWhaleBidAskTable([]);
    renderOrderSideTable([]);
    renderLargeOrderBlocks({
        blocks: [],
        bullishPremium: 0,
        bearishPremium: 0,
        netPremium: 0,
    });
    resetSpyImbalanceDirectionPanel();
}

function setClass(id, cls) {
    const el = document.getElementById(id);
    el.className = "big " + cls;
}

let netGexChart = null;

function buildNetGexCurve(calls, puts, spotPrice = null) {
    const strikes = new Map();
    const spotSq =
        Number.isFinite(spotPrice) && spotPrice > 0 ? spotPrice * spotPrice : 0;
    for (const c of calls) {
        const strike = Number(c.details?.strike_price);
        const gamma = Math.abs(c.greeks?.gamma || 0);
        const oi = Number(c.open_interest || 0);
        const gex =
            spotSq > 0 ? gamma * oi * 100 * spotSq * 0.01 : gamma * oi * 100;
        strikes.set(strike, (strikes.get(strike) || 0) + gex);
    }
    for (const p of puts) {
        const strike = Number(p.details?.strike_price);
        const gamma = Math.abs(p.greeks?.gamma || 0);
        const oi = Number(p.open_interest || 0);
        const gex =
            spotSq > 0 ? gamma * oi * 100 * spotSq * 0.01 : gamma * oi * 100;
        strikes.set(strike, (strikes.get(strike) || 0) - gex);
    }
    return [...strikes.entries()]
        .map(([strike, gex]) => ({ strike, gex }))
        .sort((a, b) => a.strike - b.strike);
}

function renderNetGexChart(curve) {
    const canvas = document.getElementById("netGexChart");
    if (!canvas || typeof Chart === "undefined") return;
    const labels = curve.map((x) => x.strike);
    const values = curve.map((x) => x.gex);
    if (netGexChart) {
        netGexChart.data.labels = labels;
        netGexChart.data.datasets[0].data = values;
        netGexChart.update("none");
        return;
    }
    netGexChart = new Chart(canvas, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Net GEX ($)",
                    data: values,
                    fill: true,
                    tension: 0.25,
                },
            ],
        },
        options: {
            animation: false,
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                tooltip: {
                    callbacks: {
                        label: (ctx) =>
                            "Net GEX: " +
                            (typeof fmtGex === "function"
                                ? fmtGex(ctx.parsed.y)
                                : ctx.parsed.y),
                    },
                },
            },
            scales: {
                y: {
                    ticks: {
                        callback: (v) =>
                            typeof fmtGex === "function" ? fmtGex(v) : v,
                    },
                },
            },
        },
    });
}

// Dealer-flow gamma estimate from quote-side size.
// All listed options have positive contract gamma. A customer buy at ASK means the dealer
// is assumed to sell the option (short gamma). A customer sale at BID means dealer long gamma.
function buildDealerFlowGex(rows, spotPrice, calls, puts) {
    const spot = Number(spotPrice || 0);
    const spotFactor = spot > 0 ? spot * spot * 0.01 : 1;
    const gammaLookup = new Map();
    for (const c of [...(calls || []), ...(puts || [])]) {
        const strike = Number(c.details?.strike_price);
        const type = String(c.details?.contract_type || "").toUpperCase();
        const gamma = Math.abs(Number(c.greeks?.gamma || 0));
        if (Number.isFinite(strike) && gamma > 0)
            gammaLookup.set(strike + "|" + type, gamma);
    }
    const byStrike = new Map();
    let longGamma = 0,
        shortGamma = 0;
    for (const r of rows || []) {
        if (
            typeof isWithinOrderFlowWindow === "function" &&
            !isWithinOrderFlowWindow(r.timeMs)
        )
            continue;
        const contracts = Math.max(0, Number(r.size || 0));
        const gamma = Math.abs(
            Number(
                r.gamma ||
                    gammaLookup.get(
                        Number(r.strike) +
                            "|" +
                            String(r.type || "").toUpperCase(),
                    ) ||
                    0,
            ),
        );
        if (!(contracts > 0 && gamma > 0)) continue;
        const unsigned = gamma * contracts * 100 * spotFactor;
        const signed =
            String(r.side || "").toUpperCase() === "ASK" ? -unsigned : unsigned;
        const strike = Number(r.strike);
        if (!byStrike.has(strike))
            byStrike.set(strike, {
                strike,
                longFlowGex: 0,
                shortFlowGex: 0,
                netFlowGex: 0,
            });
        const b = byStrike.get(strike);
        if (signed >= 0) {
            b.longFlowGex += signed;
            longGamma += signed;
        } else {
            b.shortFlowGex += Math.abs(signed);
            shortGamma += Math.abs(signed);
        }
        b.netFlowGex += signed;
    }
    return {
        byStrike,
        longGamma,
        shortGamma,
        netFlowGex: longGamma - shortGamma,
    };
}

function renderDealerFlowGex(flowData) {
    const net = Number(flowData?.netFlowGex || 0),
        longG = Number(flowData?.longGamma || 0),
        shortG = Number(flowData?.shortGamma || 0);
    const set = (id, v) => {
        const e = document.getElementById(id);
        if (e) e.textContent = v;
    };
    set("flowNetGamma", fmtGex(net));
    set("flowLongGamma", fmtGex(longG));
    set("flowShortGamma", "-" + fmtGex(shortG));
    setClass("flowNetGamma", net > 0 ? "bull" : net < 0 ? "bear" : "neutral");
    const regime = net > 0 ? "LONG GAMMA" : net < 0 ? "SHORT GAMMA" : "FLAT";
    set("flowGammaRegime", regime);
    setClass(
        "flowGammaRegime",
        net > 0 ? "bull" : net < 0 ? "bear" : "neutral",
    );
    set(
        "flowNetGammaInfo",
        "Dealer quote-flow estimate for selected window: long " +
            fmtGex(longG) +
            " versus short " +
            fmtGex(shortG) +
            ".",
    );
    set(
        "flowGammaRegimeInfo",
        net > 0
            ? "Estimated dealer long gamma may create more hedging against price moves."
            : net < 0
              ? "Estimated dealer short gamma may require hedging with price moves, increasing movement risk."
              : "Estimated long and short flow gamma are balanced.",
    );
}

function clamp(n, min, max) {
    return Math.max(min, Math.min(max, Number(n) || 0));
}

function gradeFromIps(ips) {
    if (ips >= 95) return "A+";
    if (ips >= 90) return "A";
    if (ips >= 80) return "B+";
    if (ips >= 70) return "B";
    if (ips >= 60) return "C";
    return "WATCH";
}

function gradeClass(grade) {
    if (grade === "A+") return "grade-aplus";
    if (grade === "A") return "grade-a";
    if (grade === "B+" || grade === "B") return "grade-b";
    return "grade-c";
}

function stateFromPersistence(persistence, premium) {
    if (persistence >= 5 || (persistence >= 3 && premium >= 500000))
        return "CONFIRMED";
    if (persistence >= 3 || premium >= 250000) return "BUILDING";
    return "EARLY";
}

function stateClass(state) {
    if (state === "CONFIRMED") return "state-confirmed";
    if (state === "BUILDING") return "state-building";
    return "state-early";
}
function easyStatusLabel(state, persistence) {
    const p = fmt(Math.min(persistence, 5)) + "/5";
    if (state === "CONFIRMED") return "🟢 CONFIRMED " + p;
    if (state === "BUILDING") return "🟡 BUILDING " + p;
    return "🔵 EARLY " + p;
}

function tradeSignal(row) {
    if (row.direction === "BULLISH") {
        if (row.ips >= 75 && row.persistence >= 3) return "LONG BIAS";
        if (row.ips >= 50) return "WATCH LONG";
        return "DEVELOPING";
    }
    if (row.direction === "BEARISH") {
        if (row.ips >= 75 && row.persistence >= 3) return "SHORT BIAS";
        if (row.ips >= 50) return "WATCH SHORT";
        return "DEVELOPING";
    }
    return "WAIT";
}

function signalClass(signal) {
    if (signal.includes("LONG")) return "signal-long";
    if (signal.includes("SHORT")) return "signal-short";
    return "signal-watch";
}

const IMBALANCE_NEAREST_POINTS = 10;
const IMBALANCE_MAX_ROWS = 10;

function resetSpyImbalanceDirectionPanel() {
    const values = {
        spyImbalanceDirection: "WAIT",
        spyImbalanceSignal: "Load data first.",
        spyImbalanceScore: "0",
        spyImbalanceConfidence: "0%",
        spyImbalanceBullPremium: "$0",
        spyImbalanceBearPremium: "$0",
        spyImbalanceNetPremium: "$0",
        spyImbalanceDealerHedge: "0",
        spyImbalanceBlockScore: "0",
        spyImbalancePersistence: "0/5",
        spyImbalanceGammaRegime: "WAIT",
        spyImbalanceQuality: "WAIT",
        spyImbalanceReason:
            "Waiting for live SPY price and recent imbalance flow.",
    };
    for (const [id, value] of Object.entries(values)) {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    }
    const scoreBar = document.getElementById("spyImbalanceScoreBar");
    if (scoreBar) scoreBar.style.width = "0%";
    const confBar = document.getElementById("spyImbalanceConfidenceBar");
    if (confBar) confBar.style.width = "0%";
    const dir = document.getElementById("spyImbalanceDirection");
    if (dir) dir.className = "big neutral";
    const net = document.getElementById("spyImbalanceNetPremium");
    if (net) net.className = "big neutral";
    const conf = document.getElementById("spyImbalanceConfidence");
    if (conf) conf.className = "big neutral";
}

const imbalanceScanMemory = [];

function buildMarketImbalanceDirection(
    rows,
    largeOrderBlocks,
    spyPrice,
    netGamma,
) {
    if (!Number.isFinite(spyPrice) || spyPrice <= 0) return null;

    const nearRows = (rows || [])
        .filter(
            (r) =>
                isWithinOrderFlowWindow(r.timeMs) &&
                r.premium >= getOrderFlowMinPremium(),
        )
        .filter(
            (r) =>
                Math.abs(Number(r.strike) - spyPrice) <=
                IMBALANCE_NEAREST_POINTS,
        );

    if (!nearRows.length) return null;

    let bullishPremium = 0;
    let bearishPremium = 0;
    let bullishHedgeShares = 0;
    let bearishHedgeShares = 0;

    for (const r of nearRows) {
        const dir = largeOrderDirection(r.type, r.side);
        const hedge = Math.abs(r.hedgeShares || 0);
        if (dir === "BULLISH") {
            bullishPremium += r.premium;
            bullishHedgeShares += hedge;
        } else if (dir === "BEARISH") {
            bearishPremium += r.premium;
            bearishHedgeShares += hedge;
        }
    }

    const nearbyBlocks = (largeOrderBlocks?.blocks || []).filter(
        (b) =>
            Math.abs(Number(b.strike) - spyPrice) <= IMBALANCE_NEAREST_POINTS,
    );
    const bullishBlockPremium = nearbyBlocks
        .filter((b) => b.direction === "BULLISH")
        .reduce((s, b) => s + b.premium, 0);
    const bearishBlockPremium = nearbyBlocks
        .filter((b) => b.direction === "BEARISH")
        .reduce((s, b) => s + b.premium, 0);

    const totalPremium = Math.max(1, bullishPremium + bearishPremium);
    const netPremium = bullishPremium - bearishPremium;
    const totalHedge = Math.max(1, bullishHedgeShares + bearishHedgeShares);
    const netHedgeShares = bullishHedgeShares - bearishHedgeShares;
    const totalBlockPremium = Math.max(
        1,
        bullishBlockPremium + bearishBlockPremium,
    );
    const netBlockPremium = bullishBlockPremium - bearishBlockPremium;

    const premiumScore = clamp((netPremium / totalPremium) * 100, -100, 100);
    const hedgeScore = clamp((netHedgeShares / totalHedge) * 100, -100, 100);
    const blockScore = nearbyBlocks.length
        ? clamp((netBlockPremium / totalBlockPremium) * 100, -100, 100)
        : 0;
    const gammaScore =
        Number(netGamma) < 0
            ? netPremium >= 0
                ? 18
                : -18
            : Number(netGamma) > 0
              ? 0
              : 5;

    const rawScore = clamp(
        premiumScore * 0.35 +
            hedgeScore * 0.25 +
            blockScore * 0.2 +
            gammaScore * 0.1,
        -100,
        100,
    );

    const scanDirection =
        rawScore > 8 ? "BULLISH" : rawScore < -8 ? "BEARISH" : "BALANCED";
    imbalanceScanMemory.push({
        time: Date.now(),
        direction: scanDirection,
        score: rawScore,
    });
    while (imbalanceScanMemory.length > 5) imbalanceScanMemory.shift();
    const sameDirectionCount =
        scanDirection === "BALANCED"
            ? 0
            : imbalanceScanMemory.filter((x) => x.direction === scanDirection)
                  .length;
    const persistenceScore =
        scanDirection === "BALANCED" ? 0 : (sameDirectionCount / 5) * 100;

    const finalScore = clamp(
        rawScore +
            (scanDirection === "BULLISH"
                ? 1
                : scanDirection === "BEARISH"
                  ? -1
                  : 0) *
                (persistenceScore * 0.1),
        -100,
        100,
    );
    const absScore = Math.abs(finalScore);
    const confidence = Math.round(
        clamp(
            absScore * 0.62 +
                Math.abs(premiumScore) * 0.16 +
                Math.abs(hedgeScore) * 0.12 +
                persistenceScore * 0.1,
            1,
            99,
        ),
    );

    let direction = "NEUTRAL";
    if (finalScore >= 12) direction = "BULLISH";
    else if (finalScore <= -12) direction = "BEARISH";

    let signal = "NO CLEAR EDGE";
    if (direction === "BULLISH" && confidence >= 75) signal = "LONG BIAS";
    else if (direction === "BULLISH") signal = "WATCH LONG";
    else if (direction === "BEARISH" && confidence >= 75) signal = "SHORT BIAS";
    else if (direction === "BEARISH") signal = "WATCH SHORT";

    let quality = "WEAK";
    if (confidence >= 88 && absScore >= 70 && sameDirectionCount >= 4)
        quality = "CONFIRMED";
    else if (confidence >= 75 && absScore >= 55) quality = "STRONG";
    else if (confidence >= 60 && absScore >= 35) quality = "BUILDING";
    else if (confidence >= 45) quality = "EARLY";

    return {
        direction,
        signal,
        score: Math.round(finalScore),
        confidence,
        quality,
        bullishPremium,
        bearishPremium,
        netPremium,
        bullishHedgeShares,
        bearishHedgeShares,
        netHedgeShares,
        blockScore: Math.round(blockScore),
        persistence: sameDirectionCount,
        premiumScore: Math.round(premiumScore),
        hedgeScore: Math.round(hedgeScore),
        gammaRegime:
            Number(netGamma) < 0
                ? "NEGATIVE"
                : Number(netGamma) > 0
                  ? "POSITIVE"
                  : "FLAT",
        rowCount: nearRows.length,
    };
}

function renderFlowImbalanceScanner(
    rows,
    largeOrderBlocks,
    spyPrice,
    netGamma,
) {
    const data = buildMarketImbalanceDirection(
        rows,
        largeOrderBlocks,
        spyPrice,
        netGamma,
    );
    const rangeInfo = document.getElementById("imbalanceRangeInfo");
    const minPremium = getOrderFlowMinPremium();
    const windowLabel = getOrderFlowWindowLabel();
    if (rangeInfo) {
        rangeInfo.textContent =
            Number.isFinite(spyPrice) && spyPrice > 0
                ? "SPY market direction is calculated from " +
                  money(minPremium) +
                  "+ option pressure during " +
                  windowLabel +
                  ", within +/- " +
                  IMBALANCE_NEAREST_POINTS +
                  " points of live SPY ($" +
                  fmt(spyPrice, 2) +
                  "). Strike prices are hidden; this panel only shows overall direction, score, and confidence."
                : "Waiting for current live SPY price before calculating market direction.";
    }

    if (!data) {
        resetSpyImbalanceDirectionPanel();
        const reason = document.getElementById("spyImbalanceReason");
        if (reason)
            reason.textContent =
                "No " +
                money(minPremium) +
                "+ imbalance flow found near live SPY during " +
                windowLabel +
                ".";
        return;
    }

    const dirClass =
        data.direction === "BULLISH"
            ? "call"
            : data.direction === "BEARISH"
              ? "put"
              : "neutral";
    const hedgeClass = data.netHedgeShares >= 0 ? "call" : "put";
    const scoreAbs = Math.abs(data.score);

    const setText = (id, text) => {
        const el = document.getElementById(id);
        if (el) el.textContent = text;
    };
    setText("spyImbalanceDirection", data.direction);
    setText("spyImbalanceSignal", data.signal);
    setText("spyImbalanceScore", (data.score > 0 ? "+" : "") + fmt(data.score));
    setText("spyImbalanceConfidence", fmt(data.confidence) + "%");
    setText("spyImbalanceBullPremium", money(data.bullishPremium));
    setText("spyImbalanceBearPremium", money(data.bearishPremium));
    setText("spyImbalanceNetPremium", money(data.netPremium));
    setText(
        "spyImbalanceDealerHedge",
        (data.netHedgeShares >= 0 ? "+" : "") + fmt(data.netHedgeShares, 0),
    );
    setText(
        "spyImbalanceBlockScore",
        (data.blockScore > 0 ? "+" : "") + fmt(data.blockScore),
    );
    setText("spyImbalancePersistence", data.persistence + "/5");
    setText("spyImbalanceGammaRegime", data.gammaRegime);
    setText("spyImbalanceQuality", data.quality);

    const dirEl = document.getElementById("spyImbalanceDirection");
    if (dirEl) dirEl.className = "big " + dirClass;
    const netEl = document.getElementById("spyImbalanceNetPremium");
    if (netEl)
        netEl.className = "big " + (data.netPremium >= 0 ? "call" : "put");
    const hedgeEl = document.getElementById("spyImbalanceDealerHedge");
    if (hedgeEl) hedgeEl.className = "big " + hedgeClass;
    const confEl = document.getElementById("spyImbalanceConfidence");
    if (confEl)
        confEl.className =
            "big " + (data.confidence >= 70 ? dirClass : "neutral");
    const gammaEl = document.getElementById("spyImbalanceGammaRegime");
    if (gammaEl)
        gammaEl.className =
            "big " +
            (data.gammaRegime === "NEGATIVE"
                ? "warning"
                : data.gammaRegime === "POSITIVE"
                  ? "neutral"
                  : "muted");

    const scoreBar = document.getElementById("spyImbalanceScoreBar");
    if (scoreBar) scoreBar.style.width = clamp(scoreAbs, 0, 100) + "%";
    const confBar = document.getElementById("spyImbalanceConfidenceBar");
    if (confBar) confBar.style.width = clamp(data.confidence, 0, 100) + "%";

    const reason = document.getElementById("spyImbalanceReason");
    if (reason) {
        reason.textContent =
            data.direction === "NEUTRAL"
                ? "SPY imbalance is neutral. Premium score " +
                  data.premiumScore +
                  ", hedge score " +
                  data.hedgeScore +
                  ", confidence " +
                  data.confidence +
                  "%. Wait for stronger VWAP/gamma confirmation."
                : data.direction +
                  " SPY bias: Smart Money Score " +
                  (data.score > 0 ? "+" : "") +
                  data.score +
                  "/100 with " +
                  data.confidence +
                  "% confidence. Institutional buying " +
                  money(data.bullishPremium) +
                  " vs selling " +
                  money(data.bearishPremium) +
                  ", dealer hedge " +
                  (data.netHedgeShares >= 0 ? "+" : "") +
                  fmt(data.netHedgeShares, 0) +
                  " shares, persistence " +
                  data.persistence +
                  "/5, gamma regime " +
                  data.gammaRegime +
                  ".";
    }
}

let firstGammaLoad = true;
let gammaRefreshInProgress = false;
const GAMMA_REFRESH_MS = 20000;

async function loadGammaSystem(isBackgroundRefresh = false) {
    const apiKey = MASSIVE_API_KEY;
    const expirationDate = document.getElementById("expiration").value;
    const status = document.getElementById("status");
    if (gammaRefreshInProgress) return;
    gammaRefreshInProgress = true;
    clearMarketDataError();
    if (firstGammaLoad) {
        resetUI();
        status.textContent =
            "Loading " +
            (window.__CHAIN_SYMBOL || "SPY") +
            " 0DTE pro gamma data...";
    } else if (!isBackgroundRefresh) {
        status.textContent = "Refreshing data...";
    }

    try {
        const selectedSymbol = String(
            window.__CHAIN_SYMBOL || "SPY",
        ).toUpperCase();
        const streamedSpotBeforeLoad =
            getFreshStreamedUnderlyingPrice(selectedSymbol);
        const directSpotPromise =
            streamedSpotBeforeLoad || INDEX_UNDERLYINGS.includes(selectedSymbol)
                ? Promise.resolve(streamedSpotBeforeLoad)
                : fetchLiveUnderlyingPrice(
                      selectedSymbol,
                      MASSIVE_STOCKS_API_KEY,
                  ).catch((err) => {
                      console.log(
                          "Massive live stock snapshot fallback failed:",
                          err.message,
                      );
                      return null;
                  });

        const [calls, puts] = await Promise.all([
            fetchAllOptions("call", expirationDate, apiKey),
            fetchAllOptions("put", expirationDate, apiKey),
        ]);
        if (!calls.length && !puts.length) {
            throw new Error(
                "No option contracts were returned for " +
                    selectedSymbol +
                    " on " +
                    expirationDate +
                    ". Select an active expiration/trading date and verify the Options plan on this API key.",
            );
        }

        // Never let a stock REST hostname hold the entire option-chain page open.
        // The WebSocket and the next refresh can still supply the SIP NBBO.
        const directSpot = await Promise.race([
            directSpotPromise,
            new Promise((resolve) => setTimeout(() => resolve(null), 4000)),
        ]);

        const whaleBidAskByStrikeEarly = buildWhaleBidAskByStrike(calls, puts);
        let streamedSpotAfterLoad =
            getFreshStreamedUnderlyingPrice(selectedSymbol);
        // A direct browser WebSocket can finish authenticating just after the
        // options request. Wait briefly for its first NBBO instead of failing startup.
        if (
            !streamedSpotAfterLoad &&
            !INDEX_UNDERLYINGS.includes(selectedSymbol)
        ) {
            streamedSpotAfterLoad = await waitForFreshStreamedUnderlyingPrice(
                selectedSymbol,
                3000,
            );
        }
        const spotResult = streamedSpotAfterLoad || directSpot;
        const optionSnapshotSpot = getUnderlyingSpyPrice(calls, puts);
        // Stocks/ETFs must use the independent real-time Stocks feed. Never silently
        // substitute the possibly delayed underlying price carried by an option snapshot.
        const spyPrice = INDEX_UNDERLYINGS.includes(selectedSymbol)
            ? Number(spotResult?.calculationPrice || optionSnapshotSpot || 0)
            : Number(spotResult?.calculationPrice || 0);
        if (!Number.isFinite(spyPrice) || spyPrice <= 0) {
            throw new Error(
                "Current Massive Stocks SIP price for " +
                    selectedSymbol +
                    " is unavailable. No delayed options-snapshot or option-strike estimate was used.",
            );
        }

        const fullCalls = buildWall(calls, "call", true, spyPrice);
        const netGexCurve = buildNetGexCurve(calls, puts, spyPrice);
        renderNetGexChart(netGexCurve);
        const fullPuts = buildWall(puts, "put", true, spyPrice);
        const callWall = buildWall(calls, "call", false, spyPrice);
        const putWall = buildWall(puts, "put", false, spyPrice);
        const gammaCall = maxBy(fullCalls, "gammaExposure");
        const gammaPut = maxBy(fullPuts, "gammaExposure");
        const gammaFlip = estimateGammaFlip(fullCalls, fullPuts);
        const totalCall = totalPremium(calls);
        const totalPut = totalPremium(puts);
        const premiumByStrike = buildPremiumByStrike(calls, puts);
        const whaleBidAskByStrike = whaleBidAskByStrikeEarly;
        updateTradeFlowContractInfo(calls, puts, spyPrice);
        pruneLiveTradeFlowRows();
        const orderSideFlow = buildOrderSideFlow(calls, puts);
        const recentOrderRows = rememberRecentOrderRows(
            orderSideFlow.rows.concat(liveTradeFlowRows),
        );
        const dealerFlowGex = buildDealerFlowGex(
            recentOrderRows,
            spyPrice,
            calls,
            puts,
        );
        renderDealerFlowGex(dealerFlowGex);
        const largeOrderBlocks = buildLargeOrderBlocks(
            recentOrderRows,
            spyPrice,
        );
        const topCallPremium = premiumByStrike
            .filter((r) => r.callPremium > 0)
            .sort((a, b) => b.callPremium - a.callPremium)[0];
        const topPutPremium = premiumByStrike
            .filter((r) => r.putPremium > 0)
            .sort((a, b) => b.putPremium - a.putPremium)[0];
        const dominantPremium = premiumByStrike[0];
        const topCallAskWhale = whaleBidAskByStrike
            .filter((r) => r.callAskPremium > 0)
            .sort((a, b) => b.callAskPremium - a.callAskPremium)[0];
        const topCallBidWhale = whaleBidAskByStrike
            .filter((r) => r.callBidPremium > 0)
            .sort((a, b) => b.callBidPremium - a.callBidPremium)[0];
        const topPutBidWhale = whaleBidAskByStrike
            .filter((r) => r.putBidPremium > 0)
            .sort((a, b) => b.putBidPremium - a.putBidPremium)[0];
        const topPutAskWhale = whaleBidAskByStrike
            .filter((r) => r.putAskPremium > 0)
            .sort((a, b) => b.putAskPremium - a.putAskPremium)[0];
        const totalCallAskWhalePremium = whaleBidAskByStrike.reduce(
            (s, r) => s + r.callAskPremium,
            0,
        );
        const totalCallBidWhalePremium = whaleBidAskByStrike.reduce(
            (s, r) => s + r.callBidPremium,
            0,
        );
        const totalPutBidWhalePremium = whaleBidAskByStrike.reduce(
            (s, r) => s + r.putBidPremium,
            0,
        );
        const totalPutAskWhalePremium = whaleBidAskByStrike.reduce(
            (s, r) => s + r.putAskPremium,
            0,
        );
        const netPremium = totalCall - totalPut;
        const netGamma =
            fullCalls.reduce((s, r) => s + r.gammaExposure, 0) -
            fullPuts.reduce((s, r) => s + r.gammaExposure, 0);
        const netGammaShares =
            calls.reduce(
                (s, c) =>
                    s +
                    Math.abs(Number(c.greeks?.gamma || 0)) *
                        Number(c.open_interest || 0) *
                        100,
                0,
            ) -
            puts.reduce(
                (s, p) =>
                    s +
                    Math.abs(Number(p.greeks?.gamma || 0)) *
                        Number(p.open_interest || 0) *
                        100,
                0,
            );
        window.__liveGexBasis = { symbol: selectedSymbol, netGammaShares };
        const ratio = totalPut > 0 ? totalCall / totalPut : 999;

        document.getElementById("topCall").textContent = callWall[0]
            ? "$" + callWall[0].strike
            : "--";
        document.getElementById("topPut").textContent = putWall[0]
            ? "$" + putWall[0].strike
            : "--";
        document.getElementById("gammaCallWall").textContent = gammaCall
            ? "$" + gammaCall.strike
            : "--";
        document.getElementById("gammaPutWall").textContent = gammaPut
            ? "$" + gammaPut.strike
            : "--";
        document.getElementById("gammaCallInfo").textContent = gammaCall
            ? "Gamma exposure: " +
              fmtGex(gammaCall.gammaExposure) +
              " | OI: " +
              fmt(gammaCall.openInterest)
            : "No call gamma data.";
        document.getElementById("gammaPutInfo").textContent = gammaPut
            ? "Gamma exposure: " +
              fmtGex(gammaPut.gammaExposure) +
              " | OI: " +
              fmt(gammaPut.openInterest)
            : "No put gamma data.";
        document.getElementById("gammaFlip").textContent = gammaFlip
            ? "$" + gammaFlip.strike
            : "--";
        document.getElementById("totalCallPremium").textContent =
            money(totalCall);
        document.getElementById("totalPutPremium").textContent =
            money(totalPut);
        document.getElementById("topCallPremiumStrike").textContent =
            topCallPremium ? "$" + topCallPremium.strike : "--";
        document.getElementById("topPutPremiumStrike").textContent =
            topPutPremium ? "$" + topPutPremium.strike : "--";
        document.getElementById("topCallPremiumInfo").textContent =
            topCallPremium
                ? "Call premium: " +
                  money(topCallPremium.callPremium) +
                  " | Volume: " +
                  fmt(topCallPremium.callVolume)
                : "No call premium data.";
        document.getElementById("topPutPremiumInfo").textContent = topPutPremium
            ? "Put premium: " +
              money(topPutPremium.putPremium) +
              " | Volume: " +
              fmt(topPutPremium.putVolume)
            : "No put premium data.";
        document.getElementById("netPremiumFlow").textContent =
            money(netPremium);
        setClass("netPremiumFlow", netPremium >= 0 ? "bull" : "bear");
        document.getElementById("netPremiumFlowInfo").textContent =
            netPremium >= 0
                ? "Calls have more paid premium than puts."
                : "Puts have more paid premium than calls.";
        document.getElementById("dominantPremiumStrike").textContent =
            dominantPremium ? "$" + dominantPremium.strike : "--";
        setClass(
            "dominantPremiumStrike",
            dominantPremium && dominantPremium.netPremium >= 0
                ? "bull"
                : "bear",
        );
        document.getElementById("dominantPremiumInfo").textContent =
            dominantPremium
                ? dominantPremium.bias +
                  " bias | Net premium: " +
                  money(dominantPremium.netPremium)
                : "No dominant premium strike.";
        document.getElementById("topCallAskWhaleStrike").textContent =
            topCallAskWhale ? "$" + topCallAskWhale.strike : "--";
        document.getElementById("topCallBidWhaleStrike").textContent =
            topCallBidWhale ? "$" + topCallBidWhale.strike : "--";
        document.getElementById("topPutBidWhaleStrike").textContent =
            topPutBidWhale ? "$" + topPutBidWhale.strike : "--";
        document.getElementById("topPutAskWhaleStrike").textContent =
            topPutAskWhale ? "$" + topPutAskWhale.strike : "--";
        document.getElementById("topCallAskWhaleInfo").textContent =
            topCallAskWhale
                ? "Ask vol: " +
                  fmt(topCallAskWhale.callAskVolume) +
                  " | Ask: " +
                  money(topCallAskWhale.callAsk) +
                  " | Premium: " +
                  money(topCallAskWhale.callAskPremium)
                : "No call ask whale data.";
        document.getElementById("topCallBidWhaleInfo").textContent =
            topCallBidWhale
                ? "Bid vol: " +
                  fmt(topCallBidWhale.callBidVolume) +
                  " | Bid: " +
                  money(topCallBidWhale.callBid) +
                  " | Premium: " +
                  money(topCallBidWhale.callBidPremium)
                : "No call bid whale data.";
        document.getElementById("topPutBidWhaleInfo").textContent =
            topPutBidWhale
                ? "Bid vol: " +
                  fmt(topPutBidWhale.putBidVolume) +
                  " | Bid: " +
                  money(topPutBidWhale.putBid) +
                  " | Premium: " +
                  money(topPutBidWhale.putBidPremium)
                : "No put bid whale data.";
        document.getElementById("topPutAskWhaleInfo").textContent =
            topPutAskWhale
                ? "Ask vol: " +
                  fmt(topPutAskWhale.putAskVolume) +
                  " | Ask: " +
                  money(topPutAskWhale.putAsk) +
                  " | Premium: " +
                  money(topPutAskWhale.putAskPremium)
                : "No put ask whale data.";
        document.getElementById("totalCallAskWhalePremium").textContent = money(
            totalCallAskWhalePremium,
        );
        document.getElementById("totalCallBidWhalePremium").textContent = money(
            totalCallBidWhalePremium,
        );
        document.getElementById("totalPutBidWhalePremium").textContent = money(
            totalPutBidWhalePremium,
        );
        document.getElementById("totalPutAskWhalePremium").textContent = money(
            totalPutAskWhalePremium,
        );
        document.getElementById("forcedOrderCount").textContent = fmt(
            recentOrderRows.filter((r) => r.status === "FORCED").length,
        );
        document.getElementById("delayedOrderCount").textContent = fmt(
            recentOrderRows.filter((r) => r.status === "DELAYED").length,
        );
        document.getElementById("callOrderCount").textContent = fmt(
            recentOrderRows.filter((r) => r.type === "CALL").length,
        );
        document.getElementById("putOrderCount").textContent = fmt(
            recentOrderRows.filter((r) => r.type === "PUT").length,
        );
        document.getElementById("netGamma").textContent = fmtGex(netGamma);
        document.getElementById("netGammaInfo").textContent =
            (netGamma >= 0
                ? "Positive gamma: pin/chop risk near major walls."
                : "Negative gamma: expansion/trend risk after wall breaks.") +
            " GEX spot: $" +
            fmt(spyPrice, 2) +
            " (Massive SIP NBBO midpoint)" +
            (spotResult?.bid > 0 && spotResult?.ask > 0
                ? ". SIP last: $" +
                  fmt(spotResult.displayPrice, 2) +
                  " | Bid: $" +
                  fmt(spotResult.bid, 2) +
                  " | Ask: $" +
                  fmt(spotResult.ask, 2) +
                  "."
                : ". Waiting for a complete NBBO quote; current SIP fallback is active.");

        if (netGamma > 0) {
            document.getElementById("dealerRegime").textContent =
                "POSITIVE GAMMA";
            setClass("dealerRegime", "bull");
            document.getElementById("dealerReason").textContent =
                "Market may mean-revert around gamma walls. Breakouts need strong volume confirmation.";
        } else {
            document.getElementById("dealerRegime").textContent =
                "NEGATIVE GAMMA";
            setClass("dealerRegime", "bear");
            document.getElementById("dealerReason").textContent =
                "Market can move faster when price breaks a major gamma wall. Risk expands.";
        }

        let bias = "BALANCED";
        let biasClass = "neutral";
        if (ratio >= 1.35) {
            bias = "CALL FLOW STRONG";
            biasClass = "bull";
        } else if (ratio <= 0.74) {
            bias = "PUT FLOW STRONG";
            biasClass = "bear";
        } else if (ratio > 1.1) {
            bias = "LEAN CALL";
            biasClass = "bull";
        } else if (ratio < 0.9) {
            bias = "LEAN PUT";
            biasClass = "bear";
        }
        document.getElementById("premiumBias").textContent = bias;
        setClass("premiumBias", biasClass);
        document.getElementById("premiumReason").textContent =
            "Call/Put premium ratio: " + fmt(ratio, 2);
        document.getElementById("premiumBar").style.width =
            Math.max(5, Math.min(100, ratio * 50)) + "%";

        const callLevel = gammaCall
            ? gammaCall.strike
            : callWall[0]?.strike || "--";
        const putLevel = gammaPut
            ? gammaPut.strike
            : putWall[0]?.strike || "--";
        document.getElementById("tradeMap").textContent =
            "Support $" + putLevel + " / Resistance $" + callLevel;
        document.getElementById("tradeMapReason").textContent =
            "Bull trigger: acceptance above gamma call wall. Bear trigger: acceptance below gamma put wall. Avoid chasing inside the wall range.";

        renderPremiumStrikeTable(premiumByStrike);
        renderWhaleBidAskTable(whaleBidAskByStrike, spyPrice);
        renderOrderSideTable(recentOrderRows, spyPrice);
        renderLargeOrderBlocks(largeOrderBlocks, spyPrice);
        renderFlowImbalanceScanner(
            recentOrderRows,
            largeOrderBlocks,
            spyPrice,
            netGamma,
        );
        renderTable("callTable", callWall);
        renderTable("putTable", putWall);

        firstGammaLoad = false;
        clearMarketDataError();
        status.textContent =
            "Massive SIP live trade + NBBO | GEX spot uses NBBO midpoint | Options/Greeks refresh every " +
            GAMMA_REFRESH_MS / 1000 +
            "s | Last update: " +
            new Date().toLocaleString() +
            " | Symbol: " +
            (window.__CHAIN_SYMBOL || "SPY") +
            " | GEX midpoint: $" +
            fmt(spyPrice, 2) +
            " | Expiration: " +
            expirationDate +
            " | Calls: " +
            calls.length +
            " | Puts: " +
            puts.length +
            " | Large blocks: " +
            largeOrderBlocks.blocks.length;
    } catch (err) {
        showMarketDataError(err.message);
        if (firstGammaLoad || !isBackgroundRefresh) {
            status.innerHTML =
                '<span class="warning">' + err.message + "</span>";
        } else {
            console.log("Background refresh failed:", err.message);
        }
    } finally {
        gammaRefreshInProgress = false;
    }
}

let autoRefreshTimer = null;

function startAutoRefresh() {
    if (autoRefreshTimer) clearInterval(autoRefreshTimer);
    connectLiveUnderlyingStream(MASSIVE_STOCKS_API_KEY);
    connectOptionsTradeFlow(MASSIVE_API_KEY);
    loadGammaSystem(false);
    autoRefreshTimer = setInterval(() => {
        if (document.visibilityState !== "hidden") loadGammaSystem(true);
    }, GAMMA_REFRESH_MS);
}

window.addEventListener("load", startAutoRefresh);

window.tradeTracker = window.tradeTracker || {
    orders: {},
    stats: { filled: 0, winners: 0, losers: 0, open: 0 },
};

function trackOptionPerformance(rows) {
    rows = rows || [];
    rows.forEach((r) => {
        const id = [r.strike, r.type, r.side, r.timeMs].join("|");
        if (!tradeTracker.orders[id]) {
            tradeTracker.orders[id] = {
                entryPrice: r.mark || 0,
                currentPrice: r.mark || 0,
                type: r.type,
                created: r.timeMs || Date.now(),
            };
        }
        const o = tradeTracker.orders[id];
        o.currentPrice = r.mark || o.currentPrice;

        const pnl =
            o.entryPrice > 0
                ? ((o.currentPrice - o.entryPrice) / o.entryPrice) * 100
                : 0;
        o.pnlPct = pnl;
        o.status = pnl > 5 ? "WINNER" : pnl < -5 ? "LOSER" : "OPEN";
    });

    const vals = Object.values(tradeTracker.orders);
    tradeTracker.stats.filled = vals.length;
    tradeTracker.stats.winners = vals.filter(
        (x) => x.status === "WINNER",
    ).length;
    tradeTracker.stats.losers = vals.filter((x) => x.status === "LOSER").length;
    tradeTracker.stats.open = vals.filter((x) => x.status === "OPEN").length;
}

/* ===== PERFECT COMBINED ENGINE OVERRIDE ===== */
(function () {
    function safeNum(v) {
        v = Number(v);
        return Number.isFinite(v) ? v : 0;
    }
    function rhMoney(n) {
        n = safeNum(n);
        if (n >= 1000000) return "$" + (n / 1000000).toFixed(2) + "M";
        if (n >= 1000) return "$" + (n / 1000).toFixed(1) + "K";
        return "$" + n.toFixed(0);
    }
    function optMoney(n) {
        n = safeNum(n);
        return "$" + n.toFixed(2);
    }
    function pctText(n) {
        n = safeNum(n);
        return (n > 0 ? "+" : "") + n.toFixed(2) + "%";
    }
    function rowClassByPnL(n) {
        return n > 0
            ? "gain-pos rh-win"
            : n < 0
              ? "gain-neg rh-win"
              : "gain-flat rh-win";
    }

    window.renderRobinhoodPremiumByStrike = function (rows) {
        const tbody = document.getElementById("rhPremiumStrikeTable");
        if (!tbody) return;
        const map = new Map();

        (rows || []).forEach((r) => {
            const strike = safeNum(r.strike);
            if (!Number.isFinite(strike) || !strike) return;
            if (!map.has(strike)) {
                map.set(strike, {
                    strike,
                    callRh: 0,
                    putRh: 0,
                    callFlow: 0,
                    putFlow: 0,
                    callSize: 0,
                    putSize: 0,
                    totalSize: 0,
                });
            }
            const x = map.get(strike);
            const type = String(r.type || "").toUpperCase();
            const size = safeNum(r.size);
            const ask = safeNum(r.ask);
            const rhCost = ask * size * 100; // Robinhood buy estimate
            const flowPrem = safeNum(r.premium); // actual side quote estimate
            x.totalSize += size;
            if (type === "CALL") {
                x.callRh += rhCost;
                x.callFlow += flowPrem;
                x.callSize += size;
            } else if (type === "PUT") {
                x.putRh += rhCost;
                x.putFlow += flowPrem;
                x.putSize += size;
            }
        });

        const rowsOut = [...map.values()]
            .map((x) => {
                x.totalRh = x.callRh + x.putRh;
                x.netFlow = x.callFlow - x.putFlow;
                x.bias =
                    x.netFlow > 0
                        ? "CALL LEAN"
                        : x.netFlow < 0
                          ? "PUT LEAN"
                          : "BALANCED";
                return x;
            })
            .sort((a, b) => b.totalRh - a.totalRh)
            .slice(0, 40);

        if (!rowsOut.length) {
            tbody.innerHTML =
                '<tr><td colspan="9" class="muted">No Robinhood-style premium orders found.</td></tr>';
            return;
        }

        tbody.innerHTML = rowsOut
            .map((x) => {
                const netCls = x.netFlow >= 0 ? "pos" : "neg";
                const biasCls = x.bias.includes("CALL")
                    ? "call"
                    : x.bias.includes("PUT")
                      ? "put"
                      : "neutral";
                return `<tr>
<td>$${x.strike}</td>
<td class="call rh-buy-cost">${rhMoney(x.callRh)}</td>
<td class="put rh-buy-cost">${rhMoney(x.putRh)}</td>
<td class="rh-buy-cost">${rhMoney(x.totalRh)}</td>
<td class="call">${rhMoney(x.callFlow)}</td>
<td class="put">${rhMoney(x.putFlow)}</td>
<td class="${netCls}">${rhMoney(x.netFlow)}</td>
<td>${fmt ? fmt(x.totalSize) : x.totalSize.toFixed(0)}</td>
<td class="${biasCls}">${x.bias}</td>
</tr>`;
            })
            .join("");
    };

    window.renderPerfectFlowTape = function (rows) {
        const body = document.getElementById("perfectFlowTape");
        if (!body) return;
        const shown = (rows || [])
            .slice()
            .sort(
                (a, b) =>
                    safeNum(b.timeMs) - safeNum(a.timeMs) ||
                    safeNum(b.premium) - safeNum(a.premium),
            )
            .slice(0, 60);

        if (!shown.length) {
            body.innerHTML =
                '<div class="fempty">Waiting for recent order-side flow...</div>';
            return;
        }

        body.innerHTML = shown
            .map((r) => {
                const isCall = String(r.type).toUpperCase() === "CALL";
                const typeCls = isCall ? "call" : "put";
                const arrow = isCall ? "▲" : "▼";
                const boughtPrem = safeNum(
                    r.entryPrice ||
                        r.filledPremium ||
                        (String(r.side).toUpperCase() === "ASK"
                            ? r.ask
                            : r.bid),
                ); // filled side premium per contract
                const livePrem = safeNum(r.currentMark || r.mark); // newest live option premium/mark
                const size = safeNum(r.size);
                const flowPrem = safeNum(r.premium);
                const costBasis = boughtPrem * size * 100;
                const liveValue = livePrem * size * 100;
                const winLoss =
                    boughtPrem > 0 && livePrem > 0
                        ? ((livePrem - boughtPrem) / boughtPrem) * 100
                        : 0;
                const whale = costBasis >= 500000 || flowPrem >= 500000;
                const iv = safeNum(r.iv ?? r.implied_volatility ?? 0);
                const delta = safeNum(r.delta);
                return `<div class="perfect-trow ${whale ? "whale" : ""}">
<span style="color:var(--muted)">${typeof fmtTime === "function" ? fmtTime(r.timeMs) : ""}</span>
<span class="${typeCls}" style="font-size:12px">${arrow}</span>
<span class="${typeCls}" style="font-weight:900">${r.type} ${r.side}</span>
<span>$${r.strike}</span>
<span title="Filled premium: ASK uses ask, BID uses bid">$${boughtPrem.toFixed(2)}</span>
<span title="Current live premium / mark">$${livePrem.toFixed(2)}</span>
<span>${typeof fmt === "function" ? fmt(size) : size.toFixed(0)}</span>
<span>${rhMoney(flowPrem)}</span>
<span class="rh-buy-cost" title="Bought premium × contracts × 100">${rhMoney(costBasis)}</span>
<span class="rh-buy-cost" title="Live premium × contracts × 100">${rhMoney(liveValue)}</span>
<span class="${rowClassByPnL(winLoss)}" title="(Live premium - Bought premium) ÷ Bought premium">${pctText(winLoss)}</span>
<span>${iv ? (iv * 100).toFixed(1) + "%" : "—"}</span>
<span>${delta ? delta.toFixed(2) : "—"}</span>
</div>`;
            })
            .join("");
    };

    function classifyFlowStatus(r, gainPct, currentPrice, entryPrice) {
        const side = String(r.side || "").toUpperCase();
        const size = safeNum(r.size);
        const premium = safeNum(r.premium);
        const ageMs = Date.now() - safeNum(r.timeMs);
        const isBidSell = side === "BID" || side.includes("SELL");
        const isAskBuy = side === "ASK" || side.includes("BUY");
        const bigFlow = premium >= 100000 || size >= 100;
        let label = "BUILDING";
        let cls = "flow-building";

        if (gainPct >= 100) {
            label = "🚀 MOON";
            cls = "flow-moon";
        } else if (gainPct >= 25 && isAskBuy) {
            label = "RUNNING";
            cls = "flow-running";
        } else if (gainPct >= 5 && isAskBuy) {
            label = "ACCUMULATING";
            cls = "flow-accumulating";
        } else if (
            gainPct > -5 &&
            gainPct < 5 &&
            (bigFlow || ageMs < 5 * 60 * 1000)
        ) {
            label = "BUILDING";
            cls = "flow-building";
        } else if (gainPct <= -15 || (isBidSell && gainPct < 0)) {
            label = "EXIT FLOW";
            cls = "flow-exit";
        } else if (gainPct < -5) {
            label = "WEAKENING";
            cls = "flow-weakening";
        }

        if (currentPrice <= 0 || entryPrice <= 0) {
            label = "WAIT DATA";
            cls = "flow-building";
        }
        return { label, cls };
    }

    function summarizeMirroredSide(rows) {
        rows = Array.isArray(rows) ? rows : [];
        if (!rows.length) return null;
        const priced = rows
            .map((r) => ({
                entry: safeNum(
                    r.entryPrice ||
                        r.filledPremium ||
                        (String(r.side || "").toUpperCase() === "ASK"
                            ? r.ask
                            : r.bid),
                ),
                size: safeNum(r.size),
            }))
            .filter((r) => r.entry > 0 && r.size > 0);
        const pricedSize = priced.reduce((sum, r) => sum + r.size, 0);
        const entryValue = priced.reduce((sum, r) => sum + r.entry * r.size, 0);
        return {
            latestTime: rows.reduce(
                (latest, r) => Math.max(latest, safeNum(r.timeMs)),
                0,
            ),
            bidPremium: rows
                .filter((r) => String(r.side || "").toUpperCase() === "BID")
                .reduce((sum, r) => sum + safeNum(r.premium), 0),
            askPremium: rows
                .filter((r) => String(r.side || "").toUpperCase() === "ASK")
                .reduce((sum, r) => sum + safeNum(r.premium), 0),
            size: rows.reduce((sum, r) => sum + safeNum(r.size), 0),
            orders: rows.reduce(
                (sum, r) => sum + Math.max(1, safeNum(r.orders)),
                0,
            ),
            pricedSize,
            entryPremium: pricedSize > 0 ? entryValue / pricedSize : 0,
        };
    }

    function mirroredEmptyCells(sideClass) {
        return new Array(9)
            .fill(`<td class="${sideClass} split-empty">—</td>`)
            .join("");
    }

    function mirroredOptionMetrics(strike, type, rows) {
        const chainMap = window.tradeFlowChainByStrike;
        const chainAtStrike =
            chainMap && typeof chainMap.get === "function"
                ? chainMap.get(strike)
                : null;
        const snapshot =
            chainAtStrike && chainAtStrike[type] ? chainAtStrike[type] : null;
        const latest =
            (rows || [])
                .slice()
                .sort((a, b) => safeNum(b.timeMs) - safeNum(a.timeMs))[0] ||
            null;
        if (!snapshot && !latest) return null;
        const bid =
            safeNum(snapshot && snapshot.bid) || safeNum(latest && latest.bid);
        const ask =
            safeNum(snapshot && snapshot.ask) || safeNum(latest && latest.ask);
        const mark =
            safeNum(snapshot && snapshot.mark) ||
            safeNum(latest && (latest.currentMark || latest.mark)) ||
            (bid > 0 && ask > 0 ? (bid + ask) / 2 : bid || ask);
        const flow = summarizeMirroredSide(rows);
        const entryPremium = safeNum(flow && flow.entryPremium);
        const orderContracts = safeNum(flow && flow.pricedSize);
        const entryTotalPremium =
            entryPremium > 0 && orderContracts > 0
                ? entryPremium * orderContracts * 100
                : 0;
        const currentTotalPremium =
            flow && mark > 0 && orderContracts > 0
                ? mark * orderContracts * 100
                : 0;
        const gainPct =
            entryPremium > 0 && mark > 0
                ? ((mark - entryPremium) / entryPremium) * 100
                : 0;
        const plDollars = currentTotalPremium - entryTotalPremium;
        return {
            volume: safeNum(snapshot && snapshot.volume),
            oi: safeNum(snapshot && snapshot.oi),
            bid,
            mark,
            ask,
            flow,
            latestTimeMs: safeNum(latest && latest.timeMs),
            entryPremium,
            livePremium: flow ? mark : 0,
            entryTotalPremium,
            currentTotalPremium,
            gainPct,
            plDollars,
        };
    }

    function mirroredQuote(value, markClass = "") {
        return value > 0
            ? `<span class="split-option-quote ${markClass}">$${value.toFixed(2)}</span>`
            : '<span class="split-option-quote">—</span>';
    }

    function mirroredFlowSub(label, value) {
        return value > 0
            ? `<span class="split-flow-sub">${label} ${rhMoney(value)}</span>`
            : "";
    }

    function mirroredTotalPremiumCell(
        metrics,
        totalKey,
        unitKey,
        sideClass,
        valueClass,
        label,
    ) {
        const total = safeNum(metrics && metrics[totalKey]);
        const unit = safeNum(metrics && metrics[unitKey]);
        if (!(total > 0) || !(unit > 0))
            return `<td class="${sideClass} ${valueClass} split-empty">—</td>`;
        return `<td class="${sideClass} ${valueClass}" title="${label}: ${rhMoney(total)} | Per-contract premium: $${unit.toFixed(2)}"><span class="split-option-quote">${rhMoney(total)}</span><span class="split-total-unit">$${unit.toFixed(2)} / contract</span></td>`;
    }

    function mirroredTimeCell(metrics, sideClass) {
        const timeMs = safeNum(metrics && metrics.latestTimeMs);
        if (!(timeMs > 0))
            return `<td class="${sideClass} split-time-cell split-empty">—</td>`;
        const compact = new Date(timeMs).toLocaleTimeString("en-US", {
            timeZone: "America/New_York",
            hour: "numeric",
            minute: "2-digit",
            second: "2-digit",
            hour12: true,
        });
        const isNew = Date.now() - timeMs >= 0 && Date.now() - timeMs <= 60000;
        return `<td class="${sideClass} split-time-cell" title="Individual order time: ${fmtTime(timeMs)} ET">${compact}${isNew ? '<span class="split-order-age">NEW</span>' : ""}</td>`;
    }

    function mirroredGainCell(metrics, sideClass) {
        if (
            !metrics ||
            !(metrics.entryPremium > 0) ||
            !(metrics.livePremium > 0)
        )
            return `<td class="${sideClass} split-empty">—</td>`;
        const gainClass =
            metrics.gainPct > 0
                ? "gain-pos"
                : metrics.gainPct < 0
                  ? "gain-neg"
                  : "gain-flat";
        const gainText =
            (metrics.gainPct > 0 ? "+" : "") + metrics.gainPct.toFixed(1) + "%";
        const plText =
            (metrics.plDollars > 0 ? "+" : metrics.plDollars < 0 ? "-" : "") +
            rhMoney(Math.abs(metrics.plDollars));
        return `<td class="${sideClass} split-gain-cell" title="Entry total: ${rhMoney(metrics.entryTotalPremium)} | Current total: ${rhMoney(metrics.currentTotalPremium)} | Estimated P/L: ${plText}"><span class="split-option-quote ${gainClass}">${gainText}</span><span class="split-performance-result ${gainClass}">${plText}</span></td>`;
    }

    function renderMirroredOrderFlow(rows, spyPrice) {
        const tbody = document.getElementById("orderSideTable");
        if (!tbody) return;
        const minPremium =
            typeof getOrderFlowMinPremium === "function"
                ? getOrderFlowMinPremium()
                : 100000;
        const chainMap = window.tradeFlowChainByStrike;
        const chainStrikes =
            chainMap && typeof chainMap.keys === "function"
                ? [...chainMap.keys()]
                : [];
        const rowStrikes = (rows || [])
            .map((row) => safeNum(row.strike))
            .filter((strike) => strike > 0);
        const strikeReference = chainStrikes.length ? chainStrikes : rowStrikes;
        const allowedStrikes = new Set(
            getOrderSideStrikeWindow(strikeReference, spyPrice),
        );
        const orderRows = (rows || [])
            .filter((r) => {
                const strike = safeNum(r.strike);
                const type = String(r.type || "").toUpperCase();
                return (
                    strike > 0 &&
                    (type === "CALL" || type === "PUT") &&
                    allowedStrikes.has(strike)
                );
            })
            .slice()
            .sort(
                (a, b) =>
                    safeNum(b.timeMs) - safeNum(a.timeMs) ||
                    safeNum(b.premium) - safeNum(a.premium),
            );

        const strikes = [
            ...new Set([
                ...orderRows.map((r) => safeNum(r.strike)),
                ...chainStrikes,
            ]),
        ]
            .filter((strike) => allowedStrikes.has(strike))
            .sort((a, b) => a - b);
        if (!strikes.length) {
            updateTbodyHTML(
                tbody,
                '<tr><td colspan="19" class="muted">No option-chain quotes or ' +
                    money(minPremium) +
                    "+ Call/Put flow found inside the current filter.</td></tr>",
            );
            return;
        }

        const selectedStrike = safeNum(window.__CHAIN_STRIKE);
        const nearestSpotStrike =
            Number.isFinite(Number(spyPrice)) && Number(spyPrice) > 0
                ? strikes.reduce(
                      (best, strike) =>
                          Math.abs(strike - Number(spyPrice)) <
                          Math.abs(best - Number(spyPrice))
                              ? strike
                              : best,
                      strikes[0],
                  )
                : 0;

        const expiry =
            document.getElementById("expiration")?.value ||
            window.__CHAIN_EXPIRY ||
            "";
        const listKey = [
            window.__CHAIN_SYMBOL || "SPY",
            expiry,
            minPremium,
            ORDER_SIDE_TOTAL_STRIKES,
            window.flowWindowMode || "",
            window.flowWindowSeconds || 0,
        ].join("|");
        if (window.__orderFlowListKey !== listKey) {
            window.__orderFlowListKey = listKey;
            window.orderFlowVisibleLimit = 150;
        }
        const visibleLimit = Math.max(
            150,
            safeNum(window.orderFlowVisibleLimit) || 150,
        );
        const visibleOrders = orderRows.slice(0, visibleLimit);
        const renderItems = orderRows.length
            ? visibleOrders.map((order) => ({
                  strike: safeNum(order.strike),
                  order,
              }))
            : strikes.map((strike) => ({ strike, order: null }));

        const visibleInfo = document.getElementById("orderFlowVisibleCount");
        const loadOlder = document.getElementById("orderFlowLoadOlder");
        if (visibleInfo) {
            visibleInfo.textContent = orderRows.length
                ? "Showing newest " +
                  visibleOrders.length +
                  " of " +
                  orderRows.length +
                  " individual orders. Each Entry and P/L is separate."
                : "No qualifying individual orders yet; showing current quotes for " +
                  strikes.length +
                  " strikes.";
        }
        if (loadOlder) {
            loadOlder.hidden =
                !orderRows.length || visibleOrders.length >= orderRows.length;
            loadOlder.disabled = visibleOrders.length >= orderRows.length;
        }

        const html = renderItems
            .map((item) => {
                const strike = item.strike;
                const order = item.order;
                const orderType = String(
                    (order && order.type) || "",
                ).toUpperCase();
                // Only the side that produced this order receives Entry, Live, Time, and P/L.
                // The opposite side keeps its current chain quotes for the same strike.
                const call = mirroredOptionMetrics(
                    strike,
                    "CALL",
                    orderType === "CALL" ? [order] : [],
                );
                const put = mirroredOptionMetrics(
                    strike,
                    "PUT",
                    orderType === "PUT" ? [order] : [],
                );
                const rowClasses = order
                    ? [
                          "split-individual-order",
                          orderType === "CALL"
                              ? "call-order-row"
                              : "put-order-row",
                      ]
                    : [];
                if (
                    nearestSpotStrike &&
                    Math.abs(strike - nearestSpotStrike) < 0.001
                )
                    rowClasses.push("near-spot-row");
                if (selectedStrike && Math.abs(strike - selectedStrike) < 0.001)
                    rowClasses.push("scanner-highlight-strike");
                const rowClass = rowClasses.length
                    ? ` class="${rowClasses.join(" ")}"`
                    : "";

                const callCells = call
                    ? `
${mirroredTimeCell(call, "split-call-cell")}
${mirroredTotalPremiumCell(call, "entryTotalPremium", "entryPremium", "split-call-cell", "split-entry-cell", "This Call order's total entry premium")}
${mirroredTotalPremiumCell(call, "currentTotalPremium", "livePremium", "split-call-cell", "split-live-cell", "This Call order's current total premium")}
${mirroredGainCell(call, "split-call-cell")}
<td class="split-call-cell split-chain-volume">${call.volume ? fmt(call.volume) : "—"}</td>
<td class="split-call-cell split-chain-oi">${call.oi ? fmt(call.oi) : "—"}</td>
<td class="split-call-cell" title="Call bid quote and ${money(minPremium)}+ seller-side flow">${mirroredQuote(call.bid)}${mirroredFlowSub("Bid flow", call.flow && call.flow.bidPremium)}</td>
<td class="split-call-cell">${mirroredQuote(call.mark, "mark-quote")}</td>
<td class="split-call-cell" title="Call ask quote and ${money(minPremium)}+ buyer-side flow">${mirroredQuote(call.ask)}${mirroredFlowSub("Ask flow", call.flow && call.flow.askPremium)}</td>`
                    : mirroredEmptyCells("split-call-cell");

                const putCells = put
                    ? `
<td class="split-put-cell" title="Put ask quote and ${money(minPremium)}+ buyer-side flow">${mirroredQuote(put.ask)}${mirroredFlowSub("Ask flow", put.flow && put.flow.askPremium)}</td>
<td class="split-put-cell">${mirroredQuote(put.mark, "mark-quote")}</td>
<td class="split-put-cell" title="Put bid quote and ${money(minPremium)}+ seller-side flow">${mirroredQuote(put.bid)}${mirroredFlowSub("Bid flow", put.flow && put.flow.bidPremium)}</td>
<td class="split-put-cell split-chain-oi">${put.oi ? fmt(put.oi) : "—"}</td>
<td class="split-put-cell split-chain-volume">${put.volume ? fmt(put.volume) : "—"}</td>
${mirroredGainCell(put, "split-put-cell")}
${mirroredTotalPremiumCell(put, "currentTotalPremium", "livePremium", "split-put-cell", "split-live-cell", "This Put order's current total premium")}
${mirroredTotalPremiumCell(put, "entryTotalPremium", "entryPremium", "split-put-cell", "split-entry-cell", "This Put order's total entry premium")}
${mirroredTimeCell(put, "split-put-cell")}`
                    : mirroredEmptyCells("split-put-cell");

                const titleParts = [];
                if (order)
                    titleParts.push(
                        "Individual " +
                            orderType +
                            " order; size " +
                            safeNum(order.size) +
                            "; flow premium " +
                            money(safeNum(order.premium)),
                    );
                if (
                    nearestSpotStrike &&
                    Math.abs(strike - nearestSpotStrike) < 0.001
                )
                    titleParts.push(
                        "Nearest strike to live underlying price $" +
                            Number(spyPrice).toFixed(2),
                    );
                const strikeTitle = titleParts.length
                    ? ` title="${titleParts.join(" | ")}"`
                    : "";
                return `<tr${rowClass}>${callCells}<td class="split-strike-cell"${strikeTitle}>$${strike}</td>${putCells}</tr>`;
            })
            .join("");
        updateTbodyHTML(tbody, html);
    }

    // Main table uses a mirrored Calls | Strike | Puts layout with entry/live premium P/L.
    // Individual prints stay in the expandable detail table.
    window.renderOrderSideTable = function (rows, spyPrice = null) {
        window.__latestOrderSideRows = Array.isArray(rows) ? rows : [];
        window.__latestOrderSideSpyPrice = spyPrice;
        const rawTbody = document.getElementById("orderSideRawTable");
        const rawDetails = document.querySelector(".flow-raw-details");
        const rawIsOpen = Boolean(rawDetails && rawDetails.open);
        const orderInfo = document.getElementById("orderSideRangeInfo");
        const chainStrikes =
            window.tradeFlowChainByStrike &&
            typeof window.tradeFlowChainByStrike.keys === "function"
                ? [...window.tradeFlowChainByStrike.keys()]
                : [];
        const nearSpyRows = filterOrderSideRows(
            rows,
            spyPrice,
            chainStrikes.length ? chainStrikes : rows,
        );
        const recentRows = nearSpyRows.filter((r) =>
            typeof isWithinOrderFlowWindow === "function"
                ? isWithinOrderFlowWindow(r.timeMs)
                : true,
        );
        const minPremium =
            typeof getOrderFlowMinPremium === "function"
                ? getOrderFlowMinPremium()
                : 100000;
        const premiumFiltered = recentRows.filter(
            (r) => safeNum(r.premium) >= minPremium,
        );
        const totalHedgeShares = premiumFiltered.reduce(
            (s, r) => s + (Math.abs(safeNum(r.hedgeShares)) || 0),
            0,
        );
        const shown = premiumFiltered
            .map((r) => ({
                ...r,
                hedgeSharePct:
                    totalHedgeShares > 0
                        ? (Math.abs(safeNum(r.hedgeShares)) /
                              totalHedgeShares) *
                          100
                        : 0,
            }))
            .sort(
                (a, b) =>
                    safeNum(b.timeMs) - safeNum(a.timeMs) ||
                    safeNum(b.premium) - safeNum(a.premium),
            );
        // The collapsed detail panel creates no large DOM table. When opened, show a capped recent slice.
        // Main totals, mirrored strike rows, and P/L still use every retained qualifying row.
        const rawShown = rawIsOpen ? shown.slice(0, 300) : [];

        if (typeof updateOrderSideFlowTotals === "function")
            updateOrderSideFlowTotals(shown);
        renderMirroredOrderFlow(shown, spyPrice);
        renderRobinhoodPremiumByStrike(shown);
        renderPerfectFlowTape(shown);
        const rawCount = document.getElementById("orderFlowRawCount");
        if (rawCount)
            rawCount.textContent =
                rawIsOpen && rawShown.length < shown.length
                    ? shown.length +
                      " total; latest " +
                      rawShown.length +
                      " shown"
                    : shown.length;

        if (orderInfo) {
            // Keep performance calculations running in the background, but hide the debug/status message from the page.
            orderInfo.textContent = "";
            orderInfo.style.display = "none";
        }

        let html = "";
        if (!rawIsOpen) {
            html = "";
        } else if (!shown.length) {
            html =
                '<tr><td colspan="21" class="muted">No ' +
                money(minPremium) +
                "+ order-side rows found inside the current filter.</td></tr>";
        } else {
            html = rawShown
                .map((r) => {
                    const typeClass = r.type === "CALL" ? "call" : "put";
                    const hedgeClass = r.dealerHedge === "BUY" ? "pos" : "neg";
                    const statusClass =
                        r.status === "LIVE PRINT"
                            ? "live-print"
                            : r.status === "FORCED"
                              ? "warning"
                              : r.status === "DELAYED"
                                ? "neutral"
                                : r.status === "REFRESH TIME"
                                  ? "muted"
                                  : "muted";
                    const blockTag =
                        safeNum(r.premium) >= 1000000 || safeNum(r.size) >= 250
                            ? " ⚠ MOVE BLOCK"
                            : safeNum(r.premium) >= 100000 ||
                                safeNum(r.size) >= 100
                              ? " LARGE BLOCK"
                              : "";
                    const hotClass = blockTag ? ' class="hot-block"' : "";
                    const rhEntry = safeNum(
                        r.entryPrice ||
                            r.filledPremium ||
                            (String(r.side).toUpperCase() === "ASK"
                                ? r.ask
                                : r.bid),
                    );
                    const currentPrice =
                        safeNum(r.currentMark) ||
                        safeNum(r.mark) ||
                        safeNum(r.last) ||
                        (safeNum(r.bid) + safeNum(r.ask)) / 2;
                    const rhBuyCost = rhEntry * safeNum(r.size) * 100;
                    const liveValue = currentPrice * safeNum(r.size) * 100;
                    const plDollars = liveValue - rhBuyCost;
                    const winLoss =
                        rhEntry > 0 && currentPrice > 0
                            ? ((currentPrice - rhEntry) / rhEntry) * 100
                            : 0;
                    const gainClass =
                        winLoss > 0
                            ? "gain-pos"
                            : winLoss < 0
                              ? "gain-neg"
                              : "gain-flat";
                    const plClass =
                        plDollars > 0
                            ? "pl-dollar-pos"
                            : plDollars < 0
                              ? "pl-dollar-neg"
                              : "pl-dollar-flat";
                    const flowStatus = classifyFlowStatus(
                        r,
                        winLoss,
                        currentPrice,
                        rhEntry,
                    );
                    return `<tr${hotClass}>
<td>${fmtTime(r.timeMs)}</td>
<td>${fmtAge(r.timeMs)}</td>
<td>$${r.strike}</td>
<td class="${typeClass}">${r.type}</td>
<td>${r.side}</td>
<td>${optMoney(r.bid)}</td>
<td title="Filled premium used for P/L. ASK rows use ask fill; BID rows use bid fill.">${optMoney(rhEntry)}</td>
<td title="Current live premium / mark used for P/L">${optMoney(currentPrice)}</td>
<td class="${typeClass}">${fmt(r.delta, 3)}</td>
<td>${fmt(r.size)}</td>
<td>${money(r.premium)}</td>
<td class="rh-buy-cost" title="Cost basis = filled premium × contracts × 100">${rhMoney(rhBuyCost)}</td>
<td class="rh-buy-cost" title="Live value = live premium × contracts × 100">${rhMoney(liveValue)}</td>
<td class="${gainClass}" title="Gain/Loss % = (Live premium - Filled premium) ÷ Filled premium">${pctText(winLoss)}</td>
<td class="${plClass}" title="P/L $ = (Live premium - Filled premium) × contracts × 100">${rhMoney(plDollars)}</td>
<td><span class="flow-status-badge ${flowStatus.cls}" title="Auto status from premium gain/loss, side, size, and order premium">${flowStatus.label}</span></td>
<td class="${hedgeClass}">${r.dealerHedge} ${fmt(r.hedgeShares, 0)}</td>
<td class="${hedgeClass}">${fmt(r.hedgeSharePct, 1)}%</td>
<td>${fmt(r.orders)}</td>
<td class="${statusClass}">${r.status}</td>
<td class="${typeClass}">${r.signal}${blockTag}</td>
</tr>`;
                })
                .join("");
        }
        if (typeof updateTbodyHTML === "function")
            updateTbodyHTML(rawTbody, html);
        else if (rawTbody) rawTbody.innerHTML = html;
        if (typeof scrollToScannerSelectedStrike === "function")
            scrollToScannerSelectedStrike();
    };
})();

/* Large Order Blocks upgraded to match Order Side Flow by Strike layout. */
(function () {
    function n(v) {
        return Number.isFinite(Number(v)) ? Number(v) : 0;
    }
    function pct(v, d = 0) {
        return (Number.isFinite(Number(v)) ? Number(v).toFixed(d) : "0") + "%";
    }
    function cls(el, c) {
        if (!el) return;
        el.classList.remove(
            "call",
            "put",
            "bull",
            "bear",
            "neutral",
            "pos",
            "neg",
        );
        el.classList.add(c);
    }
    function txt(id, v) {
        const el = document.getElementById(id);
        if (el) el.textContent = v;
    }
    function valCls(id, c) {
        cls(document.getElementById(id), c);
    }
    function confScore(b) {
        const prem = n(b.premium),
            size = n(b.size),
            gain = Math.abs(n(b.gainPct)),
            impact = n(b.impactScore);
        let score = 35;
        if (prem >= 1000000) score += 28;
        else if (prem >= 500000) score += 20;
        else if (prem >= 250000) score += 13;
        else if (prem >= 100000) score += 8;
        if (size >= 500) score += 14;
        else if (size >= 250) score += 10;
        else if (size >= 100) score += 6;
        if (impact >= 25000) score += 16;
        else if (impact >= 12000) score += 10;
        else if (impact >= 6000) score += 5;
        if (gain >= 15) score += 7;
        else if (gain >= 5) score += 3;
        return Math.max(1, Math.min(99, Math.round(score)));
    }
    function updateLargeBlockTotals(blocks, data) {
        blocks = Array.isArray(blocks) ? blocks : [];
        const bull = blocks.filter((b) => b.direction === "BULLISH");
        const bear = blocks.filter((b) => b.direction === "BEARISH");
        const bullTotal = bull.reduce((s, b) => s + n(b.premium), 0);
        const bearTotal = bear.reduce((s, b) => s + n(b.premium), 0);
        const total = bullTotal + bearTotal;
        const net = bullTotal - bearTotal;
        const bullPct = total > 0 ? (bullTotal / total) * 100 : 0;
        const bearPct = total > 0 ? (bearTotal / total) * 100 : 0;
        txt("largeBlockBullTotal", money(bullTotal));
        txt("largeBlockBearTotal", money(bearTotal));
        txt("largeBlockNetTotal", money(net));
        txt("largeBlockBullPct", pct(bullPct, 0));
        txt("largeBlockBearPct", pct(bearPct, 0));
        txt("largeBlockNetPct", pct((Math.abs(net) / (total || 1)) * 100, 0));
        txt("largeBlockBullCount", bull.length + " bullish blocks");
        txt("largeBlockBearCount", bear.length + " bearish blocks");
        const bullBar = document.getElementById("largeBlockBullBar"),
            bearBar = document.getElementById("largeBlockBearBar");
        if (bullBar) bullBar.style.width = (total ? bullPct : 50) + "%";
        if (bearBar) bearBar.style.width = (total ? bearPct : 50) + "%";
        let bias = "WAIT",
            strength = "WAIT",
            label = "Balanced blocks",
            c = "neutral";
        const netPct = total ? (Math.abs(net) / total) * 100 : 0;
        if (net > 0) {
            bias = "BULLISH";
            c = "call";
            label = "Bullish blocks leading by " + money(Math.abs(net));
        } else if (net < 0) {
            bias = "BEARISH";
            c = "put";
            label = "Bearish blocks leading by " + money(Math.abs(net));
        }
        if (total > 0)
            strength =
                netPct >= 65
                    ? "EXTREME"
                    : netPct >= 35
                      ? "STRONG"
                      : netPct >= 15
                        ? "BUILDING"
                        : "LIGHT";
        txt("largeBlockBias", bias);
        txt("largeBlockBiasStrength", strength);
        txt("largeBlockNetLabel", label);
        valCls("largeBlockNetTotal", c);
        valCls("largeBlockBias", c);
    }
    const previousRenderLargeOrderBlocks = window.renderLargeOrderBlocks;
    window.renderLargeOrderBlocks = function (data, spyPrice = null) {
        const tbody = document.getElementById("largeOrderBlockTable");
        if (!tbody)
            return previousRenderLargeOrderBlocks
                ? previousRenderLargeOrderBlocks(data, spyPrice)
                : undefined;
        const limitEl = document.getElementById("rowsToShow");
        const limit = Math.max(30, n(limitEl && limitEl.value) || 30);
        const allBlocks = (
            data && Array.isArray(data.blocks) ? data.blocks : []
        ).filter(
            (b) =>
                !b.latestTimeMs ||
                (typeof isWithinOrderFlowWindow === "function"
                    ? isWithinOrderFlowWindow(b.latestTimeMs)
                    : true),
        );
        const blocks = allBlocks
            .slice(0, limit)
            .map((b) => ({ ...b, confidence: confScore(b) }));
        const windowLabel =
            typeof getOrderFlowWindowLabel === "function"
                ? getOrderFlowWindowLabel()
                : "the selected window";
        updateLargeBlockTotals(blocks, data || {});
        const top = blocks[0];
        txt("bullishBlockPremium", money((data && data.bullishPremium) || 0));
        txt("bearishBlockPremium", money((data && data.bearishPremium) || 0));
        txt("largestOrderBlock", top ? "$" + top.strike : "--");
        txt(
            "largestOrderBlockInfo",
            top
                ? top.direction +
                      " " +
                      top.type +
                      " " +
                      top.side +
                      " | Premium: " +
                      money(top.premium) +
                      " | Gain/Loss: " +
                      fmtPct(top.gainPct, 1) +
                      " | Confidence: " +
                      top.confidence +
                      "%"
                : "No large block found near current SPY.",
        );
        const net = (data && data.netPremium) || 0;
        const biasEl = document.getElementById("blockImpactBias");
        if (biasEl) {
            if (net > 0) {
                biasEl.textContent = "BULLISH";
                setClass("blockImpactBias", "bull");
            } else if (net < 0) {
                biasEl.textContent = "BEARISH";
                setClass("blockImpactBias", "bear");
            } else {
                biasEl.textContent = "BALANCED";
                setClass("blockImpactBias", "neutral");
            }
        }
        txt(
            "blockImpactReason",
            "Net block premium: " +
                money(net) +
                ". Blocks are shown with live premium P/L like Order Side Flow.",
        );
        const info = document.getElementById("largeOrderBlockInfo");
        if (info)
            info.textContent =
                Number.isFinite(spyPrice) && spyPrice > 0
                    ? "Shows market-moving blocks using the same layout as Order Side Flow. Filter: " +
                      windowLabel +
                      ", $100,000+ premium or 100+ contracts, within +/- 10 of current SPY ($" +
                      fmt(spyPrice, 2) +
                      "). Filled Prem = average side fill, Live Prem = current mark, Gain/Loss = live premium vs filled premium."
                    : "Shows market-moving blocks using the same layout as Order Side Flow. Filled Prem = average side fill, Live Prem = current mark, Gain/Loss = live premium vs filled premium.";
        let html = "";
        if (!blocks.length) {
            html =
                '<tr><td colspan="19" class="muted">No large $100,000+ premium or 100+ size order blocks found during ' +
                windowLabel +
                " and within +/- 10 strikes of current SPY price</td></tr>";
        } else {
            const totalHedge = blocks.reduce(
                (s, b) => s + Math.abs(n(b.hedgeShares)),
                0,
            );
            html = blocks
                .map((b) => {
                    const typeClass = b.type === "CALL" ? "call" : "put";
                    const dirClass =
                        b.direction === "BULLISH"
                            ? "call"
                            : b.direction === "BEARISH"
                              ? "put"
                              : "neutral";
                    const hedgeClass = b.dealerHedge === "BUY" ? "pos" : "neg";
                    const gainClass =
                        n(b.gainPct) > 0
                            ? "gain-pos"
                            : n(b.gainPct) < 0
                              ? "gain-neg"
                              : "gain-flat";
                    const hot =
                        n(b.premium) >= 500000 ||
                        b.signal === "MARKET MOVE RISK" ||
                        b.signal === "HIGH IMPACT";
                    const costBasis = n(b.avgEntryPrice) * n(b.size) * 100;
                    const liveValue = n(b.avgCurrentMark) * n(b.size) * 100;
                    const hedgeShare = totalHedge
                        ? (Math.abs(n(b.hedgeShares)) / totalHedge) * 100
                        : 0;
                    const signalClass =
                        b.direction === "BULLISH"
                            ? "signal-long"
                            : b.direction === "BEARISH"
                              ? "signal-short"
                              : "signal-watch";
                    const signalText =
                        (n(b.premium) >= 1000000 ? "🐋 " : "") +
                        (b.signal || "LARGE BLOCK");
                    return `<tr${hot ? ' class="hot-block"' : ""}>
<td>${fmtTime(b.latestTimeMs)}</td>
<td>${fmtAge(b.latestTimeMs)}</td>
<td>$${b.strike}</td>
<td class="${typeClass}">${b.type}</td>
<td>${b.side}</td>
<td class="${dirClass}">${b.direction}</td>
<td title="Average filled premium paid">${optMoney(b.avgEntryPrice)}</td>
<td title="Current live premium / mark">${optMoney(b.avgCurrentMark)}</td>
<td>${fmt(b.size)}</td>
<td>${money(b.premium)}</td>
<td class="rh-buy-cost" title="Filled premium × contracts × 100">${typeof rhMoney === "function" ? rhMoney(costBasis) : money(costBasis)}</td>
<td class="rh-buy-cost" title="Live premium × contracts × 100">${typeof rhMoney === "function" ? rhMoney(liveValue) : money(liveValue)}</td>
<td class="${gainClass}" title="Avg filled: $${fmt(n(b.avgEntryPrice), 2)} | Live mark: $${fmt(n(b.avgCurrentMark), 2)}">${typeof pctText === "function" ? pctText(n(b.gainPct)) : fmtPct(n(b.gainPct), 1)}</td>
<td class="${typeClass}">${fmt(n(b.avgDelta), 3)}</td>
<td class="${hedgeClass}">${b.dealerHedge} ${fmt(n(b.hedgeShares), 0)}</td>
<td class="${hedgeClass}">${fmt(hedgeShare, 1)}%</td>
<td>${fmt(n(b.orders))}</td>
<td><div class="confidence-wrap"><span>${b.confidence}%</span><span class="confidence-track"><span class="confidence-fill" style="width:${b.confidence}%"></span></span></div></td>
<td><span class="signal-badge ${signalClass}">${signalText}</span></td>
</tr>`;
                })
                .join("");
        }
        if (typeof updateTbodyHTML === "function") updateTbodyHTML(tbody, html);
        else tbody.innerHTML = html;
    };
})();

(function () {
    function syncOrderFlowControls() {
        const windowSelect = document.getElementById("flowWindow");
        const premiumSelect = document.getElementById("flowPremiumFilter");
        if (windowSelect) {
            window.flowWindowMode =
                windowSelect.value === "all_day" ? "all_day" : "rolling";
            window.flowWindowSeconds =
                window.flowWindowMode === "all_day"
                    ? 0
                    : parseInt(windowSelect.value, 10) || 600;
        }
        if (premiumSelect)
            window.orderFlowMinPremium =
                parseInt(premiumSelect.value, 10) || 100000;
    }

    function applyOrderFlowFilters(forceRefresh) {
        syncOrderFlowControls();
        window.orderFlowVisibleLimit = 150;

        // Re-render immediately from remembered rows when available.
        // Then fetch fresh data so the selected premium and time windows stay live.
        if (typeof loadGammaSystem === "function") {
            loadGammaSystem(forceRefresh ? false : true);
        }
    }

    function bindOrderFlowControls() {
        const controls = [
            document.getElementById("flowWindow"),
            document.getElementById("flowPremiumFilter"),
        ].filter(Boolean);
        syncOrderFlowControls();
        for (const control of controls) {
            if (control.dataset.bound === "1") continue;
            control.dataset.bound = "1";
            control.addEventListener("change", function () {
                applyOrderFlowFilters(true);
            });
        }

        const rawDetails = document.querySelector(".flow-raw-details");
        if (rawDetails && rawDetails.dataset.lazyBound !== "1") {
            rawDetails.dataset.lazyBound = "1";
            rawDetails.addEventListener("toggle", function () {
                if (
                    !rawDetails.open ||
                    typeof window.renderOrderSideTable !== "function"
                )
                    return;
                window.renderOrderSideTable(
                    window.__latestOrderSideRows || [],
                    window.__latestOrderSideSpyPrice || null,
                );
            });
        }

        const loadOlder = document.getElementById("orderFlowLoadOlder");
        if (loadOlder && loadOlder.dataset.bound !== "1") {
            loadOlder.dataset.bound = "1";
            loadOlder.addEventListener("click", function () {
                window.orderFlowVisibleLimit =
                    Math.max(150, Number(window.orderFlowVisibleLimit || 150)) +
                    150;
                if (typeof window.renderOrderSideTable === "function") {
                    window.renderOrderSideTable(
                        window.__latestOrderSideRows || [],
                        window.__latestOrderSideSpyPrice || null,
                    );
                }
            });
        }
    }

    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", bindOrderFlowControls);
    } else {
        bindOrderFlowControls();
    }
})();
