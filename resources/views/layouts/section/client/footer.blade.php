<footer class="site-footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-2 col-12 mb-4">
                <img src="{{ asset('assets/images/logo/dg-gonstead-logo.png') }}" class="logo img-fluid" alt="">
            </div>
            <div class="col-lg-4 col-md-6 col-12 mb-4">
                <h5 class="site-footer-title mb-3">@lang('master_pages.footer.quickLinks')</h5>
                <ul class="footer-menu">
                    <li class="footer-menu-item">
                        <a class="footer-menu-link" href="#top">@lang('master_pages.header.home')</a>
                    </li>

                    <li class="footer-menu-item">
                        <a class="footer-menu-link" href="#section_2">@lang('master_pages.header.aboutUs')</a>
                    </li>

                    <li class="footer-menu-item">
                        <a class="footer-menu-link" href="#section_3">@lang('master_pages.header.service')</a>
                    </li>

                    <li class="footer-menu-item">
                        <a class="footer-menu-link" href="#section_4">@lang('master_pages.header.blog')</a>
                    </li>

                    <li class="footer-menu-item">
                        <a class="footer-menu-link" href="#section_5">@lang('master_pages.header.contact')</a>
                    </li>
                </ul>
            </div>

            <div class="col-lg-6 col-md-6 col-12 mx-auto">
                <h5 class="site-footer-title mb-3">@lang('master_pages.header.contact')</h5>
                <p class="d-flex mb-2">
                    <a href="tel:1900633287" class="site-footer-link">
                        <i class="bi-telephone me-2"></i>
                        @lang('master_pages.footer.hotline') 1900 633 287
                    </a>
                </p>

                <p class="d-flex mb-2">
                    <a href="tel:0972767973" class="site-footer-link">
                        <i class="bi bi-phone me-2"></i>
                        @lang('master_pages.footer.phone') 0972 76 79 73
                    </a>
                </p>

                <p class="d-flex mb-2 site-footer-link">
                    <i class="bi bi-clock me-2"></i>
                    @lang('master_pages.footer.workingHours')
                </p>

                <p class="d-flex mb-4">
                    <a target="_blank" href="https://maps.app.goo.gl/KSAsd8MyUS1EsBnJ8" class="site-footer-link">
                        <i class="bi-geo-alt me-2"></i>@lang('master_pages.footer.address')
                        @lang('master_pages.footer.addHCM')
                    </a>
                </p>
                <div class="d-flex mb-1">
                    <h5>@lang('master_pages.footer.connectWithUs')</h5>
                </div>
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

    <div class="site-footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-7 col-12">
                    <p class="copyright-text mb-0">@lang('master_pages.footer.copy')</p>
                </div>
                <div class="col-lg-6 col-md-5 col-12 ">
                    <p class="copyright-text mb-0">
                        @lang('master_pages.footer.ctyName')
                    </p>
                    <p class="copyright-text mb-0">
                        @lang('master_pages.footer.businessLicense')
                    </p>
                </div>
            </div>
        </div>
    </div>
</footer>
