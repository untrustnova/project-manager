@extends('layouts.app')

@section('title', 'Register Admin')

@section('content')
<div class="w-full min-h-screen flex items-center justify-center" style="background: linear-gradient(178.48deg, #4397BB 1.29%, #FAFAFA 116%);">
    <!-- Card Register -->
    <div class="w-full max-w-md md:max-w-lg lg:max-w-xl bg-white/90 shadow-xl rounded-2xl px-8 py-10 mx-4" style="backdrop-filter: blur(6px);">
        <!-- Logo -->
        <div class="flex justify-center mb-8">
            <div class="w-[180px] h-[48px] flex items-center justify-center">
                <img src="{{ asset('storage/image/crocodic.png') }}" alt="Crocodic Logo" class="h-10 object-contain" />
            </div>
        </div>
        <!-- Form -->
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <!-- Name -->
            <div class="relative mb-6">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fas fa-user"></i>
                </span>
                <input
                    type="text"
                    name="name"
                    placeholder="Nama Lengkap"
                    class="w-full h-12 pl-12 pr-4 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4397BB] text-gray-700 placeholder-gray-400"
                    required
                    value="{{ old('name') }}"
                >
                @error('name')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <!-- Email -->
            <div class="relative mb-6">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fas fa-envelope"></i>
                </span>
                <input
                    type="email"
                    name="email"
                    placeholder="Email"
                    class="w-full h-12 pl-12 pr-4 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4397BB] text-gray-700 placeholder-gray-400"
                    required
                    value="{{ old('email') }}"
                >
                @error('email')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <!-- Password -->
            <div class="relative mb-6">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fas fa-lock"></i>
                </span>
                <input
                    type="password"
                    name="password"
                    placeholder="Password"
                    class="w-full h-12 pl-12 pr-12 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4397BB] text-gray-700 placeholder-gray-400"
                    required
                    id="password"
                >
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 cursor-pointer" id="togglePassword">
                    <i class="fas fa-eye-slash"></i>
                </span>
                @error('password')
                    <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                @enderror
            </div>
            <!-- Password Confirmation -->
            <div class="relative mb-6">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fas fa-lock"></i>
                </span>
                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="Konfirmasi Password"
                    class="w-full h-12 pl-12 pr-4 bg-white border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4397BB] text-gray-700 placeholder-gray-400"
                    required
                    id="password_confirmation"
                >
            </div>
            <!-- Register Button -->
            <button
                type="submit"
                class="w-full h-12 bg-[#111111] text-white rounded-lg font-semibold text-base hover:bg-gray-800 transition-colors"
            >
                REGISTER
            </button>
        </form>
        <!-- Additional Buttons -->
        <div class="mt-6 space-y-3">
            <!-- Login Button -->
            <div class="text-center">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center px-4 py-2 bg-[#4397BB] text-white rounded-lg font-medium hover:bg-[#3a87a8] transition-colors">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Login
                </a>
            </div>
        </div>
    </div>
</div>
<script>
// Toggle password visibility
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');
    const eyeIcon = togglePassword.querySelector('i');
    togglePassword.addEventListener('click', function() {
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    });
});
</script>
@endsection

