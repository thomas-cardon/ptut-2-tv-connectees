function checkRecaptcha() {
    var response = grecaptcha.getResponse();
    if (response.length == 0) {
        //reCaptcha not verified
        alert("no pass");
    } else {
        //reCaptch verified
        alert("pass");
    }
}