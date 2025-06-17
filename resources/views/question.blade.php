<x-layout>
  <x-slot:heading>
    Daily Question
  </x-slot:heading>

  <div class="p-6 bg-white rounded-lg shadow ring-2 ring-black">
    <p class="text-gray-600 text-sm mb-2">Category: {{ $question->category }}</p>

    @if(session('success'))
      <div class="p-4 bg-green-100 text-green-800 rounded mb-4">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="p-4 bg-red-100 text-red-800 rounded mb-4">
        {{  session('error') }}
      </div>
    @endif

    @if($alreadySubmitted)
      <div class="p-4 bg-yellow-100 text-yellow-800 rounded">
        Question has already been answered. Questions reset at 12AM and 12PM, see you then!
      </div>
      <div class="mt-6">
        <h3 class="text-lg font-semibold mb-2">Comments</h3>
        <form method="POST" action="{{ route('question.comment') }}" class="mb-4">
          @csrf
          <input type="hidden" name="question_id" value="{{ $question->id }}">
          <textarea name="comment" rows="2" class="w-full border rounded p-2 mb-2" placeholder="Leave a comment..." required></textarea>
          <button type="submit" class="px-4 py-2 bg-slate-600 text-white rounded hover:bg-slate-500">Post Comment</button>
        </form>
        <div class="space-y-4">
          @php
            $reactionTypes = [
              'like' => '👍',
              'crying' => '😢',
              'dislike' => '👎',
              'angry' => '😡',
              'laughing' => '😂',
            ];
          @endphp
          @forelse($comments as $comment)
            <div class="p-3 pb-12 bg-gray-100 rounded shadow relative">
              <div class="text-sm text-gray-700 font-semibold mb-1 flex items-center justify-between">
                <span>{{ $comment->user->first_name }} {{ $comment->user->last_name }} <span class="text-xs text-gray-500">&bull; {{ $comment->created_at->diffForHumans() }}</span></span>
                @if(Auth::id() === $comment->user_id)
                  <form method="POST" action="{{ route('question.comment.delete', $comment->id) }}" onsubmit="return confirm('Delete this comment?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-xs text-red-600 hover:underline ml-2">Delete</button>
                  </form>
                @endif
              </div>
              <div class="text-gray-900 mb-2">{{ $comment->comment }}</div>
              <!-- Reaction Buttons with Counts -->
              <div class="absolute bottom-2 right-2">
              <div class="flex items-center gap-1 p-1 rounded-full bg-[#e8e4df] dark:bg-[#191818] w-auto text-base shadow mt-1">
                @foreach($reactionTypes as $type => $emoji)
                  <button
                    class="reaction-btn before:hidden hover:before:flex before:justify-center before:items-center before:h-2.5 before:text-[.45rem] before:px-0.5 before:content-['{{ ucfirst($type) }}'] before:bg-black dark:before:bg-white dark:before:text-black before:text-white before:bg-opacity-50 before:absolute before:-top-4 before:rounded hover:-translate-y-1 cursor-pointer hover:scale-105 bg-white dark:bg-[#191818] rounded-full p-0.5 px-1 text-sm"
                    data-comment-id="{{ $comment->id }}"
                    data-type="{{ $type }}"
                    type="button"
                  >
                    {{ $emoji }}
                    <span class="text-[10px] ml-0.5 align-middle reaction-count" style="color: white; text-shadow: 0 0 2px #000;">{{ $comment->reactions->where('type', $type)->count() }}</span>
                  </button>
                @endforeach
              </div>
              </div>
            </div>
          @empty
            <div class="text-gray-500">No comments yet. Be the first to comment!</div>
          @endforelse
        </div>
      </div>
    @else
        <div class="mt-4">
            <h3 class="text-lg font-medium mb-4">{{ $question->question }}</h3>
            <form method="POST" action="{{ route('question.submit') }}" class="space-y-3">
                @csrf
                <input type="hidden" name="question_id" value="{{ $question->id }}">
                @foreach($answers as $answer)
                    <div class="flex items-center space-x-2 rounded-lg border p-3 transition-colors">
                        <input type="radio" id="option-{{ $loop->index }}" name="answer" value="{{ $answer }}" class="form-radio text-slate-600" required>
                        <label for="option-{{ $loop->index }}" class="flex-1 cursor-pointer">{{ $answer }}</label>
                    </div>
                @endforeach
                <button type="submit" class="w-full bg-slate-600 text-white py-2 rounded mt-4 hover:bg-slate-500 transition-colors">Submit Answer</button>
            </form>
        </div>
    @endif
  </div>
  @section('scripts')
<script>
document.querySelectorAll('.reaction-btn').forEach(btn => {
  btn.addEventListener('click', function() {
    const commentId = this.dataset.commentId;
    const type = this.dataset.type;
    fetch("{{ route('question.comment.react') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ comment_id: commentId, type: type })
    })
    .then(res => res.json())
    .then(data => {
      if (data.counts) {
        // Update all counts for this comment
        document.querySelectorAll('.reaction-btn[data-comment-id="' + commentId + '"] .reaction-count').forEach(function(span) {
          const reactionType = span.parentElement.getAttribute('data-type');
          span.textContent = data.counts[reactionType] || 0;
        });
      }
    });
  });
});
</script>
@endsection

</x-layout>


