<x-layout>
  <x-slot name="heading">
    Leaderboard
  </x-slot>

  <div class="p-6 bg-white rounded shadow-lg">
    <h2 class="text-xl font-semibold mb-4">Top 10 Players by Accuracy</h2>
    <table class="w-full text-left border-collapse">
      <thead>
        <tr class="text-sm text-gray-600 border-b">
          <th class="pb-2">Rank</th>
          <th class="pb-2">Name</th>
          <th class="pb-2">Department</th>
          <th class="pb-2">Accuracy</th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $index => $user)
          <tr class="border-b text-gray-800 text-sm">
            <td class="py-2">{{ $loop->iteration }}</td>
            <td class="py-2">{{ $user->first_name }} {{ $user->last_name }}</td>
            <td class="py-2">{{ $user->department }}</td>
            <td class="py-2">{{ $user->accuracy }}%</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="p-6 bg-white rounded shadow">
  <h2 class="text-xl font-semibold mb-4">Top 10 Departments by Average Accuracy</h2>
  <table class="w-full text-left border-collapse">
    <thead>
      <tr class="text-sm text-gray-600 border-b">
        <th class="py-2 px-4">Rank</th>
        <th class="py-2 px-4">Department</th>
        <th class="py-2 px-4">Average Accuracy</th>
      </tr>
    </thead>
    <tbody>
      @foreach($departments as $index => $dept)
        <tr class="border-b text-gray-800 text-sm">
            <td class="py-2">{{ $loop->iteration }}</td>
          <td class="py-2 px-4">{{ $dept['department'] }}</td>
          <td class="py-2 px-4">{{ $dept['average_accuracy'] }}%</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
</x-layout>
