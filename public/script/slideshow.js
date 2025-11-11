let slideIndex = 0;

const slides = document.querySelectorAll('.mySlides');
const container = document.querySelector('.slideshow-container');

function showSlides() {
    container.style.transform = `translateX(-${slideIndex * 100}%)`;
    slideIndex++;
    if (slideIndex >= slides.length) slideIndex = 0;

    setTimeout(showSlides, 5000);
}

showSlides();
