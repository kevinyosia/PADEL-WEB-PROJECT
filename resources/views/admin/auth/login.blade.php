<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Bandeja Padel Arena</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Figtree', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
        }

        .login-card {
            background: #fff;
            border-radius: 16px;
            padding: 48px 32px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-header-icon {
            font-size: 48px;
            margin-bottom: 16px;
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .login-header p {
            font-size: 13px;
            color: #64748b;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 8px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Figtree', sans-serif;
            color: #0f172a;
            background: #f8fafc;
            outline: none;
            transition: all 0.15s;
        }

        .form-input:focus {
            border-color: #2563eb;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-input.is-error {
            border-color: #ef4444;
            background: #fef2f2;
        }

        .form-error {
            font-size: 11px;
            color: #ef4444;
            margin-top: 6px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #475569;
        }

        .checkbox {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #2563eb;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Figtree', sans-serif;
            cursor: pointer;
            transition: all 0.15s;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .btn-login:hover {
            background: #1d4ed8;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        }

        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 14px 16px;
            margin-top: 24px;
            font-size: 12px;
            color: #1e40af;
            line-height: 1.6;
        }

        .info-box strong {
            display: block;
            margin-bottom: 6px;
        }

        .auth-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #b91c1c;
            display: flex;
            gap: 8px;
        }

        .auth-error:empty {
            display: none;
        }

        .auth-error-icon {
            flex-shrink: 0;
            font-size: 14px;
        }

        .nav-buttons {
            position: fixed;
            bottom: 32px;
            left: 32px;
            display: flex;
            gap: 12px;
            z-index: 50;
        }

        .nav-btn {
            padding: 10px 16px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
            font-family: 'Figtree', sans-serif;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.15s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .nav-btn:hover {
            background: #1d4ed8;
            box-shadow: 0 6px 12px rgba(37, 99, 235, 0.3);
            transform: translateY(-2px);
        }

        .nav-btn.secondary {
            background: #8b5cf6;
        }

        .nav-btn.secondary:hover {
            background: #7c3aed;
        }
    </style>
</head>
<body>
    {{-- Bottom Left Navigation Buttons --}}
    <div class="nav-buttons">
        <a href="{{ route('login') }}" class="nav-btn">User Login</a>
        <a href="{{ route('manager.login') }}" class="nav-btn secondary">Manager Login</a>
    </div>

    <div class="login-container">
        <div class="login-card">
            {{-- Header --}}
            <div class="login-header">
                <div class="login-header-icon">🏐</div>
                <h1>Admin Portal</h1>
                <p>Bandeja Padel Arena</p>
            </div>

            {{-- Error display --}}
            @if ($errors->any())
                <div class="auth-error">
                    <span class="auth-error-icon">⚠️</span>
                    <div>
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('admin.login.submit') }}">
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        class="form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                        placeholder="admin@bandeja.com"
                        required
                        autofocus
                    >
                    @error('email')
                        <div class="form-error">⚠ {{ $message }}</div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-input {{ $errors->has('password') ? 'is-error' : '' }}"
                        placeholder="••••••••"
                        required
                    >
                    @error('password')
                        <div class="form-error">⚠ {{ $message }}</div>
                    @enderror
                </div>

                {{-- Remember --}}
                <div class="remember-row">
                    <input
                        id="remember"
                        type="checkbox"
                        name="remember"
                        class="checkbox"
                    >
                    <label for="remember">Remember me</label>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn-login">Login</button>
            </form>

            {{-- Info Box --}}
            <div class="info-box">
                <strong>Demo Account:</strong>
                Email: 123@admin.local<br>
                Password: 321
            </div>
        </div>
    </div>
</body>
</html>
