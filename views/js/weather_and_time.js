function dateFr() {
    // les noms de jours / mois
    let jours = new Array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi");
    let mois = new Array("janvier", "fevrier", "mars", "avril", "mai", "juin", "juillet", "aout", "septembre", "octobre", "novembre", "decembre");
    // on recupere la date
    let date = new Date();
    // on construit le message
    let message = jours[date.getDay()] + " ";   // nom du jour
    message += date.getDate() + " ";   // numero du jour
    message += mois[date.getMonth()] + " ";   // mois
    message += date.getFullYear();
    document.getElementById("Date").innerHTML =  message;
}

function heure() {
    let date = new Date();
    let heure = date.getHours();
    let minutes = date.getMinutes();
    let seconds = date.getSeconds()
    if(minutes < 10)
        minutes = "0" + minutes;
    if(seconds < 10)
        seconds = "0" + seconds;
  document.getElementById("Time").innerHTML = heure + ":" + minutes + ":"+ seconds;
}

setInterval(dateFr,  1000);
setInterval(heure,  1000);


jQuery(document).ready(function($) {
    var maLatitude;		/*Variable gobale contenant la latitude*/
    var maLongitude;	/*Variable gobale contenant la longitude*/

    if (navigator.geolocation)
        navigator.geolocation.getCurrentPosition(showPosition);
    else
        alert("Votre navigateur ne prend pas en compte la géolocalisation HTML5");
});

function showPosition(position){
    maLatitude= position.coords.latitude;
    maLongitude= position.coords.longitude;
    alert(maLatitude + ", " + maLongitude );
}

jQuery(document).ready(function($) {
    $.ajax({
        url : "http://api.wunderground.com/api/TA_KEY/geolookup/conditions/lang:FC/q/IA/\"+maLatitude+\",\"+maLongitude+\".json\"",
        dataType : "jsonp",
        success : function(parsed_json) {
            var location = parsed_json['location']['city'];
            var temp_f = parsed_json['current_observation']['temp_f'];
            alert("Current temperature in " + location + " is: " + temp_f);
        }
    });
});

