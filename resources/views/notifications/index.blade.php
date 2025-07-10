<x-layout>
  <x-slot:heading>Notifications</x-slot:heading>

  @php
    $emojiMap = [
      'like' => '👍',
      'crying' => '😢',
      'dislike' => '👎',
      'angry' => '😡',
      'laughing' => '😂',
    ];
  @endphp

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
          @endif
        </div>

        {{-- Notification message --}}
        <div class="flex-1">
          @if($note->type === 'reply')
            <p class="text-sm text-black">{{ $note->message }}</p>
            <p class="text-xs text-gray-700 mt-1 italic">
              "{{ optional(optional($note->comment)->replies)->first()->comment ?? ($note->comment->comment ?? '') }}"
            </p>
          @elseif($note->type === 'reaction')
            <p class="text-sm text-black">{{ $note->message }}</p>
            <span>
              {{ $emojiMap[$note->reaction_type] ?? '' }}
            </span>
          @endif
          <p class="text-xs text-blue-600 underline mt-1 italic">
            <a href="/question">
            Question: {{ $note->comment->question->question }}
            </a>
          </p>
          <p class="text-xs text-gray-600 mt-1">{{ $note->created_at->diffForHumans() }}</p>
        </div>

      </div>
    @empty
      <div class="p-6 bg-white dark:bg-gray-800 rounded shadow text-gray-500 text-center">
        No notifications yet.
      </div>
    @endforelse
        {{ $notifications->links() }}
  </div>
</x-layout>
