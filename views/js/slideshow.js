let slideIndex = 0;
showSlides();

/**
 * Fait défiler un diaporama
 */
function showSlides() {
    let i;
    let slides = document.getElementsByClassName("mySlides");
    console.log(slides[0]);
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    if (slideIndex === slides.length) {
        slideIndex = 0;
        console.log("Fin du diapo");
    }
    console.log("Affiche diapo numéro " + slideIndex);
    slides[slideIndex].style.display = "block";
    slideIndex++;
    setTimeout(showSlides, 10000);
}