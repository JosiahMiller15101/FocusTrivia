@php
  use Carbon\Carbon;

  $nowLocal = Carbon::now('America/Denver');

  $nextQuestionTime = $nowLocal->hour < 12
      ? Carbon::today($nowLocal->timezone)->addHours(12)
      : Carbon::tomorrow($nowLocal->timezone)->addHours(12);
@endphp

<x-layout>
  <x-slot:heading>
    Home
  </x-slot:heading>

  {{-- Container with welcome and countdown --}}
  <div class="bg-white rounded shadow-lg ring-2 ring-gray-400 w-full max-w-6xl mx-auto p-8 flex flex-col items-center text-center space-y-8">

  <!-- Welcome Message -->
  <p class="text-lg font-semibold leading-relaxed max-w-4xl">
    Welcome to FOCUS Trivia! Your twice-daily challenge begins here!
    Each day, sharpen your mind with two brand-new trivia questions.
    Questions reset at <strong>12AM</strong> and <strong>12PM</strong> —
    keep your eye on the clock to see how you and your department stack up!
  </p>

  <!-- Countdown Card -->
<div class="card">
  <div id="time-text" class="time-text">Loading...</div>
  <div class="time-sub-text">until next question</div>
  <div class="day-text">{{ $nowLocal->format('l, F j') }}</div>
  <div class="moon">⏱</div>
</div>

<!-- Button below countdown -->
 <a href="/question" class="mt-4">
<div class="mt-6">
  <button class="button-with-icon bg-slate-600">
    <span class="icon">▷</span>
    <span class="text">Start</span>
  </button>
</div>
</a>
  <!-- Stats: Daily Questions, Active Players, Departments -->
  <div class="flex justify-between w-full max-w-4xl mt-8 px-16">
    <div class="text-center">
      <div class="text-3xl font-bold text-slate-600">2</div>
      <div class="text-sm">Daily Questions</div>
    </div>
    <div class="text-center">
      <div class="text-3xl font-bold text-slate-600">16</div>
      <div class="text-sm">Active Players</div>
    </div>
    <div class="text-center">
      <div class="text-3xl font-bold text-slate-600">10</div>
      <div class="text-sm">Departments</div>
    </div>
  </div>

</div>


  {{-- Pass next question time to JS --}}
  <script>
    const nextQuestionTime = new Date("{{ $nextQuestionTime->toIso8601String() }}");

    const timeTextEl = document.getElementById('time-text');

    function updateCountdown() {
      const now = new Date();
      let diffSeconds = Math.floor((nextQuestionTime - now) / 1000);

      if (diffSeconds <= 0) {
        timeTextEl.textContent = 'Now!';
        clearInterval(intervalId);
        return;
      }

      const hours = Math.floor(diffSeconds / 3600);
      diffSeconds %= 3600;
      const minutes = Math.floor(diffSeconds / 60);
      const seconds = diffSeconds % 60;

      timeTextEl.textContent = `${hours.toString().padStart(2,'0')}h ${minutes.toString().padStart(2,'0')}m ${seconds.toString().padStart(2,'0')}s`;
    }

    updateCountdown();
    const intervalId = setInterval(updateCountdown, 1000);
  </script>

  {{-- Countdown Card Styling --}}
  <style>
    .card {
      width: 450px;
      height: 180px;
      padding-left: 15px;
      border-radius: 15px;
      display: flex;
      flex-direction: column;
      justify-content: center;
      position: relative;
      overflow: hidden;
      cursor: pointer;
      color: white;
      background: linear-gradient(to right, rgb(20, 30, 48), rgb(36, 59, 85));
      box-shadow: rgba(0, 0, 0, 0.3) 3px 6px 20px, rgba(0, 0, 0, 0.3) -3px 0px 80px;
      transition: all 0.3s ease-in-out;
    }

    .card:hover {
      box-shadow: rgba(0, 0, 0, 0.6) 5px 10px 40px, rgba(0, 0, 0, 0.6) -5px 0px 100px;
    }

    .time-text {
      font-size: 50px;
      font-weight: 600;
      font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
    }

    .time-sub-text {
      font-size: 20px;
      margin-top: 4px;
    }

    .day-text {
      font-size: 18px;
      margin-top: 6px;
      font-weight: 500;
      font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
    }

    .moon {
      font-size: 20px;
      position: absolute;
      top: 15px;
      right: 15px;
      transition: all 0.3s ease-in-out;
    }

    .card:hover > .moon {
      font-size: 23px;
    }



    .button-with-icon {
  overflow: hidden;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  border: 1px solid #3b2400;
  letter-spacing: 1px;
  padding: 0 12px;
  text-align: center;
  width: 140px;
  height: 45px;
  font-size: 14px;
  text-transform: uppercase;
  font-weight: 600;
  border-radius: 5px;
  outline: none;
  user-select: none;
  cursor: pointer;
  transform: translateY(0px);
  position: relative;
  box-shadow:
    inset 0 30px 30px -15px rgba(255, 255, 255, 0.1),
    inset 0 0 0 1px rgba(255, 255, 255, 0.3),
    inset 0 1px 20px rgba(0, 0, 0, 0),
    0 3px 0 #3b2400,
    0 3px 2px rgba(0, 0, 0, 0.2),
    0 5px 10px rgba(0, 0, 0, 0.1),
    0 10px 20px rgba(0, 0, 0, 0.1);
  color: white;
  text-shadow: 0 1px 0 rgba(0, 0, 0, 0.3);
  transition: 150ms all ease-in-out;
}

.button-with-icon .icon {
  margin-right: 8px;
  width: 24px;
  height: 24px;
  transition: all 0.5s ease-in-out;
}

.button-with-icon:active {
  transform: translateY(3px);
  box-shadow:
    inset 0 16px 2px -15px rgba(0, 0, 0, 0),
    inset 0 0 0 1px rgba(255, 255, 255, 0.15),
    inset 0 1px 20px rgba(0, 0, 0, 0.1),
    0 0 0 #0f988e,
    0 0 0 2px rgba(255, 255, 255, 0.5),
    0 0 0 rgba(0, 0, 0, 0),
    0 0 0 rgba(0, 0, 0, 0);
}

.button-with-icon:hover .text {
  transform: translateX(80px);
}

.button-with-icon:hover .icon {
  transform: translate(23px);
}

.text {
  transition: all 0.5s ease-in-out;
}
  </style>
</x-layout>
