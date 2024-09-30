<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receive Your Crypto</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            color: #333333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .brand {
            color: #336AEA;
            font-weight: 700;
        }
        .highlight {
            color: #41aef1;
        }
        .claim-text {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .claim-link {
            display: block;
            width: 100%;
            max-width: 300px;
            margin: 0 auto;
            padding: 10px;
            background-color: #336AEA;
            color: #ffffff;
            text-align: center;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            letter-spacing: 1px;
        }
        .signature {
            text-align: right;
            margin-top: 20px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="brand">
                Zend<span class="highlight">Crypto</span>
            </h1>
        </div>
        <h2>Hello,</h2>
        <p class="claim-text">
            A user with the address <strong>{{ $address }}</strong> has sent you <strong>{{ $amount }} {{ $token }}</strong>.
            Click the link below to claim your crypto.
        </p>
        <a href="{{ $link }}" class="claim-link">Claim Your Crypto</a>
        <p class="signature">
            Best Regards,<br>
            Zend Team
        </p>
    </div>
</body>
</html>
