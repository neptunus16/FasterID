@extends('layouts.admin')
@section('title', 'Edit Menu: ' . $menu->name)
@section('page-title', 'Edit Menu')
@section('content')

<div class="mb-6">
    <a href="{{ route('admin.stores.menus.index', $store) }}" class="text-xs text-orange-600 hover:underline flex items-center gap-1 font-semibold">
        ← Kembali ke Daftar Menu {{ $store->store_name }}
    </a>
</div>

<div class="max-w-xl bg-white rounded-xl shadow-sm border border-gray-100 p-8">
    <div class="border-b border-gray-100 pb-4 mb-6">
        <h2 class="text-base font-bold text-gray-800">Edit Menu: {{ $menu->name }}</h2>
        <p class="text-xs text-gray-500 mt-0.5">Toko: <strong>{{ $store->store_name }}</strong></p>
    </div>

    <form method="POST" action="{{ route('admin.stores.menus.update', [$store, $menu]) }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        {{-- Nama Menu --}}
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                Nama Menu <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name', $menu->name) }}" required
                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('name') border-red-400 @enderror">
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Harga --}}
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                Harga (Rp) <span class="text-red-500">*</span>
            </label>
            <input type="number" name="price" value="{{ old('price', (int)$menu->price) }}" required min="0" step="500"
                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('price') border-red-400 @enderror">
            @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                Deskripsi Menu
            </label>
            <textarea name="description" rows="3"
                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400 @error('description') border-red-400 @enderror">{{ old('description', $menu->description) }}</textarea>
            @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Upload Foto --}}
        <div>
            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                Foto Menu
            </label>
            @if($menu->image)
            <div class="mb-3 flex items-center gap-3 p-2 bg-gray-50 rounded-lg border border-gray-200">
                <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-14 h-14 rounded object-cover border border-gray-200">
                <span class="text-xs text-gray-500">Foto saat ini tersimpan. Unggah file baru untuk menggantinya.</span>
            </div>
            @endif
            <input type="file" name="image" accept="image/*"
                class="w-full border border-gray-200 rounded-lg px-3 py-2 text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-orange-50 file:text-orange-600 hover:file:bg-orange-100">
            <p class="text-[11px] text-gray-400 mt-1">Kosongkan jika tidak ingin mengubah foto. Format JPG, PNG, WEBP max 2MB.</p>
            @error('image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Ketersediaan --}}
        <div class="pt-2">
            <label class="inline-flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="is_available" value="1" {{ old('is_available', $menu->is_available) ? 'checked' : '' }}
                    class="rounded border-gray-300 text-orange-500 focus:ring-orange-400">
                <span class="text-sm font-medium text-gray-700">Tandai menu sebagai <strong>Tersedia</strong></span>
            </label>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-2.5 rounded-lg text-sm shadow-sm transition">
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.stores.menus.index', $store) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold px-5 py-2.5 rounded-lg text-sm transition">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection

