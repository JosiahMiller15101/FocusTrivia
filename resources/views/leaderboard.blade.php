<x-layout>
  <x-slot name="heading">
    Leaderboard
  </x-slot>

  <!-- Player Leaderboard -->
  <div class="p-6 bg-white rounded shadow-lg mb-10 ring-2 ring-black">
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
                <td class="py-2">{{ ($users->firstItem() ?? 0) + $index }}</td>
                <td class="py-2">
                  <a href="{{ route('player.dashboard', ['user' => $user->id]) }}" class="underline hover:font-bold">
                    {{ $user->first_name }} {{ $user->last_name }}
                  </a>
                </td>
                <td class="py-2">{{ $user->department }}</td>
                <td class="py-2">{{ $user->score }}</td>
                <td class="py-2">{{ $user->accuracy }}%</td>
            </tr>
        @endforeach
      </tbody>
    </table>
    <div class="mt-4 flex justify-end flex-col space-y-2">
      {{ $users->links() }}
    </div>
  </div>

  <!-- Department Leaderboard -->
  <div class="p-6 bg-white rounded shadow mt-10 ring-2 ring-black">
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
            <td class="py-2">{{ $loop->iteration }}</td>
            <td class="py-2 px-4">{{ $dept['department'] }}</td>
            <td class="py-2 px-4">{{ isset($dept['score_per_player']) ? number_format($dept['score_per_player'], 1) : 'N/A' }}</td>
            <td class="py-2 px-4">{{ $dept['total_score'] }}</td>
            <td class="py-2 px-4">{{ isset($dept['average_accuracy']) ? number_format($dept['average_accuracy'], 1) . '%' : 'N/A' }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<!-- Guest Leaderboard -->
  <div class="p-6 bg-white rounded shadow-lg mt-10 ring-2 ring-black">
    <h2 class="text-xl font-semibold mb-4">Top Guest Players</h2>
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
            <td class="py-2">{{ $index + 1 }}</td>
            <td class="py-2">
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
