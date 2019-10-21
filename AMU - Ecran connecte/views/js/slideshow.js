let slideIndex = 0;
showSlides();

/**
 * Fait défiler un diaporama
 */
function showSlides() {
    let i;
    let slides = document.getElementsByClassName("mySlides");
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    slideIndex++;
    if (slideIndex > slides.length - 1) {slideIndex = 1}
    slides[slideIndex-1].style.display = "block";
    setTimeout(showSlides, 10000);
}

setTimeout(function(){
    window.location.reload(1);
}, 300000);

