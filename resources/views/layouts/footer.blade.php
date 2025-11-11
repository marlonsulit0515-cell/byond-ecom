<footer class="footerMain">
    <div class="footer-content">
        <div class="signupForm">
            <h1 class="main-heading">Ready to go BYOND?</h1>
            <p class="description">
                Sign up now to buy the latest products, exclusive offers, and upcoming events. Join the BYOND community today and never miss out on the adventure!
            </p>
            @guest
                <a href="{{ route('register') }}" class="btn-signup">SIGN UP NOW</a>
            @endguest
        </div>

        <div class="footer-logo">
            <ul class="sm-icon">
                <li>
                    <a href="https://www.facebook.com/profile.php?id=61571159256828" target="_blank" rel="noopener noreferrer" aria-label="Facebook" alt="Facebook">
                        <i class="fab fa-facebook-f"></i> </a>
                </li>
                <li>
                    <a href="https://www.instagram.com/byondco.official/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" alt="Instagram">
                        <i class="fab fa-instagram"></i> </a>
                </li>
                <li>
                    <a href="https://www.tiktok.com/@byondcoph" target="_blank" rel="noopener noreferrer" aria-label="TikTok" alt="TikTok">
                        <i class="fab fa-tiktok"></i> </a>
                </li>
            </ul>
            <img src="{{ asset('img/logo/Byond-logo-black.png') }}" alt="Byond Co. Logo">
        </div>
    </div>

    <ul class="footer-links">
        <li><a href="#">FAQS</a></li>
        <li><a href="#">SHIPPING AND RETURNS</a></li>
        <li><a href="#">PRIVACY POLICY</a></li>
        <li><a href="#">TERMS OF USE</a></li>
    </ul>
    
    <div class="copyright">
        <span>Copyright © 2025 Byond Co.</span>
    </div>
</footer>