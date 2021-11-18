docReady(() => {
  console.log('Scroll => Chargé');
  const el = document.getElementById('scheduleList');
  const parent = document.getElementById('content-main');
  
  console.log(el.clientHeight, parent.clientHeight);
  if (el.clientHeight <= parent.clientHeight) return;
  
  el.classList.add('auto-scroll');
});