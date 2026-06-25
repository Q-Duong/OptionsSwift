<div class="header-search-center">
    <div class="custom-search-dropdown" id="dataFlowDropdown">
        <button class="dropdown-toggle-btn" id="dropdownToggleBtn">
            <span id="dropdownBtnText" style="color: var(--primary-color); font-weight: bold;">🔍 Search Ticker
                Flow</span>
            <span class="arrow">▼</span>
        </button>

        <div class="dropdown-content-area">
            <input type="text" id="searchInput" class="dropdown-search-input"
                placeholder="Type Ticker & Press Enter...">

            <div class="dropdown-flow-list" id="flowList">

                <button class="flow-item" data-ticker="SPY">SPY</button>
                <button class="flow-item" data-ticker="SPX">SPX</button>
                <button class="flow-item" data-ticker="QQQ">QQQ</button>
                <button class="flow-item" data-ticker="AAPL">AAPL</button>
                <button class="flow-item" data-ticker="MSFT">MSFT</button>
                <button class="flow-item" data-ticker="NVDA">NVDA</button>
                <button class="flow-item" data-ticker="TSLA">TSLA</button>
                <button class="flow-item" data-ticker="AMZN">AMZN</button>
                <button class="flow-item" data-ticker="META">META</button>

                @foreach ($widgets as $widget)
                    @php $tickerKey = strtoupper(trim($widget->key)); @endphp

                    @if (strpos(strtolower($tickerKey), 'scanner') === false &&
                            strtolower($tickerKey) !== 'option_chain' &&
                            !in_array($tickerKey, ['SPY', 'SPX', 'QQQ', 'AAPL', 'MSFT', 'NVDA', 'TSLA', 'AMZN', 'META']))
                        <button class="flow-item" style="color: #4db8ff;" data-ticker="{{ $tickerKey }}">
                            {{ $tickerKey }} <span
                                style="font-size: 9px; color: var(--text-muted); float: right;">(Custom)</span>
                        </button>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>