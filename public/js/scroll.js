function scrollStep(schedules, i) {
  if (schedules[i] == null) i = 0;
  
  schedules[i].scrollIntoView({ behavior: "smooth", block: "end" });
  setTimeout(() => scrollStep(schedules, i + 1), 10000);
}

function startScrollAnimation() {
  let schedules = Array.from(document.querySelectorAll('.table-responsive'));
  
  if (schedules.length > 0)
    scrollStep(schedules, 0);
}

docReady(startScrollAnimation);