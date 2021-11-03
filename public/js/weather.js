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
    console.log('Météo -> Données chargées');
    console.dir(json);

    /**
    * On transforme le tableau DOM primitif en réel tableau JS pour pouvoir utiliser les fonctions Array.filter(), Array.forEach
    * On filtre les classes qui commencent par le nom gradient, et on les retire avec #forEach
    */
    Array.from(document.getElementById('weather-card').classList)
    .filter(c => c.startsWith('gradient'))
    .forEach(g => document.getElementById('weather-card').classList.remove(g));

    document.getElementById('weather-card').classList.add('gradient-' + getIcon(json))

    document.getElementById('temperature').innerText = Math.round(getTemp(json)) + '°C';
    document.getElementById('city').innerText = getCity(json);
    document.getElementById('country').innerText = getCountry(json);

    document.getElementById('condition-icon')
    .setAttribute(
      'src',
      `${URL}/conditions/${getIcon(json)}.png`
      //`${URL}/Card ${getCondition(json)} ${new Date().getHours() >= 18 ? 'Night' : 'Day'}@3x.png"), url("${location.pathname}wp-content/plugins/plugin-ecran-connecte/public/img/Card Clear ${new Date().getHours() >= 18 ? 'Night' : 'Day'}@3x.png"`
    );

    setTimeout(refreshWeather, 900000);
};

/** Getter **/
function getAlt(json) {
    return json["weather"][0]["description"];
}


/**
 * getCondition - returns weather state
 */
const getCondition = json => json['weather'][0]['main'];
const getCountry = json => json.sys.country;
const getCity = json => json.name;
const getIcon = json => json.weather[0].icon;

/* TODO: remplacer toute les fonctions de ce type par des fonctions
         fléchées comme getCondition, fonctionnalité ES6/7 faite pour ce genre de cas
*/
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
  if (document.getElementById('temperature') !== null)
  refreshWeather();
})
