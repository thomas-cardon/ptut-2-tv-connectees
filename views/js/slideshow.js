let slideIndex = 0;
showSlides();

/**
 * Fait défiler un diaporama
 */
function showSlides() {
    if(document.getElementById("slideshow-container") !== null) {
        let slides = document.getElementsByClassName("mySlides");
        for (let i = 0; i <= slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex === slides.length - 1) {
            slideIndex = 0
        }
        slides[slideIndex].style.display = "block";
        setTimeout(showSlides, 10000);
    }
}
