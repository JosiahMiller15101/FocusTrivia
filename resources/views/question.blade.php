<x-layout>
  <x-slot:heading>
    Daily Question
  </x-slot:heading>

  <div class="p-8 bg-white rounded shadow-lg ring-2 ring-black max-w-2xl mx-auto mt-10">
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
    Question has been answered for today. Come back tomorrow for another.
  </div>
@else
  <form method="POST" action="/submit-answer">
    @csrf
    <input type="hidden" name="question_id" value="{{ $question->id }}">

    @foreach($answers as $answer)
      <label class="flex items-center space-x-2">
        <input type="radio" name="answer" value="{{ $answer }}" required>
        <span>{{ $answer }}</span>
      </label>
    @endforeach

    <div class="mt-4 flex justify-end">
      <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
        Submit
      </button>
    </div>
  </form>
@endif

  </div>
</x-layout>
