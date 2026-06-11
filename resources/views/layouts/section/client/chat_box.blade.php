<link rel="stylesheet" href="https://medicen.vn/assets/css/support/org-form.built.css?v=20250117" type="text/css"
    as="style">
<link rel="stylesheet" href="{{ versionResource('assets/css/support/chat-box.built.css') }}" type="text/css"
    as="style" />
<div class="live-chat-root">
    <div class="chat-box-root">
        <div class="chat-box-typography-root"> Chat ngay!</div>
        <img class="chat-box-icon"
            src="https://webchatstatic.caresoft.vn/static/media/online-logo.e4fdf5e88e0a09799e2ce1c78949bcf1.svg">
    </div>
</div>
<div class="maindiv">
    <div class="live-chat">
        <div class="head">
            <div class="profilebox">
                <div class="profile">
                    <h4 class="chat-title">Chat với nhân viên tư vấn</h4>
                    <div class="pdetail">
                        <div class="pdetail-img-block">
                            <img src="{{ asset('assets/images/logo.png') }}" alt="logo-chat">
                        </div>
                        <span>Em ở đây để hỗ trợ cho mình ạ</span>
                    </div>
                </div>
                <button class="btn-close-chat">
                    <i class="fa-solid fa-circle-xmark"></i>
                </button>
            </div>
        </div>
        <div class="chatbox">
            <div class="chatbox-information">
                <legend class="rs-form-label">
                    <h3 class="rs-form-label-header typography-body">Thông tin cơ bản
                    </h3>
                </legend>
                <div class="form-textbox">
                    <input type="text" id="name-chat" class="form-textbox-input" name="name_chat" autocapitalize="off"
                        autocomplete="off">
                    <span class="form-textbox-label">Nhập tên của bạn</span>
                </div>
                <div class="form-textbox">
                    <input type="text" id="phone-chat" class="form-textbox-input" name="phone_chat" autocapitalize="off"
                        autocomplete="off">
                    <span class="form-textbox-label">Nhập số điện thoại của bạn</span>
                </div>
                <div class="rs-overlay-change">
                    <button type="button"
                        class="form-button button-submit rs-lookup-submit submit-information" disabled>Bắt
                        đầu trò chuyện</button>
                </div>
            </div>
            <div class="eachmessage received animate hidden">
                <p>Xin chào quý khách!
                </p>
            </div>

            {{-- <div class="eachmessage sent animate">
                <p>Test</p>
            </div> --}}
            <div class="suggested-options-root hidden">
                <nav class="suggested-options-list-root" aria-label="secondary mailbox folder">
                    <div>
                        <li class="suggested-options-list-item-root">
                            <button class="suggested-options-list-item-button-root" tabindex="0" role="button">
                                <div class="MuiListItemText-root css-1tsvksn">
                                    <span class="MuiTypography-root MuiTypography-body1 MuiListItemText-primary">
                                        Chat Zalo
                                    </span>
                                </div>
                                <span class="MuiTouchRipple-root"></span>
                            </button>
                        </li>
                        <hr class="suggested-options-divider-root">
                    </div>
                    <div>
                        <li class="suggested-options-list-item-root">
                            <button class="suggested-options-list-item-button-root" tabindex="0" role="button">
                                <div class="MuiListItemText-root css-1tsvksn">
                                    <span class="MuiTypography-root MuiTypography-body1 MuiListItemText-primary">
                                        Gặp tư vấn viên
                                    </span>
                                </div>
                                <span class="MuiTouchRipple-root"></span>
                            </button>
                        </li>
                    </div>
                </nav>
            </div>
        </div>
        <div class="sendbox hidden">
            <textarea type="text" placeholder="Nhập nội dung…" maxlength="1000"></textarea>
            <div class="message-input-button"></div>
        </div>
    </div>
</div>
<script src="{{ versionResource('assets/js/support/chat-box.js') }}" defer></script>
<script src="https://medicen.vn/assets/js/support/essential.js"></script>
