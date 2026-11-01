<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Faster ID')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { DEFAULT: '#f97316', dark: '#ea580c' } } } }
        }
    </script>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">

    {{-- Navigation --}}
    <nav class="bg-white shadow-sm border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="text-2xl font-extrabold text-orange-500">⚡ Faster<span class="text-gray-800">ID</span></span>
            </a>
            <div class="flex items-center gap-4">
                @auth
                    <span class="text-sm text-gray-500">Halo, <strong>{{ Auth::user()->name }}</strong></span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-sm text-red-500 hover:text-red-700 font-medium">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-orange-500">Login</a>
                    <a href="{{ route('register') }}" class="bg-orange-500 hover:bg-orange-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Daftar</a>
                @endauth
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-sm">
                ✅ {{ session('success') }}
            </div>
        </div>
    @endif
    @if($errors->any())
        <div class="max-w-7xl mx-auto px-4 mt-4">
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm">
                ❌ {{ $errors->first() }}
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>

