<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bandeja Padel Arena</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#EAE3CA] h-screen flex items-center justify-center font-sans text-gray-800 relative">

    {{-- Bottom Left Navigation Buttons --}}
    <div class="fixed bottom-8 left-8 flex gap-3">
        <a href="{{ route('admin.login') }}" class="px-4 py-2 bg-[#4F6E55] text-white rounded-full text-sm font-semibold hover:bg-opacity-90 transition shadow-md">
            Admin Login
        </a>
        <a href="{{ route('manager.login') }}" class="px-4 py-2 bg-[#8B7355] text-white rounded-full text-sm font-semibold hover:bg-opacity-90 transition shadow-md">
            Manager Login
        </a>
    </div>

    <div class="w-full max-w-5xl flex flex-col md:flex-row items-center justify-between px-10">
        
        <div class="w-full md:w-1/2 flex justify-center md:justify-start mb-10 md:mb-0">
            <img src="{{ asset('images/logo.png') }}" alt="Bandeja Padel Arena Logo" class="w-3/4 max-w-md object-contain">
        </div>

        <div class="w-full md:w-1/2 flex justify-center md:justify-end">
            <div class="bg-[#EFEBE0] p-10 rounded-3xl shadow-sm w-full max-w-sm border border-gray-200">
                
                <x-auth-session-status class="mb-4" :status="session('status')" />
                
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-6">
                        <label for="email" class="block text-sm font-semibold mb-2">Email</label>
                        <input id="email" type="email" name="email" :value="old('email')" required autofocus class="w-full px-4 py-2 rounded-full border border-[#A7A7A7] bg-transparent focus:ring-[#4F6E55] focus:border-[#4F6E55]">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="mb-8">
                        <label for="password" class="block text-sm font-semibold mb-2">Password</label>
                        <input id="password" type="password" name="password" required class="w-full px-4 py-2 rounded-full border border-[#A7A7A7] bg-transparent focus:ring-[#4F6E55] focus:border-[#4F6E55]">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="flex justify-center">
                        <button type="submit" class="bg-[#4F6E55] text-white px-10 py-3 rounded-full font-bold hover:bg-opacity-90 transition w-3/4 shadow-md">
                            Log in
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center text-sm font-semibold">
                    Don't have an account? <a href="{{ route('register') }}" class="text-[#4F6E55] underline">SIGN UP</a>
                </div>

                {{-- Demo Accounts --}}
                <div style="background: #f0f7f4; border: 1px solid #c2deb0; border-radius: 12px; padding: 14px; margin-top: 20px; font-size: 11px;">
                    <div style="font-weight: 700; color: #2d4a29; margin-bottom: 8px;">📋 Demo Accounts:</div>
                    <div style="color: #3a3a2c; line-height: 1.8;">
                        <strong>Regular User:</strong><br>
                        Email: customer@example.com<br>
                        Password: password<br><br>
                        <strong>Member (with Rewards):</strong><br>
                        Email: member@bandeja.com<br>
                        Password: member123
                    </div>
                </div>
            </div>
        </div>

    </div>

</body>
</html>