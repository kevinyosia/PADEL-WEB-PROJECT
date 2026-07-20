@extends('layouts.manager')

@section('content')
<div class="space-y-8">
    {{-- ─── Header ─── --}}
    <div>
        <h1 class="text-3xl font-bold text-[#0f172a]">Reviews</h1>
        <p class="text-sm text-gray-500 mt-2">Real-time feedback management untuk Bandeja Padel Arena</p>
    </div>

    {{-- ─── Filters & Search ─── --}}
    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm flex gap-4 items-end">
        <form method="GET" class="flex gap-4 w-full">
            {{-- Search Input --}}
            <input 
                type="text" 
                name="search" 
                placeholder="Cari member atau review..." 
                value="{{ $searchQuery }}"
                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            >

            {{-- Rating Filter --}}
            <select name="rating" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Filter by Rating</option>
                <option value="5" {{ $ratingFilter === '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5 Stars)</option>
                <option value="4" {{ $ratingFilter === '4' ? 'selected' : '' }}>⭐⭐⭐⭐ (4 Stars)</option>
                <option value="3" {{ $ratingFilter === '3' ? 'selected' : '' }}>⭐⭐⭐ (3 Stars)</option>
                <option value="2" {{ $ratingFilter === '2' ? 'selected' : '' }}>⭐⭐ (2 Stars)</option>
                <option value="1" {{ $ratingFilter === '1' ? 'selected' : '' }}>⭐ (1 Star)</option>
            </select>

            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors">
                Cari
            </button>

            @if($searchQuery || $ratingFilter)
            <a href="{{ route('manager.reviews') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-colors">
                Reset
            </a>
            @endif
        </form>
    </div>

    {{-- ─── Main Content Grid ─── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Reviews List (Main) --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-[#0f172a]">Arena Reviews</h2>
                <span class="text-xs font-semibold text-gray-500">{{ $reviews->total() }} review</span>
            </div>

            @forelse($reviews as $review)
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex gap-4">
                    {{-- Avatar --}}
                    <div class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                    </div>

                    {{-- Content --}}
                    <div class="flex-1">
                        {{-- Header --}}
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <p class="font-bold text-[#0f172a]">{{ $review->user->name }}</p>
                                <p class="text-xs text-gray-500">Member sejak {{ $review->user->created_at->format('M Y') }} • {{ $review->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex gap-1">
                                @for($i = 0; $i < 5; $i++)
                                    <span class="text-lg">{{ $i < $review->rating ? '⭐' : '☆' }}</span>
                                @endfor
                            </div>
                        </div>

                        {{-- Comment --}}
                        <p class="text-sm text-gray-700 mb-3 leading-relaxed">
                            "{{ $review->komentar_review ?? 'Tidak ada komentar' }}"
                        </p>

                        {{-- Rating Breakdown (if available) --}}
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="bg-gray-50 p-2 rounded">
                                <span class="text-gray-600 font-semibold">KEBERSIHAN</span>
                                <p class="text-sm font-bold text-gray-800 mt-1">{{ $review->rating }}/5</p>
                            </div>
                            <div class="bg-gray-50 p-2 rounded">
                                <span class="text-gray-600 font-semibold">KONDISI LAPANGAN</span>
                                <p class="text-sm font-bold text-gray-800 mt-1">{{ $review->rating }}/5</p>
                            </div>
                            <div class="bg-gray-50 p-2 rounded">
                                <span class="text-gray-600 font-semibold">STAFF</span>
                                <p class="text-sm font-bold text-gray-800 mt-1">{{ $review->rating }}/5</p>
                            </div>
                            <div class="bg-gray-50 p-2 rounded">
                                <span class="text-gray-600 font-semibold">FASILITAS</span>
                                <p class="text-sm font-bold text-gray-800 mt-1">{{ $review->rating }}/5</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-xl p-12 border border-gray-100 text-center">
                <p class="text-gray-500">Belum ada review sesuai filter</p>
            </div>
            @endforelse

            {{-- Pagination --}}
            <div class="flex justify-center">
                {{ $reviews->links() }}
            </div>

            <div class="pt-6 mt-6 border-t border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-lg font-bold text-[#0f172a]">Coach Reviews</h2>
                        <p class="text-xs text-gray-500 mt-1">Ulasan member untuk setiap coach.</p>
                    </div>
                    <span class="text-xs font-semibold text-gray-500">{{ $coachReviews->total() }} review</span>
                </div>

                <div class="space-y-4">
                    @forelse($coachReviews as $coachReview)
                    <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex gap-4">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-br from-[#2d4533] to-[#6b8f47] flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                                {{ strtoupper(substr($coachReview->user->name ?? 'M', 0, 1)) }}
                            </div>

                            <div class="flex-1">
                                <div class="flex justify-between items-start mb-2 gap-4">
                                    <div>
                                        <p class="font-bold text-[#0f172a]">{{ $coachReview->user->name ?? 'Member' }}</p>
                                        <p class="text-xs text-gray-500">
                                            Untuk {{ $coachReview->coach->user->name ?? 'Coach' }} • {{ $coachReview->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="flex gap-1">
                                        @for($i = 0; $i < 5; $i++)
                                            <span class="text-lg">{{ $i < $coachReview->rating ? '⭐' : '☆' }}</span>
                                        @endfor
                                    </div>
                                </div>

                                <p class="text-sm text-gray-700 mb-3 leading-relaxed">
                                    "{{ $coachReview->review ?? 'Tidak ada komentar' }}"
                                </p>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                                    <div class="bg-gray-50 p-2 rounded">
                                        <span class="text-gray-600 font-semibold">COACH</span>
                                        <p class="text-sm font-bold text-gray-800 mt-1">{{ $coachReview->coach->user->name ?? 'Coach' }}</p>
                                    </div>
                                    <div class="bg-gray-50 p-2 rounded">
                                        <span class="text-gray-600 font-semibold">RATING</span>
                                        <p class="text-sm font-bold text-gray-800 mt-1">{{ $coachReview->rating }}/5</p>
                                    </div>
                                    <div class="bg-gray-50 p-2 rounded">
                                        <span class="text-gray-600 font-semibold">RESERVASI</span>
                                        <p class="text-sm font-bold text-gray-800 mt-1">
                                            @if($coachReview->reservation)
                                                {{ $coachReview->reservation->court->nama_lapangan ?? 'Lapangan' }} • {{ \Carbon\Carbon::parse($coachReview->reservation->tanggal_booking)->format('d M Y') }}
                                            @else
                                                -
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-xl p-12 border border-gray-100 text-center">
                        <p class="text-gray-500">Belum ada review coach sesuai filter</p>
                    </div>
                    @endforelse

                    <div class="flex justify-center">
                        {{ $coachReviews->links() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Arena Sentiment Sidebar --}}
        <div class="space-y-6">
            {{-- Sentiment Card --}}
            <div class="bg-gradient-to-br from-[#2d4533] to-[#1a2620] text-white rounded-xl p-8 shadow-lg">
                <h3 class="text-lg font-bold mb-6">Arena Sentiment</h3>

                <div class="space-y-4">
                    {{-- Court Condition --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold">Court Condition</span>
                            <span class="text-xl font-bold">{{ $courtCondition }}/5.0</span>
                        </div>
                        <div class="w-full bg-white/20 rounded-full h-2">
                            <div class="bg-green-400 h-2 rounded-full" style="width: {{ ($courtCondition / 5) * 100 }}%"></div>
                        </div>
                    </div>

                    {{-- Staff Communication --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold">Staff Communication</span>
                            <span class="text-xl font-bold">{{ $staffCommunication }}/5.0</span>
                        </div>
                        <div class="w-full bg-white/20 rounded-full h-2">
                            <div class="bg-blue-400 h-2 rounded-full" style="width: {{ ($staffCommunication / 5) * 100 }}%"></div>
                        </div>
                    </div>

                    {{-- Facility Cleanliness --}}
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold">Facility Cleanliness</span>
                            <span class="text-xl font-bold">{{ $facilityCleanless }}/5.0</span>
                        </div>
                        <div class="w-full bg-white/20 rounded-full h-2">
                            <div class="bg-purple-400 h-2 rounded-full" style="width: {{ ($facilityCleanless / 5) * 100 }}%"></div>
                        </div>
                    </div>

                    {{-- Overall Experience --}}
                    <div class="pt-4 border-t border-white/20">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold">Overall Experience</span>
                            <span class="text-2xl font-bold">{{ $overallExperience }}/5.0</span>
                        </div>
                        <div class="w-full bg-white/20 rounded-full h-3">
                            <div class="bg-yellow-400 h-3 rounded-full" style="width: {{ ($overallExperience / 5) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Review Analytics --}}
            <div class="bg-white rounded-xl p-6 border border-gray-100 shadow-sm">
                <h3 class="text-base font-bold text-[#0f172a] mb-4">Review Analytics</h3>

                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Reviews This Month</span>
                        <span class="text-2xl font-bold text-[#0f172a]">{{ $totalReviewsThisMonth }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                        <span class="text-sm text-gray-600">Positive Sentiment</span>
                        <span class="text-2xl font-bold text-green-600">{{ $positiveSentimentPercent }}%</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                        <span class="text-sm text-gray-600">Coach Reviews This Month</span>
                        <span class="text-2xl font-bold text-[#0f172a]">{{ $totalCoachReviewsThisMonth }}</span>
                    </div>
                    <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                        <span class="text-sm text-gray-600">Coach Rating</span>
                        <span class="text-2xl font-bold text-yellow-600">{{ $coachRating }}/5</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.page-content a {
    color: inherit;
}
</style>
@endsection
