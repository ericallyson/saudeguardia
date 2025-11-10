<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar • Saúde Guardiã</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f5f1, #e9f2f9);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center py-12 px-4">
<div class="w-full max-w-md">
    <div class="bg-white/80 backdrop-blur rounded-2xl shadow-xl p-8">
        <div class="flex flex-col items-center gap-3 mb-8">
            <img src="{{ asset('img/logo-horizontal.png') }}" alt="Saúde Guardiã" class="w-48">
            <h1 class="text-2xl font-semibold text-[#2d3a4d]">Bem-vindo de volta</h1>
            <p class="text-sm text-[#6b5b51] text-center">Acesse o painel para acompanhar seus pacientes em tempo real.</p>
        </div>
        <form method="POST" action="{{ route('login.attempt') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-[#4b3f36]">E-mail</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       class="mt-1 block w-full rounded-lg border border-[#d4c8bb] bg-white/80 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#9fc5e8]">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-[#4b3f36]">Senha</label>
                <input id="password" name="password" type="password" required
                       class="mt-1 block w-full rounded-lg border border-[#d4c8bb] bg-white/80 px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-[#9fc5e8]">
            </div>
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-[#6b5b51]">
                    <input type="checkbox" name="remember" class="rounded border-[#d4c8bb] text-[#2d3a4d] focus:ring-[#9fc5e8]">
                    Lembrar-me
                </label>
            </div>
            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 space-y-2">
                    <div>{{ $errors->first() }}</div>
                    @if (session('subscription_debug'))
                        @php
                            $debug = json_encode(session('subscription_debug'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        @endphp
                        <div class="rounded-md bg-white/60 px-3 py-2 text-xs font-mono text-red-600 overflow-x-auto">
                            <pre class="whitespace-pre-wrap">{{ e($debug) }}</pre>
                        </div>
                    @endif
                </div>
            @endif
            <button type="submit"
                    class="w-full py-3 rounded-lg bg-[#2d3a4d] text-white font-semibold hover:opacity-90 transition">
                Entrar
            </button>
        </form>
    </div>
    <p class="mt-6 text-center text-xs text-[#6b5b51]">© {{ date('Y') }} Saúde Guardiã. Todos os direitos reservados.</p>
</div>
</body>
</html>
