<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Saúde Guardiã — Metas')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f6f8fb, #fef4f0);
            color: #2d3748;
        }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen flex items-center justify-center py-10 px-4">
<div class="w-full max-w-3xl">
    @yield('content')
</div>
@stack('scripts')
</body>
</html>
