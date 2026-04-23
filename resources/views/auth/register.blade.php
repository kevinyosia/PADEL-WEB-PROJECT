<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Bandeja Padel Arena</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#EAE3CA] min-h-screen flex items-center justify-center font-sans text-gray-800 py-10">

    <div class="w-full max-w-6xl flex flex-col md:flex-row items-center justify-between px-10">
        
        <div class="w-full md:w-1/2 flex justify-center md:justify-start mb-10 md:mb-0">
            <img src="{{ asset('images/logo.png') }}" alt="Bandeja Padel Arena Logo" class="w-3/4 max-w-md object-contain">
        </div>

        <div class="w-full md:w-1/2 flex justify-center md:justify-end">
            <div class="bg-[#EFEBE0] p-10 rounded-3xl shadow-sm w-full max-w-md border border-gray-200">
                
                <h2 class="text-2xl font-bold text-[#4F6E55] text-center mb-6">Create your account</h2>
                
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-4">
                        <input id="name" type="text" name="name" :value="old('name')" required autofocus placeholder="Full name" 
                               class="w-full px-5 py-3 rounded-full border border-[#A7A7A7] bg-transparent focus:ring-[#4F6E55] focus:border-[#4F6E55] placeholder-gray-800 font-semibold text-sm">
                        <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="mb-4">
                        <input id="email" type="email" name="email" :value="old('email')" required placeholder="email" 
                               class="w-full px-5 py-3 rounded-full border border-[#A7A7A7] bg-transparent focus:ring-[#4F6E55] focus:border-[#4F6E55] placeholder-gray-800 font-semibold text-sm">
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="mb-4">
                        <input id="phone" type="text" name="phone" :value="old('phone')" required placeholder="Phone Number" 
                               class="w-full px-5 py-3 rounded-full border border-[#A7A7A7] bg-transparent focus:ring-[#4F6E55] focus:border-[#4F6E55] placeholder-gray-800 font-semibold text-sm">
                        <x-input-error :messages="$errors->get('phone')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="mb-4">
                        <input id="password" type="password" name="password" required placeholder="Password" 
                               class="w-full px-5 py-3 rounded-full border border-[#A7A7A7] bg-transparent focus:ring-[#4F6E55] focus:border-[#4F6E55] placeholder-gray-800 font-semibold text-sm">
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="mb-8">
                        <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="confirm Password" 
                               class="w-full px-5 py-3 rounded-full border border-[#A7A7A7] bg-transparent focus:ring-[#4F6E55] focus:border-[#4F6E55] placeholder-gray-800 font-semibold text-sm">
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500 text-sm" />
                    </div>

                    <div class="flex justify-center">
                        <button type="submit" class="bg-[#4F6E55] text-white px-10 py-3 rounded-full font-bold hover:bg-opacity-90 transition w-3/4 shadow-md">
                            Next
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center text-xs font-bold text-gray-700">
                    Already have an account? <a href="{{ route('login') }}" class="text-[#4F6E55] hover:underline uppercase">LOG IN</a>
                </div>
            </div>
        </div>

    </div>

</body>
</html>