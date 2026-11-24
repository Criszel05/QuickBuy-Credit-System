<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QuickBuy Register</title>
    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f7f1e8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .register-box {
            background: white;
            width: 400px;
            max-width: 100%;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        h2 {
            color: #4b2e20;
            text-align: center;
            margin-bottom: 8px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            color: #4b2e20;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 17px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        button {
            width: 100%;
            padding: 13px;
            background: #4b2e20;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover { background: #68432f; }

        .error {
            color: #b00020;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #777;
        }

        a {
            color: #4b2e20;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="register-box">
    <h2>QuickBuy</h2>
    <p class="subtitle">Create Your Account</p>

    @if ($errors->any())
        <div class="error">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ url('/register') }}">
        @csrf

        <label>Full Name</label>
        <input type="text" name="name"
               value="{{ old('name') }}"
               placeholder="Enter your full name" required>

        <label>Email Address</label>
        <input type="email" name="email"
               value="{{ old('email') }}"
               placeholder="Enter your email" required>

        <label>Password</label>
        <input type="password" name="password"
               placeholder="At least 8 characters" required>

        <label>Confirm Password</label>
        <input type="password" name="password_confirmation"
               placeholder="Confirm your password" required>

        <button type="submit">Create Account</button>
    </form>

    <p class="login-link">
        Already have an account?
        <a href="{{ url('/login') }}">Login here</a>
    </p>
</div>
</body>
</html>
