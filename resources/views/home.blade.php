<x-layout>
  <x-slot:heading>
    Home
  </x-slot:heading>
  <link rel="stylesheet" href="/css/home.css">

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
    <svg class="icon-top-right h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
      <circle cx="12" cy="12" r="10"/>
      <path d="M12 6v6l4 2"/>
    </svg>
    <div id="time-text" class="time-text">Loading...</div>
    <div class="time-sub-text">until next question</div>
  </div>

  <!-- Button below countdown -->
  <a href="/question" class="mt-4">
    <div class="mt-6">
      <button class="button-with-icon bg-slate-600">
        <span class="icon">▷</span>
        <span class="text">Play</span>
      </button>
    </div>
  </a>
  <!-- Stats: Daily Questions, Active Players, Departments -->
  <div class="flex justify-between w-full max-w-4xl mt-8 px-16">
    <div class="text-center">
      <div class="text-3xl font-bold text-slate-600">{{ $totalUsers }}</div>
      <div class="text-sm">Active Players</div>
    </div>
    <div class="text-center">
      <div class="text-3xl font-bold text-slate-600">{{ $totalSubmissions }}</div>
      <div class="text-sm">Total Submissions</div>
    </div>
     <div class="text-center">
      <div class="text-3xl font-bold text-slate-600">{{ $overallAccuracy }}%</div>
      <div class="text-sm">Overall Accuracy</div>
    </div>
    <div class="text-center">
      <div class="text-3xl font-bold text-slate-600">{{ $uniqueDepartments }}</div>
      <div class="text-sm">Departments</div>
    </div>
  </div>
</div>

<script src="/js/home.js"></script>
</x-layout>
