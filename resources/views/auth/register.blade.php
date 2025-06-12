<x-layout>
    <x-slot:heading>
        Register
    </x-slot:heading>

<form method="POST" action="/register">
    @csrf
  <div class="space-y-12">
    <div class="border-b border-gray-900/10 pb-12">
      <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">

          <x-form-field>
          <x-form-label for="department">Department</x-form-label>
            <select name="department" id="department">
              <option value="Guest">Guest</option>
              <option value="Accounting">Accounting</option>
              <option value="DigitalContent">Digital Content</option>
              <option value="DonorCommunications">Donor Communications</option>
              <option value="Events">Events</option>
              <option value="Finance">Finance</option>
              <option value="HR">H.R.</option>
              <option value="IT">I.T.</option>
              <option value="Marketing">Marketing</option>
              <option value="Other">Media Operations</option>
              <option value="Other">Missions</option>
              <option value="QGI">QGI</option>
              <option value="Other">Other</option>
            </select>
        </x-form-field>

        <x-form-field>
          <x-form-label for="first_name">First Name</x-form-label>
          <div class="mt-2">
            <x-form-input name="first_name" id="first_name" required></x-form-input>
            <x-form-error name="first_name"></x-form-error>
          </div>
        </x-form-field>

        <x-form-field>
          <x-form-label for="last_name">Last Name</x-form-label>
          <div class="mt-2">
            <x-form-input name="last_name" id="last_name" required></x-form-input>
            <x-form-error name="last_name"></x-form-error>
          </div>
        </x-form-field>

        <x-form-field>
          <x-form-label for="email">Email Address</x-form-label>
          <div class="mt-2">
            <x-form-input name="email" id="email" type="email" required></x-form-input>
            <x-form-error name="email"></x-form-error>
          </div>
        </x-form-field>

        <x-form-field>
          <x-form-label for="password">Password</x-form-label>
          <div class="mt-2">
            <x-form-input name="password" id="password" type="password" required></x-form-input>
            <x-form-error name="password"></x-form-error>
          </div>
        </x-form-field>

        <x-form-field>
          <x-form-label for="password_confirmation">Confirm Password</x-form-label>
          <div class="mt-2">
            <x-form-input name="password_confirmation" id="password_confirmation" type="password" required></x-form-input>
            <x-form-error name="password_confirmation"></x-form-error>
          </div>
        </x-form-field>
      </div>
    </div>
  </div>
  <div class="mt-6 flex items-center justify-end gap-x-6">
    <a href="/" class="text-sm/6 font-semibold text-gray-900">Cancel</a>
    <x-form-button>Register</x-form-button>
  </div>
  <div class="mt-6 flex items-center justify-center">
    <a href="/login/google" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-sm font-semibold text-white rounded-md shadow-sm hover:bg-indigo-500 focus-visible:outline-indigo-600">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M21.805 10.023h-9.765v3.977h5.617c-.242 1.242-1.484 3.648-5.617 3.648-3.383 0-6.148-2.797-6.148-6.25s2.765-6.25 6.148-6.25c1.93 0 3.227.82 3.969 1.523l2.711-2.633c-1.711-1.57-3.906-2.523-6.68-2.523-5.523 0-10 4.477-10 10s4.477 10 10 10c5.742 0 9.547-4.023 9.547-9.695 0-.652-.07-1.148-.156-1.652z"/></svg>
        Sign up with Google
    </a>
      <a href="/login/youtube" class="inline-flex items-center px-4 py-2 bg-red-600 text-sm font-semibold text-white rounded-md shadow-sm hover:bg-red-700 focus-visible:outline-red-600">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24"><path d="M21.8 8.001c-.2-1.5-1.5-2.7-3-2.9C16.2 4.7 12 4.7 12 4.7s-4.2 0-6.8.4c-1.5.2-2.8 1.4-3 2.9C2 9.6 2 12 2 12s0 2.4.2 3.999c.2 1.5 1.5 2.7 3 2.9C7.8 19.3 12 19.3 12 19.3s4.2 0 6.8-.4c1.5-.2 2.8-1.4 3-2.9.2-1.6.2-3.999.2-3.999s0-2.4-.2-3.999zM9.8 15.2V8.8l6.4 3.2-6.4 3.2z"/></svg>
        Sign up with YouTube
    </a>
</div>
</form>
</x-layout>