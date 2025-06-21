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

  <div class="flex flex-col md:flex-row gap-8 min-h-[900px]">
    <div class="md:w-1/3 w-full flex flex-col gap-6 min-h-[900px] rounded ring-2 ring-gray-400">
      <div class="bg-white rounded-lg shadow-lg px-6 py-8 flex flex-col items-center flex-1 justify-start">
        @if($profileImage)
          <img src="{{ $isAbsolute ? $profileImage : asset('storage/' . $profileImage) }}" alt="Profile Image" class="w-32 h-32 rounded-full object-cover mb-4">
        @else
          <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center mb-4 text-4xl text-gray-400">
            <svg xmlns='http://www.w3.org/2000/svg' class='h-16 w-16' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z' /></svg>
          </div>
        @endif
        @if(Auth::id() === $dashboardUser->id)
          <form action="{{ route('profile.uploadImage') }}" method="POST" enctype="multipart/form-data" class="mt-2 flex flex-col items-center">
            @csrf
            <label class="cursor-pointer flex items-center gap-2 text-xs text-blue-600 hover:underline">
              <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5 5 5M12 5v12" stroke-linecap="round" stroke-linejoin="round"/></svg>
              <span>Upload Photo</span>
              <input type="file" name="profile_image" accept="image/*" class="hidden" onchange="this.form.submit()">
            </label>
            @error('profile_image')
              <div class="text-xs text-red-500 mt-1">{{ $message }}</div>
            @enderror
          </form>
        @endif
        <div class="w-full mt-2">
          <div class="text-lg font-bold text-center mb-4">{{ $dashboardUser->first_name }} {{ $dashboardUser->last_name }}</div>
          <div class="grid grid-cols-2 gap-4 w-full text-center">
            <div>
              <div class="text-xs text-gray-500 uppercase tracking-wider">Department</div>
              <div class="font-semibold text-gray-800">{{ $dashboardUser->department ?? 'No Department' }}</div>
              <div class="mt-4 text-xs text-gray-500 uppercase tracking-wider">Joined</div>
              <div class="font-semibold text-gray-800">{{ $dashboardUser->created_at->format('F j, Y') }}</div>
            </div>
            <div>
              <div class="text-xs text-gray-500 uppercase tracking-wider">Rank</div>
              <div class="font-semibold text-gray-800">#{{ $playerRank ?? 'N/A' }}</div>
              <div class="mt-4 text-xs text-gray-500 uppercase tracking-wider">Streak</div>
              <div class="font-semibold text-gray-900">{{ $streak ?? 0 }} days</div>
            </div>
          </div>
          <div class="w-7/8 border-t border-gray-300 mt-4 mx-auto"></div>
        </div>
      </div>
    </div>
    <div class="md:w-2/3 w-full flex flex-col gap-6">
      <div class="grid gap-4 md:grid-cols-2">
        <x-stat-card 
          :icon="'<svg class=\'h-5 w-5 text-blue-600 mr-2\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\'><circle cx=\'12\' cy=\'8\' r=\'7\'/><path d=\'M8.21 13.89l-1.42 4.25a1 1 0 0 0 1.45 1.12l3.76-2.18 3.76 2.18a1 1 0 0 0 1.45-1.12l-1.42-4.25\'/></svg>'"
          title="Total Points"
          :value="number_format($score ?? 0)"
          :description="'Rank #' . ($playerRank ?? 'N/A') . ' overall • #' . ($departmentPlayerRank ?? 'N/A') . ' in department'"
        />
        <x-stat-card 
          :icon="'<svg class=\'h-5 w-5 text-green-600 mr-2\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\'><path d=\'M9 12l2 2l4-4\'/><circle cx=\'12\' cy=\'12\' r=\'10\'/></svg>'"
          title="Questions Answered"
          :value="$totalAnswered"
          :description="($correctAnswers ?? 0) . ' correct answers (' . ($totalAnswered > 0 ? number_format(($correctAnswers / $totalAnswered) * 100, 1) : '0') . '% accuracy)'"
        />
        <x-stat-card 
          :icon="'<svg class=\'h-5 w-5 text-amber-600 mr-2\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\'><circle cx=\'12\' cy=\'12\' r=\'10\'/><path d=\'M12 6v6l4 2\'/></svg>'"
          title="Current Streak"
          :value="($streak ?? 0) . ' days'"
          description="Keep answering daily to maintain your streak!"
        />
        <x-stat-card 
          :icon="'<svg class=\'h-5 w-5 text-purple-600 mr-2\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\'><circle cx=\'12\' cy=\'12\' r=\'10\'/><circle cx=\'12\' cy=\'12\' r=\'6\'/><circle cx=\'12\' cy=\'12\' r=\'2\'/></svg>'"
          title="Accuracy"
          :value="$totalAnswered > 0 ? number_format(($correctAnswers / $totalAnswered) * 100, 1) . '%' : 'N/A'"
          :description="'You have answered ' . ($correctAnswers ?? 0) . ' out of ' . ($totalAnswered ?? 0) . ' questions correctly.'"
        />
      </div>
      <div class="bg-white rounded-lg shadow-lg p-6 mt-2 ring-2 ring-gray-400">
        <h3 class="text-lg font-semibold mb-4">Recent Activity</h3>
        @if($history && count($history))
          <div class="flex flex-col gap-4">
            @foreach($history as $submission)
              @php
                $isCorrect = $submission->is_correct;
                $question = $submission->question;
                $category = $question->category ?? 'General';
                $date = $submission->created_at->format('M d, Y');
                $userAnswer = $submission->answer;
                $correctAnswer = $question->correct_answer ?? null;
              @endphp
              <div class="relative bg-gray-50 border border-gray-200 rounded-lg p-4 flex flex-col md:flex-row md:items-center gap-2 shadow-sm">
                <!-- Icon and points -->
                <div class="absolute top-3 right-3 flex items-center gap-1">
                  @if($isCorrect)
                    <span class="inline-flex items-center text-green-600 font-bold text-sm">
                      <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-linecap="round" stroke-linejoin="round"/></svg>
                      +100 pts
                    </span>
                  @else
                    <span class="inline-flex items-center text-red-500 font-bold text-sm">
                      <svg class="h-5 w-5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round"/></svg>
                      -100 pts
                    </span>
                  @endif
                </div>
                <div class="flex-1">
                  <div class="flex flex-wrap gap-2 items-center mb-1">
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded">{{ $category }}</span>
                    <span class="text-xs text-gray-400">{{ $date }}</span>
                  </div>
                  <div class="font-medium text-gray-800 mb-1">{{ $question->question ?? 'Question unavailable' }}</div>
                  <div class="flex flex-wrap gap-2 items-center">
                    <span class="text-xs font-semibold text-gray-500">Your answer:</span>
                    <span class="text-xs font-semibold {{ $isCorrect ? 'text-green-700' : 'text-red-600' }}">{{ $userAnswer }}</span>
                    @if(!$isCorrect && $correctAnswer)
                      <span class="text-xs font-semibold text-gray-500 ml-2">Correct answer:</span>
                      <span class="text-xs font-semibold text-green-700">{{ $correctAnswer }}</span>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-gray-500">No recent activity yet.</div>
        @endif
         <div class="mt-4 flex justify-end flex-col space-y-2">
      {{ $history->links() }}
    </div>
      </div>

      @if (!isset($user) || (isset($user) && $user->id === Auth::id()))
      <div class="bg-white rounded-lg shadow-lg px-8 py-8 w-full mt-2 md:col-span-2 ring-2 ring-gray-400">
        <h2 class="text-xl font-semibold mb-2">Update Profile</h2>
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
                @if(strtolower(trim(Auth::user()->department)) === 'guest')
                  <input type="text" name="department" value="{{ old('department', Auth::user()->department) }}"
                         class="mt-1 block flex-1 rounded-md border-gray-300 shadow-lg bg-gray-100 text-gray-400 cursor-not-allowed" disabled id="department_input">
                @else
                  <input type="text" name="department" value="{{ old('department', Auth::user()->department) }}"
                         class="mt-1 block flex-1 rounded-md border-gray-300 shadow-lg focus:ring-indigo-500 focus:border-indigo-500" disabled id="department_input">
                  <button type="button" class="shadow-lg ml-2 px-2 py-1 bg-slate-600 text-white rounded edit-btn hover:bg-slate-500" data-target="department_input">Edit</button>
                @endif
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
        document.getElementById('profileForm').addEventListener('submit', function() {
            this.querySelectorAll('input').forEach(input => {
                input.removeAttribute('disabled');
            });
        });
        </script>
      </div>
      @endif
    </div>
  </div>
</x-layout>
