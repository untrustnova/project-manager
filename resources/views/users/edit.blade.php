@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="bg-slate-50 min-h-screen">

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-10 pt-24">

        @php
            // Menentukan route untuk form action (update)
            $updateRoute = match(Auth::user()->role) {
                'admin'    => route('admin.users.update', $user->user_id),
                'hr'       => route('hr.employees.update', $user->user_id),
                'employee' => route('employee.profile.update'),
                default    => '#'
            };
            // Menentukan route untuk tombol "Cancel"
            $cancelRoute = match(Auth::user()->role) {
                'admin'    => route('admin.users.show', $user->user_id),
                'hr'       => route('hr.employees.show', $user->user_id),
                'employee' => route('employee.profile'),
                default    => route('dashboard')
            };
        @endphp

        <form action="{{ $updateRoute }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="bg-white border border-slate-200/80 rounded-2xl shadow-xl shadow-slate-200/50 overflow-hidden">
                <div class="p-6 md:p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                        {{-- Kolom Kiri: Upload Foto Profil --}}
                        <div class="col-span-1 flex flex-col items-center text-center">
                            <h3 class="text-xl font-bold text-slate-800 mb-4 w-full text-center lg:text-left">Profile Picture</h3>

                            <img id="image-preview" class="w-48 h-48 rounded-full object-cover shadow-lg mb-4"
                                 src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/default-avatar.png') }}"
                                 alt="Current profile picture">

                            <label for="image" class="cursor-pointer bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold py-2 px-4 rounded-lg transition-colors duration-200">
                                <span>Change Image</span>
                                <input id="image" name="image" type="file" class="hidden" accept="image/png, image/jpeg, image/gif">
                            </label>

                            @error('image')
                                <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kolom Kanan: Detail Informasi --}}
                        <div class="col-span-1 lg:col-span-2">
                            <h3 class="text-xl font-bold text-slate-800 mb-6">Edit Information</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Nama --}}
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-slate-600 mb-1">Full Name</label>
                                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
                                    @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label for="email" class="block text-sm font-semibold text-slate-600 mb-1">Email Address</label>
                                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition" required>
                                    @error('email') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Nomor Telepon --}}
                                <div>
                                    <label for="phone_number" class="block text-sm font-semibold text-slate-600 mb-1">Phone Number</label>
                                    <input type="tel" id="phone_number" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                    @error('phone_number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Tanggal Lahir --}}
                                <div>
                                    <label for="birth" class="block text-sm font-semibold text-slate-600 mb-1">Birth Date</label>
                                    <input type="date" id="birth" name="birth" value="{{ old('birth', $user->birth ? $user->birth->format('Y-m-d') : '') }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                    @error('birth') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Alamat --}}
                                <div class="md:col-span-2">
                                    <label for="address" class="block text-sm font-semibold text-slate-600 mb-1">Address</label>
                                    <textarea id="address" name="address" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">{{ old('address', $user->address) }}</textarea>
                                    @error('address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Link Telegram --}}
                                <div class="md:col-span-2">
                                    <label for="telegram_link" class="block text-sm font-semibold text-slate-600 mb-1">Telegram Link</label>
                                    <input type="text" id="telegram_link" name="telegram_link" value="{{ old('telegram_link', $user->telegram_link) }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                    @error('telegram_link') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                </div>

                                {{-- Ganti Password --}}
                                <div class="md:col-span-2 border-t border-slate-200 pt-6 mt-4">
                                    <p class="text-sm text-slate-500 mb-4">Fill in the password only if you want to change it.</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <label for="password" class="block text-sm font-semibold text-slate-600 mb-1">New Password</label>
                                            <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                            @error('password') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label for="password_confirmation" class="block text-sm font-semibold text-slate-600 mb-1">Confirm New Password</label>
                                            <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                        </div>
                                    </div>
                                </div>

                                {{-- Ganti Role (Hanya untuk Admin) --}}
                                @if (Auth::user()->isAdmin())
                                <div class="md:col-span-2">
                                    <label for="role" class="block text-sm font-semibold text-slate-600 mb-1">User Role</label>
                                    <select id="role" name="role" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                                        <option value="employee" @if($user->role == 'employee') selected @endif>Employee</option>
                                        <option value="hr" @if($user->role == 'hr') selected @endif>HR</option>
                                        <option value="admin" @if($user->role == 'admin') selected @endif>Admin</option>
                                    </select>
                                    @error('role')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="bg-slate-50 px-8 py-4 border-t border-slate-200 flex justify-end items-center gap-4">
                    <a href="{{ $cancelRoute }}" class="text-slate-600 font-semibold px-4 py-2 rounded-lg hover:bg-slate-200 transition-colors">Cancel</a>
                    <button type="submit" class="bg-blue-600 text-white font-bold px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors shadow-lg shadow-blue-500/20">
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </main>
</div>

@push('scripts')
<script>
document.getElementById('image').addEventListener('change', function(event) {
    const [file] = event.target.files;
    if (file) {
        const preview = document.getElementById('image-preview');
        preview.src = URL.createObjectURL(file);
        preview.onload = () => URL.revokeObjectURL(preview.src); // Free memory
    }
});
</script>
@endpush
@endsection
