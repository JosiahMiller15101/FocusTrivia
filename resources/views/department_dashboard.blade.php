<x-layout>
  <x-slot name="heading">
    {{ isset($department) ? ucfirst($department) : 'Department' }}'s Dashboard
  </x-slot>

  <div class="flex flex-col md:flex-row gap-8 min-h-[900px]">
    <div class="md:w-1/3 w-full flex flex-col gap-6 min-h-[400px] rounded ring-2 ring-gray-400">
      <div class="bg-white rounded-lg shadow-lg px-6 py-6 flex flex-1 flex-col items-center justify-start min-h-[160px]" style="min-height:160px;">
        @if(isset($department) && strtolower(trim($department)) == 'accounting')
            <img src="{{ asset('images/accounting.png') }}" alt="Accounting Department" class="w-32 h-32 rounded-full object-cover border border-gray-300 mb-4">
        @elseif(isset($department) && strtolower(trim($department)) == 'donor communications')
            <img src="{{ asset('images/donorcommunications.png') }}" alt="Donor Communications Department" class="w-32 h-32 rounded-full object-cover border border-gray-300 mb-4">
        @elseif(isset($department) && strtolower(trim($department)) == 'events')
            <img src="{{ asset('images/events.png') }}" alt="Events Department" class="w-32 h-32 rounded-full object-cover border border-gray-300 mb-4">
        @elseif(isset($department) && strtolower(trim($department)) == 'it')
            <img src="{{ asset('images/IT.png') }}" alt="IT Department" class="w-32 h-32 rounded-full object-cover border border-gray-300 mb-4">
        @elseif(isset($department) && strtolower(trim($department)) == 'hr')
            <img src="{{ asset('images/HR.png') }}" alt="HR Department" class="w-32 h-32 rounded-full object-cover border border-gray-300 mb-4">
        @elseif(isset($department) && strtolower(trim($department)) == 'summer projects')
            <img src="{{ asset('images/summerprojects.png') }}" alt="Summer Projects Department" class="w-32 h-32 rounded-full object-cover border border-gray-300 mb-4">
        @elseif(isset($department) && strtolower(trim($department)) == 'media operations')
            <img src="{{ asset('images/mediaoperations.png') }}" alt="Media Operations Department" class="w-32 h-32 rounded-full object-cover border border-gray-300 mb-4">
        @elseif(isset($department) && strtolower(trim($department)) == 'other')
            <img src="{{ asset('images/other.png') }}" alt="Other Department" class="w-32 h-32 rounded-full object-cover border border-gray-300 mb-4">
        @elseif(isset($department) && strtolower(trim($department)) == 'marketing')
            <img src="{{ asset('images/marketing.png') }}" alt="Marketing Department" class="w-32 h-32 rounded-full object-cover border border-gray-300 mb-4">
        @else
          <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center mb-4 text-4xl text-gray-400">
            <svg xmlns='http://www.w3.org/2000/svg' class='h-16 w-16' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z' /></svg>
          </div>
        @endif
        <div class="w-full mt-2">
          <div class="text-lg font-bold text-center mb-4">{{ isset($department) ? ucfirst($department) : 'Department' }}</div>
          <div class="grid grid-cols-2 gap-4 w-full text-center">
            <div>
              <div class="text-xs text-gray-500 uppercase tracking-wider">Department Rank</div>
              <div class="font-semibold text-gray-800">#{{ $departmentRank ?? 'N/A' }}</div>
              <div class="mt-4 text-xs text-gray-500 uppercase tracking-wider">Top Player</div>
              <div class="font-semibold text-gray-900">{{ $players[0]['name'] ?? 'N/A' }}</div>
            </div>
            <div>
              <div class="text-xs text-gray-500 uppercase tracking-wider">Number of Players</div>
              <div class="font-semibold text-gray-800">{{ $numPlayers ?? 0 }}</div>
              
              <div class="mt-4 text-xs text-gray-500 uppercase tracking-wider">Created At</div>
              <div class="font-semibold text-gray-900">{{ $createdAt ?? 'N/A' }}</div>
            </div>
          </div>
          <div class="w-7/8 border-t border-gray-300 mt-4 mx-auto"></div>
        </div>
      </div>
    </div>
    <div class="md:w-2/3 w-full flex flex-col gap-6">
      <div class="grid gap-4 md:grid-cols-2">
        <x-stat-card 
          :icon="'<svg class=\'h-5 w-5 text-amber-700 mr-2\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M9 7a3 3 0 0 1 6 0v2.1a7 7 0 1 1-6 0V7z\'/></svg>'"
          title="Weighted Score"
          :value="isset($scorePerPlayer) ? number_format($scorePerPlayer, 1) : 'N/A'"
          :description="'Total score / √(total # of department user answer submissions)'"
        />
        <x-stat-card 
          :icon="'<svg class=\'h-5 w-5 text-green-600 mr-2\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\'><path d=\'M9 12l2 2l4-4\'/><circle cx=\'12\' cy=\'12\' r=\'10\'/></svg>'"
          title="Total Submissions"
          :value="$totalQuestionsAnswered ?? 0"
          :description="($totalCorrectAnswers ?? 0) . ' correct (' . ($totalQuestionsAnswered > 0 ? number_format(($totalCorrectAnswers / $totalQuestionsAnswered) * 100, 1) : '0') . '% accuracy)'"
        />
        <x-stat-card 
          :icon="'<svg class=\'h-5 w-5 text-blue-600 mr-2\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\'><circle cx=\'12\' cy=\'8\' r=\'7\'/><path d=\'M8.21 13.89l-1.42 4.25a1 1 0 0 0 1.45 1.12l3.76-2.18 3.76 2.18a1 1 0 0 0 1.45-1.12l-1.42-4.25\'/></svg>'"
          title="Total Points"
          :value="number_format($totalScore ?? 0)"
          :description="'Total points earned by all players in this department.'"
        />
        <x-stat-card 
          :icon="'<svg class=\'h-5 w-5 text-purple-600 mr-2\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' viewBox=\'0 0 24 24\'><circle cx=\'12\' cy=\'12\' r=\'10\'/><circle cx=\'12\' cy=\'12\' r=\'6\'/><circle cx=\'12\' cy=\'12\' r=\'2\'/></svg>'"
          title="Average Player Accuracy"
          :value="isset($averageAccuracy) ? number_format($averageAccuracy, 1) . '%' : 'N/A'"
          :description="'Average accuracy of all department players.'"
        />
      </div>
      <div class="bg-white rounded-lg shadow-lg p-6 mt-2 ring-2 ring-gray-400 flex flex-1 flex-col min-h-[400px]" style="min-height:40vh;">
        <h2 class="text-xl font-semibold mb-2">Players in Department</h2>
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="text-sm text-gray-600 border-b">
              <th class="pb-3">Rank</th>
              <th class="pb-3">Name</th>
              <th class="pb-3">Score</th>
              <th class="pb-3">Accuracy</th>
              <th class="pb-3">Questions Answered</th>
            </tr>
          </thead>
          <tbody>
            @foreach($players as $index => $player)
              <tr class="border-b text-gray-800 text-[15px] {{ $index % 2 === 0 ? 'bg-gray-50' : 'bg-white' }} @if(!empty($player['isCurrentUser'])) bg-yellow-100 @endif @if(!empty($player['isSearchedUser'])) ring-2 ring-blue-400 bg-blue-100 @endif hover:bg-gray-100 transition-colors duration-200" style="height:56px;">
                <td class="py-3">
                  @if($index === 0)
                    🥇
                  @elseif($index === 1)
                    🥈
                  @elseif($index === 2)
                    🥉
                  @else
                    {{ $index + 1 }}
                  @endif
                </td>
                <td class="py-3">
                  <div class="flex items-center gap-2">
                    @if($player['profile_image'])
                      <img src="{{ $player['profile_image'] }}" alt="Profile" class="w-7 h-7 rounded-full object-cover border border-gray-300">
                    @else
                      <span class="inline-block w-7 h-7 rounded-full bg-gray-300"></span>
                    @endif
                    <a href="{{ route('player.dashboard', ['user' => $player['id']]) }}" class="underline hover:font-bold">
                      {{ $player['name'] }}
                    </a>
                    @if(!empty($player['isCurrentUser']))
                      <span class="ml-2 px-2 py-0.5 rounded bg-yellow-300 text-xs text-gray-800 font-bold underline">You</span>
                    @endif
                  </div>
                </td>
                <td class="py-3">{{ $player['score'] }}</td>
                <td class="py-3">{{ $player['accuracy'] }}%</td>
                <td class="py-3">{{ $player['total_answered'] }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
        <div class="flex-1"></div>
      </div>
    </div>
  </div>
</x-layout>
