@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto mt-10 p-6 bg-white rounded shadow">
    <h2 class="text-2xl font-bold mb-4">Register</h2>
    <form method="POST" action="{{ url('auth/register') }}">
        @csrf
        <div class="mb-4">
            <label for="name" class="block mb-1">Name</label>
            <input type="name" name="name" id="name" class="w-full border rounded px-3 py-2" required autofocus>
        </div>
        <div class="mb-4">
            <label for="email" class="block mb-1">Email</label>
            <input type="email" name="email" id="email" class="w-full border rounded px-3 py-2" required autofocus>
        </div>
        <div class="mb-4">
            <label for="password" class="block mb-1">Password</label>
            <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2" required>
        </div>
        <div class="mb-4">
            <label for="password" class="block mb-1">Password Confirm</label>
            <input type="password" name="password" id="password" class="w-full border rounded px-3 py-2" required>
        </div>
        @if ($errors->any())
            <div class="mb-4 text-red-600">
                {{ $errors->first() }}
            </div>
        @endif
        <button type="submit" class="w-full bg-green-600 text-white py-2 rounded">Register</button>
        <div class="mt-4 text-center">
            <a href="{{ route('login') }}" class="text-blue-600 underline">Sudah punya akun? Login</a>
        </div>
    </form>
</div>
@endsection
