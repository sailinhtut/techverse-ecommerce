@extends('layouts.web')

@section('web_content')
    <div class="min-h-[70vh] flex items-center justify-center px-4">
        <div class="w-full max-w-md bg-base-100 border border-base-300 rounded-none p-6 md:p-8 shadow-sm">

            <div class="mb-6 text-center">
                <h1 class="text-xl font-bold">Forgot Password</h1>
                <p class="text-sm text-base-content/70 mt-1">
                    Enter the email associated with your account and we’ll send you a reset link.
                </p>
            </div>

            <form action="{{ route('forgot-password.post') }}" method="POST" class="flex flex-col gap-4" x-data="{ submitting: false }"
                @submit="submitting=true">
                @csrf

                <div>
                    <label class="block text-sm font-medium mb-1">Email Address</label>
                    <input class="input input-bordered w-full" type="email" name="email" placeholder="your@email.com"
                        required>
                </div>

                 <button type="submit" class="btn btn-primary w-full" :disabled="submitting">
                    <span x-show="submitting" class="loading loading-spinner loading-sm mr-2"></span>
                    <span x-show="submitting">Sending Reset Email</span>
                    <span x-show="!submitting">
                        Send Reset Link
                    </span>
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
