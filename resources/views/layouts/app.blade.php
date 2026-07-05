<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ResQ - Emergency Response System')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; }
        .gradient-bg {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px -10px rgba(0,0,0,0.15);
        }
    </style>
    @yield('styles')
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100">
    @yield('content')
</body>
</html>