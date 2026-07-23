<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Options Swift Multi-Symbol Gamma & Flow GEX</title>
    <link rel="stylesheet" href="/assets/client/styles/hub/option-chain.css?v=1.0" type="text/css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
    <div class="container">
        <div id="chain-symbol-banner"
            style="
          background: rgba(0, 255, 140, 0.12);
          border: 1px solid rgba(0, 255, 140, 0.3);
          border-radius: 14px;
          padding: 10px 18px;
          margin-bottom: 16px;
          display: flex;
          align-items: center;
          gap: 12px;
          font-weight: 700;
          font-size: 14px;
          color: #00ff8c;
        ">
            <span>⛓ CHAIN:</span>
            <span id="chain-symbol-display" style="font-size: 20px; letter-spacing: 2px">SPY</span>
            <span style="color: #4a6660; font-size: 11px; font-weight: 400">Opened from imbalance scanner · loading
                today's expiry</span>
            <a id="back-to-scanner" href="./scanner_multi_symbol(16).html"
                style="
            margin-left: auto;
            color: #00ff8c;
            text-decoration: none;
            border: 1px solid rgba(0, 255, 140, 0.35);
            border-radius: 9px;
            padding: 7px 11px;
            font-size: 11px;
            white-space: nowrap;
          ">←
                BACK TO SCANNER</a>
        </div>

        <div class="card">
            <div class="market-strip">
                <span>⚡ LIVE FLOW</span>
                <span>📈 GAMMA TRACKING</span>
                <span>🐋 WHALE ORDERS</span>
                <span>🎯 MARKET BIAS</span>
                <span id="liveUnderlyingPrice">SPY GEX SPOT $--</span>
                <span id="liveUnderlyingFeed">MASSIVE SIP: CONNECTING</span>
            </div>

            <div id="sipSpotPanel" class="sip-spot-panel"
                aria-label="Massive real-time consolidated SIP price and NBBO">
                <div class="sip-spot-cell">
                    <span id="sipLastLabel" class="sip-spot-label">SPY SIP LAST</span><strong id="sipLast"
                        class="sip-spot-value">$--</strong>
                </div>
                <div class="sip-spot-cell">
                    <span class="sip-spot-label">NBBO BID</span><strong id="sipBid"
                        class="sip-spot-value">$--</strong>
                </div>
                <div class="sip-spot-cell">
                    <span class="sip-spot-label">NBBO ASK</span><strong id="sipAsk"
                        class="sip-spot-value">$--</strong>
                </div>
                <div class="sip-spot-cell">
                    <span class="sip-spot-label">GEX SPOT · NBBO MID</span><strong id="sipGexMid"
                        class="sip-spot-value">$--</strong>
                </div>
                <div class="sip-spot-cell">
                    <span class="sip-spot-label">NBBO SPREAD</span><strong id="sipSpread"
                        class="sip-spot-value">$--</strong>
                </div>
                <div class="sip-spot-cell">
                    <span class="sip-spot-label">MASSIVE SIP FEED</span><strong id="sipFeedStatus"
                        class="sip-spot-value">CONNECTING</strong>
                </div>
            </div>
            <div id="marketDataError" class="market-data-error" role="alert"></div>

            <div class="controls">
                <select id="chainSymbol" onchange="changeChainSymbol(this.value)">
                    <option value="SPY">SPY</option>
                    <option value="QQQ">QQQ</option>
                    <option value="SPX">SPX</option>
                    <option value="AAPL">AAPL</option>
                    <option value="MSFT">MSFT</option>
                    <option value="NVDA">NVDA</option>
                    <option value="AMZN">AMZN</option>
                    <option value="META">META</option>
                    <option value="GOOGL">GOOGL</option>
                    <option value="TSLA">TSLA</option>
                </select>
                <input id="expiration" type="date" />
                <select id="rowsToShow">
                    <option value="30" selected>Top 30 Rows</option>
                    <option value="50">Top 50 Rows</option>
                    <option value="75">Top 75 Rows</option>
                </select>
                <button onclick="loadGammaSystem()">Load Pro Gamma</button>
            </div>
            <p id="status" class="muted"></p>
        </div>

        <div class="grid4">
            <div class="card">
                <h2 class="call">Flow Call Wall</h2>
                <div id="topCall" class="big">--</div>
                <p class="muted">Best blended wall: OI + volume + gamma + premium.</p>
            </div>
            <div class="card">
                <h2 class="put">Flow Put Wall</h2>
                <div id="topPut" class="big">--</div>
                <p class="muted">Best blended downside wall.</p>
            </div>
            <div class="card">
                <h2 class="gamma">Gamma Call Wall</h2>
                <div id="gammaCallWall" class="big">--</div>
                <p id="gammaCallInfo" class="muted">Highest call gamma exposure.</p>
            </div>
            <div class="card">
                <h2 class="gamma">Gamma Put Wall</h2>
                <div id="gammaPutWall" class="big">--</div>
                <p id="gammaPutInfo" class="muted">Highest put gamma exposure.</p>
            </div>
        </div>

        <div class="grid4">
            <div class="card">
                <h2>Total Call Premium</h2>
                <div id="totalCallPremium" class="big call">$0</div>
                <p class="muted">Mid price × volume × 100.</p>
            </div>
            <div class="card">
                <h2>Total Put Premium</h2>
                <div id="totalPutPremium" class="big put">$0</div>
                <p class="muted">Mid price × volume × 100.</p>
            </div>
            <div class="card">
                <h2>OI Net GEX (Convention)</h2>
                <div id="netGamma" class="big">0</div>
                <p id="netGammaInfo" class="muted">
                    Calls positive minus puts, measured per 1% underlying move.
                </p>
            </div>
            <div class="card">
                <h2>Gamma Flip Estimate</h2>
                <div id="gammaFlip" class="big gamma">--</div>
                <p class="muted">
                    Nearest strike where signed OI gamma shifts direction.
                </p>
            </div>
        </div>

        <div class="grid4">
            <div class="card">
                <h2>Live Dealer Flow GEX</h2>
                <div id="flowNetGamma" class="big neutral">$0</div>
                <p id="flowNetGammaInfo" class="muted">
                    Estimated from current Bid/Ask quote size during the selected flow
                    window.
                </p>
            </div>
            <div class="card">
                <h2>Long-Gamma Flow</h2>
                <div id="flowLongGamma" class="big call">$0</div>
                <p class="muted">
                    Bid-side contracts: customer sale / dealer option purchase
                    assumption.
                </p>
            </div>
            <div class="card">
                <h2>Short-Gamma Flow</h2>
                <div id="flowShortGamma" class="big put">$0</div>
                <p class="muted">
                    Ask-side contracts: customer purchase / dealer option sale
                    assumption.
                </p>
            </div>
            <div class="card">
                <h2>Flow Gamma Regime</h2>
                <div id="flowGammaRegime" class="big neutral">WAIT</div>
                <p id="flowGammaRegimeInfo" class="muted">
                    Positive may dampen movement; negative may amplify movement.
                </p>
            </div>
        </div>

        <div class="grid4">
            <div class="card">
                <h2 class="call">Top Call Premium Strike</h2>
                <div id="topCallPremiumStrike" class="big call">--</div>
                <p id="topCallPremiumInfo" class="muted">
                    Highest call premium by strike.
                </p>
            </div>
            <div class="card">
                <h2 class="put">Top Put Premium Strike</h2>
                <div id="topPutPremiumStrike" class="big put">--</div>
                <p id="topPutPremiumInfo" class="muted">
                    Highest put premium by strike.
                </p>
            </div>
            <div class="card">
                <h2>Net Premium Flow</h2>
                <div id="netPremiumFlow" class="big neutral">$0</div>
                <p id="netPremiumFlowInfo" class="muted">
                    Call premium minus put premium.
                </p>
            </div>
            <div class="card">
                <h2>Dominant Strike Flow</h2>
                <div id="dominantPremiumStrike" class="big neutral">--</div>
                <p id="dominantPremiumInfo" class="muted">
                    Largest net call/put premium at one strike.
                </p>
            </div>
        </div>

        <div class="grid4">
            <div class="card">
                <h2 class="whale">Top Call Ask Whale</h2>
                <div id="topCallAskWhaleStrike" class="big whale">--</div>
                <p id="topCallAskWhaleInfo" class="muted">
                    Highest call ask-size pressure by strike.
                </p>
            </div>
            <div class="card">
                <h2 class="whale">Top Call Bid Whale</h2>
                <div id="topCallBidWhaleStrike" class="big whale">--</div>
                <p id="topCallBidWhaleInfo" class="muted">
                    Highest call bid-size pressure by strike.
                </p>
            </div>
            <div class="card">
                <h2 class="whale">Top Put Bid Whale</h2>
                <div id="topPutBidWhaleStrike" class="big whale">--</div>
                <p id="topPutBidWhaleInfo" class="muted">
                    Highest put bid-size pressure by strike.
                </p>
            </div>
            <div class="card">
                <h2 class="whale">Top Put Ask Whale</h2>
                <div id="topPutAskWhaleStrike" class="big whale">--</div>
                <p id="topPutAskWhaleInfo" class="muted">
                    Highest put ask-size pressure by strike.
                </p>
            </div>
        </div>

        <div class="grid4">
            <div class="card">
                <h2>Call Ask Whale Premium</h2>
                <div id="totalCallAskWhalePremium" class="big call">$0</div>
                <p class="muted">Call ask × ask size × 100.</p>
            </div>
            <div class="card">
                <h2>Call Bid Whale Premium</h2>
                <div id="totalCallBidWhalePremium" class="big call">$0</div>
                <p class="muted">Call bid × bid size × 100.</p>
            </div>
            <div class="card">
                <h2>Put Bid Whale Premium</h2>
                <div id="totalPutBidWhalePremium" class="big put">$0</div>
                <p class="muted">Put bid × bid size × 100.</p>
            </div>
            <div class="card">
                <h2>Put Ask Whale Premium</h2>
                <div id="totalPutAskWhalePremium" class="big put">$0</div>
                <p class="muted">Put ask × ask size × 100.</p>
            </div>
        </div>

        <div class="grid4">
            <div class="card">
                <h2>Forced Orders</h2>
                <div id="forcedOrderCount" class="big warning">0</div>
                <p class="muted">
                    Aggressive bid/ask pressure detected by size imbalance.
                </p>
            </div>
            <div class="card">
                <h2>Delayed Orders</h2>
                <div id="delayedOrderCount" class="big neutral">0</div>
                <p class="muted">Quotes older than the delay threshold.</p>
            </div>
            <div class="card">
                <h2>Call Orders</h2>
                <div id="callOrderCount" class="big call">0</div>
                <p class="muted">Number of call-side bid/ask quote orders found.</p>
            </div>
            <div class="card">
                <h2>Put Orders</h2>
                <div id="putOrderCount" class="big put">0</div>
                <p class="muted">Number of put-side bid/ask quote orders found.</p>
            </div>
        </div>

        <div class="grid4">
            <div class="card">
                <h2 class="warning">Largest Order Block</h2>
                <div id="largestOrderBlock" class="big warning">--</div>
                <p id="largestOrderBlockInfo" class="muted">
                    Highest premium cluster near current SPY.
                </p>
            </div>
            <div class="card">
                <h2 class="call">Bullish Block Premium</h2>
                <div id="bullishBlockPremium" class="big call">$0</div>
                <p class="muted">
                    Call ask + put bid blocks that may require dealer buying.
                </p>
            </div>
            <div class="card">
                <h2 class="put">Bearish Block Premium</h2>
                <div id="bearishBlockPremium" class="big put">$0</div>
                <p class="muted">
                    Call bid + put ask blocks that may require dealer selling.
                </p>
            </div>
            <div class="card">
                <h2>Block Impact Bias</h2>
                <div id="blockImpactBias" class="big neutral">WAIT</div>
                <p id="blockImpactReason" class="muted">Net large-block pressure.</p>
            </div>
        </div>

        <div class="grid3">
            <div class="card">
                <h2>Dealer Gamma Regime</h2>
                <div id="dealerRegime" class="big neutral">WAIT</div>
                <p id="dealerReason" class="muted">Load data first.</p>
            </div>
            <div class="card">
                <h2>Premium Flow Bias</h2>
                <div id="premiumBias" class="big neutral">WAIT</div>
                <div class="bar-bg">
                    <div id="premiumBar" class="bar-fill"></div>
                </div>
                <p id="premiumReason" class="muted">Call/put premium ratio.</p>
            </div>
            <div class="card">
                <h2>Pro Trade Map</h2>
                <div id="tradeMap" class="mid">--</div>
                <p id="tradeMapReason" class="muted">Key levels will appear here.</p>
            </div>
        </div>

        <div class="card">
            <h2>Premium Flow by Strike</h2>
            <p class="muted">
                Shows call premium, put premium, and net premium per strike. Positive
                net = call premium stronger. Negative net = put premium stronger.
            </p>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Strike</th>
                            <th>Call Premium</th>
                            <th>Put Premium</th>
                            <th>Net Premium</th>
                            <th>Call Vol</th>
                            <th>Put Vol</th>
                            <th>Bias</th>
                        </tr>
                    </thead>
                    <tbody id="premiumStrikeTable"></tbody>
                </table>
            </div>
        </div>

        <div class="card">
            <h2>
                Order Side Flow by Strike
                <span style="float: right; font-size: 12px">Trades:
                    <span id="tradeFlowStatus" class="offline">OFFLINE</span> &nbsp;
                    Strikes: 14 Total (7 Below / 7 Above) &nbsp; Premium:
                    <select id="flowPremiumFilter">
                        <option value="100000" selected>$100K+</option>
                        <option value="200000">$200K+</option>
                        <option value="300000">$300K+</option>
                        <option value="400000">$400K+</option>
                        <option value="500000">$500K+</option>
                    </select>
                    &nbsp; Flow Window:
                    <select id="flowWindow">
                        <option value="1800" selected>30 Minutes</option>
                    </select></span>
            </h2>
            <p id="orderSideRangeInfo" class="muted">
                Shows Call/Put quotes, flow entry premium, live premium, and P/L by
                strike. Use the Premium and Flow Window controls to scan the selected
                order size across a rolling window or the full New York regular
                trading session.
            </p>
            <div class="order-flow-chain-shell">
                <div class="order-flow-chain-banner">
                    <div class="order-flow-chain-side call-side">
                        <span><span id="orderFlowSplitCallPct">0%</span> Calls</span>
                        <strong id="orderFlowSplitCallTotal">$0</strong>
                    </div>
                    <div class="order-flow-chain-center">Flow by Strike</div>
                    <div class="order-flow-chain-side put-side">
                        <strong id="orderFlowSplitPutTotal">$0</strong>
                        <span>Puts <span id="orderFlowSplitPutPct">0%</span></span>
                    </div>
                </div>
                <div class="split-flow-scroll">
                    <table class="split-flow-table">
                        <thead>
                            <tr class="split-group-row">
                                <th colspan="9" class="split-call-head">Calls</th>
                                <th rowspan="2" class="split-strike-head">Strike</th>
                                <th colspan="9" class="split-put-head">Puts</th>
                            </tr>
                            <tr class="split-columns-row">
                                <th class="split-time-head">Time ET</th>
                                <th class="split-entry-head">Entry $</th>
                                <th class="split-live-head">Current $</th>
                                <th class="split-gain-head">P/L</th>
                                <th>Vol</th>
                                <th>OI</th>
                                <th>Bid</th>
                                <th>Mark</th>
                                <th>Ask</th>
                                <th>Ask</th>
                                <th>Mark</th>
                                <th>Bid</th>
                                <th>OI</th>
                                <th>Vol</th>
                                <th class="split-gain-head">P/L</th>
                                <th class="split-live-head">Current $</th>
                                <th class="split-entry-head">Entry $</th>
                                <th class="split-time-head">Time ET</th>
                            </tr>
                        </thead>
                        <tbody id="orderSideTable"></tbody>
                    </table>
                </div>
                <div class="split-flow-pager">
                    <span id="orderFlowVisibleCount">Newest individual orders appear first.</span>
                    <button id="orderFlowLoadOlder" type="button" hidden>
                        Load 150 Older Orders
                    </button>
                </div>
            </div>
            <details class="flow-raw-details">
                <summary>
                    View individual flow prints (<span id="orderFlowRawCount">0</span>)
                </summary>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Order Time ET</th>
                                <th>Age</th>
                                <th>Strike</th>
                                <th>Type</th>
                                <th>Side</th>
                                <th>Bid</th>
                                <th>Filled Prem</th>
                                <th>Live Prem</th>
                                <th>Delta</th>
                                <th>Size</th>
                                <th>Flow Prem$</th>
                                <th>Cost Basis</th>
                                <th>Live Value</th>
                                <th>Gain/Loss</th>
                                <th>P/L $</th>
                                <th>Flow Status</th>
                                <th>Dealer Hedge</th>
                                <th>Hedge Share</th>
                                <th>Orders</th>
                                <th>Status</th>
                                <th>Signal</th>
                            </tr>
                        </thead>
                        <tbody id="orderSideRawTable"></tbody>
                    </table>
                </div>
            </details>
            <div class="order-flow-totals">
                <div class="order-flow-total-box">
                    <div class="order-flow-total-label">
                        <span>🟢 Call Flow Total</span><span id="orderFlowCallPct">0%</span>
                    </div>
                    <div id="orderFlowCallTotal" class="order-flow-total-value call">
                        $0
                    </div>
                    <div id="orderFlowCallCount" class="order-flow-mini">
                        0 call rows
                    </div>
                </div>
                <div class="order-flow-total-box">
                    <div class="order-flow-total-label">
                        <span>🔴 Put Flow Total</span><span id="orderFlowPutPct">0%</span>
                    </div>
                    <div id="orderFlowPutTotal" class="order-flow-total-value put">
                        $0
                    </div>
                    <div id="orderFlowPutCount" class="order-flow-mini">0 put rows</div>
                </div>
                <div class="order-flow-total-box">
                    <div class="order-flow-total-label">
                        <span>⚡ Net Call - Put</span><span id="orderFlowNetPct">0%</span>
                    </div>
                    <div id="orderFlowNetTotal" class="order-flow-total-value neutral">
                        $0
                    </div>
                    <div id="orderFlowNetLabel" class="order-flow-mini">
                        Balanced flow
                    </div>
                </div>
                <div class="order-flow-total-box">
                    <div class="order-flow-total-label">
                        <span>🎯 Flow Bias</span><span id="orderFlowBiasStrength">WAIT</span>
                    </div>
                    <div id="orderFlowBias" class="order-flow-total-value neutral">
                        WAIT
                    </div>
                    <div class="order-flow-bar">
                        <div id="orderFlowCallBar" class="order-flow-bar-call"></div>
                        <div id="orderFlowPutBar" class="order-flow-bar-put"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid">
            <div class="card">
                <h2 class="call">Top Call Gamma / Flow Walls</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Strike</th>
                                <th>OI</th>
                                <th>Vol</th>
                                <th>Gamma Exp</th>
                                <th>Delta Exp</th>
                                <th>Premium</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody id="callTable"></tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <h2 class="put">Top Put Gamma / Flow Walls</h2>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Strike</th>
                                <th>OI</th>
                                <th>Vol</th>
                                <th>Gamma Exp</th>
                                <th>Delta Exp</th>
                                <th>Premium</th>
                                <th>Score</th>
                            </tr>
                        </thead>
                        <tbody id="putTable"></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card">
            <h2 class="gamma">Live Net Gamma Exposure Curve</h2>
            <p class="muted">
                Net gamma exposure by strike. Positive = call-dominant OI convention,
                Negative = put-dominant OI convention.
            </p>
            <div style="height: 400px"><canvas id="netGexChart"></canvas></div>
        </div>

        <div class="card">
            <h2>How to Read It</h2>
            <p class="muted">
                <span class="pill">Positive net gamma</span> market often pins/chops
                near walls. <span class="pill">Negative net gamma</span> market can
                trend harder after wall breaks.
                <span class="pill">Gamma call wall</span> resistance zone.
                <span class="pill">Gamma put wall</span> support/magnet zone.
            </p>
        </div>
    </div>

    <script>
        window.AppConfig = {
            csrfToken: "{{ csrf_token() }}"
        };
    </script>
    <script src="/assets/client/js/hub/option_chain.js?v=1.0"></script>
</body>

</html>
