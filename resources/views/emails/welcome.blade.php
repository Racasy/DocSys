<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laipni lūdzam DocSys</title>
    <style>
        /* Reset styles for email clients */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #FFEDD8;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #FFEDD8;
        }
        .content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            height: 32px;
        }
        h1 {
            color: #891652;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }
        p {
            color: #891652;
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .details {
            background-color: #FFEDD8;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .details p {
            margin: 0;
            color: #891652;
            font-size: 16px;
        }
        .button-container {
            text-align: center;
            margin-bottom: 20px;
        }
        .button {
            background-color: #891652;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
            font-weight: medium;
            display: inline-block;
        }
        .button:hover {
            background-color: #d4a258;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
        }
        .footer p {
            color: #891652;
            font-size: 14px;
            margin: 0;
        }
        .footer p.small {
            font-size: 12px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="{{ config('app.url') }}/images/logo2.png" alt="Abrams DocSys Logo" />
        </div>

        <!-- Content -->
        <div class="content">
            <h1>Sveiki, {{ $user->name }}</h1>

            <p>Laipni lūdzam Abrams DocSys! Mēs esam priecīgi, ka pievienojāties mums.</p>

            <p>Jūsu pieteikšanās dati ir šādi:</p>

            <div class="details">
                <p><strong>E-pasts:</strong> {{ $user->email }}</p>
                <p><strong>Parole:</strong> {{ $temporaryPassword }}</p>
            </div>

            <p>Šī parole ir zināma tikai jums. Jūs varat to mainīt jebkurā laikā savā konta iestatījumos.</p>

            <div class="button-container">
                <a href="{{ route('login') }}" class="button">Pieslēgties</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Ar cieņu,<br>Abrams DocSys komanda</p>
            <p class="small">© "SIA Abrams Business Services" Visas tiesības aizsargātas.</p>
        </div>
    </div>
</body>
</html>