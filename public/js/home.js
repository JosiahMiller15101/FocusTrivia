// JS for home page countdown and any other home-specific scripts


  const timeTextEl = document.getElementById('time-text');

  function getNext12HourMark() {
    const now = new Date();
    const next = new Date(now);

    if (now.getHours() < 12) {
      // It's before noon → target 12:00 PM today
      next.setHours(12, 0, 0, 0);
    } else {
      // It's after noon → target 12:00 AM tomorrow
      next.setDate(next.getDate() + 1);
      next.setHours(0, 0, 0, 0);
    }

    return next;
  }

  let nextTargetTime = getNext12HourMark();

  function updateCountdown() {
    const now = new Date();
    let diffSeconds = Math.floor((nextTargetTime - now) / 1000);

    if (diffSeconds <= 0) {
      // Time reached → restart countdown
      nextTargetTime = getNext12HourMark();
      diffSeconds = Math.floor((nextTargetTime - now) / 1000);
    }

    const hours = Math.floor(diffSeconds / 3600);
    diffSeconds %= 3600;
    const minutes = Math.floor(diffSeconds / 60);
    const seconds = diffSeconds % 60;

    timeTextEl.textContent = 
      `${hours.toString().padStart(2, '0')}h ` +
      `${minutes.toString().padStart(2, '0')}m ` +
      `${seconds.toString().padStart(2, '0')}s`;
  }

  updateCountdown();
  setInterval(updateCountdown, 1000);
