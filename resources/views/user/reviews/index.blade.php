@extends('layouts.user')
@section('title', 'Reviews — Bandeja Padel Arena')

@push('styles')
<style>
    :root {
        --green-deep: #2D4A1E; --green-mid: #3A5C28;
        --cream-bg: #EDE8D8; --cream-card: #F5F1E6; --cream-white: #FAFAF5;
        --text-dark: #1A1A0F; --text-muted: #6B6B5A; --gold: #F0A500;
    }
    .page-wrap { min-height: 100vh; background: var(--cream-bg); padding: 0; }

    /* Topbar */
    .review-topbar {
        padding: 24px 32px 18px;
        display: flex; align-items: flex-start; justify-content: space-between;
    }
    .topbar-title { font-family: 'DM Serif Display', serif; font-size: 28px; color: var(--text-dark); }
    .topbar-loc   { font-size: 13px; color: var(--text-muted); margin-top: 3px; }
    .topbar-rating {
        display: flex; align-items: center; gap: 8px;
        font-size: 22px; font-weight: 800; color: var(--text-dark);
    }
    .topbar-star { color: var(--gold); font-size: 22px; }
    .topbar-count { font-size: 14px; color: var(--text-muted); font-weight: 600; }

    /* Review form card */
    .review-card {
        margin: 0 32px 28px;
        background: var(--cream-card); border-radius: 20px;
        padding: 28px 32px;
        border: 1px solid rgba(0,0,0,0.06);
    }
    .review-card-title { font-size: 22px; font-weight: 800; color: var(--text-dark); text-align: center; margin-bottom: 22px; letter-spacing: .02em; }
    .review-card-subtitle { margin: -12px auto 22px; max-width: 520px; text-align: center; color: var(--text-muted); font-size: 13px; line-height: 1.5; }
    .coach-review-card { margin-top: -8px; }
    .helper-box {
        background: #FEF3CD; border: 1px solid #FFE69C; border-radius: 10px;
        padding: 16px; margin-bottom: 18px; font-size: 13px; color: #856404; text-align: center; line-height: 1.5;
    }
    .review-error { display: none; font-size: 12px; color: #C0392B; margin-top: 8px; }
    .review-error.show { display: block; }

    /* Star rows */
    .star-rows { border-top: 1px solid rgba(0,0,0,0.08); }
    .star-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 14px 0; border-bottom: 1px solid rgba(0,0,0,0.08);
    }
    .star-row-label { font-size: 15px; font-weight: 600; color: var(--text-dark); }
    .star-input { display: flex; gap: 4px; flex-direction: row-reverse; justify-content: flex-end; }
    .star-input input[type="radio"] { display: none; }
    .star-input label {
        font-size: 24px; color: #D9D4C4; cursor: pointer;
        transition: color .12s; line-height: 1;
    }
    /* Highlight hovered and all before it (flex row-reverse trick) */
    .star-input label:hover,
    .star-input label:hover ~ label,
    .star-input input[type="radio"]:checked ~ label { color: var(--gold); }

    /* Text area */
    .review-textarea-wrap { margin-top: 22px; }
    .review-textarea-label { font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 10px; display: block; }
    .review-textarea {
        width: 100%; padding: 14px 16px;
        border: 1.5px solid rgba(0,0,0,0.1); border-radius: 12px;
        font-size: 14px; font-family: 'Figtree', sans-serif; color: var(--text-dark);
        background: var(--cream-white); resize: vertical; min-height: 120px;
        outline: none; line-height: 1.55; transition: border-color .15s;
    }
    .review-textarea:focus { border-color: var(--green-mid); box-shadow: 0 0 0 3px rgba(58,92,40,0.1); }
    .review-textarea::placeholder { color: #B0AA98; }

    /* Reservation select */
    .reservation-select-wrap { margin-bottom: 18px; }
    .res-label { font-size: 12px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: .07em; margin-bottom: 7px; display: block; }
    .res-select {
        width: 100%; padding: 10px 14px;
        border: 1.5px solid rgba(0,0,0,0.1); border-radius: 10px;
        font-size: 14px; font-family: 'Figtree', sans-serif; color: var(--text-dark);
        background: var(--cream-white); outline: none; cursor: pointer;
        appearance: none; background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236B6B5A' stroke-width='1.5' fill='none'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 14px center; padding-right: 36px;
        transition: border-color .15s;
    }
    .res-select:focus { border-color: var(--green-mid); }

    /* Submit */
    .submit-btn {
        display: block; width: 100%; padding: 14px;
        background: var(--green-deep); color: #fff;
        border: none; border-radius: 50px; margin-top: 20px;
        font-size: 15px; font-weight: 700; font-family: 'Figtree', sans-serif;
        cursor: pointer; transition: all .18s;
    }
    .submit-btn:hover { background: var(--green-mid); }
    .submit-btn:disabled { background: #C5C0B0; cursor: wait; }

    /* WhatsApp */
    .wa-section { text-align: center; margin-top: 20px; }
    .wa-line { font-size: 13px; color: var(--text-muted); margin-bottom: 10px; }
    .wa-btn {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 11px 24px; background: #25D366; color: #fff;
        border-radius: 50px; text-decoration: none;
        font-size: 14px; font-weight: 700; transition: all .15s;
    }
    .wa-btn:hover { background: #1EB855; }

    /* Thanks modal */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.35); z-index: 9000; align-items: center; justify-content: center; }
    .modal-overlay.show { display: flex; }
    .thanks-box {
        background: var(--cream-white); border-radius: 24px; padding: 40px 36px;
        width: 380px; text-align: center;
        box-shadow: 0 20px 60px rgba(0,0,0,0.18);
    }
    .thanks-icon { font-size: 52px; color: var(--gold); margin-bottom: 14px; display: block; }
    .thanks-title { font-size: 22px; font-weight: 800; color: var(--text-dark); margin-bottom: 8px; }
    .thanks-sub { font-size: 13px; color: var(--text-muted); line-height: 1.6; max-width: 280px; margin: 0 auto; }

    /* My reviews list */
    .my-reviews-section { margin: 0 32px 32px; }
    .section-title { font-size: 16px; font-weight: 800; color: var(--text-dark); margin-bottom: 14px; }
    .review-item {
        background: var(--cream-card); border-radius: 14px; padding: 16px 18px;
        border: 1px solid rgba(0,0,0,0.06); margin-bottom: 10px;
    }
    .ri-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 6px; }
    .ri-target { font-size: 14px; font-weight: 700; color: var(--text-dark); }
    .ri-date   { font-size: 11px; color: var(--text-muted); }
    .ri-stars  { color: var(--gold); font-size: 13px; letter-spacing: 1px; margin-bottom: 6px; }
    .ri-comment{ font-size: 12px; color: var(--text-muted); line-height: 1.5; }
    .ri-kind {
        display: inline-flex; align-items: center; margin-right: 8px; padding: 3px 8px;
        border-radius: 999px; background: rgba(58,92,40,0.12); color: var(--green-deep);
        font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: .06em;
    }
    @media (max-width: 720px) {
        .review-topbar { padding: 20px 18px 16px; flex-direction: column; gap: 12px; }
        .review-card, .my-reviews-section { margin-left: 18px; margin-right: 18px; padding: 22px 18px; }
        .star-row { align-items: flex-start; flex-direction: column; gap: 10px; }
        .ri-header { align-items: flex-start; flex-direction: column; gap: 4px; }
    }
