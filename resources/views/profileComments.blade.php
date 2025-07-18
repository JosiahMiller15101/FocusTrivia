<x-layout>
  <x-slot:heading>{{ $user->first_name }}'s Profile Comments</x-slot:heading>

  <div class="space-y-3 p-4 bg-white ring-2 ring-gray-400 rounded shadow-lg">
    @forelse($comments as $comment)
      <div class="flex items-start gap-3 p-4 bg-white shadow-lg border-l-4 ring-2 ring-gray-400 border-blue-500 rounded hover:shadow-md transition-all">
        {{-- Author profile image --}}
        <div>
          @php
            $profileImage = $comment->author->profile_image ?? null;
            $isAbsolute = $profileImage && Str::startsWith($profileImage, ['http://', 'https://']);
          @endphp
          @if($profileImage)
            <img src="{{ $isAbsolute ? $profileImage : asset('storage/' . $profileImage) }}" alt="Profile" class="w-14 h-14 rounded-full object-cover">
          @else
            <div class="w-14 h-14 rounded-full bg-gray-200 flex items-center justify-center text-xl text-gray-400">
              <svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z' /></svg>
            </div>
          @endif
        </div>
        <div class="flex-1">
          <div class="font-semibold text-gray-800">{{ $comment->author->first_name }} {{ $comment->author->last_name }}</div>
          <p class="text-sm text-black mt-1">{{ $comment->comment }}</p>
          <p class="text-xs text-gray-600 mt-1">{{ $comment->created_at->diffForHumans() }}</p>
        </div>
      </div>
    @empty
      <div class="p-6 bg-white dark:bg-gray-800 rounded shadow text-gray-500 text-center">
        No profile comments yet.
      </div>
    @endforelse
    <div class="mt-4">
      {{ $comments->links() }}
    </div>
  </div>
</x-layout>