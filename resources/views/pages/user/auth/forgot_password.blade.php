@extends('layouts.web')

@section('web_content')
    <div>
        <p>Forgot Password</p>
        <p>If you forget your account password, don't be panic. Enter your account email and reset it real quick.</p>
        <form action="{{ route('forgot-password.post') }}" method="POST">
            @csrf
            <input class="input" type="email" name="email" placeholder='Enter Your Account Email' required>
            <button type='submit' class="btn btn-primary w-fit">Send Reset Password Email</button>
        </form>
    </div>
@endsection
