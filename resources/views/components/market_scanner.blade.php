<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPTIONS SWIFT MULTI-SYMBOL SCANNER</title>
    <link rel="stylesheet" href="/assets/client/styles/hub/market-scanner.css?v=1.0" type="text/css">
</head>

<body>
    <div id="topbar">
        <div id="topbar-left">
            <div id="title">◈ OPTIONS SWIFT · SPY QQQ SPX + MAG 7 · FLOW · PERSIST · VWAP</div>
            <div class="live-badge">
                <div class="live-dot"></div><span>LIVE · 30s</span>
            </div>
            <div id="trade-date-wrap">
                <label for="tradeDate">TRADE DATE</label>
                <input type="date" id="tradeDate" onchange="onTradeDateChange()">
                <button class="date-quick-btn" onclick="setTradeDateToday()">TODAY</button>
            </div>
        </div>
        <span id="countdown" style="font-size:10px;color:var(--muted);letter-spacing:1px">NEXT SCAN: 30s</span>
    </div>

    <div id="stat-row">
        <div class="stat-item">
            <div class="stat-val dual" id="stat-dual">0</div>
            <div class="stat-lbl">DUAL</div>
        </div>
        <div class="stat-item">
            <div class="stat-val conf" id="stat-conf">0</div>
            <div class="stat-lbl">CONF</div>
        </div>
        <div class="stat-item">
            <div class="stat-val reload" id="stat-reload">0</div>
            <div class="stat-lbl">RELOAD</div>
        </div>
        <div id="stat-time">
            <span id="stat-age">—</span>
            <button onclick="runScan()">⟳</button>
        </div>
    </div>

    <div id="pressure-row">
        <span class="pressure-label bull">BULL</span>
        <div id="pressure-track">
            <div id="pressure-fill"></div>
        </div>
        <span class="pressure-label bear">BEAR</span>
    </div>

    <div id="side-toggle-row">
        <button class="side-btn bull-active" id="btn-bull" onclick="setSide('bull')">BULL</button>
        <button class="side-btn" id="btn-bear" onclick="setSide('bear')">BEAR</button>
    </div>

    <div id="ips-filter-row">
        <span id="ips-filter-label">IPS ≥</span>
        <input type="range" id="ips-slider" min="0" max="90" value="0" step="5"
            oninput="onIpsSlider(this.value)">
        <span id="ips-val">0</span>
    </div>

    <div id="filter-row">
        <span class="filter-section-label">STATE</span>
        <button class="flt-btn active" id="fs-all" onclick="setStateFilter('all')">ALL</button>
        <button class="flt-btn" id="fs-confirmed" onclick="setStateFilter('CONFIRMED')">CONFIRMED</button>
        <button class="flt-btn" id="fs-building" onclick="setStateFilter('BUILDING')">BUILDING</button>
        <button class="flt-btn" id="fs-early" onclick="setStateFilter('EARLY')">EARLY</button>
        <span class="filter-divider">│</span>
        <span class="filter-section-label">GRADE</span>
        <button class="flt-btn active" id="fg-all" onclick="setGradeFilter('all')">ALL</button>
        <button class="flt-btn" id="fg-a" onclick="setGradeFilter('A')">A+/A</button>
        <button class="flt-btn" id="fg-b" onclick="setGradeFilter('B')">B+/B</button>
        <span class="filter-divider">│</span>
        <span class="filter-section-label">VWAP</span>
        <button class="flt-btn active" id="fv-all" onclick="setVwapFilter('all')">ALL</button>
        <button class="flt-btn" id="fv-above" onclick="setVwapFilter('above')">ABOVE</button>
        <button class="flt-btn" id="fv-below" onclick="setVwapFilter('below')">BELOW</button>
        <span id="flow-label">BULLISH FLOW</span>
        <span id="count-label"></span>
    </div>

    <div id="main-layout">
        <div id="list-pane">
            <table id="imb-table">
                <thead>
                    <tr>
                        <th>SYMBOL</th>
                        <th>IMBALANCE · Δ</th>
                        <th>Q / PERSIST</th>
                        <th>STATE / CONF</th>
                        <th class="c">VWAP</th>
                        <th class="r">SCORE</th>
                        <th class="r">GRD</th>
                    </tr>
                </thead>
                <tbody id="imb-tbody">
                    <tr id="empty-row">
                        <td colspan="7">LOADING DATA…</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- DETAIL DRAWER -->
        <div id="detail-pane">
            <div id="drawer-inner">
                <div id="drawer-header">
                    <div>
                        <div id="drawer-sym">—</div>
                        <div id="drawer-meta"></div>
                    </div>
                    <button id="drawer-close" onclick="closeDrawer()">✕</button>
                </div>

                <div id="drawer-score-row">
                    <div>
                        <div id="drawer-ips-big">—</div>
                        <div id="drawer-ips-sub">/ 100 IPS</div>
                    </div>
                    <div id="drawer-grade-big"></div>
                </div>
                <div id="drawer-ips-bar">
                    <div id="drawer-ips-bar-fill" style="width:0%"></div>
                </div>
                <div id="drawer-state-badge" class="EARLY">—</div>

                <!-- Sub-score bars: Imbalance, Urgency, Persistence, Quality, Confirm -->
                <div id="drawer-sub-scores"></div>

                <hr class="drawer-divider">

                <div id="drawer-details"></div>

                <button id="drawer-cta" onclick="drawerViewChain()">VIEW <span id="drawer-cta-sym"></span> IN CHAIN
                    →</button>
            </div>
        </div>
    </div>

    <!-- BOTTOM PANELS -->
    <div id="bottom-panels">
        <div id="watchlist-panel">
            <h3>WATCHLIST BUCKETS</h3>
            <div class="bucket">
                <div class="bucket-label confirmed">CONFIRMED ✓</div>
                <div class="bucket-syms" id="bucket-confirmed">—</div>
            </div>
            <div class="bucket">
                <div class="bucket-label early">EARLY / BUILDING</div>
                <div class="bucket-syms" id="bucket-early">—</div>
            </div>
            <div class="bucket">
                <div class="bucket-label reload">RELOAD CANDIDATES</div>
                <div class="bucket-syms" id="bucket-reload">—</div>
            </div>
            <div class="bucket">
                <div class="bucket-label fade">FADE / AVOID</div>
                <div class="bucket-syms" id="bucket-fade">—</div>
            </div>
        </div>

        <div id="legend-panel">
            <h3>STATE LEGEND</h3>
            <div class="legend-grid">
                <div class="legend-cell">
                    <div class="legend-cell-title">EARLY</div>
                    <div class="legend-cell-desc">pressure just crossed</div>
                </div>
                <div class="legend-cell building">
                    <div class="legend-cell-title">BUILDING</div>
                    <div class="legend-cell-desc">imbalance increasing</div>
                </div>
                <div class="legend-cell confirmed">
                    <div class="legend-cell-title">CONFIRMED</div>
                    <div class="legend-cell-desc">persist + confirm aligned</div>
                </div>
                <div class="legend-cell extended">
                    <div class="legend-cell-title">EXTENDED</div>
                    <div class="legend-cell-desc">strong but stretched</div>
                </div>
                <div class="legend-cell reload">
                    <div class="legend-cell-title">RELOAD</div>
                    <div class="legend-cell-desc">new cluster, same dir</div>
                </div>
                <div class="legend-cell fade">
                    <div class="legend-cell-title">FADE / MIXED</div>
                    <div class="legend-cell-desc">avoid / dim</div>
                </div>
            </div>
        </div>
    </div>

    <div id="bottom-bar">
        <div class="leg-item">
            <div class="leg-dot" style="background:#b06bff"></div>DUAL spike+skew
        </div>
        <div class="leg-item">
            <div class="leg-dot" style="background:#4db8ff"></div>TRIPLE +persist+confirm
        </div>
        <div class="leg-item">
            <div class="leg-dot" style="background:#ffcc00"></div>RELOAD accumulation
        </div>
        <div class="leg-item">
            <div class="leg-dot" style="background:#ff8c42"></div>SPIKE Δ ratio jump
        </div>
        <div class="leg-item">
            <div class="leg-dot" style="background:#00ff8c"></div>NEW entry
        </div>
        <span style="margin-left:auto">IPS=40% hedge · 30% premium · 20% volume · 10% persist</span>
    </div>
    <div id="status-bar">INITIALIZING…</div>
    <script>
        window.AppConfig = {
            csrfToken: "{{ csrf_token() }}"
        };
    </script>
    <script src="/assets/client/js/hub/market_scanner.js?v=1.0"></script>
</body>

</html>
