<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>We Miss You – Reactivate Your Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background-color: #f9f9f9;
            padding: 30px;
        }
        .container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
        h2 {
            color: #5A3733;
        }
        p {
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 24px;
            background-color: #EB6D2F;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            font-size: 12px;
            color: #aaa;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Hello {{ $branch->branch_name }},</h2>
        @if ($isFirstTime)
            <p>Welcome to {{ config('app.name') }}! We noticed that you haven't logged into your account yet.</p>

            <p>Your account is active and ready to use. Click below to log in and start sending messages today!</p>
        @else
            {{-- <p>We noticed your account has been inactive since <strong>{{ \Carbon\Carbon::parse($branch->last_seen)->format('F j, Y') }}</strong>.</p> --}}
            <p>We noticed that your last login was <strong>{{ \Carbon\Carbon::parse($branch->last_seen)->diffForHumans(null, true) }}</strong> ago (on {{ \Carbon\Carbon::parse($branch->last_seen)->format('F j, Y') }}).</p>

            <p>We'd love to have you back and help you stay connected with your customers.</p>
        @endif
        <a href="{{ url('/login') }}" class="button" style="color:#fff">Log in Now</a>

        <p>If you need any help or have questions, feel free to reach out to our support team at <a href="mailto:info@cloudrebue.co.ke">info@cloudrebue.co.ke</a>.</p>

        <p>We're looking forward to seeing you again!</p>

        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>
</html>
