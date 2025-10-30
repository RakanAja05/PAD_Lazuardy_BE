<!DOCTYPE html>
<html>
<head>
    <title>Google Login</title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: #f5f5f5;
        }
        .loader {
            text-align: center;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4285f4;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        p {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="loader">
        <div class="spinner"></div>
        <p>Memproses login...</p>
    </div>
    
    <script>
        // Data yang akan dikirim
        const data = {!! $data !!};
        
        // Langsung kirim ke parent window
        if (window.opener) {
            // Kirim via postMessage
            window.opener.postMessage(data, '{{ $frontendUrl }}');
            // Langsung close
            window.close();
        } else {
            // Jika tidak ada opener (dibuka di tab baru), redirect ke frontend
            window.location.href = '{{ $frontendUrl }}';
        }
    </script>
</body>
</html>
