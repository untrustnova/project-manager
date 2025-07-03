@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-md mt-10">
    <div class="bg-white shadow-md rounded px-8 py-6">
        <h2 class="text-2xl font-bold mb-6 text-center">Reset Password</h2>
        @if (session('status'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('status') }}
            </div>
        @endif
        <form method="POST" action="{{ route('reset') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token ?? '' }}">

            <div class="mb-4">
                <label for="email" class="block text-gray-700 mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-3 py-2 border rounded @error('email') border-red-500 @enderror">
                @error('email')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700 mb-2">New Password</label>
                <input id="password" type="password" name="password" required
                    class="w-full px-3 py-2 border rounded @error('password') border-red-500 @enderror">
                @error('password')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password-confirm" class="block text-gray-700 mb-2">Confirm Password</label>
                <input id="password-confirm" type="password" name="password_confirmation" required
                    class="w-full px-3 py-2 border rounded">
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">
                    Reset Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
