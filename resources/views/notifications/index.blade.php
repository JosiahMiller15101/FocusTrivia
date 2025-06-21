<x-layout>
  <x-slot:heading>Notifications</x-slot:heading>

  <div class="space-y-3 p-4 bg-white ring-2 ring-gray-400 rounded shadow-lg">
    @forelse($notifications as $note)
      <div class="flex items-start gap-3 p-4 bg-white shadow-lg border-l-4 ring-2 ring-gray-400
            @if($note->type === 'reply') border-blue-500
            @elseif($note->type === 'reaction') border-yellow-500
            @else border-gray-300 @endif
            rounded hover:shadow-md transition-all">


        {{-- Icon based on type --}}
        <div class="text-xl">
          @if($note->type === 'reply')
            💬
          @elseif($note->type === 'reaction')
            🎯
          @else
            🔔
          @endif
        </div>

        {{-- Notification message --}}
        <div class="flex-1">
          <p class="text-sm text-black">{{ $note->message }}</p>
          @if($note->comment && $note->comment->question)
            <p class="text-xs text-blue-600 underline mt-1 italic">
              <a href="/question">
              Question: {{ $note->comment->question->question }}
              </a>
            </p>
          @endif
            <p class="text-xs text-gray-600 mt-1">{{ $note->created_at->diffForHumans() }}</p>
        </div>

      </div>
    @empty
      <div class="p-6 bg-white dark:bg-gray-800 rounded shadow text-gray-500 text-center">
        No notifications yet.
      </div>
    @endforelse
  </div>
</x-layout>

