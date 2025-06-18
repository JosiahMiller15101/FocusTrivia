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
      <div id="time-text" class="time-text">--:--:--</div>
      <div class="time-sub-text">until next question</div>
      <div class="moon">⏱</div>
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
      width: 1000px;
      height: 200px;
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
  </style>
</x-layout>
