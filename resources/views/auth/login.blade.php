<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Admin Panel</title>

    <!-- Bootstrap CSS -->
    @include('backend.partials.style')

    <!-- Bootstrap Icons (for input icons) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #0da100, #0c460c);
            /* same as welcome page */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            color: #fff;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.1);
            /* glass effect */
            backdrop-filter: blur(12px);
            padding: 50px 40px;
            border-radius: 20px;
            box-shadow: 0px 15px 35px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 480px;
        }

        .login-card h3 {
            font-weight: 700;
            color: #fff;
            font-size: 1.8rem;
        }

        .login-card p {
            color: #f1f1f1;
            font-size: 1rem;
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px 0 0 10px;
            border: none;
            color: #fff;
            font-size: 1.2rem;
        }

        .form-control {
            border-radius: 0 10px 10px 0;
            padding: 12px 15px;
            font-size: 1rem;
            border: none;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }

        .form-control:focus {
            outline: none;
            box-shadow: 0px 0px 8px rgba(255, 255, 255, 0.5);
        }

        .btn-custom {
            background: #4f46e5;
            color: #fff;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            padding: 12px;
            transition: all 0.3s ease-in-out;
            width: 140px;
            border: none;
        }

        .btn-custom:hover {
            background: #3730a3;
            transform: translateY(-2px);
        }

        .forgot-link {
            font-size: 0.9rem;
            color: #fff;
            transition: opacity 0.3s;
        }

        .forgot-link:hover {
            opacity: 0.8;
        }

        .text-dark {
            color: #fff !important;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <h3>Welcome Back 👋</h3>
            <p class="">Sign in to continue</p>
        </div>

        <!-- Session Status -->
        @if (session('status'))
            <div class="alert alert-success mb-3">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label text-white">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope-fill"></i></span>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                        name="email" value="{{ old('email') }}" required autofocus placeholder="Enter your email">
                </div>
                @error('email')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label for="password" class="form-label text-white">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password"
                        name="password" required placeholder="Enter your password">
                </div>
                @error('password')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                <label class="form-check-label text-white" for="remember_me">Remember me</label>
            </div>

            <!-- Buttons -->
            <div class="d-flex justify-content-between align-items-center">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link text-decoration-none">Forgot
                        password?</a>
                @endif
                <button type="submit" class="btn btn-custom">Login</button>
            </div>
        </form>

    </div>
</body>

</html>

