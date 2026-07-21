<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Presensi') — Overhaul</title>
    <meta name="description" content="Sistem Presensi Karyawan Overhaul - Check In/Out">
    <meta name="theme-color" content="#0f172a">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'] } } },
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { background: linear-gradient(180deg, #0f172a 0%, #1e293b 50%, #0f172a 100%); min-height: 100vh; min-height: 100dvh; }
        .glow-ring { box-shadow: 0 0 40px rgba(51, 141, 252, 0.3), 0 0 80px rgba(51, 141, 252, 0.1); }
        @keyframes scan-line { 0% { transform: translateY(-100%); } 100% { transform: translateY(100%); } }
        .scan-animation { animation: scan-line 2s ease-in-out infinite; }
        @keyframes ripple { 0% { transform: scale(1); opacity: 0.4; } 100% { transform: scale(2.5); opacity: 0; } }
        .ripple-ring { animation: ripple 2s ease-out infinite; }
    </style>
    @stack('styles')
</head>
<body class="font-sans text-white antialiased">
    @yield('content')
    @stack('scripts')
</body>
</html>
