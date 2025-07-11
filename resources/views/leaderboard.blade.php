<x-layout>
  <x-slot name="heading">
    Leaderboard
  </x-slot>

<!-- Player Leaderboard -->
<div class="p-6 bg-white rounded shadow-lg mb-10 ring-2 ring-gray-400">
  <h2 class="text-xl font-semibold mb-4">Top Players by Score</h2>
  <a href="/explained" title="Explained" class="text-xs underline hover:text-gray-600 mb-2 inline-block font-semibold">Ranking Explained</a>
  <table class="w-full text-left border-collapse">
    <thead>
      <tr class="text-sm text-gray-600 border-b bg-gray-100 font-semibold tracking-wide">
        <th class="py-2 px-4">Rank</th>
        <th class="py-2 px-4">Name</th>
        <th class="py-2 px-4">Department</th>
        <th class="py-2 px-4">Score</th>
        <th class="py-2 px-4">Accuracy</th>
      </tr>
    </thead>
    <tbody>
      @foreach($users as $index => $user)
        @php
          $rank = ($users->firstItem() ?? 0) + $index;
          $isCurrentUser = Auth::check() && $user->id === Auth::id();
        @endphp
        <tr class="border-b text-gray-800 text-sm {{ $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' }} @if($isCurrentUser) bg-yellow-100 @endif hover:bg-gray-100 transition-colors duration-200">
          <td class="py-2 px-4 whitespace-nowrap">
            @if($rank === 1)
              🥇
            @elseif($rank === 2)
              🥈
            @elseif($rank === 3)
              🥉
            @else
              {{ $rank }}
            @endif
            @if(isset($user->rank_movement))
              @if($user->rank_movement === 'up')
                <span title="Rank Up" class="ml-1 text-green-600">&#9650;</span>
              @elseif($user->rank_movement === 'down')
                <span title="Rank Down" class="ml-1 text-red-600">&#9660;</span>
              @endif
            @endif
          </td>
          <td class="py-2 px-4 flex items-center gap-2 min-w-[120px]">
            @if($user->profile_image)
              <img src="{{ $user->profile_image }}" alt="Profile" class="w-7 h-7 rounded-full object-cover border border-gray-300">
            @else
              <span class="inline-block w-7 h-7 rounded-full bg-gray-300"></span>
            @endif
            <a href="{{ route('player.dashboard', ['user' => $user->id]) }}" class="underline hover:font-bold transition-all duration-150">
              {{ $user->first_name }} {{ $user->last_name }}
              @if($isCurrentUser)
                <span class="ml-2 px-2 py-0.5 rounded bg-yellow-300 text-xs text-gray-800 font-bold">You</span>
              @endif
            </a>
          </td>
          <td class="py-2 px-4 min-w-[160px]">
            <a href="{{ route('department.dashboard', ['department' => $user->department]) }}" class="underline hover:font-bold transition-all duration-150">
              {{ $user->department }}
            </a>
          </td>
          <td class="py-2 px-4 whitespace-nowrap">{{ $user->display_score }}</td>
          <td class="py-2 px-4 whitespace-nowrap">{{ $user->display_accuracy }}%</td>
        </tr>
      @endforeach
    </tbody>
  </table>
  <div class="mt-4 flex justify-end flex-col space-y-2">
    {{ $users->links() }}
  </div>
