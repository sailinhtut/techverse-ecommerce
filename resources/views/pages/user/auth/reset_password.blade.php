@extends('layouts.web')

@section('web_content')
    <div>
        <p>Reset Your New Password Here</p>
        <form action="{{ route('reset-password.post') }}" method="POST">
            @csrf
            <input type="hidden" name="email" value={{ $email }}>
            <input type="hidden" name="token" value={{ $token }}>
            <input class="input" type="password" name="password" placeholder='Enter New Password' required>
            <input class="input" type="password" name="password_confirmation" placeholder='Confirm New Password' required>
            <button type='submit' class="btn btn-primary w-fit">Update Password</button>
        </form>
    </div>
@endsection
