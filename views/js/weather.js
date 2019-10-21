/**
 * Affiche l'heure
 */
function refreshTime(){
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("Time").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET", "/wp-content/plugins/TeleConnecteeAmu/views/js/utils/utilsWeather.php?action=getTime", true);
    xmlhttp.send();
}
refreshTime();
setInterval(function() {refreshTime()},  1000);

/**
 * Affiche la date
 */
function refreshDate(){
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("Date").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET", "/wp-content/plugins/TeleConnecteeAmu/views/js/utils/utilsWeather.php?action=getDate", true);
    xmlhttp.send();
}
refreshDate();
setInterval(function() {refreshDate()},  1000);
var meteoRequest = new XMLHttpRequest();
var longitude = 5.4510;
var latitude = 43.5156;
var url = "http://api.openweathermap.org/data/2.5/weather?lat=" + latitude + "&lon=" + longitude + "&lang=fr&APPID=ae546c64c1c36e47123b3d512efa723e";

/**
 * Affiche la météo
 */
function refreshWeather(){
    meteoRequest.open('GET', url, true);
    meteoRequest.setRequestHeader('Accept', 'application/json');
    meteoRequest.send();
}
meteoRequest.onload =  function () {
    var json = JSON.parse(this.responseText);
    var temp = Math.round(getTemp(json));
    var vent = getWind(json).toFixed(0);
    var div = document.getElementById('Weather');
    div.innerHTML = "";
    var weather = document.createElement("DIV");
    weather.innerHTML = temp + "<span style=\"font-size: 16px;\">°C</span>";
    weather.id = "weather";
    var imgTemp = document.createElement("IMG");
    imgTemp.id = "icon";
    imgTemp.src = "/wp-content/plugins/TeleConnecteeAmu/views/imagesWeather/" + getIcon(json) + ".png";
    imgTemp.alt = getAlt(json);
    weather.appendChild(imgTemp);
    var wind = document.createElement("DIV");
    wind.innerHTML = vent + "<span style=\"font-size: 16px;\">km/h</span>";
    wind.id = "wind";
    var imgVent = document.createElement("IMG");
    imgVent.src = "/wp-content/plugins/TeleConnecteeAmu/views/imagesWeather/wind.png";
    imgVent.alt = "Img du vent";
    wind.appendChild(imgVent);
    div.appendChild(weather);
    div.appendChild(wind);
    setTimeout(refreshWeather, 900000);
};
function getAlt(json){
    return json["weather"][0]["description"];
}
function getIcon(json){
    return cutIcon(json["weather"][0]["icon"]);
}
function cutIcon(str){
    return str.substr(0, str.length -1);
}
function getTemp(json){
    return kelvinToC(json["main"]["temp"]);
}
function kelvinToC(kelvin){
    return kelvin - 273.15;
}
function getWind(json) {
    return msToKmh(json["wind"]["speed"]);
}
function msToKmh(speed) {
    return speed * 3.6;
}
refreshWeather();