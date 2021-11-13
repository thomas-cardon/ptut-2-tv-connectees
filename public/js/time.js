let lastDate = new Date();

/**
 * Met-à-jour la date de l'interface TV
 * grâce à l'API JavaScript pour un gain de performances
 * @author Thomas Cardon
 */
function updateDate() {
  if (document.getElementById('date') !== null)
      document.getElementById('date').innerHTML = lastDate.toLocaleDateString('fr-FR',  { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' });
}

/**
 * Met-à-jour l'heure de l'interface TV
 * grâce à l'API JavaScript pour un gain de performances
 * @author Thomas Cardon
 */
function updateTime() {
  lastDate = new Date();
  
  if (document.getElementById('time') !== null)
      document.getElementById('time').innerHTML = lastDate.toLocaleTimeString();
}

setInterval(updateTime, 100); // Appel de la fonction updateTime toute les 100ms
setInterval(updateDate, 60 * 1000); // Appel de la fonction updateDate toute les 60*1000 ms = 60s

updateDate();