</style>
@endpush

@section('content')
<div class="page-wrap">

    {{-- Topbar --}}
    <div class="review-topbar">
        <div>
            <div class="topbar-title">Bandeja Padel Arena</div>
            <div class="topbar-loc">Ancol, Jakarta Utara</div>
        </div>
        <div class="topbar-rating">
            <span class="topbar-star">★</span>
            {{ number_format($avgRating, 1) }}
            <span class="topbar-count">| {{ $totalReviews }} Reviews</span>
        </div>
    </div>

    {{-- Review Form --}}
    <div class="review-card">
        <div class="review-card-title">REVIEW</div>

        @if(session('review_sent'))
        <div style="background:#E6EDD8;border:1px solid #C2DEB0;border-radius:10px;padding:12px 16px;margin-bottom:18px;font-size:13px;color:var(--green-deep);text-align:center;font-weight:700;">
            ✓ Terima kasih! Review Anda telah dikirim.
        </div>
        @endif

        <form method="POST" action="{{ route('reviews.store') }}" id="reviewForm">
            @csrf

            {{-- Pilih reservasi --}}
            @if($reservations->count())
            <div class="reservation-select-wrap">
                <label class="res-label" for="reservation_id">Berdasarkan Reservasi</label>
                <select name="reservation_id" id="reservation_id" class="res-select" required>
                    <option value="">Pilih reservasi Anda...</option>
                    @foreach($reservations as $res)
                    <option value="{{ $res->id }}" {{ old('reservation_id') == $res->id ? 'selected' : '' }}>
                        {{ $res->court->nama_lapangan ?? '—' }} ·
                        {{ \Carbon\Carbon::parse($res->tanggal_booking)->format('d M Y') }} ·
                        {{ $res->jam_mulai }}–{{ $res->jam_selesai }}
                    </option>
                    @endforeach
                </select>
                @error('reservation_id') <div style="font-size:11px;color:#C0392B;margin-top:4px;">{{ $message }}</div> @enderror
            </div>
            @else
            <div style="background:#FEF3CD;border:1px solid #FFE69C;border-radius:10px;padding:16px;margin-bottom:18px;font-size:13px;color:#856404;text-align:center;">
                <strong>ℹ️ Belum ada reservasi yang selesai</strong><br>
                Anda harus memiliki reservasi yang sudah completed untuk bisa menulis review.
            </div>
            @endif

            {{-- Star categories --}}
            <div class="star-rows">
                @foreach(['kebersihan'=>'Kebersihan','kondisi_lapangan'=>'Kondisi Lapangan','komunikasi_staff'=>'Komunikasi Staff','fasilitas'=>'Fasilitas'] as $key => $label)
                <div class="star-row">
                    <span class="star-row-label">{{ $label }}</span>
                    <div class="star-input">
                        @for($s = 5; $s >= 1; $s--)
                        <input type="radio" id="{{ $key }}_{{ $s }}" name="{{ $key }}" value="{{ $s }}"
                            {{ old($key) == $s ? 'checked' : '' }}>
                        <label for="{{ $key }}_{{ $s }}">★</label>
                        @endfor
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Comment --}}
            <div class="review-textarea-wrap">
                <label class="review-textarea-label" for="komentar_review">Tulis Ulasan</label>
                <textarea
                    id="komentar_review" name="komentar_review"
                    class="review-textarea"
                    placeholder="Ceritakan pengalaman anda..."
                    rows="4"
                >{{ old('komentar_review') }}</textarea>
                @error('komentar_review') <div style="font-size:11px;color:#C0392B;margin-top:4px;">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="submit-btn">Submit</button>
        </form>

        <hr style="border:none;border-top:1px solid rgba(0,0,0,0.08);margin:22px 0;">

        <div class="wa-section">
            <div class="wa-line">Feedback langsung ke kami?</div>
            <a href="https://wa.me/6281234567890" target="_blank" class="wa-btn">
                💬 Hubungi via Whatsapp
            </a>
        </div>
    </div>

    {{-- Coach Review Form --}}
    <div class="review-card coach-review-card">
        <div class="review-card-title">REVIEW COACH</div>
        <div class="review-card-subtitle">Berikan ulasan untuk coach berdasarkan sesi booking yang sudah selesai.</div>

        @if($coachReservations->count())
        <form id="coachReviewForm" data-url-template="{{ route('coach.reviews.store', ['coach' => '__COACH_ID__']) }}">
            @csrf

            <div class="reservation-select-wrap">
                <label class="res-label" for="coach_reservation_id">Booking Coach</label>
                <select name="reservation_id" id="coach_reservation_id" class="res-select" required>
                    <option value="">Pilih booking coach Anda...</option>
                    @foreach($coachReservations as $res)
                    <option
                        value="{{ $res->id }}"
                        data-coach-id="{{ $res->coach_id }}"
                        data-coach-name="{{ $res->coach->user->name ?? 'Coach' }}"
                    >
                        Coach {{ $res->coach->user->name ?? 'Coach' }} &middot;
                        {{ $res->court->nama_lapangan ?? 'Lapangan' }} &middot;
                        {{ \Carbon\Carbon::parse($res->tanggal_booking)->format('d M Y') }} &middot;
                        {{ $res->jam_mulai }}-{{ $res->jam_selesai }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="star-rows">
                <div class="star-row">
                    <span class="star-row-label">Rating Coach</span>
                    <div class="star-input">
                        @for($s = 5; $s >= 1; $s--)
                        <input type="radio" id="coach_rating_{{ $s }}" name="rating" value="{{ $s }}">
                        <label for="coach_rating_{{ $s }}">&#9733;</label>
                        @endfor
                    </div>
                </div>
            </div>

            <div class="review-textarea-wrap">
                <label class="review-textarea-label" for="coach_review">Tulis Ulasan Coach</label>
                <textarea
                    id="coach_review" name="review"
                    class="review-textarea"
                    placeholder="Ceritakan pengalaman latihan dengan coach..."
                    rows="4"
                    maxlength="500"
                ></textarea>
                <div class="review-error" id="coachReviewError"></div>
            </div>

            <button type="submit" class="submit-btn" id="coachReviewSubmit">Submit Review Coach</button>
        </form>
        @else
        <div class="helper-box">
            <strong>Belum ada booking coach yang bisa direview</strong><br>
            Review coach akan tersedia setelah Anda menyelesaikan booking dengan coach, atau jika semua booking coach sudah pernah direview.
        </div>
        @endif
    </div>

    {{-- My reviews list --}}
    @if($myFeedbacks->count() || $myCoachReviews->count())
    <div class="my-reviews-section">
        <div class="section-title">Ulasan Saya</div>

        @foreach($myFeedbacks as $fb)
        <div class="review-item">
            <div class="ri-header">
                <span class="ri-target"><span class="ri-kind">Lapangan</span>{{ $fb->reservation->court->nama_lapangan ?? 'Lapangan' }}</span>
                <span class="ri-date">{{ \Carbon\Carbon::parse($fb->created_at)->format('d M Y') }}</span>
            </div>
            <div class="ri-stars">{{ str_repeat('★', $fb->rating) }}{{ str_repeat('☆', 5 - $fb->rating) }}</div>
            @if($fb->komentar_review)
            <div class="ri-comment">{{ $fb->komentar_review }}</div>
            @endif
        </div>
        @endforeach

        @foreach($myCoachReviews as $cr)
        <div class="review-item">
            <div class="ri-header">
                <span class="ri-target">Coach: {{ $cr->coach->user->name ?? '—' }}</span>
                <span class="ri-date">{{ \Carbon\Carbon::parse($cr->created_at)->format('d M Y') }}</span>
            </div>
            <div class="ri-stars">{{ str_repeat('★', $cr->rating) }}{{ str_repeat('☆', 5 - $cr->rating) }}</div>
            @if($cr->reservation)
            <div class="ri-comment" style="margin-bottom:6px;">
                {{ $cr->reservation->court->nama_lapangan ?? 'Lapangan' }} &middot;
                {{ \Carbon\Carbon::parse($cr->reservation->tanggal_booking)->format('d M Y') }}
            </div>
            @endif
            @if($cr->review)
            <div class="ri-comment">{{ $cr->review }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif

</div>

{{-- Thanks modal (shown if just submitted) --}}
@if(session('review_sent'))
<div class="modal-overlay show" id="thanksModal" onclick="this.classList.remove('show')">
    <div class="thanks-box" onclick="event.stopPropagation()">
        <span class="thanks-icon">⚠</span>
        <div class="thanks-title">Thanks for your feedback</div>
        <div class="thanks-sub">Your review helps others make better decisions. Thanks for contributing</div>
        <button style="margin-top:20px;padding:10px 24px;background:var(--green-deep);color:#fff;border:none;border-radius:50px;font-size:13px;font-weight:700;cursor:pointer;" onclick="document.getElementById('thanksModal').classList.remove('show')">Tutup</button>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('coachReviewForm');
    if (!form) return;

    const select = document.getElementById('coach_reservation_id');
    const submitButton = document.getElementById('coachReviewSubmit');
    const errorBox = document.getElementById('coachReviewError');
    const token = document.querySelector('meta[name="csrf-token"]')?.content;

    const setError = (message) => {
        errorBox.textContent = message;
        errorBox.classList.toggle('show', Boolean(message));
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        setError('');

        const selectedOption = select.options[select.selectedIndex];
        const coachId = selectedOption?.dataset.coachId;
        const rating = form.querySelector('input[name="rating"]:checked')?.value;
        const review = document.getElementById('coach_review').value.trim();

        if (!select.value || !coachId) {
            setError('Pilih booking coach yang ingin direview.');
            return;
        }

        if (!rating) {
            setError('Pilih rating coach terlebih dahulu.');
            return;
        }

        submitButton.disabled = true;
        submitButton.textContent = 'Mengirim...';

        try {
            const url = form.dataset.urlTemplate.replace('__COACH_ID__', coachId);
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                },
                body: JSON.stringify({
                    reservation_id: select.value,
                    rating,
                    review,
                }),
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(data.error || data.message || 'Review coach gagal dikirim.');
            }

            showToast('success', data.message || 'Review coach berhasil dikirim.');
            setTimeout(() => window.location.reload(), 900);
        } catch (error) {
            setError(error.message);
            showToast('error', error.message);
        } finally {
            submitButton.disabled = false;
            submitButton.textContent = 'Submit Review Coach';
        }
    });
});
</script>
@endpush
