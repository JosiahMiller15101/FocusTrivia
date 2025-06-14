<x-layout>
  <x-slot:heading>
    Home
  </x-slot:heading>

  <div class="flex items-center justify-center text-lg text-center px-16 py-4 bg-white rounded shadow-lg ring-2 ring-black w-full max-w-6xl min-h-[60px] mx-auto">
    <p class="leading-relaxed tracking-wide w-full">
      <strong>
        Welcome to FOCUS Trivia! Your twice-daily challenge begins here! Each day, sharpen your mind with two brand-new trivia questions. Questions reset at 12AM and 12PM so keep your eye on the clock. See how you and your department stack up against others across FOCUS departments. Are you ready to rise to the top?
      </strong>
    </p>
  </div>
  <div class="flex items-center justify-center mt-10">
    <div class="bg-gray-200 p-8 rounded-lg shadow-lg">
      <img class="w-90 h-90" src="{{ asset('images/transparentlogo.png') }}" alt="FocusTrivia">
    </div>
  </div>
</x-layout>