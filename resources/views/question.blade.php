@php
  use Carbon\Carbon;
@endphp
<x-layout>
  <x-slot:heading>
    Daily Question
  </x-slot:heading>

  <div class="p-6 bg-white rounded-lg shadow ring-2 ring-gray-400">
    <p class="text-gray-600 text-sm mb-2">Category: {{ $question->category }}</p>

    @if(session('success'))
      <div class="p-4 bg-green-100 text-green-800 rounded mb-4">
        {{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="p-4 bg-red-100 text-red-800 rounded mb-4">
        {{ session('error') }}
      </div>
    @endif

    @php 
    $nowLocal = Carbon::now('America/Denver');
    $halfOfDay  = (int) floor($nowLocal->hour / 12);
    if ($nowLocal->hour < 12) {
        $nextQuestionTime = Carbon::today($nowLocal->timezone)->addHours(12);
    } else {
        $nextQuestionTime = Carbon::tomorrow($nowLocal->timezone)->addHours(12);
    }
    $diffSeconds = $nowLocal->diffInSeconds($nextQuestionTime, false);
    if ($diffSeconds > 0) {
        $hours = floor($diffSeconds / 3600);
        $minutes = floor(($diffSeconds % 3600) / 60);
        $seconds = $diffSeconds % 60;
        $timeUntilNext = sprintf('%02dh %02dm %02ds', $hours, $minutes, $seconds);
    } else {
        $timeUntilNext = 'Now!';
    }
    @endphp
    
    @if($alreadySubmitted)
      <div class="p-4 bg-yellow-100 text-yellow-800 rounded">
        <p>Question has already been answered. Questions reset at 12AM and 12PM, see you then!</p>
        <p>Time until next question: {{ $timeUntilNext }}</p>
      </div>

      {{-- Comments --}}
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
            $reactionTypes = ['like' => '👍', 'crying' => '😢', 'dislike' => '👎', 'angry' => '😡', 'laughing' => '😂'];
          @endphp

          @forelse($comments as $comment)
            <div class="p-3 pb-12 bg-gray-100 rounded shadow relative">
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
                  @foreach($reactionTypes as $type => $emoji)
                    <button
                      class="reaction-btn hover:-translate-y-1 cursor-pointer hover:scale-105 bg-white dark:bg-[#191818] rounded-full p-0.5 px-1 text-sm"
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
            </div>
          @empty
            <div class="text-gray-500">No comments yet. Be the first to comment!</div>
          @endforelse
        </div>
      </div>
    @else
      @if(session('error'))
        <div class="p-4 bg-red-100 text-red-800 rounded mb-4">
          {{ session('error') }}
        </div>
      @endif
      {{-- Timer and Form --}}
      <div class="mt-4">
        <h3 class="text-lg font-medium mb-4">{{ $question->question }}</h3>

        <div id="timer-box" class="mb-4">
          <div class="flex items-center space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm text-gray-500">Time Remaining:</span>
            <span id="time-left" class="text-lg font-bold text-amber-600">10</span>
          </div>
          <div class="w-full h-2 mt-2 bg-gray-200 rounded-full overflow-hidden">
            <div id="timer-progress" class="h-full bg-amber-500 transition-all duration-1000 ease-linear" style="width: 100%"></div>
          </div>
        </div>

        <form id="question-form" method="POST" action="/question/submit" class="space-y-3">
          @csrf
          <input type="hidden" name="question_id" value="{{ $question->id }}">
          @foreach($answers as $answer)
            @php
              $isCorrect = session('submitted') && session('correct_answer') === $answer;
              $isIncorrect = session('submitted') && session('selected_answer') === $answer && !session('is_correct');
            @endphp
            <div class="flex items-center space-x-2 rounded-lg border p-3 transition-colors
              @if($isCorrect) border-green-500 bg-green-50
              @elseif($isIncorrect) border-red-500 bg-red-50
              @endif">
              <input type="radio" id="option-{{ $loop->index }}" name="answer" value="{{ $answer }}" class="form-radio text-slate-600" required>
              <label for="option-{{ $loop->index }}" class="flex-1 cursor-pointer">{{ $answer }}</label>
            </div>
          @endforeach

          <button id="submit-button" type="submit" class="w-full bg-slate-600 text-white py-2 rounded mt-4 hover:bg-slate-500 transition-colors">Submit Answer</button>
        </form>
      </div>
    @endif
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

    document.addEventListener('DOMContentLoaded', () => {
      const timerEl = document.getElementById('time-left');
      const form = document.getElementById('question-form');
      const progressBar = document.getElementById('timer-progress');

      let timeLeft = 10;
      const totalTime = 10;

      if (!timerEl || !form || !progressBar) return;

      const countdown = setInterval(() => {
        timeLeft--;
        timerEl.textContent = timeLeft;

        const percent = (timeLeft / totalTime) * 100;
        progressBar.style.width = percent + '%';

        if (timeLeft <= 0) {
          clearInterval(countdown);
          if (!form.classList.contains('submitted')) {
            form.submit();
          }
        }
      }, 1000);
    });
  </script>

  @if(session('submitted'))
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('input[type="radio"]').forEach(el => el.disabled = true);
        document.getElementById('submit-button')?.setAttribute('disabled', 'true');
      });
    </script>
  @endif
  @endsection
</x-layout>
