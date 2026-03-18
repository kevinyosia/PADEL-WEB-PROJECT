<x-guest-layout>
    <div class="relative min-h-screen flex items-center justify-center bg-[#EAE3CA] w-full py-10">

        <div class="relative z-10 w-full max-w-md p-8 bg-white/40 backdrop-blur-sm rounded-3xl shadow-xl border border-white/50">
            
            <div class="text-center mb-8 flex flex-col items-center">
                <img src="{{ asset('images/logo.png') }}" alt="Bandeja Logo" class="h-20 w-auto object-contain mb-3">
                
                <p class="text-[#44664D] font-bold text-lg">Create your account.</p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-6">
                @csrf
                
                <div>
                    <input id="name" type="text" name="name" required autofocus placeholder="Full Name" 
                           class="w-full bg-white/80 border border-[#44664D]/30 rounded-xl px-5 py-4 text-black placeholder-[#44664D]/60 focus:outline-none focus:ring-2 focus:ring-[#44664D] transition-all">
                </div>

                <div>
                    <input id="email" type="email" name="email" required placeholder="Email Address" 
                           class="w-full bg-white/80 border border-[#44664D]/30 rounded-xl px-5 py-4 text-black placeholder-[#44664D]/60 focus:outline-none focus:ring-2 focus:ring-[#44664D] transition-all">
                </div>

                <div>
                    <input id="phone" type="tel" name="phone" required placeholder="Phone Number" 
                           class="w-full bg-white/80 border border-[#44664D]/30 rounded-xl px-5 py-4 text-black placeholder-[#44664D]/60 focus:outline-none focus:ring-2 focus:ring-[#44664D] transition-all">
                </div>

                <div>
                    <input id="password" type="password" name="password" required placeholder="Password" 
                           class="w-full bg-white/80 border border-[#44664D]/30 rounded-xl px-5 py-4 text-black placeholder-[#44664D]/60 focus:outline-none focus:ring-2 focus:ring-[#44664D] transition-all">
                </div>

                <div>
                    <input id="password_confirmation" type="password" name="password_confirmation" required placeholder="Confirm Password" 
                           class="w-full bg-white/80 border border-[#44664D]/30 rounded-xl px-5 py-4 text-black placeholder-[#44664D]/60 focus:outline-none focus:ring-2 focus:ring-[#44664D] transition-all">
                </div>
                
                <button type="submit" class="w-full bg-[#44664D] text-white font-extrabold py-4 rounded-xl hover:bg-[#2d4533] transition-colors duration-300 shadow-lg mt-6 tracking-wide">
                    SIGN UP
                </button>
            </form>

            <p class="text-center text-[#44664D] text-sm mt-8 font-medium">
                Already have an account? <a href="{{ route('login') }}" class="font-extrabold hover:underline">Log in here</a>
            </p>
        </div>
    </div>
</x-guest-layout>