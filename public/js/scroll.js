function scrollStep(schedules, i, margin = 145) {
  if (schedules[i] == null) i = 0;
  console.log(i);
  
  let dims = schedules[i].getBoundingClientRect();
  window.scrollTo({
    top: dims.top - (schedules.length == i + 1 ? 0 : margin),
    left: window.scrollX,
    behavior: 'smooth'
  });

  setTimeout(() => scrollStep(schedules, i + 1), 10000);
}

function startScrollAnimation() {
  let schedules = Array.from(document.querySelectorAll('.table-responsive'));
  
  if (schedules.length > 0)
    scrollStep(schedules, 0);
}

docReady(startScrollAnimation);