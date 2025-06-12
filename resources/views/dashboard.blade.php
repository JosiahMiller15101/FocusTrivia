<x-layout>
  <x-slot name="heading">
    {{ $first_name }}'s Dashboard
  </x-slot>

  <div class="p-6 bg-white rounded shadow-lg mb-10 ring-2 ring-black">
    <h2 class="text-xl font-semibold mb-2">Trivia Stats</h2>
    
    <p class="mb-2">
    Member since: <strong>{{ Auth::user()->created_at->format('F j, Y') }}</strong>
    </p>

    <p class="mb-2">Player Rank: <strong>#{{ $playerRank }}</strong></p>

    <p class="mb-2">Department: <strong>{{ Auth::user()->department }}</strong></p>

    <p class="mb-2">Department Rank: <strong>#{{  $departmentRank }}</strong></p>

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
  </div>

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
            <button type="button" class="ml-2 px-2 py-1 bg-indigo-500 text-white rounded edit-btn" data-target="first_name_input">Edit</button>
        </div>

        <div class="mb-4 flex items-center gap-x-2">
            <label class="block text-sm font-medium text-gray-700 min-w-[110px]">Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name', Auth::user()->last_name) }}"
                   class="mt-1 block flex-1 rounded-md border-gray-300 shadow-lg focus:ring-indigo-500 focus:border-indigo-500" disabled id="last_name_input">
            <button type="button" class="ml-2 px-2 py-1 bg-indigo-500 text-white rounded edit-btn" data-target="last_name_input">Edit</button>
        </div>

        <div class="mb-4 flex items-center gap-x-2">
            <label class="block text-sm font-medium text-gray-700 min-w-[110px]">Email</label>
            <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                   class="mt-1 block flex-1 rounded-md border-gray-300 shadow-lg focus:ring-indigo-500 focus:border-indigo-500" disabled id="email_input">
            <button type="button" class="ml-2 px-2 py-1 bg-indigo-500 text-white rounded edit-btn" data-target="email_input">Edit</button>
        </div>

        <div class="mb-6 flex items-center gap-x-2">
            <label class="block text-sm font-medium text-gray-700 min-w-[110px]">Department</label>
            <input type="text" name="department" value="{{ old('department', Auth::user()->department) }}"
                   class="mt-1 block flex-1 rounded-md border-gray-300 shadow-lg focus:ring-indigo-500 focus:border-indigo-500" disabled id="department_input">
            <button type="button" class="ml-2 px-2 py-1 bg-indigo-500 text-white rounded edit-btn" data-target="department_input">Edit</button>
        </div>

        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-500">
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
</x-layout>
