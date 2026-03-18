<x-guest-layout>
    <div class="relative min-h-screen flex items-center justify-center bg-[#EAE3CA] w-full">

        <div class="relative z-10 w-full max-w-md p-8 bg-white/40 backdrop-blur-sm rounded-3xl shadow-xl border border-white/50">
            
            <div class="text-center mb-10 flex flex-col items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Bandeja Logo" class="h-24 w-auto object-contain mb-4">
                
                <p class="text-[#44664D] font-bold text-lg">Welcome back. Ready to play?</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <div>
                    <input id="email" type="email" name="email" required autofocus placeholder="Email Address" 
                           class="w-full bg-white/80 border border-[#44664D]/30 rounded-xl px-5 py-4 text-black placeholder-[#44664D]/60 focus:outline-none focus:ring-2 focus:ring-[#44664D] transition-all">
                </div>

                <div>
                    <input id="password" type="password" name="password" required placeholder="Password" 
                           class="w-full bg-white/80 border border-[#44664D]/30 rounded-xl px-5 py-4 text-black placeholder-[#44664D]/60 focus:outline-none focus:ring-2 focus:ring-[#44664D] transition-all">
                </div>

                <button type="submit" class="w-full bg-[#44664D] text-white font-extrabold py-4 rounded-xl hover:bg-[#2d4533] transition-colors duration-300 shadow-lg mt-6 tracking-wide">
                    LOG IN
                </button>
            </form>

            <p class="text-center text-[#44664D] text-sm mt-8 font-medium">
                Don't have an account? <a href="{{ route('register') }}" class="font-extrabold hover:underline">Sign Up here</a>
            </p>
        </div>
    </div>
</x-guest-layout>