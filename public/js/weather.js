/**
 * refreshWeather
 * Displays the weather, updated using the last ECMAScript specs (fetch > XMLHttpRequest, especially in 2021)
 * Now includes hourly forecast
 * @author Thomas Cardon
 */
function refreshWeather() {
    let myHeaders = new Headers();
    myHeaders.append("Accept", "application/json");

    fetch(`https://api.openweathermap.org/data/2.5/onecall?lat=${weather.lat}&lon=${weather.lon}&lang=fr&APPID=${weather.api_key}&exclude=minutely`,
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
    document.getElementById('wind').innerText = Math.round(getWind(json)) + ' KM/H';
    document.getElementById('humidity').innerText = getHumidity(json) + '% humidité';
    document.getElementById('sunset').innerText = getSunset(json);

    document.getElementById('condition-icon')
    .setAttribute(
      'src',
      `${weather.ASSETS_URL}/conditions/${getIcon(json)}.svg`
    );
    
    /* Prévisions par jour */
    json.daily.slice(1, 4).forEach((day, i) => {
      let d = new Date(day.dt * 1000); // Conversion unix DT vers JS DT
      
      document.querySelector(`#forecast-d${i} strong`).innerText = d.toLocaleDateString('fr-FR', { weekday: 'long' });
      document.querySelector(`#forecast-d${i} h6`).innerText = Math.round(kelvinToC(day.temp.day)) + '°C';
      
      document.querySelector(`#forecast-d${i} img`)
      .setAttribute(
        'src',
        `${weather.ASSETS_URL}/conditions/${day.weather[0].icon}.svg`
      );
    });
    
    /* Prévisions par heure */
    json.hourly.slice(0, 5).forEach((hour, i) => {
      let h = getHourFromTimestamp(hour.dt * 1000); // Conversion unix DT vers JS DT
      
      document.querySelector(`#forecast-h${i} strong`).innerText = h;
      document.querySelector(`#forecast-h${i} h6`).innerText = Math.round(kelvinToC(hour.temp)) + '°C';
      
      document.querySelector(`#forecast-h${i} img`)
      .setAttribute(
        'src',
        `${weather.ASSETS_URL}/conditions/${hour.weather[0].icon}.svg`
      );
    });

    setTimeout(refreshWeather, 900000);
};

/** Getter **/
function getAlt(json) {
    return json["weather"][0]["description"];
}


/**
 * Constant arrow functions - work for current data, not hourly forecast
 */
const getCountry = () => 'France'; // L'API ne contient plus le pays
const getCity = () => 'Aix-en-Provence'; // L'API ne contient plus la ville

const getCondition = json => json.current.weather[0].main;
const getIcon = json => json.current.weather[0].icon;

const getTemp = json => kelvinToC(json.current.temp);
const getWind = json => msToKmh(json.current.wind_speed);
const getHumidity = json => json.current.humidity;

const getHourFromTimestamp = dt => new Date(dt * 1000).toLocaleTimeString().slice(0, 5);
const getSunset = json => getHourFromTimestamp(json.current.sunset);

const kelvinToC = k => k - 273.15;
const msToKmh = speed => speed * 3.6;

docReady(() => {
  if (document.getElementById('temperature') == null) return;
  refreshWeather();
})
