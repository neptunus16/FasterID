@extends('layouts.admin')
@section('title', $store ? 'Edit Toko' : 'Tambah Toko')
@section('page-title', $store ? 'Edit Toko' : 'Tambah Toko Baru')
@section('content')

<div class="max-w-lg bg-white rounded-xl shadow-sm border border-gray-100 p-8">
    <form method="POST" action="{{ $store ? route('admin.stores.update', $store) : route('admin.stores.store') }}" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @if($store) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko</label>
            <input type="text" name="store_name" value="{{ old('store_name', $store?->store_name) }}" required
                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
            <textarea name="address" rows="3" required
                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">{{ old('address', $store?->address) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
            <input type="text" name="phone" value="{{ old('phone', $store?->phone) }}"
                class="w-full border border-gray-200 rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Toko</label>
            <input type="file" name="image" accept="image/*"
                class="w-full border border-gray-200 rounded-lg px-4 py-2 text-sm">
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white font-bold px-6 py-2.5 rounded-lg text-sm">
                {{ $store ? 'Simpan Perubahan' : 'Tambah Toko' }}
            </button>
            <a href="{{ route('admin.stores') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-6 py-2.5 rounded-lg text-sm">Batal</a>
        </div>
    </form>
</div>

@endsection

