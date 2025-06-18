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
        <tr class="text-sm text-gray-600 border-b">
          <th class="pb-2">Rank</th>
          <th class="pb-2">Name</th>
          <th class="pb-2">Department</th>
          <th class="pb-2">Score</th>
          <th class="pb-2">Accuracy</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $index => $user)
            <tr class="border-b text-gray-800 text-sm">
                <td class="py-2">
                  @php
                    $rank = ($users->firstItem() ?? 0) + $index;
                  @endphp
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
                <td class="py-2 flex items-center gap-2">
                  @php
                    // Always use the accessor for profile_image, which handles guest logic
                    $profileImage = $user->profile_image;
                    $isAbsolute = $profileImage && (Str::startsWith($profileImage, ['http://', 'https://']) || Str::startsWith($profileImage, ['/']));
                  @endphp
                  @if($profileImage)
                    <img src="{{ $profileImage }}" alt="Profile" class="w-7 h-7 rounded-full object-cover border border-gray-300">
                  @else
                    <span class="inline-block w-7 h-7 rounded-full bg-gray-300"></span>
                  @endif
                  <a href="{{ route('player.dashboard', ['user' => $user->id]) }}" class="underline hover:font-bold">
                    {{ $user->first_name }} {{ $user->last_name }}
                  </a>
                </td>
                <td class="py-2">
                  <a href="{{ route('department.dashboard', ['department' => $user->department]) }}" class="underline hover:font-bold">
                    {{ $user->department }}
                  </a>
                </td>
                <td class="py-2">{{ $user->display_score }}</td>
                <td class="py-2">{{ $user->display_accuracy }}%</td>
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
  <h2 class="text-xl font-semibold mb-4">Top 10 Departments</h2>
  <a href="/explained" title="Explained" class="text-xs underline hover:text-gray-600 mb-2 inline-block font-semibold">Ranking Explained</a>
  <table class="w-full text-left border-collapse">
    <thead>
      <tr class="text-sm text-gray-600 border-b">
        <th class="pb-2">Rank</th>
        <th class="pb-2">Department</th>
        <th class="pb-2">Weighted Score</th>
        <th class="pb-2">Total Score</th>
        <th class="pb-2">Avg. Accuracy</th>
      </tr>
    </thead>
    <tbody>
      @php
          $nonGuestDepartments = collect($departments)->filter(function($dept) {
              return strtolower(trim($dept['department'])) !== 'guest';
          })->take(10);
      @endphp
      @foreach($nonGuestDepartments as $index => $dept)
        <tr class="border-b text-gray-800 text-sm">
            <td class="py-2">
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
            <td class="py-2 px-4">
              <a href="{{ route('department.dashboard', ['department' => $dept['department']]) }}" class="underline hover:font-bold">
                {{ $dept['department'] }}
                @if(isset($dept['num_players']))
                  ({{ $dept['num_players'] }})
                @endif
              </a>
            </td>
            <td class="py-2 px-4">{{ isset($dept['score_per_player']) ? number_format($dept['score_per_player'], 1) : 'N/A' }}</td>
            <td class="py-2 px-4">{{ $dept['total_score'] }}</td>
            <td class="py-2 px-4">{{ isset($dept['average_accuracy']) ? number_format($dept['average_accuracy'], 1) . '%' : 'N/A' }}</td>
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
        <tr class="text-sm text-gray-600 border-b">
          <th class="pb-2">Rank</th>
          <th class="pb-2">Name</th>
          <th class="pb-2">Score</th>
          <th class="pb-2">Accuracy</th>
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
          <tr class="border-b text-gray-800 text-sm">
            <td class="py-2">
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
            <td class="py-2 flex items-center gap-2">
              @php
                $profileImage = $user->profile_image;
                $isAbsolute = $profileImage && (Str::startsWith($profileImage, ['http://', 'https://']) || Str::startsWith($profileImage, ['/']));
              @endphp
              @if($profileImage)
                <img src="{{ $profileImage }}" alt="Profile" class="w-7 h-7 rounded-full object-cover border border-gray-300">
              @else
                <span class="inline-block w-7 h-7 rounded-full bg-gray-300"></span>
              @endif
              <a href="{{ route('player.dashboard', ['user' => $user->id]) }}" class="underline hover:font-bold">
                {{ $user->first_name }} {{ $user->last_name }}
              </a>
            </td>
            <td class="py-2">{{ $user->score }}</td>
            <td class="py-2">{{ $user->accuracy }}%</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-layout>
