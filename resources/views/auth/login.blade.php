@extends('layouts.app')
@section('title', 'Login')
@section('content')

<div class="min-h-[70vh] flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <div class="text-center mb-8">
            <span class="text-3xl font-extrabold text-orange-500">⚡ FasterID</span>
            <p class="text-gray-500 mt-1 text-sm">Masuk ke akun Anda</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('email') border-red-400 @enderror">
                @error('email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                <input type="checkbox" name="remember" class="rounded">
                Ingat saya
            </label>
            <button type="submit"
                class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 rounded-lg transition text-sm">
                Masuk
            </button>
        </form>

        <p class="text-center text-sm text-gray-500 mt-6">
            Belum punya akun?
            <a href="{{ route('register') }}" class="text-orange-500 font-semibold hover:underline">Daftar</a>
        </p>
    </div>
</div>

@endsection

