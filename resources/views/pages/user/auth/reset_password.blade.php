@extends('layouts.web')

@section('web_content')
    <div class="min-h-[70vh] flex items-center justify-center px-4">
        <div class="w-full max-w-md bg-base-100 border border-base-300 rounded-xl p-6 md:p-8 shadow-sm">

            <div class="mb-6 text-center">
                <h1 class="text-xl font-bold">Reset Password</h1>
                <p class="text-sm text-base-content/70 mt-1">
                    Enter your new password below to reset your account.
                </p>
            </div>

            <form action="{{ route('reset-password.post') }}" method="POST" class="flex flex-col gap-4">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <input type="hidden" name="token" value="{{ $token }}">

                <div>
                    <label class="block text-sm font-medium mb-1">New Password</label>
                    <input class="input input-bordered w-full" type="password" name="password"
                        placeholder="Enter new password" required>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Confirm Password</label>
                    <input class="input input-bordered w-full" type="password" name="password_confirmation"
                        placeholder="Confirm new password" required>
                </div>

                <button type="submit" class="btn btn-primary w-full">
                    Update Password
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-primary hover:underline">
                    Back to login
                </a>
            </div>
        </div>
    </div>
@endsection
