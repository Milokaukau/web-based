let slideIndex = 1;
let slideInterval;
showSlides(slideIndex);

function currentSlide(n) {
    clearInterval(slideInterval); // Reset timer if user clicks
    showSlides(slideIndex = n);
    startAutoSlide(); // Restart timer
}

function showSlides(n) {
    let i;
    let slides = document.getElementsByClassName("slide");
    let dots = document.getElementsByClassName("dot");
    if (!slides.length) return;

    if (n > slides.length) { slideIndex = 1 }
    if (n < 1) { slideIndex = slides.length }

    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
        slides[i].classList.remove("active");
    }
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }

    slides[slideIndex - 1].style.display = "block";
    slides[slideIndex - 1].classList.add("active");
    dots[slideIndex - 1].className += " active";
}

function startAutoSlide() {
    slideInterval = setInterval(() => {
        slideIndex++;
        showSlides(slideIndex);
    }, 5000);
}

startAutoSlide();