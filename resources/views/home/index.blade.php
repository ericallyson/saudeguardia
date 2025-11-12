<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saúde Guardiã — Acompanhe pacientes com cuidado contínuo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top left, #fef6ec, #f3f7fb 55%, #eef2f6);
            color: #1f2933;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            box-shadow: 0 20px 45px rgba(31, 45, 61, 0.08);
            backdrop-filter: blur(12px);
        }
        .hero-shape {
            background: linear-gradient(135deg, rgba(255, 223, 186, 0.45), rgba(193, 216, 255, 0.45));
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
<header class="w-full">
    <div class="max-w-6xl mx-auto px-6 py-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo-horizontal.svg') }}" alt="Saúde Guardiã" class="h-12 w-auto">
        </div>
        <nav class="flex items-center gap-4 text-sm font-medium text-slate-600">
            <a href="#solucoes" class="hover:text-slate-900 transition">Soluções</a>
            <a href="#planos" class="hover:text-slate-900 transition">Planos</a>
            <a href="#faq" class="hover:text-slate-900 transition">Dúvidas</a>
            <a href="{{ route('login') }}"
               class="inline-flex items-center gap-2 rounded-full border border-slate-300 px-5 py-2 text-sm font-semibold text-slate-700 hover:border-slate-400 hover:text-slate-900 transition">
                Entrar
            </a>
        </nav>
    </div>
</header>

<main class="flex-1">
    <section class="max-w-6xl mx-auto px-6 pt-12 pb-24 grid lg:grid-cols-2 gap-12 items-center">
        <div>
            <span class="inline-flex items-center rounded-full bg-[#fef3c7] text-[#b45309] px-4 py-1 text-xs font-semibold uppercase tracking-wider">Monitoramento longitudinal inteligente</span>
            <h1 class="mt-6 text-4xl sm:text-5xl font-extrabold text-slate-900 leading-tight">
                Acompanhe seus pacientes diariamente com automações humanizadas
            </h1>
            <p class="mt-5 text-lg text-slate-600 leading-relaxed">
                A Saúde Guardiã integra lembretes inteligentes, análises de engajamento e relatórios automáticos para que você ofereça cuidados contínuos sem aumentar a carga administrativa.
            </p>
            <div class="mt-8 flex flex-wrap gap-4">
                <a href="{{ route('login') }}"
                   class="inline-flex items-center justify-center rounded-full bg-[#2d3a4d] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-400/20 transition hover:opacity-95">
                    Entrar
                </a>
                <a href="#planos" data-scroll="planos"
                   class="inline-flex items-center justify-center rounded-full border border-[#2d3a4d] px-6 py-3 text-sm font-semibold text-[#2d3a4d] hover:bg-[#2d3a4d] hover:text-white transition">
                    Assine agora
                </a>
            </div>
            <dl class="mt-10 grid grid-cols-2 gap-6 text-sm text-slate-600">
                <div class="glass-card rounded-2xl border border-white/60 p-6">
                    <dt class="text-xs uppercase tracking-widest text-slate-500">Pacientes engajados</dt>
                    <dd class="mt-2 text-3xl font-bold text-[#2d3a4d]">+85%</dd>
                    <p class="mt-2 text-xs text-slate-500">Aumento médio de adesão às metas acompanhadas.</p>
                </div>
                <div class="glass-card rounded-2xl border border-white/60 p-6">
                    <dt class="text-xs uppercase tracking-widest text-slate-500">Automação de mensagens</dt>
                    <dd class="mt-2 text-3xl font-bold text-[#2d3a4d]">24h/dia</dd>
                    <p class="mt-2 text-xs text-slate-500">Fluxos personalizados via WhatsApp com monitoramento contínuo.</p>
                </div>
            </dl>
        </div>
        <div class="relative">
            <div class="absolute inset-0 hero-shape rounded-[2.5rem] blur-3xl opacity-80"></div>
            <div class="relative glass-card rounded-[2.5rem] border border-white/70 p-8">
                <h2 class="text-lg font-semibold text-[#2d3a4d]">Tudo o que você precisa em um painel</h2>
                <ul class="mt-6 space-y-4 text-sm text-slate-600">
                    <li class="flex gap-3">
                        <span class="mt-1 inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-[#2d3a4d]/10 text-[#2d3a4d] font-semibold">1</span>
                        <div>
                            <p class="font-semibold text-slate-800">Metas personalizadas para cada paciente</p>
                            <p class="text-slate-500">Defina lembretes, periodicidade e acompanhe em tempo real.</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-[#2d3a4d]/10 text-[#2d3a4d] font-semibold">2</span>
                        <div>
                            <p class="font-semibold text-slate-800">Dashboard com indicadores claros</p>
                            <p class="text-slate-500">Monitore evolução, engajamento e alertas críticos em um só lugar.</p>
                        </div>
                    </li>
                    <li class="flex gap-3">
                        <span class="mt-1 inline-flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-[#2d3a4d]/10 text-[#2d3a4d] font-semibold">3</span>
                        <div>
                            <p class="font-semibold text-slate-800">Relatórios automáticos</p>
                            <p class="text-slate-500">Receba insights semanais para agir com rapidez e precisão.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <section id="solucoes" class="bg-white/70 py-20">
        <div class="max-w-6xl mx-auto px-6 grid md:grid-cols-3 gap-8">
            <article class="glass-card rounded-3xl border border-slate-100 p-8">
                <h3 class="text-xl font-semibold text-[#2d3a4d]">Monitoramento contínuo</h3>
                <p class="mt-3 text-sm text-slate-600">Acompanhe peso, pressão arterial, glicemias, hábitos e muito mais com lembretes automáticos.</p>
            </article>
            <article class="glass-card rounded-3xl border border-slate-100 p-8">
                <h3 class="text-xl font-semibold text-[#2d3a4d]">Mensagens humanizadas</h3>
                <p class="mt-3 text-sm text-slate-600">Envios via WhatsApp com linguagem acolhedora para motivar pacientes a cumprirem as metas.</p>
            </article>
            <article class="glass-card rounded-3xl border border-slate-100 p-8">
                <h3 class="text-xl font-semibold text-[#2d3a4d]">Relatórios inteligentes</h3>
                <p class="mt-3 text-sm text-slate-600">Receba resumos automáticos e identifique rapidamente quem precisa de atenção.</p>
            </article>
        </div>
    </section>

    <section id="planos" class="py-20">
        <div class="max-w-6xl mx-auto px-6">
            <div class="max-w-2xl">
                <span class="inline-flex items-center rounded-full bg-[#dbeafe] text-[#1d4ed8] px-4 py-1 text-xs font-semibold uppercase tracking-wider">Escolha o plano ideal</span>
                <h2 class="mt-6 text-3xl font-extrabold text-[#1f2937]">Planos flexíveis para escalar o cuidado</h2>
                <p class="mt-4 text-slate-600">Todos os planos incluem integrações com WhatsApp, dashboards em tempo real e suporte dedicado.</p>
            </div>

            @if ($plansError)
                <div class="mt-8 rounded-2xl border border-red-200 bg-red-50 px-6 py-4 text-sm text-red-700">
                    {{ $plansError }}
                </div>
            @endif

            <div class="mt-12 grid gap-8 lg:grid-cols-{{ max(count($plans), 1) > 1 ? 2 : 1 }}">
                @forelse ($plans as $plan)
                    @php
                        $billingPeriod = $plan['billing_period'] ?? 'monthly';
                        $price = $plan['prices'][$billingPeriod] ?? null;
                        $formattedPrice = $price !== null ? 'R$ '.number_format((float) $price, 2, ',', '.') : 'Sob consulta';
                        $periodLabel = [
                            'monthly' => 'mês',
                            'yearly' => 'ano',
                            'quarterly' => 'trimestre',
                            'semiannual' => 'semestre',
                            'semi-annually' => 'semestre',
                        ][$billingPeriod] ?? 'período';
                        $trialDays = (int) ($plan['trial_days'] ?? 0);
                        $features = collect($plan['features'] ?? [])->take(6);
                    @endphp
                    <article class="glass-card flex flex-col rounded-3xl border border-slate-100/80 p-8">
                        <header>
                            <p class="text-xs uppercase tracking-[0.25em] text-slate-500">{{ $plan['slug'] ?? 'Plano' }}</p>
                            <h3 class="mt-3 text-2xl font-bold text-[#2d3a4d]">{{ $plan['name'] }}</h3>
                            <p class="mt-2 text-sm text-slate-600">{{ $plan['description'] ?? 'Plano completo para sua equipe médica.' }}</p>
                        </header>
                        <div class="mt-6 flex items-baseline gap-2 text-[#2d3a4d]">
                            <span class="text-4xl font-extrabold">{{ $formattedPrice }}</span>
                            <span class="text-sm font-semibold text-slate-500">/ {{ $periodLabel }}</span>
                        </div>
                        @if ($trialDays > 0)
                            <p class="mt-2 text-xs font-medium text-emerald-600">{{ $trialDays }} dias de teste incluídos</p>
                        @endif

                        <ul class="mt-6 flex-1 space-y-3 text-sm text-slate-600">
                            @forelse ($features as $feature)
                                <li class="flex gap-3">
                                    <span class="mt-1 inline-flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-[#2d3a4d]/10 text-xs font-bold text-[#2d3a4d]">✓</span>
                                    <div>
                                        <p class="font-medium text-slate-800">{{ $feature['name'] ?? 'Funcionalidade exclusiva' }}</p>
                                        @if (!empty($feature['description']))
                                            <p class="text-xs text-slate-500">{{ $feature['description'] }}</p>
                                        @endif
                                        @if (!empty($feature['limit']['value']) && !empty($feature['limit']['unit']))
                                            <p class="text-xs text-slate-400">Limite: {{ $feature['limit']['value'] }} {{ $feature['limit']['unit'] }}</p>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="flex gap-3">
                                    <span class="mt-1 inline-flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-[#2d3a4d]/10 text-xs font-bold text-[#2d3a4d]">✓</span>
                                    <p>Recursos completos para acompanhamento longitudinal.</p>
                                </li>
                            @endforelse
                        </ul>

                        <div class="mt-8">
                            <button type="button"
                                    data-plan-button
                                    data-plan-id="{{ $plan['id'] }}"
                                    class="w-full rounded-full bg-[#2d3a4d] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-400/20 transition hover:opacity-95">
                                Começar agora
                            </button>
                        </div>
                    </article>
                @empty
                    <div class="rounded-3xl border border-slate-200 bg-white/70 p-10 text-center text-slate-600">
                        <p class="text-lg font-semibold text-slate-700">Nenhum plano disponível no momento.</p>
                        <p class="mt-2 text-sm">Entre em contato com nossa equipe comercial para encontrar a melhor opção para a sua clínica.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="faq" class="bg-[#f8fafc] py-20">
        <div class="max-w-5xl mx-auto px-6">
            <h2 class="text-3xl font-extrabold text-[#1f2937]">Dúvidas frequentes</h2>
            <div class="mt-10 space-y-6">
                <article class="glass-card rounded-3xl border border-slate-100 p-6">
                    <h3 class="text-lg font-semibold text-[#2d3a4d]">Como funciona a integração com WhatsApp?</h3>
                    <p class="mt-2 text-sm text-slate-600">Conectamos sua conta profissional para disparar lembretes automáticos nos dias e horários definidos, com confirmações das respostas dos pacientes.</p>
                </article>
                <article class="glass-card rounded-3xl border border-slate-100 p-6">
                    <h3 class="text-lg font-semibold text-[#2d3a4d]">Posso personalizar as metas de cada paciente?</h3>
                    <p class="mt-2 text-sm text-slate-600">Sim! Configure metas de peso, pressão, exames, atividades físicas e outros indicadores com periodicidade personalizada para cada paciente.</p>
                </article>
                <article class="glass-card rounded-3xl border border-slate-100 p-6">
                    <h3 class="text-lg font-semibold text-[#2d3a4d]">Os relatórios são automáticos?</h3>
                    <p class="mt-2 text-sm text-slate-600">Toda sexta-feira geramos um PDF com indicadores de evolução e engajamento para apoiar suas condutas médicas.</p>
                </article>
            </div>
        </div>
    </section>
</main>

<footer class="bg-white/80">
    <div class="max-w-6xl mx-auto px-6 py-10 flex flex-col md:flex-row items-center justify-between gap-4 text-sm text-slate-500">
        <p>&copy; {{ now()->year }} Saúde Guardiã. Todos os direitos reservados.</p>
        <div class="flex items-center gap-4">
            <a href="#planos" class="hover:text-slate-700">Planos</a>
            <a href="#faq" class="hover:text-slate-700">Perguntas frequentes</a>
            <a href="mailto:contato@saudeguardia.com.br" class="hover:text-slate-700">Fale conosco</a>
        </div>
    </div>
</footer>

<div id="signup-overlay" class="fixed inset-0 z-40 hidden bg-slate-900/60 backdrop-blur-sm"></div>
<div id="signup-modal" class="fixed inset-0 z-50 hidden items-center justify-center px-4">
    <div class="glass-card relative w-full max-w-lg rounded-3xl border border-white/70 bg-white p-8 shadow-2xl">
        <button type="button" id="signup-close" class="absolute right-4 top-4 text-slate-400 transition hover:text-slate-600" aria-label="Fechar">
            &times;
        </button>
        <h2 class="text-2xl font-bold text-[#2d3a4d]">Cadastre-se para começar</h2>
        <p class="mt-1 text-sm text-slate-600">Conclua seu cadastro para vincular sua assinatura e acessar o painel Saúde Guardiã.</p>

        @if ($errors->has('registration'))
            <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                {{ $errors->first('registration') }}
            </div>
        @endif

        <form method="POST" action="{{ route('home.register') }}" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="plan_id" id="signup-plan-id" value="{{ old('plan_id') }}">

            <div class="rounded-2xl bg-[#f8fafc] px-4 py-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Plano selecionado</p>
                <p id="signup-plan-name" class="text-lg font-bold text-[#2d3a4d] mt-1">Selecione um plano</p>
                <p id="signup-plan-summary" class="text-sm text-slate-500"></p>
                <ul id="signup-plan-features" class="mt-3 space-y-1 text-xs text-slate-500"></ul>
                @error('plan_id')
                    <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="signup-name" class="block text-sm font-medium text-slate-600">Nome completo</label>
                <input type="text" id="signup-name" name="name" value="{{ old('name') }}"
                       class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-[#2d3a4d] focus:outline-none focus:ring-2 focus:ring-[#2d3a4d]/30"
                       required>
                @error('name')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="signup-email" class="block text-sm font-medium text-slate-600">E-mail profissional</label>
                <input type="email" id="signup-email" name="email" value="{{ old('email') }}"
                       class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-[#2d3a4d] focus:outline-none focus:ring-2 focus:ring-[#2d3a4d]/30"
                       required>
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="signup-password" class="block text-sm font-medium text-slate-600">Senha</label>
                    <input type="password" id="signup-password" name="password"
                           class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-[#2d3a4d] focus:outline-none focus:ring-2 focus:ring-[#2d3a4d]/30"
                           required>
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="signup-password_confirmation" class="block text-sm font-medium text-slate-600">Confirmar senha</label>
                    <input type="password" id="signup-password_confirmation" name="password_confirmation"
                           class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-[#2d3a4d] focus:outline-none focus:ring-2 focus:ring-[#2d3a4d]/30"
                           required>
                </div>
            </div>

            <div>
                <label for="signup-phone" class="block text-sm font-medium text-slate-600">Telefone</label>
                <input type="text" id="signup-phone" name="phone" value="{{ old('phone') }}"
                       class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-[#2d3a4d] focus:outline-none focus:ring-2 focus:ring-[#2d3a4d]/30"
                       placeholder="+55 11 99999-0000">
                @error('phone')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="signup-document_type" class="block text-sm font-medium text-slate-600">Tipo de documento</label>
                    <select id="signup-document_type" name="document_type"
                            class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-[#2d3a4d] focus:outline-none focus:ring-2 focus:ring-[#2d3a4d]/30">
                        <option value="">Selecione</option>
                        <option value="CPF" @selected(old('document_type') === 'CPF')>CPF</option>
                        <option value="CNPJ" @selected(old('document_type') === 'CNPJ')>CNPJ</option>
                    </select>
                    @error('document_type')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="signup-document_number" class="block text-sm font-medium text-slate-600">Número do documento</label>
                    <input type="text" id="signup-document_number" name="document_number" value="{{ old('document_number') }}"
                           class="mt-1 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm shadow-sm focus:border-[#2d3a4d] focus:outline-none focus:ring-2 focus:ring-[#2d3a4d]/30">
                    @error('document_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <button type="submit"
                    class="w-full rounded-full bg-[#2d3a4d] px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-slate-400/20 transition hover:opacity-95">
                Criar minha conta e avançar para o pagamento
            </button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const scrollButtons = document.querySelectorAll('[data-scroll]');
        const overlay = document.getElementById('signup-overlay');
        const modal = document.getElementById('signup-modal');
        const closeBtn = document.getElementById('signup-close');
        const planInput = document.getElementById('signup-plan-id');
        const planName = document.getElementById('signup-plan-name');
        const planSummary = document.getElementById('signup-plan-summary');
        const planFeatures = document.getElementById('signup-plan-features');
        const planButtons = document.querySelectorAll('[data-plan-button]');
        const plans = @json($plans);

        const openModal = (planId = null) => {
            planFeatures.innerHTML = '';

            if (planId) {
                const selectedPlan = plans.find(plan => Number(plan.id) === Number(planId));
                if (selectedPlan) {
                    planInput.value = selectedPlan.id;
                    planName.textContent = selectedPlan.name || 'Plano escolhido';
                    planSummary.textContent = selectedPlan.description || '';

                    const features = (selectedPlan.features || []).slice(0, 6);

                    if (features.length > 0) {
                        features.forEach(feature => {
                            const item = document.createElement('li');
                            item.textContent = feature.name || '';
                            planFeatures.appendChild(item);
                        });
                    } else {
                        const empty = document.createElement('li');
                        empty.textContent = 'Plano completo para acompanhamento longitudinal.';
                        planFeatures.appendChild(empty);
                    }
                } else {
                    planName.textContent = 'Selecione um plano';
                    planSummary.textContent = '';
                    planInput.value = '';
                }
            }

            overlay.classList.remove('hidden');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        };

        const closeModal = () => {
            overlay.classList.add('hidden');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        };

        scrollButtons.forEach(button => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                const targetId = button.getAttribute('data-scroll');
                const target = document.getElementById(targetId);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        planButtons.forEach(button => {
            button.addEventListener('click', () => {
                const planId = button.getAttribute('data-plan-id');
                openModal(planId);
            });
        });

        overlay.addEventListener('click', closeModal);
        closeBtn.addEventListener('click', closeModal);

        if (planInput.value) {
            openModal(planInput.value);
        }

        const hasErrors = @json($errors->any());
        if (hasErrors && !planInput.value && plans.length > 0) {
            openModal(plans[0].id);
        }
    });
</script>
</body>
</html>
