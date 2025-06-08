<x-layout>
  <x-slot name="heading">
    {{ $first_name }}'s Dashboard
  </x-slot>

  <div class="p-6 bg-white rounded shadow-lg">
    <h2 class="text-xl font-semibold mb-2">Trivia Stats</h2>
    
    <p class="text-gray-700 mb-2">
    Member since: <strong>{{ Auth::user()->created_at->format('F j, Y') }}</strong>
    </p>

    <p class="mb-2">Player Rank: <strong>#{{ $playerRank }}</strong></p>

    <p class="mb-2">Department: <strong>{{ Auth::user()->department }}</strong></p>

    <p class="mb-2">Department Rank: <strong>#{{  $departmentRank }}</strong></p>

    <p class="text-gray-700 mb-2">
      Total Questions Answered: <strong>{{ $totalAnswered }}</strong>
    </p>

    <p class="text-gray-700 mb-2">
      Correct Answers: <strong>{{ $correctAnswers }}</strong>
    </p>

    <p class="text-gray-700 mb-2">
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
</x-layout>
