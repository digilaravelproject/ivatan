<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    
    <!-- Open Graph / Social Media Meta Tags -->
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ $title }}" />
    <meta property="og:description" content="{{ $description }}" />
    <meta property="og:image" content="{{ $image }}" />
    <meta property="og:url" content="{{ $url }}" />
    <meta property="og:site_name" content="iVatan" />

    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $title }}" />
    <meta name="twitter:description" content="{{ $description }}" />
    <meta name="twitter:image" content="{{ $image }}" />

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <!-- Styling -->
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .card {
            background: #1e293b;
            border-radius: 16px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
            max-width: 440px;
            width: 100%;
            overflow: hidden;
            text-align: center;
            border: 1px solid #334155;
        }
        .preview-img {
            width: 100%;
            height: 240px;
            object-fit: cover;
            background-color: #334155;
        }
        .content {
            padding: 24px;
        }
        .logo {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            margin-bottom: 12px;
        }
        h1 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #ffffff;
            line-height: 1.3;
        }
        p {
            font-size: 14px;
            color: #94a3b8;
            line-height: 1.5;
            margin-bottom: 24px;
        }
        .btn-group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .btn {
            display: inline-block;
            width: 100%;
            padding: 14px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: #ffffff;
            box-shadow: 0 4px 14px 0 rgba(79, 70, 229, 0.4);
        }
        .btn-primary:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }
        .btn-secondary {
            background: #334155;
            color: #cbd5e1;
        }
        .btn-secondary:hover {
            background: #475569;
        }
        .footer-text {
            font-size: 12px;
            color: #64748b;
            margin-top: 16px;
        }
    </style>
</head>
<body>
    <div class="card">
        @if(!empty($image))
            <img src="{{ $image }}" alt="Preview" class="preview-img" onerror="this.style.display='none';">
        @endif
        <div class="content">
            <h1>{{ $title }}</h1>
            <p>{{ $description }}</p>

            <div class="btn-group">
                <a href="{{ $deepLink }}" class="btn btn-primary" id="openAppBtn">Open in App</a>
                <a href="https://play.google.com/store/apps/details?id=com.octroid.ivatan_app" target="_blank" class="btn btn-secondary">Get the iVatan App</a>
            </div>

            <p class="footer-text">Experience full features on the iVatan Mobile App</p>
        </div>
    </div>

    <script>
        // Auto attempt opening deep link on mobile devices
        window.addEventListener('DOMContentLoaded', () => {
            const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
            if (isMobile) {
                setTimeout(() => {
                    window.location.href = "{{ $deepLink }}";
                }, 500);
            }
        });
    </script>
</body>
</html>
