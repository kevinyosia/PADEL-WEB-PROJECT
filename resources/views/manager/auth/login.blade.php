<x-guest-layout>
    <div class="relative min-h-screen flex items-center justify-center bg-[#EAE3CA] w-full">

        <div class="relative z-10 w-full max-w-md p-8 bg-white/40 backdrop-blur-sm rounded-3xl shadow-xl border border-white/50">
            
            <div class="text-center mb-10 flex flex-col items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Bandeja Logo" class="h-24 w-auto object-contain mb-4">
                
                <p class="text-[#44664D] font-bold text-lg">Selamat Datang, Manajemen</p>
                <p class="text-[#44664D]/70 text-sm mt-2">Kelola platform Bandeja Padel Arena</p>
            </div>

            <form method="POST" action="{{ route('manager.login.submit') }}" class="space-y-6">
                @csrf
                
                <div>
                    <input id="email" type="email" name="email" required autofocus placeholder="Email" 
                           value="{{ old('email') }}"
                           class="w-full bg-white/80 border border-[#44664D]/30 rounded-xl px-5 py-4 text-black placeholder-[#44664D]/60 focus:outline-none focus:ring-2 focus:ring-[#44664D] transition-all">
                    @error('email')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <input id="password" type="password" name="password" required placeholder="Password" 
                           class="w-full bg-white/80 border border-[#44664D]/30 rounded-xl px-5 py-4 text-black placeholder-[#44664D]/60 focus:outline-none focus:ring-2 focus:ring-[#44664D] transition-all">
                    @error('password')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-[#44664D] text-white font-extrabold py-4 rounded-xl hover:bg-[#2d4533] transition-colors duration-300 shadow-lg mt-6 tracking-wide">
                    MASUK
                </button>
            </form>

            <p class="text-center text-[#44664D] text-xs mt-6 font-medium opacity-70">
                Bandeja Padel Arena © 2026
            </p>
        </div>
    </div>
</x-guest-layout>
