<x-layout>
    <x-slot name="heading">
    How Rankings Work
  </x-slot>

  <div class="p-6 bg-white rounded shadow-lg mb-10 ring-2 ring-black">
    <h2 class="text-xl font-semibold mb-4">Understanding the Rankings</h2>
    <p class="mb-4">
      The rankings in FOCUS Trivia are designed to reflect both individual and departmental performance. Here's how it works:
    </p>
    
    <h3 class="text-lg font-semibold mb-2">Individual Player Rankings</h3>
    <ul class="list-disc pl-5 mb-4">
      <li>Players are ranked based on their total score, which is calculated from the number of correct answers they provide. Every correct answer will increase a player's score by 10 points, while incorrect answers subtract 10 points</li>
      <li>The total number of questions a player has answered is also considered, with a higher number of submissions boosting a player's rank.</li>
      <li>Two players with identical scores will have different rankings if one has answered 10 questions and the other has answered 1</li>
      <li>Consistency is the easiest way to boost your score</li>
      <li>Players can view their personal stats and compare them with others on the leaderboard.</li>
    </ul>

    <h3 class="text-lg font-semibold mb-2">Departmental Rankings</h3>
    <ul class="list-disc pl-5 mb-4">
      <li>Departments are ranked based on the weighted average score of all their members.</li>
      <li>The weighted score is calculated by this formula: Weighted score = (Total department score / √(# of players in department)).</li>
      <li>This method discourages bias toward larger departments while keeping consistent submissions necessary to stay on the leaderboard.</li>
    </ul>
  </div>
</x-layout>