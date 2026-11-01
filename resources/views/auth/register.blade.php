@extends('layouts.app')
@section('title', 'Daftar')
@section('content')

<div class="min-h-[80vh] flex items-center justify-center py-8">
    <div class="w-full max-w-lg bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="text-center mb-8">
            <span class="text-3xl font-extrabold text-orange-500">⚡ FasterID</span>
            <p class="text-gray-500 mt-1 text-sm">Buat akun baru</p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('username') border-red-400 @enderror">
                    @error('username')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Daftar sebagai</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="role" value="customer" {{ old('role','customer') === 'customer' ? 'checked' : '' }} class="text-orange-500">
                        <span class="text-sm">🛍️ Customer</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="role" value="driver" {{ old('role') === 'driver' ? 'checked' : '' }} class="text-orange-500">
                        <span class="text-sm">🏍️ Driver</span>
                    </label>
                </div>
                @error('role')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-lg transition text-sm mt-2">
                Daftar Sekarang
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="text-orange-500 font-semibold hover:underline">Login</a>
        </p>
    </div>
</div>

@endsection

