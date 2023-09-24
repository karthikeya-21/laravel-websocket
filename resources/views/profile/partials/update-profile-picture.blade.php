<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Update Profile Picture') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile picture.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}"  enctype="multipart/form-data">
        @csrf
    </form>

    <form method="post" action="{{ route('avatar.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <img src="{{ '/storage/' . $user->user_image }}" width=300px height=400px >
        @if(session()->has('message'))
    <div class="alert alert-success">
        <p style="color:white;">{{ session('message') }}</p>
    </div>
@endif
        <div>
        <x-input-label for="image" :value="__('Image')" />
            <x-file-input id="image" class="block mt-1 w-full" type="file"  name="image" :value="old('image',$user->user_image)" autofocus autocomplete="image" />
            <x-input-error :messages="$errors->get('image')" class="mt-2" />
        </div>


        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
