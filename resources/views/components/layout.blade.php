<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FOCUS Trivia</title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                <div class="bg-gray-200 p-1 rounded-md">
                  <img class="size-10" src="{{ asset('images/transparentlogo.png') }}" alt="FocusTrivia">
                </div>
              </a>
            </div>
            <div class="hidden md:block">
              <div class="ml-10 flex items-baseline space-x-4">
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
              <x-nav-link href="/register" :active="request()->is('register')">Register</x-nav-link>
              <x-nav-link href="/login" :active="request()->is('login')">Login</x-nav-link>
            @endguest

            @auth
              @php
                  $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->where('read', false)->count();
                  $bellIcon = '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                              </svg>';
                @endphp

                <x-nav-link href="{{ route('notifications.index') }}" :icon="$bellIcon" :badgeCount="$unreadCount" />

                <!-- Logout Button -->
                <form method="POST" action="/logout" class="ml-2">
                  @csrf
                  <x-form-button>Logout</x-form-button>
                </form>
              @endauth
            </div>
          </div>
        </div>
      </div>
    </nav>

    <header class="bg-white shadow-lg">
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900">{{ $heading }}</h1>
      </div>
    </header>

    <main>
      <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        {{ $slot }}
      </div>
    </main>

    @yield('scripts')
  </div>

  @yield('scripts')
</body>
</html>
