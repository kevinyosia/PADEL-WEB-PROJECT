@extends('layouts.user')
@section('title', 'Coaches — Bandeja Padel Arena')

@push('styles')
<style>
    :root {
        --green-deep: #2D4A1E; --green-mid: #3A5C28;
        --cream-bg: #EDE8D8; --cream-card: #F5F1E6;
        --text-dark: #1A1A0F; --text-muted: #6B6B5A;
        --gold: #C8922A;
    }
    .page-wrap { min-height: 100vh; background: var(--cream-bg); padding: 28px 32px; }
    .page-header { margin-bottom: 24px; }
    .page-header h1 { font-family: 'DM Serif Display', serif; font-size: 28px; color: var(--text-dark); }
    .page-header p  { font-size: 13px; color: var(--text-muted); margin-top: 4px; }

    /* Filter pills */
    .filter-bar { display: flex; gap: 8px; margin-bottom: 22px; flex-wrap: wrap; }
    .filter-pill {
        padding: 7px 16px; border-radius: 50px;
        background: var(--cream-card); border: 1.5px solid rgba(0,0,0,0.08);
        font-size: 12px; font-weight: 700; color: var(--text-muted);
        cursor: pointer; transition: all .15s;
    }
    .filter-pill:hover { background: #E8E3D3; }
    .filter-pill.active { background: var(--green-mid); color: #fff; border-color: var(--green-mid); }

    /* Coach cards */
    .coaches-list { display: flex; flex-direction: column; gap: 12px; }

    .coach-card {
        background: var(--cream-card); border-radius: 16px;
        padding: 18px 22px;
        border: 1px solid rgba(0,0,0,0.06);
        display: flex; align-items: center; gap: 18px;
        transition: box-shadow .18s;
    }
    .coach-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,0.08); }

    .coach-avatar {
        width: 60px; height: 60px; border-radius: 50%;
        background: linear-gradient(135deg, var(--green-mid), #7AB55C);
        flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; font-weight: 800; color: #fff;
    }

    .coach-info { flex: 1; min-width: 0; }
    .coach-name-row { display: flex; align-items: center; gap: 10px; margin-bottom: 3px; }
    .coach-name { font-size: 17px; font-weight: 800; color: var(--text-dark); }
    .avail-badge {
        font-size: 10px; font-weight: 700; padding: 3px 9px;
        border-radius: 20px; text-transform: uppercase; letter-spacing: .05em;
    }
    .avail-active   { background: #D6EDCC; color: #2D5016; }
    .avail-inactive { background: #E8E3D3; color: #7A7A6A; }
    .avail-on_leave { background: #FEF3CD; color: #92610A; }

    .coach-desc { font-size: 12px; color: var(--text-muted); line-height: 1.5; margin-bottom: 8px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    .coach-meta { display: flex; align-items: center; gap: 14px; }
    .coach-rating { display: flex; align-items: center; gap: 4px; font-size: 13px; font-weight: 700; color: var(--text-dark); }
    .star { color: var(--gold); font-size: 14px; }
    .rating-count {
        border: 0; background: none; padding: 0; font: inherit;
        font-size: 11px; color: var(--text-muted); font-weight: 600;
        cursor: pointer; text-decoration: none;
    }
    .rating-count:hover { color: var(--green-deep); text-decoration: underline; }
    .coach-rate { font-size: 15px; font-weight: 800; color: var(--text-dark); }
    .per-sesi { font-size: 11px; color: var(--text-muted); font-weight: 600; }

    /* Schedule days */
    .sched-days { display: flex; gap: 4px; }
    .sched-day {
        width: 24px; height: 24px; border-radius: 6px;
        font-size: 9px; font-weight: 800; text-transform: uppercase;
        display: flex; align-items: center; justify-content: center;
    }
    .sched-on  { background: #D6EDCC; color: var(--green-deep); }
    .sched-off { background: #E8E3D3; color: #B0AA98; }

    /* Book button */
    .book-btn {
        padding: 11px 24px; background: var(--green-deep); color: #fff;
        border: none; border-radius: 50px;
        font-size: 13px; font-weight: 700; font-family: 'Figtree', sans-serif;
        cursor: pointer; transition: all .18s; text-decoration: none;
        white-space: nowrap; flex-shrink: 0;
    }
    .book-btn:hover { background: var(--green-mid); }
    .book-btn.inactive { background: #C5C0B0; cursor: not-allowed; }

    /* Schedule expand */
    .sched-panel {
        display: none; border-top: 1px solid rgba(0,0,0,0.06);
        padding-top: 14px; margin-top: 14px;
    }
    .sched-panel.open { display: block; }
    .sched-grid {
        display: grid; grid-template-columns: repeat(5, 1fr);
        gap: 6px;
    }
    .sched-col { text-align: center; }
    .sched-col-day { font-size: 10px; font-weight: 800; color: var(--text-muted); text-transform: uppercase; margin-bottom: 6px; }

    .empty-state { text-align: center; padding: 64px 20px; }
    .empty-state .e-icon { font-size: 40px; margin-bottom: 10px; }
    .empty-state p { color: var(--text-muted); font-size: 14px; }

    .reviews-modal-overlay {
        display: none; position: fixed; inset: 0; z-index: 9000;
        background: rgba(15, 23, 42, 0.42); padding: 24px;
        align-items: center; justify-content: center;
    }
    .reviews-modal-overlay.show { display: flex; }
    .reviews-modal {
        width: min(620px, 100%); max-height: min(720px, 90vh);
        background: var(--cream-card); border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.08);
        box-shadow: 0 24px 80px rgba(0,0,0,0.22);
        display: flex; flex-direction: column; overflow: hidden;
    }
    .reviews-modal-head {
        display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;
        padding: 22px 24px 16px; border-bottom: 1px solid rgba(0,0,0,0.08);
    }
    .reviews-modal-title { font-size: 18px; font-weight: 800; color: var(--text-dark); }
    .reviews-modal-sub { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
    .reviews-modal-close {
        width: 32px; height: 32px; border-radius: 50%; border: 1px solid rgba(0,0,0,0.12);
        background: var(--cream-bg); color: var(--text-dark); cursor: pointer; font-size: 20px; line-height: 1;
    }
    .reviews-modal-body { padding: 18px 24px 24px; overflow-y: auto; }
    .coach-review-item { padding: 14px 0; border-bottom: 1px solid rgba(0,0,0,0.08); }
    .coach-review-item:last-child { border-bottom: 0; }
    .coach-review-row { display: flex; justify-content: space-between; gap: 16px; margin-bottom: 8px; }
    .coach-review-user { font-size: 14px; font-weight: 800; color: var(--text-dark); }
    .coach-review-date { font-size: 11px; color: var(--text-muted); white-space: nowrap; }
    .coach-review-stars { color: var(--gold); font-size: 13px; letter-spacing: 1px; margin-bottom: 6px; }
    .coach-review-text { font-size: 13px; color: var(--text-muted); line-height: 1.55; }
    .reviews-modal-empty { padding: 42px 12px; text-align: center; color: var(--text-muted); font-size: 14px; }
    .reviews-modal-loading { padding: 34px 12px; text-align: center; color: var(--text-muted); font-size: 14px; }

    @media (max-width: 720px) {
        .page-wrap { padding: 22px 18px; }
        .coach-card { align-items: flex-start; }
        .coach-meta { align-items: flex-start; flex-direction: column; gap: 8px; }
        .reviews-modal-overlay { padding: 16px; }
    }
</style>
@endpush

@section('content')
<div class="page-wrap">
    <div class="page-header">
        <h1>Coaches</h1>
        <p>Pilih pelatih profesional untuk sesi Anda</p>
    </div>

    {{-- Filter --}}
    <div class="filter-bar">
        <div class="filter-pill active" data-filter="all"     onclick="filterCoaches('all',this)">Semua</div>
        <div class="filter-pill"        data-filter="active"  onclick="filterCoaches('active',this)">Aktif</div>
        <div class="filter-pill"        data-filter="on_leave" onclick="filterCoaches('on_leave',this)">On Leave</div>
    </div>

    {{-- Coaches list --}}
    <div class="coaches-list" id="coachList">
        @forelse($coaches as $coach)
        @php
            $avgRating = $coach->reviews->avg('rating') ?? 0;
            $reviewCount = $coach->reviews->count();
            $days = ['mon'=>'S','tue'=>'S','wed'=>'R','thu'=>'K','fri'=>'J'];
        @endphp
        <div class="coach-card" data-status="{{ $coach->availability_status ?? 'inactive' }}" id="coachCard{{ $coach->id }}">
            <div class="coach-avatar">
                @if($coach->photo)
                    <img src="{{ asset('storage/' . $coach->photo) }}" alt="{{ $coach->user->name }}" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                @else
                    {{ strtoupper(substr($coach->user->name ?? 'C', 0, 1)) }}
                @endif
            </div>

            <div class="coach-info">
                <div class="coach-name-row">
                    <span class="coach-name">{{ $coach->user->name ?? 'Coach' }}</span>
                    @php $status = $coach->availability_status ?? 'inactive'; @endphp
                    <span class="avail-badge avail-{{ $status }}">
                        {{ $status === 'on_leave' ? 'On Leave' : ucfirst($status) }}
                    </span>
                </div>
                <div class="coach-desc">{{ $coach->deskripsi_keahlian }}</div>
                <div class="coach-meta">
                    <span class="coach-rating">
                        <span class="star">★</span>
                        {{ $avgRating > 0 ? number_format($avgRating,1) : '—' }}
                        <button
                            type="button"
                            class="rating-count"
                            data-coach-id="{{ $coach->id }}"
                            data-coach-name="{{ $coach->user->name ?? 'Coach' }}"
                            data-review-url="{{ route('coach.reviews.get', $coach) }}"
                            onclick="openCoachReviews(this)"
                        >| {{ $reviewCount }} Reviews</button>
                    </span>
                    <span class="coach-rate">
                        Rp{{ number_format($coach->harga_per_jam,0,',','.') }}
                        <span class="per-sesi">/peserta/sesi</span>
                    </span>
                    @if(!empty($coach->schedule))
                    <div class="sched-days">
                        @foreach($days as $key => $label)
                            <div class="sched-day {{ $coach->isAvailableOnDay($key) ? 'sched-on' : 'sched-off' }}">{{ $label }}</div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state">
            <div class="e-icon">👤</div>
            <p>Belum ada coach yang terdaftar.</p>
        </div>
        @endforelse
    </div>
</div>

<div class="reviews-modal-overlay" id="coachReviewsModal" onclick="closeCoachReviews(event)">
    <div class="reviews-modal" role="dialog" aria-modal="true" aria-labelledby="coachReviewsTitle" onclick="event.stopPropagation()">
        <div class="reviews-modal-head">
            <div>
                <div class="reviews-modal-title" id="coachReviewsTitle">Coach Reviews</div>
                <div class="reviews-modal-sub" id="coachReviewsSummary">Memuat review...</div>
            </div>
            <button type="button" class="reviews-modal-close" onclick="closeCoachReviews()">&times;</button>
        </div>
        <div class="reviews-modal-body" id="coachReviewsBody">
            <div class="reviews-modal-loading">Memuat review...</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const coachReviewsModal = document.getElementById('coachReviewsModal');
const coachReviewsTitle = document.getElementById('coachReviewsTitle');
const coachReviewsSummary = document.getElementById('coachReviewsSummary');
const coachReviewsBody = document.getElementById('coachReviewsBody');

function filterCoaches(filter, el) {
    document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
    el.classList.add('active');
    document.querySelectorAll('.coach-card').forEach(card => {
        const match = filter === 'all' || card.dataset.status === filter;
        card.style.display = match ? '' : 'none';
    });
}

async function openCoachReviews(button) {
    const coachName = button.dataset.coachName || 'Coach';
    coachReviewsTitle.textContent = `Review ${coachName}`;
    coachReviewsSummary.textContent = 'Memuat review...';
    coachReviewsBody.innerHTML = '<div class="reviews-modal-loading">Memuat review...</div>';
    coachReviewsModal.classList.add('show');

    try {
        const response = await fetch(button.dataset.reviewUrl, {
            headers: { 'Accept': 'application/json' }
        });
        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.error || 'Review coach gagal dimuat.');
        }

        const total = data.stats?.total ?? data.reviews.length;
        const average = data.stats?.average ?? 0;
        coachReviewsSummary.textContent = total > 0
            ? `${average}/5 dari ${total} review`
            : 'Belum ada review untuk coach ini.';

        if (!data.reviews.length) {
            coachReviewsBody.innerHTML = '<div class="reviews-modal-empty">Belum ada user yang memberikan review untuk coach ini.</div>';
            return;
        }

        coachReviewsBody.innerHTML = data.reviews.map((review) => {
            const rating = Number(review.rating || 0);
            const filled = '★'.repeat(rating);
            const empty = '☆'.repeat(5 - rating);
            const userName = escapeHtml(review.user?.name || 'Member');
            const comment = escapeHtml(review.review || 'Tidak ada komentar.');
            const date = review.created_at ? new Date(review.created_at).toLocaleDateString('id-ID', {
                day: '2-digit', month: 'short', year: 'numeric'
            }) : '';

            return `
                <div class="coach-review-item">
                    <div class="coach-review-row">
                        <div class="coach-review-user">${userName}</div>
                        <div class="coach-review-date">${date}</div>
                    </div>
                    <div class="coach-review-stars">${filled}${empty}</div>
                    <div class="coach-review-text">${comment}</div>
                </div>
            `;
        }).join('');
    } catch (error) {
        coachReviewsSummary.textContent = 'Review tidak bisa dimuat.';
        coachReviewsBody.innerHTML = `<div class="reviews-modal-empty">${escapeHtml(error.message)}</div>`;
    }
}

function closeCoachReviews(event) {
    if (event && event.target !== coachReviewsModal) return;
    coachReviewsModal.classList.remove('show');
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeCoachReviews();
});
</script>
@endpush
