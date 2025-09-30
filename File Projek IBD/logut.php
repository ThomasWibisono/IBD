<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Logout</title>
    <meta http-equiv="refresh" content="3;url=login.php"> <!-- Auto redirect -->
    <style>
        body {
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #667eea, #764ba2);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: #fff;
            overflow: hidden;
        }
        .box {
            text-align: center;
            animation: fadeIn 1.5s ease-in-out;
        }
        h1 {
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        p {
            font-size: 1.1rem;
            opacity: 0.8;
        }
        .loader {
            margin: 20px auto;
            border: 6px solid rgba(255, 255, 255, 0.3);
            border-top: 6px solid #fff;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="box">
        <h1>👋 Anda berhasil logout</h1>
        <p>Terima kasih sudah menggunakan sistem ini.</p>
        <div class="loader"></div>
        <p>Anda akan diarahkan ke halaman login...</p>
    </div>
</body>
</html>
