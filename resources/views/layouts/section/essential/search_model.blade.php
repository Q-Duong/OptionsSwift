<div class="search-model">
    <div class="d-flex align-items-center justify-content-center">
        <div class="overlay"></div>
        <form action="{{ URL::to('/search') }}" method="POST" autocomplete="off" class="search-model-form">
            {{ csrf_field() }}
            <div class="input_container">
                <img src="{{ asset('frontend/img/icon/search-icon.svg') }}" class="input_img" atl="search">
                <input type="text" id="keywords" id="search-input" name="keywords_submit"
                    placeholder="Tìm kiếm sản phẩm" class="input">
                <div class="search-close-switch">+</div>
            </div>
            <div class="search_seggest">
                <p>Cụm từ tìm kiếm phổ biến</p>
            </div>

            <div id="search_ajax"></div>
        </form>
    </div>
</div>