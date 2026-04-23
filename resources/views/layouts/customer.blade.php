<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Bandeja Padel Arena')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-screen flex overflow-hidden font-sans antialiased text-gray-900 bg-[#EAE3CA]">

    <aside class="w-64 bg-[#4F6E55] flex flex-col justify-between shadow-lg z-20">
        
        <div>
            <div class="p-8 pb-12">
                <img src="{{ asset('images/logo.png') }}" alt="Bandeja Padel Arena Logo" class="h-16 w-auto">
            </div>

            <nav class="flex flex-col space-y-1 px-4 text-white">
                <a href="/courts" class="px-6 py-3 rounded-lg font-bold text-lg {{ Request::is('courts') ? 'bg-[#3d5742]' : 'hover:bg-[#3d5742] transition-colors' }}">
                    Courts
                </a>
                <a href="/coaches" class="px-6 py-3 rounded-lg font-bold text-lg {{ Request::is('coaches') ? 'bg-[#3d5742]' : 'hover:bg-[#3d5742] transition-colors' }}">
                    Coaches
                </a>
                <a href="/pro-shops" class="px-6 py-3 rounded-lg font-bold text-lg {{ Request::is('pro-shops') ? 'bg-[#3d5742]' : 'hover:bg-[#3d5742] transition-colors' }}">
                    Pro Shops
                </a>
                <a href="/membership" class="px-6 py-3 rounded-lg font-bold text-lg {{ Request::is('membership') ? 'bg-[#3d5742]' : 'hover:bg-[#3d5742] transition-colors' }}">
                    Membership
                </a>
                <a href="/reviews" class="px-6 py-3 rounded-lg font-bold text-lg {{ Request::is('reviews') ? 'bg-[#3d5742]' : 'hover:bg-[#3d5742] transition-colors' }}">
                    Reviews
                </a>
            </nav>
        </div>

        <div class="p-4 mb-4 text-white">
            <a href="/profile" class="block px-6 py-3 rounded-lg font-bold text-lg hover:bg-[#3d5742] transition-colors">
                Settings
            </a>
            
            <form method="POST" action="{{ route('logout') }}" class="mt-2">
                @csrf
                <button type="submit" class="w-full text-left px-6 py-3 rounded-lg font-bold text-lg text-red-300 hover:bg-[#3d5742] transition-colors">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 overflow-y-auto p-8 relative">
        
        @yield('content')
        
    </main>

</body>
</html>