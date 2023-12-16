var meteoRequest = new XMLHttpRequest();
var longitude = 5.4510;
var latitude = 43.5156;
var url = "https://api.openweathermap.org/data/2.5/weather?lat=" + latitude + "&lon=" + longitude + "&lang=fr&APPID=ae546c64c1c36e47123b3d512efa723e";

/**
 * Display the weather
 */
function refreshWeather() {
    meteoRequest.open('GET', url, true);
    meteoRequest.setRequestHeader('Accept', 'application/json');
    meteoRequest.send();
}

meteoRequest.onload = function () {
    var json = JSON.parse(this.responseText);
    var temp = Math.round(getTemp(json));
    var vent = getWind(json).toFixed(0);
    if (document.getElementById('Weather') !== null) {
        var div = document.getElementById('Weather');
        div.innerHTML = "";
        var weather = document.createElement("DIV");
        weather.innerHTML = temp + "<span class=\"degree\">°C</span>";
        weather.id = "weather";
        var imgTemp = document.createElement("IMG");
        imgTemp.id = "icon";
        imgTemp.src = "/wp-content/plugins/plugin-ecran-connecte/public/img/" + getIcon(json) + ".png";
        imgTemp.alt = getAlt(json);
        weather.appendChild(imgTemp);
        var wind = document.createElement("DIV");
        wind.innerHTML = vent + "<span class=\"kmh\">km/h</span>";
        wind.id = "wind";
        var imgVent = document.createElement("IMG");
        imgVent.src = "/wp-content/plugins/plugin-ecran-connecte/public/img/wind.png";
        imgVent.alt = "Img du vent";
        wind.appendChild(imgVent);
        div.appendChild(weather);
        div.appendChild(wind);
        setTimeout(refreshWeather, 900000);
    }
};

/** Getter **/
function getAlt(json) {
    return json["weather"][0]["description"];
}

function getIcon(json) {
    return cutIcon(json["weather"][0]["icon"]);
}

function cutIcon(str) {
    return str.substr(0, str.length - 1);
}

function getTemp(json) {
    return kelvinToC(json["main"]["temp"]);
}

function kelvinToC(kelvin) {
    return kelvin - 273.15;
}

function getWind(json) {
    return msToKmh(json["wind"]["speed"]);
}

function msToKmh(speed) {
    return speed * 3.6;
}
/**
 * Get the date in french format
 */
function dateFr() {
    // les noms de jours / mois
    let jours = ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"];
    let mois = ["janvier", "fevrier", "mars", "avril", "mai", "juin", "juillet", "aout", "septembre", "octobre", "novembre", "decembre"];
    // on recupere la date
    let date = new Date();
    // on construit le message
    let message = jours[date.getDay()] + " ";   // nom du jour
    message += date.getDate() + " ";   // numero du jour
    message += mois[date.getMonth()] + " ";   // mois
    message += date.getFullYear();
    if (document.getElementById('Date') !== null)
        document.getElementById("Date").innerHTML = message;
}

/**
 * Get the time
 */
function heure() {
    let date = new Date();
    let heure = date.getHours();
    let minutes = date.getMinutes();
    let seconds = date.getSeconds();
    if (minutes < 10)
        minutes = "0" + minutes;
    if (seconds < 10)
        seconds = "0" + seconds;
    if (document.getElementById('Time') !== null)
        document.getElementById("Time").innerHTML = heure + ":" + minutes + ":" + seconds;
}

setInterval(dateFr, 1000);
setInterval(heure, 1000);

refreshWeather();