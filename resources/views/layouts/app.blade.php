<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Saúde Guardiã — Dashboard Premium')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f5f1, #e9f2f9);
            color: #3d3d3d;
        }
        .card {
            background: rgba(255, 255, 255, 0.85);
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            transition: transform .2s ease, box-shadow .3s ease;
        }
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.1);
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen">
<div class="flex min-h-screen">
    @include('partials.sidebar')

    <main class="flex-1 p-8">
        @yield('main')
    </main>
</div>

@stack('scripts')
</body>
</html>
