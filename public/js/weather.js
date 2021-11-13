/**
 * refreshWeather
 * Displays the weather, updated using the last ECMAScript specs (fetch > XMLHttpRequest, especially in 2021)
 * Now includes hourly forecast
 * @author Thomas Cardon
 */
function refreshWeather(lon = 5.4510, lat = 43.5156) {
    let myHeaders = new Headers();
    myHeaders.append("Accept", "application/json");

    fetch(`https://api.openweathermap.org/data/2.5/onecall?lat=${lat}&lon=${lon}&lang=fr&APPID=ae546c64c1c36e47123b3d512efa723e&exclude=minutely,daily`,
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
    );
    
    json.hourly.slice(0, 5).forEach((hour, i) => {
      let h = new Date(hour.dt * 1000).toLocaleTimeString().slice(0, 5); // Conversion unix DT vers JS DT
      
      document.querySelector(`#forecast-${i} strong`).innerText = h;
      document.querySelector(`#forecast-${i} h6`).innerText = Math.round(kelvinToC(hour.temp)) + '°C';
      
      document.querySelector(`#forecast-${i} img`)
      .setAttribute(
        'src',
        `${URL}/conditions/${hour.weather[0].icon}.png`
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

const kelvinToC = k => k - 273.15;
const msToKmh = speed => speed * 3.6;

docReady(() => {
  if (document.getElementById('temperature') == null) return;
  refreshWeather();
})
