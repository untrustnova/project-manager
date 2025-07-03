@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Login</h2>
    <form method="POST" action="{{ url('auth/login') }}">
        @csrf
        <div class="mb-4">
            <label for="email" class="block mb-1">Email</label>
            <input type="email" name="email" id="email" class="w-full border rounded px-3 py-2" required autofocus>
        </div>
        <div class="mb-4">
            <label for="password" class="block mb-1">Password</label>
            <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2" required>
        </div>
        @if ($errors->any())
            <div class="mb-4 text-red-600">
                {{ $errors->first() }}
            </div>
        @endif
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">Login</button>
        <div class="mt-4 text-center">
            <a href="{{ route('register') }}" class="text-blue-600 underline">Belum punya akun? Register</a>
        </div>
        <div class="mt-2 text-center">
            <a href="{{ route('reset') }}" class="text-blue-600 underline">Lupa password?</a>
        </div>
    </form>
</div>
@endsection
