<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Website</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms"></script>
</head>
<body class="bg-gray-200">
  <div class="min-h-full">
  <nav class="bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="flex h-16 items-center justify-between">
        <div class="flex items-center">
          <div class="shrink-0">
            <a href="/">
            <img class="size-10" src="{{ asset('images/2.png') }}" alt="FocusTrivia">
            </a>
          </div>
          <div class="hidden md:block">
            <div class="ml-10 flex items-baseline space-x-4">
              <!-- Current: "bg-gray-900 text-white", Default: "text-gray-300 hover:bg-gray-700 hover:text-white" -->
              <x-nav-link href="/" :active="request()->is('/')">Home</x-nav-link>
              <x-nav-link href="/question" :active="request()->is('question')">Daily Question</x-nav-link>
              <x-nav-link href="/leaderboard" :active="request()->is('leaderboard')">Leaderboard</x-nav-link>
              @auth
              <x-nav-link href="/dashboard" :active="request()->is('dashboard')">Dashboard</x-nav-link>
              @endauth
            </div>
          </div>
        </div>
        <div class="ml-4 flex items-center md:ml-6">
              @guest
              <!-- <x-nav-link href="/register">Register</x-nav-link> -->
              <x-nav-link href="/register" :active="request()->is('register')">Register</x-nav-link>
              <x-nav-link href="/login" :active="request()->is('login')">Login</x-nav-link>
              @endguest
              @auth
              <form method="POST" action="/logout">
                @csrf
                  <x-form-button>Logout</x-form-button>
              </form>
              @endauth
              </div>
          </div>
        </div>
      </div>
    </div>
  </nav>
  <header class="bg-white shadow-sm">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $heading }}</h1>
    </div>
  </header>
  <main>
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      {{ $slot }}
    </div>
  </main>
</div>
</body>
</html>