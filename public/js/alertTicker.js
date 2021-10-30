/**
 * Yes
 */
docReady(function () {
  const alert = document.getElementById('alert');
  if (alert)
  new Marquee(alert, {
      continuous: true,
      direction: 'ltr',
      delayAfter: 1000,
      delayBefore: 0,
      speed: 0.5,
      loops: -1
  });
});
