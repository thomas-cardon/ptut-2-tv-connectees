let countPDF = 0;
let countSlides = 0;
let slideIndex = 0;
displayOrHide();

/**
 * Fait défiler un diaporama
 */
function displayOrHide() {

    let slides = document.getElementsByClassName("myInfoSlides");
    if(slides.length <= 0) {
        slides = document.getElementsByClassName("mySlides");
    }

    if(slides.length > 0) {
        for (let i = 0; i < slides.length; ++i) {
            slides[i].style.display = "none";
        }

        if(slideIndex === slides.length) {
            slideIndex = 0;
        }

        console.log("Diapo numéro " + slideIndex);
        if(slides[slideIndex] !== undefined) {
            slides[slideIndex].style.display = "block";
            if(slides[slideIndex].childNodes[0].childNodes[0] !== undefined) {
                console.log("PDF lu");
                let pdf = slides[slideIndex].getElementsByClassName("pdfemb-inner-div");
                if(pdf.length > 0) {
                    for(let j = 0; j < pdf.length; ++j) {
                        pdf[j].style.display = "none";
                    }
                    console.log("Page numéro " + countPDF);
                    pdf[countPDF].style.display = "block";
                    ++countPDF;
                    if(countPDF === pdf.length) {
                        countPDF = 0;
                        ++slideIndex;
                    }
                }
            } else {
                console.log("Image lu");
                ++slideIndex;
            }
        }
    }

    setTimeout(displayOrHide, 8000);
}

// if(slides[slideIndex] !== undefined) {
//     if(slides[slideIndex].childNodes[0].childNodes[0] !== undefined) {
//         slides[slideIndex].style.display = "block";
//         let pdf = slides[slideIndex].getElementsByClassName("pdfemb-inner-div");
//         if(pdf.length > 0) {
//             if(countPDF > 0) {
//                 pdf[countPDF - 1].style.display = "none";
//             }
//             if(countPDF === pdf.length) {
//                 pdf[countPDF - 1].style.display = "none";
//                 countPDF = 0;
//                 ++slideIndex;
//             }
//             console.log("Affiche : " + countPDF);
//             pdf[countPDF].style.display = "block";
//             countPDF = countPDF + 1;
//         }
//     } else {
//         slides[slideIndex].style.display = "block";
//         ++slideIndex;
//     }
// }

// for(let i = 0; i < slides.length; ++i) {
//     console.log("Début du diaporama");
//     if(slides[i] !== undefined) {
//         slides[i].style.display = "block";
//         if(slides[i].childNodes[0].childNodes[0] !== undefined) {
//             let pdf = slides[i].getElementsByClassName("pdfemb-inner-div");
//             if(pdf.length > 0) {
//                 for(let j = 0; j < slides[i].length; ++j) {
//                     slides[i][j].style.display = "block";
//                     setTimeout(function (){ slides[i][j].style.display = "none"; }, 10000);
//                 }
//             }
//         }
//         setTimeout(function (){ slides[i].style.display = "none"; }, 10000);
//     }
//     console.log("Fin du diaporama");
// }