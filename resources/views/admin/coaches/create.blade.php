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
        .field-select { appearance: none; -webkit-appearance: none; cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%2394A3B8' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 14px center;
            padding-right: 36px;
        }
        .field-hint { font-size: 11px; color: #94A3B8; margin-top: 2px; }
        .field-error { font-size: 11px; color: #EF4444; display: flex; align-items: center; gap: 4px; }

        /* Price input with prefix */
        .input-prefix-wrap { position: relative; }
        .input-prefix {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            font-size: 13px; font-weight: 700; color: #64748B; pointer-events: none;
        }
        .input-prefix-wrap .field-input { padding-left: 42px; }

        /* Schedule checkboxes */
        .schedule-grid { display: flex; gap: 10px; flex-wrap: wrap; }
        .day-checkbox-wrap { display: flex; flex-direction: column; align-items: center; gap: 5px; }
        .day-checkbox-label {
            font-size: 10px; font-weight: 800; color: #64748B;
            text-transform: uppercase; letter-spacing: 0.06em;
        }
        .day-checkbox {
            display: none; /* hide native */
        }
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

        /* Password note */
        .info-note {
            background: #EFF6FF; border: 1px solid #BFDBFE;
            border-radius: 9px; padding: 12px 16px;
            font-size: 12px; color: #1E40AF; line-height: 1.55;
            display: flex; gap: 8px; align-items: flex-start;
        }
        .info-note-icon { flex-shrink: 0; font-size: 14px; }
    </style>

    <div class="form-page-wrap">
        {{-- Header --}}
        <div class="form-page-header">
            <h1>Register Coach</h1>
            <p>Daftarkan coach baru — akun login akan otomatis dibuat</p>
        </div>

        {{-- Password info --}}
        <div class="info-note" style="margin-bottom:20px;">
            <span class="info-note-icon">ℹ️</span>
            <div>Password default akun coach: <strong>password123</strong> — coach dapat menggantinya setelah login pertama kali.</div>
        </div>

        <div class="form-card">
            <form method="POST" action="{{ route('admin.coaches.store') }}">
                @csrf

                {{-- ── Identitas ── --}}
                <div class="form-section">
                    <div class="form-section-title">Identitas Coach</div>

                    <div class="form-row" style="margin-bottom:18px;">
                        <div class="field-group">
                            <label for="name" class="field-label">Nama Lengkap <span class="field-required">*</span></label>
                            <input
                                id="name" type="text" name="name"
                                value="{{ old('name') }}"
                                class="field-input {{ $errors->has('name') ? 'is-error' : '' }}"
                                placeholder="Marco Silva" maxlength="100" required
                            >
                            @error('name')
                                <div class="field-error">⚠ {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="field-group">
                            <label for="phone" class="field-label">Nomor Telepon <span class="field-required">*</span></label>
                            <input
                                id="phone" type="tel" name="phone"
                                value="{{ old('phone') }}"
                                class="field-input {{ $errors->has('phone') ? 'is-error' : '' }}"
                                placeholder="+62 812 3456 7890" maxlength="20" required
                            >
                            @error('phone')
                                <div class="field-error">⚠ {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row full">
                        <div class="field-group">
                            <label for="email" class="field-label">Email <span class="field-required">*</span></label>
                            <input
                                id="email" type="email" name="email"
                                value="{{ old('email') }}"
                                class="field-input {{ $errors->has('email') ? 'is-error' : '' }}"
                                placeholder="coach@bandeja.com" required
                            >
                            @error('email')
                                <div class="field-error">⚠ {{ $message }}</div>
                            @enderror
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
                                placeholder="Contoh: Head Professional dengan pengalaman 10 tahun di padel kompetitif. Spesialisasi teknik serve dan strategi doubles."
                                maxlength="1000" required
                            >{{ old('deskripsi_keahlian') }}</textarea>
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
                                    value="{{ old('harga_per_jam') }}"
                                    class="field-input {{ $errors->has('harga_per_jam') ? 'is-error' : '' }}"
                                    placeholder="150000" min="10000" required
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
                                <option value="" disabled {{ old('availability_status') ? '' : 'selected' }}>Pilih status...</option>
                                <option value="active"   {{ old('availability_status') === 'active'   ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('availability_status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="on_leave" {{ old('availability_status') === 'on_leave' ? 'selected' : '' }}>On Leave</option>
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
                            <div class="day-checkbox-wrap">
                                <span class="day-checkbox-label">{{ $label }}</span>
                                <input
                                    type="checkbox"
                                    id="schedule_{{ $key }}"
                                    name="schedule[{{ $key }}]"
                                    value="1"
                                    class="day-checkbox"
                                    {{ old("schedule.$key") ? 'checked' : '' }}
                                    onchange="this.nextElementSibling.classList.toggle('checked-style', this.checked)"
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
                    <button type="submit" class="btn-submit">Register Coach →</button>
                </div>

            </form>
        </div>
    </div>

    <script>
        // Sync hidden boolean inputs karena Laravel butuh nilai false juga
        // (checkbox unchecked tidak terkirim, tapi StoreCoachRequest butuh 'required, boolean')
        document.querySelector('form').addEventListener('submit', function() {
            const days = ['mon','tue','wed','thu','fri'];
            days.forEach(day => {
                const cb = document.getElementById('schedule_' + day);
                // Inject hidden input 0 jika unchecked
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
