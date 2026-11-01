@extends('layouts.app')
@section('title', 'Beranda')
@section('content')

{{-- Hero --}}
<section class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl text-white px-10 py-16 text-center mb-12">
    <h1 class="text-4xl md:text-5xl font-extrabold mb-4">🚀 Pesan Makan, Cepat Sampai!</h1>
    <p class="text-lg text-orange-100 mb-8">Faster ID menghubungkan Anda dengan ratusan warung mitra & kurir terpercaya di kota Anda.</p>
    <div class="flex gap-4 justify-center">
        <a href="{{ route('register') }}" class="bg-white text-orange-600 font-bold px-8 py-3 rounded-xl hover:bg-orange-50 transition">Pesan Sekarang</a>
        <a href="{{ route('login') }}" class="border-2 border-white text-white font-bold px-8 py-3 rounded-xl hover:bg-white hover:text-orange-600 transition">Login</a>
    </div>
</section>

{{-- Keunggulan --}}
<section class="mb-12">
    <h2 class="text-2xl font-bold text-center mb-8">Mengapa Faster ID?</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach([
            ['⚡', 'Cepat & Andal', 'Pesanan diterima & diantar dalam waktu singkat oleh kurir terverifikasi.'],
            ['🏪', 'Banyak Pilihan', 'Ratusan warung & restoran mitra siap melayani selera Anda.'],
            ['💳', 'Pembayaran Mudah', 'Bayar tunai atau transfer bank, semuanya fleksibel.'],
        ] as [$icon, $title, $desc])
        <div class="bg-white rounded-xl shadow-sm p-6 text-center border border-gray-100">
            <div class="text-4xl mb-3">{{ $icon }}</div>
            <h3 class="font-bold text-lg mb-2">{{ $title }}</h3>
            <p class="text-gray-500 text-sm">{{ $desc }}</p>
        </div>
        @endforeach
    </div>
</section>

{{-- CTA --}}
<section class="text-center bg-gray-800 rounded-2xl py-12 text-white">
    <h2 class="text-2xl font-bold mb-2">Daftar sebagai Mitra Driver</h2>
    <p class="text-gray-400 mb-6">Mulai penghasilan tambahan bersama Faster ID. Daftar gratis, cepat disetujui.</p>
    <a href="{{ route('register') }}" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-8 py-3 rounded-xl transition">Daftar sebagai Driver</a>
</section>

@endsection

