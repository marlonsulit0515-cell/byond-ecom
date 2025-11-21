@extends('layouts.default')

@section('maincontent')
<div class="contact-section">
    <div class="contact-container">

        <div class="contact-form-wrapper">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-semibold">CONTACT US</h2>

            <div class="direct-contact-list">
                <p>For immediate assistance, general inquiries, or partnerships, please use one of the contact methods below. We aim to respond to all inquiries within 24-48 hours.</p>

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
                            What is your return and exchange policy?
                        </summary>
                        <p class="faq-content">We accept returns and exchanges within 30 days of purchase for items in their original condition (unworn, unwashed, with tags). Please visit our 'Shipping & Returns' page for detailed instructions.</p>
                    </details>
                    
                    <details class="faq-details">
                        <summary class="faq-summary">
                            What shipping options are available and what are the costs?
                        </summary>
                        <p class="faq-content">We offer standard and express shipping nationwide. Standard shipping is free on all orders over ₱2,000. Costs vary based on your location and chosen speed; calculated at checkout.</p>
                    </details>

                    <details class="faq-details">
                        <summary class="faq-summary">
                            How do I track my order?
                        </summary>
                        <p class="faq-content">Once your order ships, you will receive an email confirmation with a tracking number and a link to the carrier's tracking portal.</p>
                    </details>

                    <details class="faq-details">
                        <summary class="faq-summary">
                            What payment methods do you accept?
                        </summary>
                        <p class="faq-content">We accept Visa, Mastercard, PayPal, and Gcash. Cash on Delivery (COD) is also available in select regions.</p>
                    </details>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection