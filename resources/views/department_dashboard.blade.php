<x-layout>
  <x-slot name="heading">
    {{ $department }} Department Dashboard
  </x-slot>

  <div class="p-6 bg-white rounded shadow-lg mb-10 ring-2 ring-black">
    <h2 class="text-xl font-semibold mb-2">Department Stats</h2>
    <p class="mb-2">Weighted Score: <strong>{{ number_format($scorePerPlayer, 1) }}</strong></p>
    <p class="mb-2">Total Score: <strong>{{ $totalScore }}</strong></p>
    <p class="mb-2">Average Accuracy: <strong>{{ number_format($averageAccuracy, 1) }}%</strong></p>
  </div>

  <div class="p-6 bg-white rounded shadow-lg ring-2 ring-black">
    <h2 class="text-xl font-semibold mb-2">Players in Department</h2>
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="text-sm text-gray-600 border-b">
          <th class="pb-2">Rank</th>
          <th class="pb-2">Name</th>
          <th class="pb-2">Score</th>
          <th class="pb-2">Accuracy</th>
          <th class="pb-2">Questions Answered</th>
        </tr>
      </thead>
      <tbody>
        @foreach($players as $index => $player)
          <tr class="border-b text-gray-800 text-sm">
            <td class="py-2">{{ $index + 1 }}</td>
            <td class="py-2">
              <a href="{{ route('player.dashboard', ['user' => $player['id']]) }}" class="underline hover:font-bold">
                {{ $player['name'] }}
              </a>
            </td>
            <td class="py-2">{{ $player['score'] }}</td>
            <td class="py-2">{{ $player['accuracy'] }}%</td>
            <td class="py-2">{{ $player['total_answered'] }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-layout>
