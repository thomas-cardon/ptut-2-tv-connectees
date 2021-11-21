docReady(() => {
  console.log('Scroll => Chargé');

  Array.from(document.querySelectorAll('.schedule-table')).forEach(el => {
    console.log('Scroll => actif');
    el.classList.add('auto-scroll');
  });

});
