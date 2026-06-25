function openNewFlowWindow(tickerSymbol) {
    const cleanTicker = tickerSymbol.trim().toUpperCase();
    if (!cleanTicker) return;
    const targetUrl = optionChainBaseUrl + "?symbol=" + cleanTicker;
    const windowFeatures =
        "width=1300,height=850,top=100,left=100,resizable=yes,scrollbars=yes,toolbar=no,menubar=no,location=no,status=no";
    window.open(targetUrl, "FlowData_" + cleanTicker, windowFeatures);

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
            const dataFlowDropdown =
                document.getElementById("dataFlowDropdown");
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
                    openNewFlowWindow(val);
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
            openNewFlowWindow(ticker);
            if (searchInput) searchInput.value = "";
            flowItems.forEach((i) => (i.style.display = "block"));
        });
    });

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
    // 3. LOGIC CỬA SỔ POPUP & NHÚNG IFRAME (CÁCH TỐT NHẤT)
    // =====================================================================
    const modal = document.getElementById("flowModal");
    const popupContent = document.getElementById("popupContent");
    const modalFlowTitle = document.getElementById("modalFlowTitle");
    const closeModalBtn = document.getElementById("closeModalBtn");
    const iframeLoader = document.getElementById("iframeLoader");

    // Hàm mở Popup Iframe
    function openFlowPopup(ticker, expiry) {
        const cleanTicker = ticker.toUpperCase();
        if (modalFlowTitle)
            modalFlowTitle.innerHTML = `DATA FLOW: <span>${cleanTicker}</span>`;
        if (iframeLoader) iframeLoader.style.display = "flex";
        if (popupContent) {
            popupContent.style.opacity = "0";
            popupContent.innerHTML = "";
        }
        if (modal) modal.classList.add("open");

        let targetUrl = optionChainBaseUrl + "?symbol=" + cleanTicker;
        if (expiry) targetUrl += "&expiry=" + expiry;

        // THÊM CÁC THUỘC TÍNH NÀY ĐỂ TỐI ƯU TỐC ĐỘ:
        const iframeHTML = `
        <iframe src="${targetUrl}" 
                style="width: 100%; height: 100%; border: none; border-radius: 8px; background:#030712;" 
                loading="eager" 
                sandbox="allow-same-origin allow-scripts"
                onload="document.getElementById('iframeLoader').style.display='none'; this.parentElement.style.opacity='1';">
        </iframe>
    `;

        if (popupContent) {
            setTimeout(() => {
                popupContent.innerHTML = iframeHTML;
            }, 50);
        }
    }
    // Hàm đóng Popup an toàn
    function closeFlowWindow() {
        modal.classList.remove("open");
        const iframe = popupContent.querySelector("iframe");
        if (iframe) {
            iframe.src = "about:blank";
        }

        setTimeout(() => {
            popupContent.innerHTML = "";
            popupContent.style.opacity = "0";
        }, 300);
    }

    if (closeModalBtn) closeModalBtn.addEventListener("click", closeFlowWindow);
    if (modal) {
        modal.addEventListener("click", function (e) {
            if (e.target === modal) closeFlowWindow();
        });
    }

    // Lắng nghe tín hiệu từ Scanner ở màn hình chính báo mở Popup
    window.addEventListener("message", function (event) {
        if (event.data && event.data.action === "openFlowPopup") {
            openFlowPopup(event.data.ticker, event.data.expiry);
        }
    });
});
