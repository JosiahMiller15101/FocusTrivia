<x-layout>
  <x-slot:heading>
    Home
  </x-slot:heading>

  <div class="flex items-center justify-center text-lg text-center px-16 py-4 bg-white rounded shadow-lg ring-2 ring-black w-full max-w-6xl min-h-[60px] mx-auto">
    <p class="leading-relaxed tracking-wide w-full">
      <strong>
        Welcome to FOCUS Trivia! Your daily challenge begins here! Each day, sharpen your mind with a brand-new trivia question and see how you and your department stack up against others across Focus departments. Are you ready to rise to the top?
      </strong>
    </p>
  </div>
<div class="flex items-center justify-center mt-10 bg-gray-200 shadow-lg">
    <img class="w-90 h-90" src="{{ asset('images/2.png') }}" alt="FocusTrivia">
</div>
</x-layout>









<!-- resources/views/about.blade.php
<!DOCTYPE html>
<html class="h-full bg-gray-100">
<head>
    <title>Home Page</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="h-full">
  <div class="min-h-full">
    <nav class="bg-gray-800">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
          <div class="flex items-center">
            <div class="shrink-0">
              <a href="/">
                <img class="size-10" src="{{ asset('images/2.png') }}" alt="My Image">
              </a>
            </div>
            <div class="hidden md:block">
              <div class="ml-10 flex items-baseline space-x-4">
                <a href="/" class="rounded-md bg-gray-900 px-3 py-2 text-sm font-medium text-white" aria-current="page">Home</a>
                <a href="/dashboard" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Dashboard</a>
                <a href="/leaderboard" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Leaderboard</a>
                <a href="/recommendations" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-700 hover:text-white">Recommendations</a>
              </div>
            </div>
            <div class="ml-4 flex items-center md:ml-6">
          </div>
          </div>
          <div class="-mr-2 flex md:hidden">
          </div>
            @guest
            <a href="/register" :active="request()->is('register')" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">Register</a>
            <a href="/login" :active="request()->is('login')" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-white hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">Login</a>
            @endguest
        </div>
      </div>
    </nav>

    ✅ Now outside nav -->
    <!-- <header class="bg-white shadow-sm">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">Home</h1>
      </div>
    </header>

    <main>
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        Your content -->
      <!-- </div>
    </main>
  </div>
</body>

</html> -->