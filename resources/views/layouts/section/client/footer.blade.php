<footer class="os-footer">
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-brand">
                <a href="{{ route('home.index') }}">
                    <img src="{{ asset('assets/images/logo/options-swift-logo.png') }}" alt="Options Swift Logo"
                        class="footer-image">
                </a>
                <p>Professional Real-Time Institutional Money Flow Intelligence.</p>
                <p class="risk-warning">
                    <strong>Disclaimer:</strong> Trading involves substantial risk.
                    Not a registered investment advisor.
                </p>
            </div>

            <div class="footer-links">
                <h4>Platform</h4>
                <ul>
                    <li><a href="{{ route('about') }}"
                            class="{{ request()->routeIs('about') ? 'active' : '' }}">About</a></li>
                    <li><a href="{{ route('contact') }}"
                            class="{{ request()->routeIs('contact') ? 'active' : '' }}">Contact
                            Support</a></li>
                </ul>
            </div>

            <div class="footer-links">
                <h4>Legal</h4>
                <ul>
                    <li><a href="{{ route('terms') }}" class="{{ request()->routeIs('terms') ? 'active' : '' }}">Terms &
                            Conditions</a></li>
                    {{-- <li><a href="#">Privacy Policy</a></li> --}}
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} Options Swift LLC. All rights reserved.</p>
        </div>
    </div>
</footer>
