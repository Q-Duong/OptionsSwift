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

    window.open(targetUrl, "_blank");

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
        if (event.data && event.data.action === "openFlowPopup") {
            const ticker = event.data.ticker;
            const expiry = event.data.expiry;
            openNewFlowWindow(ticker, expiry);
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    function lockButtonOnSubmit(
        formId,
        btnId,
        loadingText,
        textElementId = null,
        spinnerElementId = null,
    ) {
        const form = document.getElementById(formId);

        // Nếu trang hiện tại không có form này (vd: đang ở trang chủ) -> Bỏ qua, không báo lỗi
        if (!form) return;

        form.addEventListener("submit", function () {
            const btn = document.getElementById(btnId);
            if (!btn) return;

            // 1. Khóa nút bằng class CSS
            btn.classList.add("is-loading");

            // 2. Đổi chữ (Hỗ trợ tìm theo ID hoặc class .btn-text)
            const textTarget = textElementId
                ? document.getElementById(textElementId)
                : btn.querySelector(".btn-text");
            if (textTarget) {
                textTarget.innerText = loadingText;
            }

            // 3. Hiện spinner (dành riêng cho các nút cấu hình spinner ẩn bằng style)
            if (spinnerElementId) {
                const spinner = document.getElementById(spinnerElementId);
                if (spinner) {
                    spinner.style.display = "block";
                }
            }
        });
    }

    // Nút "Resend Link" ở trang Profile
    lockButtonOnSubmit(
        "formResendEmail",
        "btnResendEmail",
        "Sending...",
        "textResend",
        "spinnerResend",
    );

    // Nút "Resend Authentication Link" ở trang chặn Verify
    lockButtonOnSubmit(
        "formResendVerify",
        "btnResendVerify",
        "Sending...",
        "textResendVerify",
    );

    const btnTrigger = document.getElementById("btnTriggerCancel");
    const modal = document.getElementById("retentionModal");
    const btnKeep = document.getElementById("btnKeepPlan");
    const btnConfirm = document.getElementById("btnConfirmCancel");
    const formCancel = document.getElementById("formCancelSubscription");

    if (btnTrigger && modal) {
        btnTrigger.addEventListener("click", function () {
            modal.classList.add("is-active");
        });

        if (btnKeep) {
            btnKeep.addEventListener("click", function () {
                modal.classList.remove("is-active");
            });
        }

        if (btnConfirm) {
            btnConfirm.addEventListener("click", function () {
                this.classList.add("is-loading");
                const textSpan = this.querySelector(".btn-text");
                const spinner = document.getElementById("spinnerCancel");

                if (textSpan) textSpan.innerText = "Canceling...";
                if (spinner) spinner.style.display = "block";
                formCancel.submit();
            });
        }
    } else {
        console.warn("⚠️ Thiếu HTML của Modal Cancel trên trang này!");
    }

    // --- KHỐI XỬ LÝ RESUME ---
    const formResume = document.getElementById("formResumeSubscription");
    const btnResume = document.getElementById("btnResumeSubscription");

    if (formResume && btnResume) {
        formResume.addEventListener("submit", function (event) {
            const confirmResume = confirm("Are you sure you want to resume your subscription? Your automatic billing cycle will be reactivated.");

            if (!confirmResume) {
                event.preventDefault();
                return;
            }
            
            btnResume.style.pointerEvents = "none";
            btnResume.style.opacity = "0.7";

            const textSpan = btnResume.querySelector(".btn-text");
            const spinner = document.getElementById("spinnerResume");

            if (textSpan) textSpan.innerText = "Resuming...";
            if (spinner) spinner.style.display = "block";
        });
    }
});
