@extends('layouts.app')
@section('title', 'Pesan dari Toko Non-Mitra (Custom Order)')
@section('content')

<div class="mb-6">
    <a href="{{ route('customer.dashboard') }}" class="text-xs text-orange-600 hover:underline flex items-center gap-1 font-semibold">
        ← Kembali ke Dashboard
    </a>
    <h1 class="text-2xl font-bold text-gray-800 mt-2">🛍️ Pesan Toko Bebas / Non-Mitra</h1>
    <p class="text-gray-500 text-xs mt-1">
        Ingin beli makanan dari warung/toko favorit yang belum menjadi mitra resmi FasterID? Tulis pesanan Anda di bawah, driver kami siap membelikan dan mengantar ke lokasi Anda!
    </p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Form Custom Order --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <form method="POST" action="{{ route('customer.custom-order.store') }}" class="space-y-6">
            @csrf

            {{-- 1. Lokasi Toko --}}
            <div class="space-y-4">
                <h2 class="text-sm font-bold text-gray-800 border-b border-gray-100 pb-2 flex items-center gap-2">
                    <span>🏪</span> 1. Informasi Warung / Toko
                </h2>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">
                        Nama Toko / Warung <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="store_name" value="{{ old('store_name') }}" required placeholder="Contoh: Nasi Padang Murah Meriah / Warung Soto Mbak Sri"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('store_name') border-red-400 @enderror">
                    @error('store_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">
                        Alamat / Patokan Lokasi Toko <span class="text-red-500">*</span>
                    </label>
                    <textarea name="store_address" rows="2" required placeholder="Contoh: Jl. Ahmad Yani No. 12 (depan pom bensin lama atau samping apotek)"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('store_address') border-red-400 @enderror">{{ old('store_address') }}</textarea>
                    @error('store_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- 2. Detail Pesanan --}}
            <div class="space-y-4 pt-2">
                <h2 class="text-sm font-bold text-gray-800 border-b border-gray-100 pb-2 flex items-center gap-2">
                    <span>🧾</span> 2. Rincian Barang / Makanan Yang Ingin Dipesan
                </h2>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">
                        Daftar Pesanan & Kuantitas <span class="text-red-500">*</span>
                    </label>
                    <textarea name="notes" rows="4" required placeholder="Tuliskan daftar menu sejelas mungkin. Contoh:&#10;- 2 Bungkus Nasi Rendang (kuah banyak)&#10;- 1 Es Teh Manis&#10;- 1 Kerupuk Kaleng"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-orange-400 @error('notes') border-red-400 @enderror">{{ old('notes') }}</textarea>
                    <p class="text-[11px] text-gray-400 mt-1">Driver akan membelikan sesuai rincian di atas dan mengonfirmasi total belanja via Live Chat.</p>
                    @error('notes')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- 3. Pengantaran & Pembayaran --}}
            <div class="space-y-4 pt-2">
                <h2 class="text-sm font-bold text-gray-800 border-b border-gray-100 pb-2 flex items-center gap-2">
                    <span>📍</span> 3. Tujuan Pengantaran & Pembayaran
                </h2>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">
                        Alamat Pengantaran Anda <span class="text-red-500">*</span>
                    </label>
                    <textarea name="delivery_address" rows="2" required placeholder="Masukkan alamat lengkap rumah/kantor Anda..."
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('delivery_address') border-red-400 @enderror">{{ old('delivery_address', Auth::user()->address) }}</textarea>
                    @error('delivery_address')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1.5">
                        Catatan Tambahan untuk Driver (Opsional)
                    </label>
                    <input type="text" name="customer_note" value="{{ old('customer_note') }}" placeholder="Contoh: Tolong belikan sendok plastik atau titip di pagar"
                        class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-2">
                        Metode Pembayaran
                    </label>
                    <div class="flex flex-wrap gap-4">
                        <label class="flex items-center gap-2 cursor-pointer text-sm">
                            <input type="radio" name="payment_method" value="cash" checked class="text-orange-500">
                            <span>💵 Tunai (COD saat driver tiba)</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm">
                            <input type="radio" name="payment_method" value="transfer" class="text-orange-500">
                            <span>💳 Transfer Bank / Dompet Digital</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Tombol Submit --}}
            <div class="pt-4 border-t border-gray-100">
                <button type="submit"
                    class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3.5 rounded-xl transition text-sm shadow-sm flex items-center justify-center gap-2">
                    <span>🚀 Kirim Pesanan ke Driver</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Kolom Kanan: Panduan & Info Biaya --}}
    <div class="space-y-6">
        <div class="bg-orange-50 border border-orange-200 rounded-xl p-6 text-sm text-orange-950 space-y-3">
            <h3 class="font-bold flex items-center gap-2 text-orange-900">
                <span>💡</span> Cara Kerja Custom Order
            </h3>
            <ol class="list-decimal list-inside space-y-2 text-xs text-orange-900/90 leading-relaxed">
                <li>Isi nama dan perkiraan lokasi warung yang ingin dibeli.</li>
                <li>Tuliskan item yang Anda inginkan selengkap mungkin.</li>
                <li>Driver akan menerima pesanan dan menuju warung terkait.</li>
                <li>Gunakan <strong>Live Chat</strong> untuk konfirmasi stok & harga riil dari penjual.</li>
                <li>Setelah pesanan diantar, driver memasukkan nota belanja aktual dan Anda membayar (Total Belanja + Ongkir).</li>
            </ol>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-3 text-sm">
            <h3 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-2">💵 Estimasi Biaya</h3>
            <div class="flex justify-between text-xs text-gray-600">
                <span>Total Belanja Toko:</span>
                <span class="font-semibold text-gray-800">Menyesuaikan nota driver</span>
            </div>
            <div class="flex justify-between text-xs text-gray-600">
                <span>Ongkos Kirim Kurir (Flat):</span>
                <span class="font-bold text-orange-600">Rp 10.000</span>
            </div>
            <div class="pt-2 border-t border-gray-100 text-[11px] text-gray-400 leading-normal">
                *Total bayar akhir dihitung dari (Harga Belanja Riil Toko + Ongkir Rp 10.000).
            </div>
        </div>
    </div>
</div>

@endsection

