@extends('layouts.admin')

@section('content')
<style>
        .form-page-wrap { max-width: 720px; }

        .form-page-header { margin-bottom: 28px; }
        .form-page-header h1 { font-size: 26px; font-weight: 800; color: #0F172A; }
        .form-page-header p  { font-size: 13px; color: #64748B; margin-top: 4px; }

        .form-card {
            background: #fff; border: 1px solid #E2E8F0;
            border-radius: 14px; overflow: hidden;
        }

        .form-section {
            padding: 24px 28px;
            border-bottom: 1px solid #F1F5F9;
        }
        .form-section:last-of-type { border-bottom: none; }

        .form-section-title {
            font-size: 11px; font-weight: 800; color: #94A3B8;
            text-transform: uppercase; letter-spacing: 0.09em;
            margin-bottom: 18px;
            display: flex; align-items: center; gap: 8px;
        }
        .form-section-title::after {
            content: ''; flex: 1; height: 1px; background: #F1F5F9;
        }

        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; }
        .form-row.full { grid-template-columns: 1fr; }

        .field-group { display: flex; flex-direction: column; gap: 6px; }
        .field-label {
            font-size: 11px; font-weight: 700; color: #374151;
            text-transform: uppercase; letter-spacing: 0.06em;
        }
        .field-required { color: #EF4444; margin-left: 2px; }

        .field-input, .field-select, .field-textarea {
            padding: 10px 14px;
            border: 1.5px solid #E2E8F0; border-radius: 9px;
            font-size: 14px; font-family: 'Figtree', sans-serif;
            color: #0F172A; background: #F8FAFC;
            outline: none; transition: all 0.15s;
            width: 100%;
        }
        .field-input:focus, .field-select:focus, .field-textarea:focus {
            border-color: #2563EB; background: #fff;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .field-input.is-error, .field-select.is-error, .field-textarea.is-error {
            border-color: #EF4444; background: #FEF2F2;
        }
        .field-textarea { resize: vertical; min-height: 90px; line-height: 1.5; }
        .field-select {
            appearance: none; -webkit-appearance: none; cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394A3B8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 14px center;
            padding-right: 36px;
        }
        .field-hint { font-size: 11px; color: #94A3B8; margin-top: 2px; }
        .field-error { font-size: 11px; color: #EF4444; display: flex; align-items: center; gap: 4px; }

        /* Readonly display fields */
        .field-readonly {
            padding: 10px 14px;
            border: 1.5px solid #E2E8F0; border-radius: 9px;
            font-size: 14px; font-weight: 600;
            color: #64748B; background: #F1F5F9;
            width: 100%;
            display: flex; align-items: center;
        }
        .readonly-note { font-size: 10px; color: #CBD5E1; font-weight: 400; margin-left: 8px; }

        /* Price input with prefix */
        .input-prefix-wrap { position: relative; }
        .input-prefix {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            font-size: 13px; font-weight: 700; color: #64748B; pointer-events: none;
        }
        .input-prefix-wrap .field-input { padding-left: 42px; }

        /* Coach identity panel at top */
        .coach-identity-panel {
            display: flex; align-items: center; gap: 16px;
            background: #F8FAFC; border: 1px solid #E2E8F0;
            border-radius: 10px; padding: 16px 20px;
            margin-bottom: 20px;
        }
        .coach-avatar-lg {
            width: 52px; height: 52px; border-radius: 50%;
            background: linear-gradient(135deg, #2563EB, #60A5FA);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; font-weight: 800; color: #fff; flex-shrink: 0;
            position: relative;
        }
        .coach-id-info { flex: 1; }
        .coach-id-name { font-size: 16px; font-weight: 800; color: #0F172A; }
        .coach-id-email { font-size: 12px; color: #64748B; margin-top: 2px; }
        .immutable-note {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 10px; font-weight: 700; color: #F59E0B;
            background: #FEF9C3; padding: 4px 10px; border-radius: 20px;
            flex-shrink: 0;
        }

        /* Schedule checkboxes */
        .schedule-grid { display: flex; gap: 10px; flex-wrap: wrap; }
        .day-checkbox-wrap { display: flex; flex-direction: column; align-items: center; gap: 5px; }
        .day-checkbox-label {
            font-size: 10px; font-weight: 800; color: #64748B;
            text-transform: uppercase; letter-spacing: 0.06em;
        }
        .day-checkbox { display: none; }
        .day-checkbox-btn {
            width: 40px; height: 40px; border-radius: 9px;
            border: 1.5px solid #E2E8F0; background: #F8FAFC;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 800; color: #94A3B8;
            cursor: pointer; transition: all 0.15s; user-select: none;
        }
        .day-checkbox:checked + .day-checkbox-btn {
            background: #DBEAFE; border-color: #3B82F6; color: #1D4ED8;
            box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
        }

        /* Photo upload */
        .photo-upload-wrap {
            position: relative; flex-shrink: 0;
        }
        .coach-avatar-lg {
            cursor: pointer;
            transition: all 0.2s;
        }
        .coach-avatar-lg:hover .avatar-overlay {
            opacity: 1;
        }
        .avatar-img {
            width: 52px; height: 52px; border-radius: 50%;
            object-fit: cover; border: 2px solid #E2E8F0;
            display: block;
        }
        .avatar-overlay {
            position: absolute; inset: 0;
            border-radius: 50%;
            background: rgba(0,0,0,0.45);
            display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: opacity 0.2s;
            cursor: pointer;
        }
        .avatar-overlay svg { width: 18px; height: 18px; color: #fff; }
        .avatar-change-hint {
            font-size: 10px; color: #94A3B8; margin-top: 5px;
            text-align: center; line-height: 1.3;
        }
        .photo-upload-input { display: none; }

        /* Form actions */
        .form-actions {
            padding: 20px 28px;
            background: #F8FAFC; border-top: 1px solid #E2E8F0;
            display: flex; align-items: center; justify-content: flex-end; gap: 10px;
        }
        .btn-cancel {
            padding: 10px 20px;
            background: #fff; color: #475569;
            border: 1.5px solid #E2E8F0; border-radius: 9px;
            font-size: 13px; font-weight: 700; font-family: 'Figtree', sans-serif;
            text-decoration: none; cursor: pointer; transition: all 0.15s;
        }
        .btn-cancel:hover { background: #F1F5F9; }
        .btn-submit {
            padding: 10px 22px;
            background: #2563EB; color: #fff;
            border: none; border-radius: 9px;
            font-size: 13px; font-weight: 700; font-family: 'Figtree', sans-serif;
            cursor: pointer; transition: all 0.15s;
        }
        .btn-submit:hover { background: #1D4ED8; box-shadow: 0 4px 14px rgba(37,99,235,0.3); }
    </style>

    <div class="form-page-wrap">
        {{-- Header --}}
        <div class="form-page-header">
            <h1>Edit Coach</h1>
            <p>Perbarui data, tarif, dan jadwal coach</p>
        </div>

        <div class="form-card">
            <form method="POST" action="{{ route('admin.coaches.update', $coach) }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                {{-- ── Identity Panel (readonly) ── --}}
                <div class="form-section">
                    <div class="form-section-title">Identitas Coach</div>

                    <div class="coach-identity-panel">
                        {{-- Photo Upload --}}
                        <div class="photo-upload-wrap">
                            <input
                                type="file"
                                id="photo_input"
                                name="photo"
                                class="photo-upload-input"
                                accept="image/jpeg,image/png,image/webp"
                            >
                            <div class="coach-avatar-lg" id="avatar-trigger" onclick="document.getElementById('photo_input').click()" title="Klik untuk ganti foto">
                                @if($coach->photo)
                                    <img src="{{ asset('storage/' . $coach->photo) }}" alt="Foto Coach" class="avatar-img" id="avatar-preview">
                                @else
                                    <span id="avatar-initials">{{ strtoupper(substr($coach->user->name ?? 'C', 0, 1)) }}</span>
                                    <img src="" alt="" class="avatar-img" id="avatar-preview" style="display:none;">
                                @endif
                                <div class="avatar-overlay">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="avatar-change-hint">Klik untuk<br>ganti foto</div>
                            @error('photo')
                                <div class="field-error" style="margin-top:4px; font-size:10px;">⚠ {{ $message }}</div>
                            @enderror
                        </div>
                        <div class="coach-id-info">
                            <div class="coach-id-name">{{ $coach->user->name }}</div>
                            <div class="coach-id-email">{{ $coach->user->email }} &nbsp;·&nbsp; {{ $coach->user->phone }}</div>
                        </div>
                        <span class="immutable-note">🔒 Tidak dapat diubah</span>
                    </div>

                    <div class="form-row">
                        <div class="field-group">
                            <label class="field-label">Nama</label>
                            <div class="field-readonly">
                                {{ $coach->user->name }}
                                <span class="readonly-note">read-only</span>
                            </div>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Email</label>
                            <div class="field-readonly">
                                {{ $coach->user->email }}
                                <span class="readonly-note">read-only</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Keahlian & Rate ── --}}
                <div class="form-section">
                    <div class="form-section-title">Keahlian & Tarif</div>

                    <div class="form-row full" style="margin-bottom:18px;">
                        <div class="field-group">
                            <label for="deskripsi_keahlian" class="field-label">Deskripsi Keahlian <span class="field-required">*</span></label>
                            <textarea
                                id="deskripsi_keahlian" name="deskripsi_keahlian"
                                class="field-textarea {{ $errors->has('deskripsi_keahlian') ? 'is-error' : '' }}"
                                maxlength="1000" required
                            >{{ old('deskripsi_keahlian', $coach->deskripsi_keahlian) }}</textarea>
                            @error('deskripsi_keahlian')
                                <div class="field-error">⚠ {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field-group">
                            <label for="harga_per_jam" class="field-label">Harga per Jam <span class="field-required">*</span></label>
                            <div class="input-prefix-wrap">
                                <span class="input-prefix">Rp</span>
                                <input
                                    id="harga_per_jam" type="number" name="harga_per_jam"
                                    value="{{ old('harga_per_jam', $coach->harga_per_jam) }}"
                                    class="field-input {{ $errors->has('harga_per_jam') ? 'is-error' : '' }}"
                                    min="10000" required
                                >
                            </div>
                            <span class="field-hint">Minimal Rp 10.000</span>
                            @error('harga_per_jam')
                                <div class="field-error">⚠ {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-group">
                            <label for="availability_status" class="field-label">Status Ketersediaan <span class="field-required">*</span></label>
                            <select
                                id="availability_status" name="availability_status"
                                class="field-select {{ $errors->has('availability_status') ? 'is-error' : '' }}"
                                required
                            >
                                @php $currentStatus = old('availability_status', $coach->availability_status); @endphp
                                <option value="active"   {{ $currentStatus === 'active'   ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ $currentStatus === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="on_leave" {{ $currentStatus === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                            </select>
                            @error('availability_status')
                                <div class="field-error">⚠ {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- ── Jadwal ── --}}
                <div class="form-section">
                    <div class="form-section-title">Jadwal Mingguan</div>

                    @error('schedule')
                        <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:8px;padding:10px 14px;margin-bottom:14px;font-size:12px;color:#B91C1C;">
                            ⚠ {{ $message }}
                        </div>
                    @enderror

                    <div class="schedule-grid">
                        @foreach(['mon'=>'Sen','tue'=>'Sel','wed'=>'Rab','thu'=>'Kam','fri'=>'Jum'] as $key => $label)
                            @php
                                $isChecked = old("schedule.$key") !== null
                                    ? (bool) old("schedule.$key")
                                    : $coach->isAvailableOnDay($key);
                            @endphp
                            <div class="day-checkbox-wrap">
                                <span class="day-checkbox-label">{{ $label }}</span>
                                <input
                                    type="checkbox"
                                    id="schedule_{{ $key }}"
                                    name="schedule[{{ $key }}]"
                                    value="1"
                                    class="day-checkbox"
                                    {{ $isChecked ? 'checked' : '' }}
                                >
                                <label for="schedule_{{ $key }}" class="day-checkbox-btn">
                                    {{ strtoupper($key[0]) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <p class="field-hint" style="margin-top:10px;">Pilih hari kerja coach. Minimal 1 hari.</p>
                </div>

                {{-- Actions --}}
                <div class="form-actions">
                    <a href="{{ route('admin.coaches.index') }}" class="btn-cancel">Batal</a>
                    <button type="submit" class="btn-submit">Simpan Perubahan →</button>
                </div>

            </form>
        </div>
    </div>

    <script>
        // Preview foto saat dipilih
        document.getElementById('photo_input').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(ev) {
                const preview = document.getElementById('avatar-preview');
                const initials = document.getElementById('avatar-initials');

                preview.src = ev.target.result;
                preview.style.display = 'block';
                if (initials) initials.style.display = 'none';

                // Hapus background gradient setelah ada foto
                document.getElementById('avatar-trigger').style.background = 'transparent';
            };
            reader.readAsDataURL(file);
        });

        // Inject hidden 0 untuk hari yang tidak dicentang saat submit
        document.querySelector('form').addEventListener('submit', function() {
            const days = ['mon','tue','wed','thu','fri'];
            days.forEach(day => {
                const cb = document.getElementById('schedule_' + day);
                if (!cb.checked) {
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = `schedule[${day}]`;
                    hidden.value = '0';
                    this.appendChild(hidden);
                }
            });
        });
    </script>
@endsection