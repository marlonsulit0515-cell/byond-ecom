@extends('layouts.default')

@section('maincontent')
<div class="contact-section">
  <div class="contact-container">

    <!-- Form Column -->
      <div class="contact-form-wrapper">
        <form onsubmit="event.preventDefault();">
          <h2>CONTACT US</h2>

          <div class="form-group">
            <x-text-input id="first-name" name="FirstName" type="text" placeholder="FIRST NAME" class="form-input" />
          </div>

          <div class="form-group">
            <x-text-input id="email" name="Email" type="email" placeholder="EMAIL" class="form-input" />
          </div>

          <div class="form-group">
            <x-text-input id="phone" name="PhoneNumber" type="tel" placeholder="PHONE NUMBER" class="form-input" />
          </div>

          <div class="form-group" style="margin-bottom: 20px;">
            <label for="query" class="form-label">
              WHAT DO YOU HAVE IN MIND?
            </label>
            <textarea id="query" class="form-textarea" placeholder="Please enter query..."></textarea>
          </div>

          <button type="submit" class="submit-btn btn-primary-color btn-md">
            Submit
          </button>
            <div class="separator-container">
                <div class="separator-line"></div>
                <span class="separator-text">or</span>
                <div class="separator-line"></div>
            </div>
          <!-- Contact Info -->
          <div class="contact-details" id="contact-us-socials" style="margin-top: 30px;">
            <div style="display: flex; gap: 20px; align-items: center; justify-content: center; flex-wrap: wrap; font-size: 14px; margin-bottom: 20px;">
              <span>Email: info@byondco.com</span>
              <span>Contact: +63 123 456 7890</span>
            </div>

            <!-- Social Media Icons -->
            <div class="footer-logo">
              <ul class="sm-icon" style="display: flex; gap: 15px; list-style: none; padding: 0; margin: 0; justify-content: center;">
                <li>
                  <a href="https://www.facebook.com/profile.php?id=61571159256828" target="_blank" rel="noopener noreferrer" aria-label="Facebook" alt="Facebook">
                    <i class="fab fa-facebook-f"></i>
                  </a>
                </li>
                <li>
                  <a href="https://www.instagram.com/byondco.official/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" alt="Instagram">
                    <i class="fab fa-instagram"></i>
                  </a>
                </li>
                <li>
                  <a href="https://www.tiktok.com/@byondcoph" target="_blank" rel="noopener noreferrer" aria-label="TikTok" alt="TikTok">
                    <i class="fab fa-tiktok"></i>
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </form>
      </div>

    <!-- Info & Map Column -->
    <div class="contact-info-wrapper">
      <div>
        <h2>Visit Us at</h2>
         <div class="map-placeholder">
            <iframe 
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6549.709844553582!2d121.10166929468029!3d14.543831805579027!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397c647e6dfd81f%3A0xacd3c2d8ab00278a!2sPasig%2C%201602%20Metro%20Manila!5e1!3m2!1sen!2sph!4v1762344478041!5m2!1sen!2sph"
              allowfullscreen=""
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              class="map-frame">
            </iframe>
          </div>
      </div>
    </div>
  </div>
</div>
@endsection