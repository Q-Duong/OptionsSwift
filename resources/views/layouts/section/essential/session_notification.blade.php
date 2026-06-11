<div data-core-fade-transition-wrapper
    class="rc-overlay rc-overlay-popup rc-overlay-fixed-width r-fade-transition-enter-done"
    data-core-overlay-cover data-core-overlay-session>
    <div class="notification">
        <div class="notification-container">
            <div class="notification-header">
                <div class="notification-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <div class="notification-content">
                    <p class="notification-title">
                        Phiên của bạn đã hết hạn
                    </p>
                    <span class="notification-description">Vui lòng tải lại trang để tiếp tục sử dụng ứng dụng.</span>
                </div>
            </div>
            <div class="notification-button">
                <a href="{{ url()->current() }}" class="notification-reload">
                    <span>Tải lại trang</span>
                </a>
            </div>
        </div>
    </div>
</div>
