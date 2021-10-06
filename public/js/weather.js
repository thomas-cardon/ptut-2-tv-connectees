var meteoRequest = new XMLHttpRequest();
var longitude = 5.4510;
var latitude = 43.5156;
var url = "https://api.openweathermap.org/data/2.5/weather?lat=" + latitude + "&lon=" + longitude + "&lang=fr&APPID=ae546c64c1c36e47123b3d512efa723e";

/**
 * refreshWeather - Displays the weather, updated using the last ECMAScript specs (fetch > XMLHttpRequest, especially in 2021)
 */
function refreshWeather(lon = 5.4510, lat = 43.5156) {
    let myHeaders = new Headers();
    myHeaders.append("Accept", "application/json");

    fetch(`https://api.openweathermap.org/data/2.5/weather?lat=${lat}&lon=${lon}&lang=fr&APPID=ae546c64c1c36e47123b3d512efa723e`,
    { method: 'GET', headers: myHeaders })
    .then(res => res.json())
    .then(render)
    .catch(console.error);
}

function render(json) {
    let temp = Math.round(getTemp(json));
    let vent = getWind(json).toFixed(0);

    if (document.getElementById('Weather') !== null) {
      document.querySelector('.Infos')
      .style
      .backgroundImage = `url("${location.pathname}wp-content/plugins/plugin-ecran-connecte/public/img/Card ${getCondition(json)} ${new Date().getHours() >= 18 ? 'Night' : 'Day'}@3x.png"), url("${location.pathname}wp-content/plugins/plugin-ecran-connecte/public/img/Card Clear ${new Date().getHours() >= 18 ? 'Night' : 'Day'}@3x.png")`;

        let div = document.getElementById('Weather');
        div.innerHTML = "";
        let weather = document.createElement('div');
        weather.innerHTML = temp + "<span class=\"degree\">&nbsp;°C</span>";
        weather.id = "weather";

        let wind = document.createElement('div');
        wind.innerHTML = vent + "<span class=\"kmh\">&nbsp;km/h</span>";
        wind.id = "wind";
        div.appendChild(weather);
        div.appendChild(wind);

        setTimeout(refreshWeather, 900000);
    }
};

/** Getter **/
function getAlt(json) {
    return json["weather"][0]["description"];
}


/**
 * getCondition - returns weather state
 */
const getCondition = json => json['weather'][0]['main'];

/* TODO: remplacer toute les fonctions de ce type par des fonctions
fléchées comme getCondition, fonctionnalité ES6/7 faite pour ce genre de cas */
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

docReady(() => {
  if (document.getElementById('Weather') !== null)
  refreshWeather();
})
