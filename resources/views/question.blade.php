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
    <div 
        x-data="questionComponent({ 
            correct: @json($question->correct_answer), 
            timeLimit: 10, 
            answers: @json($answers) 
        })" 
        x-init="startTimer()" 
        class="relative"
    >
        <pre>{{ json_encode($answers) }}</pre>
        <div class="flex justify-between items-center mb-2">
            <div>
                <h3 class="text-lg font-medium">{{ $question->question }}</h3>
            </div>
            <div class="flex items-center gap-2 text-amber-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"/><path d="M12 6v6l4 2" stroke-width="2"/></svg>
                <span class="font-medium" x-text="timeLeft + 's'"></span>
            </div>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
            <div class="bg-amber-500 h-2 rounded-full" :style="`width: ${(timeLeft/timeLimit)*100}%`"></div>
        </div>
        <form @submit.prevent="submitAnswer" class="space-y-3">
            <template x-if="answers.length === 0">
                <div class="text-red-600">No answers available for this question.</div>
            </template>
            <template x-for="(answer, idx) in answers" :key="idx">
                <div 
                    class="flex items-center space-x-2 rounded-lg border p-3 transition-colors"
                    :class="{
                        'border-green-500 bg-green-50': isAnswered && answer === correct,
                        'border-red-500 bg-red-50': isAnswered && answer === selected && answer !== correct,
                        'border-blue-500 bg-blue-50': !isAnswered && selected === answer
                    }"
                >
                    <input type="radio" :id="'option-'+idx" :value="answer" x-model="selected" :disabled="isAnswered" class="sr-only" />
                    <label :for="'option-'+idx" class="flex flex-1 items-center justify-between cursor-pointer">
                        <span x-text="answer"></span>
                        <template x-if="isAnswered && answer === correct">
                            <svg class="h-5 w-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2"/></svg>
                        </template>
                        <template x-if="isAnswered && answer === selected && answer !== correct">
                            <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2"/></svg>
                        </template>
                    </label>
                </div>
            </template>
            <button type="submit" class="w-full bg-slate-600 text-white py-2 rounded mt-4 hover:bg-slate-500 transition-colors" :disabled="!selected || isAnswered">Submit Answer</button>
        </form>
        <template x-if="isAnswered">
            <div class="mt-4 w-full text-center space-y-3">
                <div x-show="result === 'correct'" class="bg-green-50 p-4 rounded-lg">
                    <h3 class="font-bold text-green-700 flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7" stroke-width="2"/></svg>
                        Correct!
                    </h3>
                    <p class="text-green-600">You earned 10 points!</p>
                </div>
                <div x-show="result === 'incorrect'" class="bg-red-50 p-4 rounded-lg">
                    <h3 class="font-bold text-red-700 flex items-center justify-center gap-2">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2"/></svg>
                        Incorrect
                    </h3>
                    <p class="text-red-600">Better luck next time!</p>
                </div>
            </div>
        </template>
    </div>
    <script>
    function questionComponent({ correct, timeLimit, answers }) {
        return {
            answers: answers,
            correct: correct,
            selected: '',
            isAnswered: false,
            result: null,
            points: 0,
            timeLimit: timeLimit,
            timeLeft: timeLimit,
            timer: null,
            startTimer() {
                if (this.timer) clearInterval(this.timer);
                this.timeLeft = this.timeLimit;
                this.timer = setInterval(() => {
                    if (!this.isAnswered && this.timeLeft > 0) {
                        this.timeLeft--;
                    }
                    if (this.timeLeft === 0 && !this.isAnswered) {
                        this.submitAnswer();
                    }
                }, 1000);
            },
            submitAnswer() {
                this.isAnswered = true;
                clearInterval(this.timer);
                if (this.selected === this.correct) {
                    this.result = 'correct';
                    this.points = Math.max(10, 10 + this.timeLeft);
                } else {
                    this.result = 'incorrect';
                }
            }
        }
    }
    </script>
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


