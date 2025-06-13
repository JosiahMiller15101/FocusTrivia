<x-layout>
  <x-slot name="heading">
    Leaderboard
  </x-slot>

  <div class="p-6 bg-white rounded shadow-lg mb-10 ring-2 ring-black">
    <h2 class="text-xl font-semibold mb-4">Top Players by Score</h2>
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

  <div class="p-6 bg-white rounded shadow mt-10 ring-2 ring-black">
  <h2 class="text-xl font-semibold mb-4">Top 10 Departments by Score per Player</h2>
  <table class="w-full text-left border-collapse">
    <thead>
      <tr class="text-sm text-gray-600 border-b">
        <th class="py-2 px-4">Rank</th>
        <th class="py-2 px-4">Department</th>
        <th class="py-2 px-4">Score</th>
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
</x-layout>
