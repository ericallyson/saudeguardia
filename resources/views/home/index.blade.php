<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Saúde Guardiã - Plataforma de Acompanhamento Médico Longitudinal. Revolucione o cuidado dos seus pacientes com monitoramento contínuo via WhatsApp.">
    <meta name="keywords" content="saúde, medicina, acompanhamento médico, whatsapp, telemedicina, monitoramento pacientes">
    <title>Saúde Guardiã - Acompanhamento Médico Longitudinal</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-inter bg-gray-50">
    <!-- Header/Navigation -->
    <header class="bg-white shadow-sm fixed w-full top-0 z-50">
        <nav class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <img src="https://app.saudeguardia.com.br/img/logo-horizontal.png" alt="Saúde Guardiã" class="h-10 w-auto">
                </div>

                <div class="hidden md:flex items-center space-x-8">
                    <a href="#funcionalidades" class="text-gray-600 hover:text-amber-600 transition-colors">Funcionalidades</a>
                    <a href="#como-funciona" class="text-gray-600 hover:text-amber-600 transition-colors">Como Funciona</a>
                    <a href="#precos" class="text-gray-600 hover:text-amber-600 transition-colors">Preços</a>
                    <a href="#contato" class="text-gray-600 hover:text-amber-600 transition-colors">Contato</a>
                </div>

                <div class="flex items-center space-x-4">
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-amber-600 transition-colors">Entrar</a>
                    <a href="#demo" class="bg-amber-600 text-white px-6 py-2 rounded-lg hover:bg-amber-700 transition-colors">
                        Demonstração Gratuita
                    </a>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button" class="text-gray-600 hover:text-amber-600">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 pb-4">
                <div class="flex flex-col space-y-4">
                    <a href="#funcionalidades" class="text-gray-600 hover:text-amber-600 transition-colors">Funcionalidades</a>
                    <a href="#como-funciona" class="text-gray-600 hover:text-amber-600 transition-colors">Como Funciona</a>
                    <a href="#precos" class="text-gray-600 hover:text-amber-600 transition-colors">Preços</a>
                    <a href="#contato" class="text-gray-600 hover:text-amber-600 transition-colors">Contato</a>
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-amber-600 transition-colors">Entrar</a>
                    <a href="#demo" class="bg-amber-600 text-white px-6 py-2 rounded-lg hover:bg-amber-700 transition-colors inline-block text-center">
                        Demonstração Gratuita
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="pt-24 pb-16 bg-gradient-to-br from-amber-50 to-orange-50">
        <div class="container mx-auto px-6">
            <div class="flex flex-col lg:flex-row items-center">
                <div class="lg:w-1/2 lg:pr-12 mb-12 lg:mb-0">
                    <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                        Revolucione o
                        <span class="text-amber-600">Acompanhamento</span>
                        dos seus Pacientes
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                        Mantenha contato permanente com seus pacientes através do WhatsApp.
                        Monitore metas, receba dados em tempo real e gere relatórios automáticos.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="#demo" class="bg-amber-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-amber-700 transition-colors text-center">
                            <i class="fas fa-play mr-2"></i>
                            Demonstração Gratuita
                        </a>
                        <a href="#como-funciona" class="border-2 border-amber-600 text-amber-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-amber-600 hover:text-white transition-colors text-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Como Funciona
                        </a>
                    </div>

                    <!-- Stats -->
                    <div class="flex flex-wrap gap-8 mt-12">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-amber-600">95%</div>
                            <div class="text-gray-600">Engajamento</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-amber-600">24/7</div>
                            <div class="text-gray-600">Monitoramento</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-amber-600">30 dias</div>
                            <div class="text-gray-600">Implementação</div>
                        </div>
                    </div>
                </div>

                <div class="lg:w-1/2">
                    <div class="relative">
                        <img src="https://app.saudeguardia.com.br/img/dashboard-preview.png" alt="Dashboard Saúde Guardiã" class="w-full rounded-2xl shadow-2xl">
                        <div class="absolute -top-4 -right-4 bg-green-500 text-white p-3 rounded-full">
                            <i class="fab fa-whatsapp text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="funcionalidades" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Funcionalidades que Transformam o Cuidado
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Nossa plataforma oferece tudo que você precisa para manter um acompanhamento médico contínuo e eficaz
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-gray-50 p-8 rounded-2xl hover:shadow-lg transition-shadow">
                    <div class="bg-amber-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fab fa-whatsapp text-2xl text-amber-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Integração WhatsApp</h3>
                    <p class="text-gray-600">
                        Envie lembretes automáticos e receba dados dos pacientes diretamente pelo WhatsApp Business.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-2xl hover:shadow-lg transition-shadow">
                    <div class="bg-blue-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-2xl text-blue-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Dashboard Inteligente</h3>
                    <p class="text-gray-600">
                        Visualize métricas em tempo real, alertas de pacientes e evolução do tratamento.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-2xl hover:shadow-lg transition-shadow">
                    <div class="bg-green-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-file-medical text-2xl text-green-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Relatórios Automáticos</h3>
                    <p class="text-gray-600">
                        Gere relatórios semanais em PDF com análise de engajamento e evolução clínica.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-2xl hover:shadow-lg transition-shadow">
                    <div class="bg-purple-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-heartbeat text-2xl text-purple-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Monitoramento Contínuo</h3>
                    <p class="text-gray-600">
                        Acompanhe peso, pressão, glicemia, medicação e atividade física dos pacientes.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-2xl hover:shadow-lg transition-shadow">
                    <div class="bg-red-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Alertas Inteligentes</h3>
                    <p class="text-gray-600">
                        Receba notificações automáticas sobre pacientes que precisam de atenção especial.
                    </p>
                </div>

                <div class="bg-gray-50 p-8 rounded-2xl hover:shadow-lg transition-shadow">
                    <div class="bg-indigo-100 w-16 h-16 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-cloud text-2xl text-indigo-600"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Plataforma SaaS</h3>
                    <p class="text-gray-600">
                        Acesse de qualquer lugar, com backup automático e segurança de dados garantida.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- How it Works Section -->
    <section id="como-funciona" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Como Funciona em 4 Passos Simples
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Implementação rápida e fácil para começar a transformar o cuidado dos seus pacientes
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="bg-amber-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                        1
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Cadastre o Paciente</h3>
                    <p class="text-gray-600">
                        Registre as informações do paciente e defina metas personalizadas de acompanhamento.
                    </p>
                </div>

                <div class="text-center">
                    <div class="bg-amber-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                        2
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Configure Lembretes</h3>
                    <p class="text-gray-600">
                        Defina horários e frequência dos lembretes automáticos via WhatsApp.
                    </p>
                </div>

                <div class="text-center">
                    <div class="bg-amber-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                        3
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Monitore em Tempo Real</h3>
                    <p class="text-gray-600">
                        Acompanhe as respostas dos pacientes e métricas de saúde pelo dashboard.
                    </p>
                </div>

                <div class="text-center">
                    <div class="bg-amber-600 text-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-6 text-2xl font-bold">
                        4
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Receba Relatórios</h3>
                    <p class="text-gray-600">
                        Obtenha relatórios automáticos semanais com análise completa do progresso.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="precos" class="py-20 bg-white">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Planos que Cabem no seu Orçamento
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Escolha o plano ideal para o tamanho da sua prática médica
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                <div class="bg-gray-50 p-8 rounded-2xl">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Starter</h3>
                    <div class="text-4xl font-bold text-amber-600 mb-6">
                        R$ 100
                        <span class="text-lg text-gray-600 font-normal">/mês</span>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Até 10 médicos</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>WhatsApp integrado</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Relatórios básicos</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Suporte por email</span>
                        </li>
                    </ul>
                    <a href="#contato" class="block w-full bg-gray-200 text-gray-800 text-center py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                        Começar Agora
                    </a>
                </div>

                <div class="bg-amber-600 p-8 rounded-2xl text-white relative">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2 bg-orange-500 text-white px-4 py-1 rounded-full text-sm font-semibold">
                        Mais Popular
                    </div>
                    <h3 class="text-2xl font-bold mb-4">Professional</h3>
                    <div class="text-4xl font-bold mb-6">
                        R$ 10
                        <span class="text-lg font-normal">/médico adicional</span>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-white mr-3"></i>
                            <span>Médicos ilimitados</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-white mr-3"></i>
                            <span>Todas as funcionalidades</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-white mr-3"></i>
                            <span>Relatórios avançados</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-white mr-3"></i>
                            <span>Suporte prioritário</span>
                        </li>
                    </ul>
                    <a href="#contato" class="block w-full bg-white text-amber-600 text-center py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                        Começar Agora
                    </a>
                </div>

                <div class="bg-gray-50 p-8 rounded-2xl">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Enterprise</h3>
                    <div class="text-4xl font-bold text-amber-600 mb-6">
                        Custom
                        <span class="text-lg text-gray-600 font-normal">/mês</span>
                    </div>
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Solução personalizada</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Integração com HIS</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>API dedicada</span>
                        </li>
                        <li class="flex items-center">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Suporte 24/7</span>
                        </li>
                    </ul>
                    <a href="#contato" class="block w-full bg-gray-200 text-gray-800 text-center py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors">
                        Falar com Vendas
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-amber-600 to-orange-600">
        <div class="container mx-auto px-6 text-center">
            <h2 class="text-4xl font-bold text-white mb-6">
                Pronto para Revolucionar sua Prática Médica?
            </h2>
            <p class="text-xl text-amber-100 mb-8 max-w-3xl mx-auto">
                Junte-se a centenas de médicos que já transformaram o cuidado dos seus pacientes com a Saúde Guardiã
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#demo" class="bg-white text-amber-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-100 transition-colors">
                    <i class="fas fa-play mr-2"></i>
                    Demonstração Gratuita
                </a>
                <a href="#contato" class="border-2 border-white text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-white hover:text-amber-600 transition-colors">
                    <i class="fas fa-phone mr-2"></i>
                    Falar com Especialista
                </a>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contato" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">
                    Entre em Contato
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Nossa equipe está pronta para ajudar você a implementar a solução ideal
                </p>
            </div>

            <div class="grid lg:grid-cols-2 gap-12 max-w-6xl mx-auto">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-8">Fale Conosco</h3>

                    <div class="space-y-6">
                        <div class="flex items-center">
                            <div class="bg-amber-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-phone text-amber-600"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Telefone</div>
                                <div class="text-gray-600">(11) 99999-9999</div>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="bg-amber-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                                <i class="fas fa-envelope text-amber-600"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">Email</div>
                                <div class="text-gray-600">contato@saudeguardia.com.br</div>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div class="bg-amber-100 w-12 h-12 rounded-full flex items-center justify-center mr-4">
                                <i class="fab fa-whatsapp text-amber-600"></i>
                            </div>
                            <div>
                                <div class="font-semibold text-gray-900">WhatsApp</div>
                                <div class="text-gray-600">(11) 99999-9999</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Horário de Atendimento</h4>
                        <div class="text-gray-600">
                            <div>Segunda a Sexta: 8h às 18h</div>
                            <div>Sábado: 8h às 12h</div>
                            <div>Domingo: Fechado</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <form>
                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="nome" class="block text-sm font-medium text-gray-700 mb-2">Nome</label>
                                <input type="text" id="nome" name="nome" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent" required>
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent" required>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="telefone" class="block text-sm font-medium text-gray-700 mb-2">Telefone</label>
                                <input type="tel" id="telefone" name="telefone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent" required>
                            </div>
                            <div>
                                <label for="especialidade" class="block text-sm font-medium text-gray-700 mb-2">Especialidade</label>
                                <input type="text" id="especialidade" name="especialidade" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="mensagem" class="block text-sm font-medium text-gray-700 mb-2">Mensagem</label>
                            <textarea id="mensagem" name="mensagem" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent" placeholder="Como podemos ajudar você?"></textarea>
                        </div>

                        <button type="submit" class="w-full bg-amber-600 text-white py-3 rounded-lg font-semibold hover:bg-amber-700 transition-colors">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Enviar Mensagem
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-6">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <img src="https://app.saudeguardia.com.br/img/logo-horizontal.png" alt="Saúde Guardiã" class="h-10 w-auto mb-4 filter brightness-0 invert">
                    <p class="text-gray-400 mb-4">
                        Revolucionando o acompanhamento médico com tecnologia e cuidado humanizado.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                    </div>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Produto</h4>
                    <ul class="space-y-2">
                        <li><a href="#funcionalidades" class="text-gray-400 hover:text-white transition-colors">Funcionalidades</a></li>
                        <li><a href="#precos" class="text-gray-400 hover:text-white transition-colors">Preços</a></li>
                        <li><a href="#demo" class="text-gray-400 hover:text-white transition-colors">Demonstração</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">API</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Empresa</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Sobre Nós</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Blog</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Carreiras</a></li>
                        <li><a href="#contato" class="text-gray-400 hover:text-white transition-colors">Contato</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-lg font-semibold mb-4">Suporte</h4>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Central de Ajuda</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Documentação</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Status</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Política de Privacidade</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
                <p>&copy; {{ now()->year }} Saúde Guardiã. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
