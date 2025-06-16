<x-layout>
  <x-slot:heading>
    Daily Question
  </x-slot:heading>

  <div class="p-6 bg-white rounded-lg shadow ring-2 ring-black">
    <p class="text-gray-600 text-sm mb-2">Category: {{ $question->category }}</p>
    <h2 class="text-xl font-semibold text-black mb-4">{{ $question->question }}</h2>

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
      <textarea name="comment" rows="2" class="w-full border rounded p-2 mb-2" placeholder="Leave a comment, let me know how angry you are at me..." required></textarea>
      <button type="submit" class="px-4 py-2 bg-slate-600 text-white rounded hover:bg-slate-500">Post Comment</button>
    </form>
    <div class="space-y-4">
      @forelse($comments as $comment)
        <div class="p-3 bg-gray-100 rounded shadow">
          <div class="text-sm text-gray-700 font-semibold mb-1">{{ $comment->user->first_name }} {{ $comment->user->last_name }} <span class="text-xs text-gray-500">&bull; {{ $comment->created_at->diffForHumans() }}</span></div>
          <div class="text-gray-900">{{ $comment->comment }}</div>
        </div>
      @empty
        <div class="text-gray-500">No comments yet. Be the first to comment!</div>
      @endforelse
    </div>
  </div>
@else
  <form method="POST" action="/submit-answer" id="answerForm">
    @csrf
    <input type="hidden" name="question_id" value="{{ $question->id }}">

    @foreach($answers as $answer)
      <label class="flex items-center space-x-2">
        <input type="radio" name="answer" value="{{ $answer }}" required class="accent-slate-600 focus:ring-slate-500 focus:outline-none">
        <span>{{ $answer }}</span>
      </label>
    @endforeach

    <div class="mt-4 flex items-center w-full">
      <div class="flex-1 flex justify-center">
        <span class="text-sm text-red-600 font-semibold text-center">Please press the submit button only once, even if the page takes a second to load.</span>
      </div>
      <button type="submit" class="shadow-lg px-4 py-2 bg-slate-600 text-white rounded hover:bg-slate-500 ml-4" id="submitBtn">
        Submit
      </button>
    </div>
  </form>
  <script>
        let formSubmitted = false;
    document.getElementById('answerForm')?.addEventListener('submit', function(e) {
      if (formSubmitted) {
        e.preventDefault(); // block any additional submits
        return;
      }
      formSubmitted = true;
      const btn = document.getElementById('submitBtn');
      if (btn) {
        btn.disabled = true;
        btn.innerText = 'Submitting...';
      }
    });
  </script>
@endif
  </div>
</x-layout>
