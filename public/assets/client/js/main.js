// =====================================================================
// 1. HÀM MỞ WINDOW MỚI SIÊU TỐC
// =====================================================================
function openNewFlowWindow(ticker, expiry) {
    const cleanTicker = ticker.trim().toUpperCase();
    if (!cleanTicker) return;
    
    let targetUrl = optionChainBaseUrl + "?symbol=" + cleanTicker;
    if (expiry) {
        targetUrl += "&expiry=" + expiry;
    }

    // Mở cửa sổ độc lập, đặt tên theo mã để tránh mở trùng nhiều tab
    const windowFeatures = "width=1400,height=900,scrollbars=yes,resizable=yes";
    window.open(targetUrl, "FlowData_" + cleanTicker, windowFeatures);

    // UX: Tự động đổi tên nút và đóng dropdown (nếu đang mở từ Header/Dashboard)
    const dropdownBtnText = document.getElementById("dropdownBtnText");
    if (dropdownBtnText) dropdownBtnText.innerHTML = `🔍 ${cleanTicker} Flow`;
    
    const dataFlowDropdown = document.getElementById("dataFlowDropdown");
    if (dataFlowDropdown) dataFlowDropdown.classList.remove("open");
}

// =====================================================================
// 2. KHỞI TẠO DOM CONTENT LOADED
// =====================================================================
document.addEventListener("DOMContentLoaded", function () {
    
    // --- UI: LOGIC DROPDOWN PROFILE ---
    const profileDropdown = document.getElementById("profileDropdown");
    const userMenuBtn = document.getElementById("userMenuBtn");
    const userDropdownMenu = document.getElementById("userDropdownMenu");

    if (userMenuBtn) {
        userMenuBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            profileDropdown.classList.toggle("open");
            userDropdownMenu.classList.toggle("show");
            const dataFlowDropdown = document.getElementById("dataFlowDropdown");
            if (dataFlowDropdown) dataFlowDropdown.classList.remove("open");
        });
    }

    // --- UI: LOGIC SEARCH & DROPDOWN ---
    const dataFlowDropdown = document.getElementById("dataFlowDropdown");
    const dropdownToggleBtn = document.getElementById("dropdownToggleBtn");
    const searchInput = document.getElementById("searchInput");
    const flowItems = document.querySelectorAll(".flow-item");

    if (dropdownToggleBtn) {
        dropdownToggleBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            dataFlowDropdown.classList.toggle("open");
            if (dataFlowDropdown.classList.contains("open") && searchInput) {
                searchInput.focus();
            }
            if (profileDropdown) profileDropdown.classList.remove("open");
            if (userDropdownMenu) userDropdownMenu.classList.remove("show");
        });
    }

    if (searchInput) {
        searchInput.addEventListener("input", function () {
            const keyword = this.value.toLowerCase().trim();
            flowItems.forEach((item) => {
                const text = item.innerText.toLowerCase();
                if (text.includes(keyword)) {
                    item.style.display = "block";
                } else {
                    item.style.display = "none";
                }
            });
        });

        searchInput.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
                e.preventDefault();
                const val = this.value.trim();
                if (val) {
                    openNewFlowWindow(val); // Gọi hàm đã được nâng cấp
                    this.value = "";
                    flowItems.forEach((item) => (item.style.display = "block"));
                }
            }
        });
    }

    flowItems.forEach((item) => {
        item.addEventListener("click", function (e) {
            e.stopPropagation();
            const ticker = this.getAttribute("data-ticker");
            openNewFlowWindow(ticker); // Gọi hàm đã được nâng cấp
            if (searchInput) searchInput.value = "";
            flowItems.forEach((i) => (i.style.display = "block"));
        });
    });

    // Đóng các menu khi click ra ngoài
    document.addEventListener("click", function (e) {
        if (profileDropdown && !profileDropdown.contains(e.target)) {
            profileDropdown.classList.remove("open");
            if (userDropdownMenu) userDropdownMenu.classList.remove("show");
        }
        if (dataFlowDropdown && !dataFlowDropdown.contains(e.target)) {
            dataFlowDropdown.classList.remove("open");
        }
    });

    // =====================================================================
    // 3. LẮNG NGHE TÍN HIỆU TỪ SCANNER
    // =====================================================================
    window.addEventListener("message", function (event) {
        if (event.data && event.data.action === 'openFlowPopup') {
            const ticker = event.data.ticker;
            const expiry = event.data.expiry;
            openNewFlowWindow(ticker, expiry); 
        }
    });
});