<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') — Faster ID</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex">

    {{-- Sidebar --}}
    <aside class="w-64 bg-gray-900 text-white flex flex-col min-h-screen fixed">
        <div class="h-16 flex items-center px-6 border-b border-gray-700">
            <span class="text-xl font-extrabold text-orange-400">⚡ FasterID</span>
            <span class="ml-2 text-xs bg-orange-500 px-2 py-0.5 rounded text-white font-semibold">Admin</span>
        </div>
        <nav class="flex-1 px-4 py-6 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('admin.orders.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg hover:bg-gray-700 text-sm font-medium {{ request()->routeIs('admin.orders*') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                <span class="flex items-center gap-3">
                    📦 Kelola Pesanan
                </span>
                @php
                    $pendingCount = \App\Models\Order::where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="bg-orange-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full animate-pulse">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>
            <a href="{{ route('admin.stores') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-700 text-sm font-medium {{ request()->routeIs('admin.stores*') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
                🏪 Kelola Toko
            </a>
            <a href="{{ route('admin.customers.index') }}" 
   class="flex items-center px-4 py-3 text-gray-300 hover:bg-gray-800 hover:text-white rounded-lg mb-1 {{ request()->routeIs('admin.customers.*') ? 'bg-gray-800 text-white' : '' }}">
    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
    </svg>
    Data Customer
</a>
        </nav>
        <div class="px-4 py-4 border-t border-gray-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full text-left text-sm text-gray-400 hover:text-white px-3 py-2">🚪 Keluar</button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="ml-64 flex-1 flex flex-col">
        <header class="h-16 bg-white shadow-sm flex items-center px-8 justify-between">
            <h1 class="text-lg font-semibold text-gray-700">@yield('page-title', 'Dashboard')</h1>
            <span class="text-sm text-gray-500">{{ Auth::user()->name }}</span>
        </header>

        @if(session('success'))
            <div class="mx-8 mt-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">✅ {{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mx-8 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">❌ {{ $errors->first() }}</div>
        @endif

        <main class="flex-1 p-8">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>

