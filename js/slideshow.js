let slideIndex = 0;
let slidePDF = 0;

let urlUpload = "/wp-content/uploads/media/";

let pdfUrl = null;
let numPage = 0; // Numéro de page courante
let totalPage = null; // Nombre de pages
let myCanvas = null;

infoSlideShow();
scheduleSlideshow();

function infoSlideShow() {
    if(document.getElementsByClassName("myInfoSlides").length > 0) {
        displayOrHide(document.getElementsByClassName("myInfoSlides"));
    }
}

function scheduleSlideshow() {
    if(document.getElementsByClassName("mySlides").length > 0) {
        displayOrHide(document.getElementsByClassName("mySlides"));
    }
}

/**
 * Display a slideshow
 */
function displayOrHide(slides) {

    if(slides.length > 0) {
        for (let i = 0; i < slides.length; ++i) {
            slides[i].style.display = "none";
        }

        if(slideIndex === slides.length) {
            slideIndex = 0;
        }

        // Check if the slide exist
        if(slides[slideIndex] !== undefined) {
            // Display the slide
            slides[slideIndex].style.display = "block";
            // Check child
            if(slides[slideIndex].childNodes) {
                var count = 0;
                // Try to find if it's a PDF
                for(i = 0; i < slides[slideIndex].childNodes.length; ++i) {
                    // If is a PDF
                    if(slides[slideIndex].childNodes[i].className === 'canvas_pdf') {
                        count = count + 1;

                        // Generate the url
                        let pdfLink = slides[slideIndex].childNodes[i].id;
                        pdfUrl = urlUpload + pdfLink;

                        let loadingTask = pdfjsLib.getDocument(pdfUrl);
                        loadingTask.promise.then(function(pdf) {
                            totalPage = pdf.numPages;
                            ++numPage;
                            if(totalPage >= numPage) {
                                pdf.getPage(numPage).then(function(page) {
                                    var scale = 1.5;
                                    var viewport = page.getViewport({ scale: scale, });

                                    var canvas = document.getElementById('the-canvas-' + pdfLink);
                                    var context = canvas.getContext('2d');
                                    canvas.height = viewport.height;
                                    canvas.width = viewport.width;

                                    var renderContext = {
                                        canvasContext: context,
                                        viewport: viewport
                                    };

                                    // Give the CSS to the canvas
                                    if(slides === document.getElementsByClassName("mySlides")) {
                                        canvas.style.maxHeight = "99vh";
                                        canvas.style.maxWidth = "100%";
                                        canvas.style.height = "99vh";
                                        canvas.style.width = "auto";
                                    } else {
                                        canvas.style.maxHeight = "68vh";
                                        canvas.style.maxWidth = "100%";
                                        canvas.style.height = "auto";
                                        canvas.style.width = "auto";
                                    }

                                    page.render(renderContext);
                                    if (context) {
                                        context.clearRect(0, 0, canvas.width, canvas.height);
                                        context.beginPath();
                                    }
                                });
                            } else {
                                // Reinitialise variables
                                totalPage = null;
                                numPage = 0;
                                // Go to the next slide
                                ++slideIndex;
                            }
                            //renderPDF(pdf);
                        });
                    }
                }
                if(count === 0) {
                    // Go to the next slide
                    ++slideIndex;
                }
            } else {
                // Go to the next slide
                ++slideIndex;
            }
        }
    }
    setTimeout(function(){displayOrHide(slides)} , 10000);
}



// Display a pdf
function renderPDF(pdf) {

    // Take the number of pages
    if(totalPage == null) {
        totalPage = pdf.numPages;
    }

    console.log(totalPage);

    // Display each page
    if(numPage <= totalPage) {
        pdf.getPage(numPage).then(renderPage);
        ++numPage;
        setTimeout(function() { renderPDF(pdf); }, 8000);
    } else {
        // Reinitialise variables
        totalPage = null;
        numPage = 1;
        ++slidePDF;
    }
}

// Display a page in a canvas
function renderPage(page) {

    // Delete the previous Canvas
    if(myCanvas) {
        myCanvas.remove();
    }

    // Take the div link to the canvas
    var div = document.getElementsByClassName('canvas_pdf');
    if(div.length === slidePDF) {
        slidePDF = 0;
    }

    // Initiate the canvas
    var scale = 1;
    var viewport = page.getViewport(scale);

    // Build the canvas
    var canvas = document.createElement('canvas');
    canvas.id = 'pdf_renderer_' + numPage;

    console.log(canvas);

    console.log(div[slidePDF]);
    div[slidePDF].append(canvas);
    myCanvas = canvas;

    var context = canvas.getContext('2d');
    // Size of the canvas (useless but needed)
    canvas.height = viewport.height;
    canvas.width = viewport.width;

    // Build the context
    var renderContext = {
        canvasContext: context,
        viewport: viewport
    };

    // Real size of the canvas
    canvas.setAttribute("class", "container_fluid");


    // Display the canvas / Page of the PDF
    page.render(renderContext);
}