@extends('layouts.customer')

@section('title', 'Courts - Bandeja Padel Arena')

@section('content')
    
    <div class="flex flex-col gap-8 pb-10">
        
        <div class="w-full h-48 bg-gray-200 rounded-2xl flex items-center justify-center shadow-inner">
            <span class="text-xl font-bold text-gray-400">Area Banner Promo (Menunggu Gambar)</span>
        </div>

        <div class="w-full flex flex-col items-center">
            
            <img src="{{ asset('images/Denah Lapangan Padel.png') }}" alt="Denah Lapangan Padel" class="w-[80%] h-auto mb-10">
            
            <div class="w-full bg-[#f4f4f4] rounded-[2rem] p-8 shadow-sm">
                
                <h3 class="text-sm font-bold text-gray-700 mb-6 uppercase tracking-widest">Choose Courts</h3>
                
                <div class="w-full py-12 border-2 border-dashed border-gray-300 rounded-2xl text-center">
                    <p class="text-gray-500 font-medium">Di sini kita akan membangun UI Kalender & Accordion Jam Lapangan</p>
                </div>

            </div>

        </div>

    </div>

@endsection