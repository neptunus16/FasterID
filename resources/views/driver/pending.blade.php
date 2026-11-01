@extends('layouts.app')
@section('title', 'Menunggu Persetujuan')
@section('content')

<div class="min-h-[70vh] flex items-center justify-center">
    <div class="text-center max-w-md">
        <div class="text-8xl mb-6">⏳</div>
        <h1 class="text-2xl font-bold text-gray-800 mb-3">Pendaftaran Driver Sedang Diproses</h1>
        <p class="text-gray-500 mb-6">
            Akun driver Anda (<strong>{{ Auth::user()->name }}</strong>) sedang menunggu verifikasi dari Admin Faster ID.
            Proses ini biasanya memakan waktu 1×24 jam.
        </p>
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 text-sm text-orange-700 mb-6">
            📱 Anda akan mendapat pemberitahuan setelah akun disetujui.
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium px-6 py-2.5 rounded-lg text-sm transition">Keluar</button>
        </form>
    </div>
</div>

@endsection

