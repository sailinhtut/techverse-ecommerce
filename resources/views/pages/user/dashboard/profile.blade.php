@extends('layouts.user.user_dashboard')

@section('user_dashboard_content')
    <div class="p-4 lg:p-6 max-w-3xl">

        {{-- Page Title --}}
        <div class="mb-5">
            <p class="lg:text-lg font-semibold">Your Profile</p>
            <p class="text-sm text-slate-500">Manage your personal information and account settings.</p>
        </div>

        {{-- Profile Photo Card --}}
        <div class="border border-base-300 rounded-md p-4" x-data="{ preview: '{{ auth()->user()->jsonResponse()['profile'] ?? asset('assets/images/blank_profile.png') }}' }">

            <div class="flex items-start gap-5">
                {{-- Profile Image --}}
                <div class="avatar">
                    <div class="w-28 rounded-full border border-slate-300">
                        <img :src="preview" class="object-cover object-center" />
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <p class="font-medium text-base">Welcome, {{ auth()->user()->name }}</p>

                    <div class="flex flex-col gap-2">
                        <form action="{{ route('profile.update.post', auth()->user()->id) }}" method="POST"
                            enctype="multipart/form-data" x-data>
                            @csrf

                            <button type="button" class="btn btn-xs w-fit" @click="$refs.profileInput.click()">
                                Choose & Update Photo
                            </button>

                            <input type="file" name="profile" class="hidden" accept="image/*" x-ref="profileInput"
                                @change="$el.form.submit()">
                        </form>


                        <form action="{{ route('profile.update.post', auth()->user()->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="remove_profile" value="1">
                            <button class="btn btn-error btn-outline btn-xs w-fit">Remove Photo</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>

        {{-- Profile Info --}}
        <div class="border border-base-300 rounded-md p-4 mt-6">
            <h2 class="font-semibold mb-4">Personal Information</h2>

            <form action="{{ route('profile.update.post', auth()->user()->id) }}" method="POST" class="grid gap-4">
                @csrf

                <input type="text" name="name" class="input input-bordered input-sm" placeholder="Username"
                    value="{{ auth()->user()->name }}">

                <input type="text" name="email" class="input input-bordered input-sm" placeholder="Email"
                    value="{{ auth()->user()->email }}" readonly>

                <input type="text" name="phone_one" class="input input-bordered input-sm" placeholder="Phone 1"
                    value="{{ old('phone_one', auth()->user()->phone_one) }}">

                <input type="text" name="phone_two" class="input input-bordered input-sm" placeholder="Phone 2"
                    value="{{ old('phone_two', auth()->user()->phone_two) }}">

                <button class="btn btn-primary btn-sm w-fit">Save Changes</button>
            </form>
        </div>

        {{-- Email Verification --}}
        <div class="border border-base-300 rounded-md p-4 mt-6">
            <h2 class="font-semibold">Email Verification</h2>

            @if (auth()->user()->email_verified_at)
                <p class="text-success mt-2 text-sm">Your email is verified ✔</p>
            @else
                <p class="mt-2 text-sm text-error">Your email is not verified.</p>
                <form method="POST" action="{{ route('email.resend.post') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm w-fit mt-3">Send Verification Email</button>
                </form>
            @endif
        </div>

        {{-- Password Reset --}}
        <div class="border border-base-300 rounded-md p-4 mt-6">
            <h2 class="font-semibold">Reset Password</h2>
            <p class="text-sm mt-1">Forgot your password? Receive a reset link.</p>

            <form method="POST" action="{{ route('forgot-password.get') }}" class="mt-3">
                @csrf
                <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                <button type="submit" class="btn btn-primary btn-sm w-fit">Send Reset Link</button>
            </form>
        </div>

        {{-- Delete Account --}}
        <div class="border border-base-300 rounded-md p-4 mt-6">
            <h2 class="font-semibold text-error">Delete Account</h2>
            <p class="text-sm mt-2 mb-4">
                Deleting your account is permanent and cannot be undone.
            </p>

            <button onclick="delete_account_modal.showModal()" class="btn btn-error btn-outline btn-sm w-fit">
                Delete Account
            </button>

            <dialog class="modal" id="delete_account_modal">
                <div class="modal-box border border-base-300 rounded-md">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>

                    <h3 class="font-semibold text-lg mb-2">Confirm Deletion</h3>
                    <p class="text-sm mb-3">Are you sure you want to delete your account?</p>

                    <div class="modal-action mt-0">
                        <form method="dialog">
                            <button class="btn btn-sm">Cancel</button>
                        </form>

                        <form action="{{ route('profile.delete.delete', auth()->user()->id) }}" method="POST">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-error btn-sm">Delete</button>
                        </form>
                    </div>
                </div>
            </dialog>
        </div>

    </div>
@endsection
