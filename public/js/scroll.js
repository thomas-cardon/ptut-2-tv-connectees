docReady(() => {
  console.log('Scroll => Chargé');
  
  Array.from(document.querySelectorAll('.schedule')).forEach(schedule => {
    let inReverse = false;
    
    setInterval(() => {
      if (schedule.clientHeight <= document.documentElement.clientHeight) return;
      
      let val = schedule.style.top == '' ? -1 : parseInt(schedule.style.top.slice(0, -2));
      let max = schedule.clientHeight;
      
      if (Math.abs(val) == max || val == 0) inReverse = !inReverse;
      
      console.log(val, max, Math.abs(val) == max, inReverse, schedule.style.top);
      
      if (inReverse) schedule.style.top = ++val + 'px';
      else schedule.style.top = --val + 'px';
    }, 100);

  });

});