</div>


  <!-- Department Leaderboard -->
  <div class="p-6 bg-white rounded shadow mt-10 ring-2 ring-gray-400">
    <h2 class="text-xl font-semibold mb-4 border-b border-gray-300 pb-2">
      Top 10 Departments
    </h2>
    <a href="/explained" title="Explained" class="text-xs underline hover:text-gray-600 mb-2 inline-block font-semibold transition-colors duration-200">
      Ranking Explained
    </a>
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="text-sm text-gray-600 border-b bg-gray-100 font-semibold tracking-wide">
          <th class="py-2 px-4">Rank</th>
          <th class="py-2 px-4">Department</th>
          <th class="py-2 px-4">Weighted Score</th>
          <th class="py-2 px-4">Total Score</th>
          <th class="py-2 px-4">Avg. Accuracy</th>
        </tr>
      </thead>
      <tbody>
        @php
          $nonGuestDepartments = collect($departments)->filter(function($dept) {
            return strtolower(trim($dept['department'])) !== 'guest';
          })->take(10);
        @endphp
        @foreach($nonGuestDepartments as $index => $dept)
          @php
            $userDept = Auth::check() ? strtolower(trim(Auth::user()->department ?? '')) : '';
            $deptName = isset($dept['department']) ? strtolower(trim(preg_replace('/\s*\(.*/', '', $dept['department']))) : '';
            $isUserDept = $userDept !== '' && $deptName !== '' && $userDept === $deptName;
          @endphp
          <tr class="border-b text-gray-800 text-sm {{ $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' }} @if($isUserDept) bg-yellow-100 @endif hover:bg-gray-100 transition-colors duration-200">
            <td class="py-3 px-4 whitespace-nowrap">
              @if($loop->iteration === 1)
                🥇
              @elseif($loop->iteration === 2)
                🥈
              @elseif($loop->iteration === 3)
                🥉
              @else
                {{ $loop->iteration }}
              @endif
            </td>
            <td class="py-3 px-4">
              <span class="inline-block min-w-[180px]">
                <a href="{{ route('department.dashboard', ['department' => $dept['department']]) }}" class="underline hover:text-blue-600 transition-colors duration-200">
                  {{ $dept['department'] }}
                </a>
                @if(isset($dept['num_players']))
                  ({{ $dept['num_players'] }})
                @endif
                @if($isUserDept)
                  <span class="ml-2 px-2 py-0.5 rounded bg-yellow-300 text-xs text-gray-800 font-semibold shadow-sm">
                    Your Department
                  </span>
                @endif
              </span>
            </td>
            <td class="py-3 px-4">{{ isset($dept['score_per_player']) ? number_format($dept['score_per_player'], 1) : 'N/A' }}</td>
            <td class="py-3 px-4">{{ $dept['total_score'] }}</td>
            <td class="py-3 px-4">{{ isset($dept['average_accuracy']) ? number_format($dept['average_accuracy'], 1) . '%' : 'N/A' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

<!-- Guest Leaderboard -->
<div class="p-6 bg-white rounded shadow-lg mt-10 ring-2 ring-gray-400">
  <h2 class="text-xl font-semibold mb-4">Top Guest Players</h2>
  <a href="/explained" title="Explained" class="text-xs underline hover:text-gray-600 mb-2 inline-block font-semibold">Ranking Explained</a>
  <table class="w-full text-left border-collapse">
    <thead>
      <tr class="text-sm text-gray-600 border-b bg-gray-100 font-semibold tracking-wide">
        <th class="py-2 px-4">Rank</th>
        <th class="py-2 px-4">Name</th>
        <th class="py-2 px-4">Score</th>
        <th class="py-2 px-4">Accuracy</th>
      </tr>
    </thead>
    <tbody>
      @php
        $allUsers = \App\Models\User::with('submissions')->get()->map(function ($user) {
          $correct = $user->submissions->where('is_correct', true)->count();
          $total = $user->submissions->count();
          $wrong = $total - $correct;
          $user->accuracy = $total > 0 ? round($correct / $total * 100, 1) : 0;
          $user->score = ($correct * 10) - ($wrong * 10);
          return $user;
        })->filter(function($user) {
          return strtolower(trim($user->department)) === 'guest';
        })->sortByDesc(function ($user) {
          return sprintf('%08d%08d', $user->score, $user->submissions->count());
        })->values();
      @endphp
      @foreach($allUsers as $index => $user)
        @php
          $isCurrentUser = Auth::check() && $user->id === Auth::id();
        @endphp
        <tr class="border-b text-gray-800 text-sm {{ $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' }} @if($isCurrentUser) bg-yellow-100 @endif hover:bg-gray-100 transition-colors duration-200">
          <td class="py-2 px-4 whitespace-nowrap">
            @if($index + 1 === 1)
              🥇
            @elseif($index + 1 === 2)
              🥈
            @elseif($index + 1 === 3)
              🥉
            @else
              {{ $index + 1 }}
            @endif
          </td>
          <td class="py-2 px-4 flex items-center gap-2 min-w-[120px]">
            @if($user->profile_image)
              <img src="{{ $user->profile_image }}" alt="Profile" class="w-7 h-7 rounded-full object-cover border border-gray-300">
            @else
              <span class="inline-block w-7 h-7 rounded-full bg-gray-300"></span>
            @endif
            <a href="{{ route('player.dashboard', ['user' => $user->id]) }}" class="underline hover:font-bold transition-all duration-150">
              {{ $user->first_name }} {{ $user->last_name }}
              @if($isCurrentUser)
                <span class="ml-2 px-2 py-0.5 rounded bg-yellow-300 text-xs text-gray-800 font-bold">You</span>
              @endif
            </a>
          </td>
          <td class="py-2 px-4 whitespace-nowrap">{{ $user->score * 10 }}</td>
          <td class="py-2 px-4 whitespace-nowrap">{{ $user->accuracy }}%</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
</x-layout>
