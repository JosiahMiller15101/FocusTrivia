<x-layout>
  <x-slot name="heading">
    @php
      $dashboardUser = isset($user) ? $user : Auth::user();
      $profileImage = $dashboardUser->profile_image ?? null;
      $isAbsolute = $profileImage && Str::startsWith($profileImage, ['http://', 'https://']);
    @endphp
    @if($profileImage)
      <img src="{{ $isAbsolute ? $profileImage : asset('storage/' . $profileImage) }}" alt="Profile" class="inline w-10 h-10 rounded-full object-cover align-middle mr-2">
    @endif
    {{ isset($user) ? $user->first_name : (isset($first_name) ? $first_name : 'Dashboard') }}'s Dashboard
  </x-slot>

  <div class="p-6 bg-white rounded shadow-lg mb-10 ring-2 ring-black flex flex-row items-start gap-8">
    <div class="flex-1">
      <h2 class="text-xl font-semibold mb-2">Trivia Stats</h2>
      <p class="mb-2">
      Member since: <strong>{{ isset($user) ? $user->created_at->format('F j, Y') : Auth::user()->created_at->format('F j, Y') }}</strong>
      </p>
      <p class="mb-2">Player Rank: <strong>#{{ isset($playerRank) ? $playerRank : 'N/A' }}</strong></p>
      <p class="mb-2">Department: <strong>{{ isset($user) ? $user->department : (Auth::user()->department ?? 'N/A') }}</strong></p>
      <p class="mb-2">Department Rank: <strong>#{{ isset($departmentRank) ? $departmentRank : 'N/A' }}</strong></p>
      <p class="mb-2">
        Total Questions Answered: <strong>{{ $totalAnswered }}</strong>
      </p>
      <p class="mb-2">
        Correct Answers: <strong>{{ $correctAnswers }}</strong>
      </p>
      <p class="mb-2">
        Correct Answer Percentage: 
        <strong>
          @if ($totalAnswered > 0)
            {{ number_format(($correctAnswers / $totalAnswered) * 100, 1) }}%
          @else
            N/A
          @endif
        </strong>
      </p>
      <p class="mb-2">Score: <strong>{{ isset($score) ? $score : 'N/A' }}</strong></p>
    </div>
    @if (!isset($user) || (isset($user) && $user->id === Auth::id()))
    <div class="flex flex-col items-center min-w-[220px]">
      @if(session('success'))
        <div class="mb-2 text-green-600 text-center">{{ session('success') }}</div>
      @endif
      @if($errors->has('profile_image'))
        <div class="mb-2 text-red-600 text-center">
          {{ $errors->first('profile_image') }}
        </div>
      @endif
      @if(Auth::user()->profile_image)
        @php
          $profileImage = Auth::user()->profile_image;
          $isAbsolute = Str::startsWith($profileImage, ['http://', 'https://']);
        @endphp
        <img src="{{ $isAbsolute ? $profileImage : asset('storage/' . $profileImage) }}" alt="Profile Image" class="w-40 h-40 rounded-full object-cover mb-4">
      @else
        <span class="text-gray-500 mb-4">No profile image uploaded.</span>
      @endif
      <form method="POST" action="{{ route('profile.uploadImage') }}" enctype="multipart/form-data" class="w-full">
        @csrf
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700">Profile Image</label>
          <input type="file" name="profile_image" accept="image/*" class="mt-1 block w-full border-gray-300 rounded-md shadow-lg text-xs py-1 px-2">
        </div>
        <button type="submit" class="shadow px-2 py-1 bg-slate-600 text-xs text-white rounded hover:bg-slate-500 w-full">Upload</button>
      </form>
    </div>
    @elseif(isset($user))
    <div class="flex flex-col items-center min-w-[220px]">
      @php
        $profileImage = $user->profile_image;
        $isAbsolute = $profileImage && Str::startsWith($profileImage, ['http://', 'https://']);
      @endphp
      <img src="{{ $isAbsolute ? $profileImage : asset('storage/' . $profileImage) }}" alt="Profile Image" class="w-40 h-40 rounded-full object-cover mb-4">
    </div>
    @endif
  </div>

  @if (!isset($user) || (isset($user) && $user->id === Auth::id()))
  <div class="p-6 bg-white rounded shadow-lg mt-10 ring-2 ring-black">
    <h2 class="text-xl font-semibold mb-2">Profile Information</h2>
    @if(session('success'))
        <div class="mb-4 text-green-600">
            {{ session('success') }}
        </div>
    @endif
    <form method="POST" action="{{ route('profile.update') }}" id="profileForm">
        @csrf
        @method('PUT')
        <div class="mb-4 flex items-center gap-x-2">
            <label class="block text-sm font-medium text-gray-700 min-w-[110px]">First Name</label>
            <input type="text" name="first_name" value="{{ old('first_name', Auth::user()->first_name) }}"
                   class="mt-1 block flex-1 rounded-md border-gray-300 shadow-lg focus:ring-indigo-500 focus:border-indigo-500" disabled id="first_name_input">
            <button type="button" class="shadow-lg ml-2 px-2 py-1 bg-slate-600 text-white rounded edit-btn hover:bg-slate-500" data-target="first_name_input">Edit</button>
        </div>

        <div class="mb-4 flex items-center gap-x-2">
            <label class="block text-sm font-medium text-gray-700 min-w-[110px]">Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name', Auth::user()->last_name) }}"
                   class="mt-1 block flex-1 rounded-md border-gray-300 shadow-lg focus:ring-indigo-500 focus:border-indigo-500" disabled id="last_name_input">
            <button type="button" class="shadow-lg ml-2 px-2 py-1 bg-slate-600 text-white rounded edit-btn hover:bg-slate-500" data-target="last_name_input">Edit</button>
        </div>

        <div class="mb-4 flex items-center gap-x-2">
            <label class="block text-sm font-medium text-gray-700 min-w-[110px]">Email</label>
            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                   class="mt-1 block flex-1 rounded-md border-gray-300 shadow-lg focus:ring-indigo-500 focus:border-indigo-500" disabled id="email_input">
            <button type="button" class="shadow-lg ml-2 px-2 py-1 bg-slate-600 text-white rounded edit-btn hover:bg-slate-500" data-target="email_input">Edit</button>
        </div>

        <div class="mb-6 flex items-center gap-x-2">
            <label class="block text-sm font-medium text-gray-700 min-w-[110px]">Department</label>
            <input type="text" name="department" value="{{ old('department', Auth::user()->department) }}"
                   class="mt-1 block flex-1 rounded-md border-gray-300 shadow-lg focus:ring-indigo-500 focus:border-indigo-500" disabled id="department_input">
            <button type="button" class="shadow-lg ml-2 px-2 py-1 bg-slate-600 text-white rounded edit-btn hover:bg-slate-500" data-target="department_input">Edit</button>
        </div>

        <button type="submit" class="shadow-lg px-4 py-2 bg-slate-600 text-white rounded hover:bg-slate-500">
            Save Changes
        </button>
    </form>
    <script>
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = document.getElementById(this.dataset.target);
            if (input) {
                input.removeAttribute('disabled');
                input.focus();
            }
        });
    });

    // Enable all inputs before submitting the form
    document.getElementById('profileForm').addEventListener('submit', function() {
        this.querySelectorAll('input').forEach(input => {
            input.removeAttribute('disabled');
        });
    });
</script>
  </div>
  @endif
</x-layout>
