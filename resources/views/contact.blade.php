@extends('layouts.default')

@section('maincontent')
<div class="contact-section">
    <div class="contact-container">

        <div class="contact-form-wrapper">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-semibold">CONTACT US</h2>

            <div class="direct-contact-list">
                <p class="content-text">For immediate assistance, or general inquiries, please use one of the contact methods below.</p>

                <ul class="info-list">
                    <li>
                      <i class="fas fa-envelope list-icon"></i>
                      <a class="contact" href="mailto:{{ 'byondcoph@gmail.com' }}">byondcoph@gmail.com</a>
                    </li>
                    <li>
                    <i class="fab fa-facebook-messenger list-icon"></i>
                      <span><a class="contact" href="https://www.facebook.com/profile.php?id=61571159256828" target="_blank" rel="noopener noreferrer">BYOND CO.</a></span>
                    </li>
                    <li>
                        <i class="fas fa-map-marker-alt list-icon"></i>
                        <span class="contact ">Pasig, Philippines, 1602</span>
                    </li>
                </ul>
            </div>

            <hr class="contact-separator">

            <div class="social-media-links">
                <h3>Follow us at</h3>
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
            </div>
        </div>

        <div class="contact-info-wrapper">
            <div class="faq-section">
                <h2>FAQ</h2>
                <div class="faq-list">
                    <details class="faq-details">
                        <summary class="faq-summary">
                           How Long The Delivery Takes?
                        </summary>
                        <p class="faq-content">LBC usually take 2-7 days to deliver depending on the location. Please order in advance if you need the items as soon as possible.</p>
                    </details>
                    
                    <details class="faq-details">
                        <summary class="faq-summary">
                            How can we return or exchange an item?
                        </summary>
                        <p class="faq-content">You can exchange or replace your item, though refund is not allowed. See more in our Return and Exchange Policy.</p>
                    </details>

                    <details class="faq-details">
                        <summary class="faq-summary">
                            How do I track my order?
                        </summary>
                        <p class="faq-content">Once your order ships, you will receive an email confirmation with a tracking number and a link that you can use for the LBC order tracking. Just paste it to thier website to track your order. Or you can check in your user dashboard about the status of your order. </p>
                    </details>

                    <details class="faq-details">
                        <summary class="faq-summary">
                            What payment methods do you accept?
                        </summary>
                        <p class="faq-content">PayPal, GCash and Maya through Xendit Gateway.</p>
                    </details>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection