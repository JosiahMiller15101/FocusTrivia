<x-layout>
  <x-slot:heading>{{ $user->first_name }}'s Profile Comments</x-slot:heading>

  <div class="space-y-3 p-4 bg-white ring-2 ring-gray-400 rounded shadow-md">
    @forelse($comments as $comment)
      <div class="p-4 bg-white rounded mb-3 ring-2 ring-gray-400 flex gap-3 relative border-l-4 border-blue-600 pl-3">
        {{-- Author profile image for main comment --}}
        <div>
          @php
            $profileImage = $comment->author->profile_image ?? null;
            $isAbsolute = $profileImage && Str::startsWith($profileImage, ['http://', 'https://']);
          @endphp
          @if($profileImage)
            <img src="{{ $isAbsolute ? $profileImage : asset('storage/' . $profileImage) }}" alt="Profile" class="w-12 h-12 rounded-full object-cover">
          @else
            <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-xl text-gray-400">
              <svg xmlns='http://www.w3.org/2000/svg' class='h-6 w-6' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z' /></svg>
            </div>
          @endif
        </div>
        <div class="flex-1">
          <div class="text-sm text-gray-700 font-semibold mb-1 flex items-center justify-between relative">
            <span>{{ $comment->author->first_name }} {{ $comment->author->last_name }}
              <span class="text-xs text-gray-500">&bull; {{ $comment->created_at->diffForHumans() }}</span>
              <span>@if(Auth::id() === $comment->author_id)
                    <form method="POST" action="{{ route('profile.comment.delete', $comment->id) }}" class="absolute top-2 right-2">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-xs text-red-600 rounded hover:underline ml-2">Delete</button>
                    </form>
                  @endif</span>
            </span>
          </div>
          <div class="text-gray-900 mb-2">{{ $comment->comment }}</div>
          <div class="ml-8 space-y-3">
            @foreach($comment->replies as $reply)
              <div class="flex gap-3 p-3 bg-white shadow-md rounded ring-2 ring-gray-400 relative border-l-4 border-blue-600 pl-3">
                {{-- Author profile image for reply --}}
                <div>
                  @php
                    $replyProfileImage = $reply->author->profile_image ?? null;
                    $replyIsAbsolute = $replyProfileImage && Str::startsWith($replyProfileImage, ['http://', 'https://']);
                  @endphp
                  @if($replyProfileImage)
                    <img src="{{ $replyIsAbsolute ? $replyProfileImage : asset('storage/' . $replyProfileImage) }}" alt="Profile" class="w-12 h-12 rounded-full object-cover">
                  @else
                    <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-xl text-gray-400">
                      <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z' /></svg>
                    </div>
                  @endif
                </div>
                <div class="flex-1">
                  <div class="text-sm text-gray-700 font-semibold mb-1 flex items-center justify-between">
                    <span>{{ $reply->author->first_name }} {{ $reply->author->last_name }}
                      <span class="text-xs text-gray-500">&bull; {{ $reply->created_at->diffForHumans() }}</span>
                    <span>
                      @if(Auth::id() === $reply->author_id)
                        <form method="POST" action="{{ route('profile.comment.delete', $reply->id) }}" class="absolute top-2 right-2">
                          @csrf
                          @method('DELETE')
                          <button type="submit" class="text-xs text-red-600 rounded hover:underline ml-2">Delete</button>
                        </form>
                      @endif
                    </span>
                    </span>
                  </div>
                  <div class="text-gray-900 mb-2">{{ $reply->comment }}</div>
                  <div class="mt-2 relative" style="min-height:2.5rem;">
                  <button onclick="document.getElementById('reply-form-{{ $reply->id }}').classList.toggle('hidden')" class="text-xs text-blue-600 hover:underline absolute bottom-2 right-1 ml-2">Reply</button>
                    <form id="reply-form-{{ $reply->id }}" method="POST" action="{{ route('profile.comment', ['user' => $user->id]) }}" class="hidden mt-2">
                      @csrf
                      <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                      <textarea name="comment" class="w-full p-2 border rounded" rows="2" placeholder="Reply..."></textarea>
                      <button class="px-2 py-1 bg-slate-600 text-white rounded mt-1">Post</button>
                    </form>
                  </div>
                  {{-- Recursive replies --}}
                  @if($reply->replies && $reply->replies->count())
                    <div class="ml-8 space-y-3">
                      @foreach($reply->replies as $childReply)
                        <div class="flex gap-3 p-3 bg-white rounded shadow-md ring-2 ring-gray-400 relative border-l-4 border-blue-600 pl-3">
                          <div>
                            @php
                              $childProfileImage = $childReply->author->profile_image ?? null;
                              $childIsAbsolute = $childProfileImage && Str::startsWith($childProfileImage, ['http://', 'https://']);
                            @endphp
                            @if($childProfileImage)
                              <img src="{{ $childIsAbsolute ? $childProfileImage : asset('storage/' . $childProfileImage) }}" alt="Profile" class="w-12 h-12 rounded-full object-cover">
                            @else
                              <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-xl text-gray-400">
                                <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z' /></svg>
                              </div>
                            @endif
                          </div>
                          <div class="flex-1">
                            <div class="text-sm text-gray-700 font-semibold mb-1 flex items-center justify-between">
                              <span>{{ $childReply->author->first_name }} {{ $childReply->author->last_name }}
                                <span class="text-xs text-gray-500">&bull; {{ $childReply->created_at->diffForHumans() }}</span>
                              <span>
                                @if(Auth::id() === $childReply->author_id)
                                  <form method="POST" action="{{ route('profile.comment.delete', $childReply->id) }}" class="absolute top-2 right-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 rounded hover:underline ml-2">Delete</button>
                                  </form>
                                @endif
                              </span>
                              </span>
                            </div>
                            <div class="text-gray-900 mb-2">{{ $childReply->comment }}</div>
                            <div class="mt-2 relative" style="min-height:2.5rem;">
                                <button onclick="document.getElementById('reply-form-{{ $reply->id }}').classList.toggle('hidden')" class="text-xs text-blue-600 hover:underline absolute bottom-2 right-1 ml-2">Reply</button>
                              <form id="reply-form-{{ $childReply->id }}" method="POST" action="{{ route('profile.comment', ['user' => $user->id]) }}" class="hidden mt-2">
                                @csrf
                                <input type="hidden" name="parent_id" value="{{ $childReply->id }}">
                                <textarea name="comment" class="w-full p-2 border rounded" rows="2" placeholder="Reply..."></textarea>
                                <button class="px-2 py-1 bg-slate-600 text-white rounded mt-1">Post</button>
                              </form>
                            </div>
                            {{-- Recursive: render further nested replies --}}
                            @if($childReply->replies && $childReply->replies->count())
                              <div class="ml-8 space-y-3">
                                @foreach($childReply->replies as $grandChildReply)
                                  <div class="flex gap-3 p-3 bg-white rounded ring-1 ring-gray-400 relative border-l-4 border-blue-600 pl-3">
                                    <div>
                                      @php
                                        $grandChildProfileImage = $grandChildReply->author->profile_image ?? null;
                                        $grandChildIsAbsolute = $grandChildProfileImage && Str::startsWith($grandChildProfileImage, ['http://', 'https://']);
                                      @endphp
                                      @if($grandChildProfileImage)
                                        <img src="{{ $grandChildIsAbsolute ? $grandChildProfileImage : asset('storage/' . $grandChildProfileImage) }}" alt="Profile" class="w-12 h-12 rounded-full object-cover">
                                      @else
                                        <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center text-xl text-gray-400">
                                          <svg xmlns='http://www.w3.org/2000/svg' class='h-5 w-5' fill='none' viewBox='0 0 24 24' stroke='currentColor'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5.121 17.804A13.937 13.937 0 0112 15c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z' /></svg>
                                        </div>
                                      @endif
                                    </div>
                                    <div class="flex-1">
                                      <div class="text-sm text-gray-700 font-semibold mb-1 flex items-center justify-between">
                                        <span>{{ $grandChildReply->author->first_name }} {{ $grandChildReply->author->last_name }}
                                          <span class="text-xs text-gray-500">&bull; {{ $grandChildReply->created_at->diffForHumans() }}</span>
                                        <span>
                                          @if(Auth::id() === $grandChildReply->author_id)
                                            <form method="POST" action="{{ route('profile.comment.delete', $grandChildReply->id) }}" class="absolute top-2 right-2">
                                              @csrf
                                              @method('DELETE')
                                              <button type="submit" class="text-xs text-red-600 rounded hover:underline ml-2">Delete</button>
                                            </form>
                                          @endif
                                        </span>
                                        </span>
                                      </div>
                                      <div class="text-gray-900 mb-2">{{ $grandChildReply->comment }}</div>
                                      <div class="mt-2 relative" style="min-height:2.5rem;">
                                          <button onclick="document.getElementById('reply-form-{{ $reply->id }}').classList.toggle('hidden')" class="text-xs text-blue-600 hover:underline absolute ml-2">Reply</button>
                                        <form id="reply-form-{{ $grandChildReply->id }}" method="POST" action="{{ route('profile.comment', ['user' => $user->id]) }}" class="hidden mt-2">
                                          @csrf
                                          <input type="hidden" name="parent_id" value="{{ $grandChildReply->id }}">
                                          <textarea name="comment" class="w-full p-2 border rounded" rows="2" placeholder="Reply..."></textarea>
                                          <button class="px-2 py-1 bg-slate-600 text-white rounded mt-1">Post</button>
                                        </form>
                                      </div>
                                      {{-- You can continue recursion for deeper levels if desired --}}
                                    </div>
                                  </div>
                                @endforeach
                              </div>
                            @endif
                          </div>
                        </div>
                      @endforeach
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
          <div class="mt-2 relative" style="min-height:2.5rem;">
              <button onclick="document.getElementById('reply-form-{{ $comment->id }}').classList.toggle('hidden')" class="text-xs text-blue-600 hover:underline absolute bottom-2 right-1 ml-2">Reply</button>
            <form id="reply-form-{{ $comment->id }}" method="POST" action="{{ route('profile.comment', ['user' => $user->id]) }}" class="hidden mt-2">
              @csrf
              <input type="hidden" name="parent_id" value="{{ $comment->id }}">
              <textarea name="comment" class="w-full p-2 border rounded" rows="2" placeholder="Reply..."></textarea>
              <button class="px-2 py-1 bg-slate-600 text-white rounded mt-1">Post</button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="p-6 bg-white dark:bg-gray-800 rounded shadow-md text-gray-500 text-center">
        No profile comments yet.
      </div>
    @endforelse
    <div class="mt-4">
      {{ $comments->links() }}
    </div>
  </div>
</x-layout>