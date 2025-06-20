<div class="p-3 pb-12 bg-gray-100 rounded relative mb-3 ml-{{ $comment->parent_id ? '8' : '0' }}
            ring-2 {{ $comment->parent_id ? 'ring-gray-200 shadow-lg' : 'ring-gray-100 shadow' }}">

    <div class="text-sm text-gray-700 font-semibold mb-1 flex items-center justify-between">
        <span>{{ $comment->user->first_name }} {{ $comment->user->last_name }}
            <span class="text-xs text-gray-500">&bull; {{ $comment->created_at->diffForHumans() }}</span>
        </span>
        @if(Auth::id() === $comment->user_id)
            <form method="POST" action="{{ route('question.comment.delete', $comment->id) }}" onsubmit="return confirm('Delete this comment?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs text-red-600 hover:underline ml-2">Delete</button>
            </form>
        @endif
    </div>
    <div class="text-gray-900 mb-2">{{ $comment->comment }}</div>

    <div class="absolute bottom-2 right-2">
        <div class="flex items-center gap-1 p-1 rounded-full bg-[#e8e4df] dark:bg-[#191818] w-auto text-base shadow mt-1">
            @php $reactionTypes = ['like' => '👍', 'crying' => '😢', 'dislike' => '👎', 'angry' => '😡', 'laughing' => '😂']; @endphp
            @foreach($reactionTypes as $type => $emoji)
                <button
                    class="reaction-btn hover:-translate-y-1 cursor-pointer hover:scale-105 bg-white dark:bg-[#2f2f2f] rounded-full p-0.5 px-1 text-sm"
                    data-comment-id="{{ $comment->id }}"
                    data-type="{{ $type }}"
                    type="button"
                >
                    {{ $emoji }}
                    <span class="text-[10px] ml-0.5 align-middle reaction-count" style="color: white; text-shadow: 0 0 2px #000;">
                        {{ $comment->reactions->where('type', $type)->count() }}
                    </span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Reply form --}}
    <button onclick="document.getElementById('reply-form-{{ $comment->id }}').classList.toggle('hidden')"
        class="absolute bottom-2 left-2 text-xs text-blue-600 hover:underline">Reply</button>

    <form id="reply-form-{{ $comment->id }}" method="POST" action="{{ route('question.comment') }}" class="hidden mt-2">
        @csrf
        <input type="hidden" name="question_id" value="{{ $comment->question_id }}">
        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
        <textarea name="comment" class="w-full p-2 border rounded" rows="2" placeholder="Reply..."></textarea>
        <button class="px-2 py-1 bg-slate-600 text-white rounded mt-1">Post</button>
    </form>

    @foreach($comment->replies as $reply)
        @include('comment', ['comment' => $reply])
    @endforeach
</div>

@section('scripts')
<script>
document.querySelectorAll('.reaction-btn').forEach(btn => {
  btn.addEventListener('click', function () {
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
        document.querySelectorAll('.reaction-btn[data-comment-id="' + commentId + '"] .reaction-count').forEach(function (span) {
          const reactionType = span.parentElement.getAttribute('data-type');
          span.textContent = data.counts[reactionType] || 0;
        });
      }
    });
  });
});
</script>
@endsection
