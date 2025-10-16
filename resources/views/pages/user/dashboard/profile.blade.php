@extends('layouts.user.user_dashboard')

@section('user_dashboard_content')
    <div class="p-3 lg:p-5">
        <p class="text-base lg:text-lg font-semibold">Profile</p>
        <img src="{{ auth()->user()->jsonResponse()['profile'] ?? asset('assets/images/blank_profile.png') }}"
            class="size-32 rounded-full border border-slate-300 object-cover object-center" />
        <p class="text-sm">Welcome, {{ auth()->user()->name }}</p>
        <form action="{{ route('profile.update.post', auth()->user()->id) }}" method="POST" class="flex flex-col"
            enctype="multipart/form-data">
            @csrf
            <input type="file" name="profile" class="file-input" placeholder="Choose Profile" required>
            <button class="btn btn-primary w-fit">Update Profile</button>
        </form>
        <form action="{{ route('profile.update.post', auth()->user()->id) }}" method="POST" class="flex flex-col">
            @csrf
            <input type="hidden" name="remove_profile" value="1">
            <button class="btn btn-primary w-fit">Remove Profile</button>
        </form>
        <form action="{{ route('profile.update.post', auth()->user()->id) }}" method="POST" class="flex flex-col">
            @csrf
            <input type="text" name="name" class="input input-sm" placeholder="Username"
                value="{{ auth()->user()->name }}">
            <input type="text" name="email" class="input input-sm" placeholder="Email"
                value="{{ auth()->user()->email }}" readonly>
            <input type="text" name="phone_one" class="input input-sm" placeholder="Phone 1"
                value="{{ old('phone_one', auth()->user()->phone_one) }}">
            <input type="text" name="phone_two" class="input input-sm" placeholder="Phone 2"
                value="{{ old('phone_two', auth()->user()->phone_two) }}">
            <button class="btn btn-primary w-fit">Save</button>
        </form>

        @if (auth()->user()->email_verified_at)
            <p>Your Email is verified</p>
        @else
            <div>
                <p>Your Email is not verified. Plese verify now.</p>
                <form method="POST" action="{{ route('email.resend.post') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary w-fit">Send Email</button>
                </form>
            </div>
        @endif

        <div>
            <p>Forgot Password ? Reset Your Passowrd Now.</p>
            <form method="POST" action="{{ route('forgot-password.get') }}">
                @csrf
                <input type="hidden" name="email" value="{{ auth()->user()->email }}">
                <button type="submit" class="btn btn-primary w-fit">Send Reset Password Email</button>
            </form>
        </div>

        <div>
            <p>Delete Account</p>
            <p>Deleting account is irreversible and it can lost all your data. Please continue with caution.</p>

            <button onclick="delete_account_modal.showModal()" type="submit" class="btn btn-error btn-outline w-fit">Delete
                Account</button>

            <dialog class="modal" id="delete_account_modal">
                <div class="modal-box">
                    <form method="dialog">
                        <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
                    </form>
                    <p class="font-semibold py-0">Delete Account</p>
                    <p class="py-2 mb-0">Are you sure to delete account ?</p>
                    <div class="modal-action mt-0">
                        <form method="dialog">
                            <button class="btn">Close</button>
                        </form>
                        {{-- <form method="POST" action="{{ route('logout.post') }}">
                            @csrf
                            <button type="submit" class="btn btn-error">Logout</button>
                        </form> --}}
                        <form action="{{ route('profile.delete.delete', auth()->user()->id) }}" method="POST">
                            @csrf
                            @method('delete')
                            <button type="submit" class="btn btn-error">Delete Account</button>
                        </form>
                    </div>
                </div>
            </dialog>
        </div>
    </div>
@endsection
