<header class="site-header">
    <div class="container">
        <div class="row">

            <div class="col-lg-8 col-12 d-flex flex-wrap">
                <p class="d-flex me-4 mb-0">
                    <a target="_blank" href="https://maps.app.goo.gl/KSAsd8MyUS1EsBnJ8">
                        <i class="bi-geo-alt me-2"></i>
                        @lang('master_pages.footer.addHCM')
                    </a>
                </p>

                <p class="d-flex mb-0">
                    <a href="tel:1900633287">
                        <i class="bi bi-telephone me-2"></i>
                        1900 633 287
                    </a>
                </p>
            </div>

            <div class="col-lg-3 col-12 ms-auto d-lg-block d-none">
                <ul class="social-icon">
                    <li class="social-icon-item">
                        <a target="_blank" href="https://www.tiktok.com/@dunggonstead"
                            class="social-icon-link bi bi-tiktok"></a>
                    </li>

                    <li class="social-icon-item">
                        <a target="_blank" href="https://www.facebook.com/profile.php?id=61554579022426"
                            class="social-icon-link bi-facebook"></a>
                    </li>

                    <li class="social-icon-item">
                        <a target="_blank" href="https://www.instagram.com/dgchiropracticgonstead"
                            class="social-icon-link bi-instagram"></a>
                    </li>

                    <li class="social-icon-item">
                        <a target="_blank" href="https://www.youtube.com/@DGGonstead"
                            class="social-icon-link bi-youtube"></a>
                    </li>

                    <li class="social-icon-item">
                        <a href="mailto:dgchiro.gonstead@gmail.com" class="social-icon-link bi-envelope"></a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</header>

<nav class="navbar navbar-expand-lg bg-light shadow-lg">
    <div class="container">
        <a class="navbar-brand" href="index.html">
            <img src="{{ asset('assets/images/logo/dg-gonstead-logo.png') }}" class="logo img-fluid"
                alt="Kind Heart Charity">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link click-scroll" href="#top">@lang('master_pages.header.home')</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link click-scroll" href="#section_2">@lang('master_pages.header.aboutUs')</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link click-scroll" href="#section_3">@lang('master_pages.header.service')</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link click-scroll" href="#section_4">@lang('master_pages.header.blog')</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link click-scroll" href="#section_5">@lang('master_pages.header.contact')</a>
                </li>

                {{-- <li class="nav-item ms-3">
                    <div class="globalnav-languages-item">
                        <button class="button-languages" dropdown="false">
                            <div class="globalnav__languages">
                                <span class="globalnav__languages-icon">
                                    <img src="{{ App::getLocale() == 'en' ? asset('assets/images/icon/united-states.png') : asset('assets/images/icon/vietnam.png') }}"
                                        alt="{{ App::getLocale() == 'en' ? 'en_GB' : 'vn' }}"
                                        class="globalnav__language-flag">
                                </span>
                            </div>
                        </button>
                        <div class="language-dropdown-menu languages-desktop">
                            <div class="globalnav__languages-list">
                                <div class="language {{ App::getLocale() == 'vn' ? 'selection' : '' }}">
                                    <a href="{{ Route('locale', 'vn') }}" class="language__link"
                                        aria-label="@lang('master_pages.header.langVN')" data-analytics-title="@lang('master_pages.header.langVN')"
                                        previewlistener="true"></a>
                                    <span class="language__flag">
                                        <img src="{{ asset('assets/images/icon/vietnam.png') }}" alt="VietNamese">
                                    </span>
                                    <span class="language__title">@lang('master_pages.header.vietnamese')</span>
                                </div>
                                <div class="language {{ App::getLocale() == 'en' ? 'selection' : '' }}">
                                    <a href="{{ Route('locale', 'en') }}" class="language__link"
                                        aria-label="@lang('master_pages.header.langEN')" data-analytics-title="@lang('master_pages.header.langEN')"
                                        previewlistener="true"></a>
                                    <span class="language__flag">
                                        <img src="{{ asset('assets/images/icon/united-states.png') }}"
                                            alt="English (United Kingdom)">
                                    </span>
                                    <span class="language__title">@lang('master_pages.header.english')</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </li> --}}
            </ul>
        </div>
    </div>
</nav>
