<!DOCTYPE html>
<html>
<head>
    <title>Verify Your Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            color: #4CAF50;
            text-align: center;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            text-align: center;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px auto;
            background-color: #4CAF50;
            color: #ffffff;
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            text-align: center;
        }
        .button:hover {
            background-color: #45a049;
        }
        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #888;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Silahkan Verifikasi Akun Anda</h2>
        <p>Untuk memverifikasi akun anda, silahkan klik tombol di bawah ini:</p>
        <div style="text-align: center">
        <a href="{{ $url }}" class="button" style="color: white">Verifikasi Akun</a>
        </div>
        <p class="footer">Terima kasih,<br>Smart Finance Team</p>
    </div>
</body>
</html>
