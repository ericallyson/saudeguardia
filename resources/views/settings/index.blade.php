@extends('layouts.app')

@section('title', 'Configurações — Saúde Guardiã')

@section('main')
    <div class="max-w-5xl mx-auto space-y-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Configurações</h1>
            <p class="text-gray-600 mt-2">Gerencie a integração do WhatsApp da sua conta.</p>
        </div>

        @if (session('status'))
            <div class="p-4 rounded-lg bg-green-100 text-green-700 border border-green-200">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-lg bg-red-100 text-red-700 border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <div class="card p-6">
            <h2 class="text-xl font-semibold text-gray-800">Instância do WhatsApp</h2>
            <p class="text-gray-600 mt-1">Cada usuário pode possuir uma instância configurada para envio e recebimento de mensagens.</p>

            @if (! $user->whatsapp_instance_uuid)
                <div class="mt-6">
                    <p class="text-gray-700">Nenhuma instância configurada para este usuário.</p>
                    <form method="POST" action="{{ route('settings.instance.create') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="px-5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg shadow-sm transition-colors">
                            Criar instância
                        </button>
                    </form>
                </div>
            @else
                <dl class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-white/80 rounded-lg border border-gray-200 p-4">
                        <dt class="text-sm text-gray-500 uppercase tracking-wide">UUID da instância</dt>
                        <dd class="mt-2 text-gray-800 break-all font-medium">{{ $user->whatsapp_instance_uuid }}</dd>
                    </div>
                    <div class="bg-white/80 rounded-lg border border-gray-200 p-4">
                        <dt class="text-sm text-gray-500 uppercase tracking-wide">Webhook configurado</dt>
                        <dd class="mt-2 text-gray-800 break-all font-medium">{{ $webhookUrl }}</dd>
                    </div>
                </dl>

                <div class="mt-6 space-y-4">
                    <div class="bg-white/80 border border-gray-200 rounded-lg p-4 flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wide">Status atual</p>
                            <p id="instance-status-text" class="mt-1 text-lg font-semibold text-gray-800">
                                {{ $initialStatus ? $initialStatusLabel : 'Carregando status...' }}
                            </p>
                        </div>
                        <div id="status-indicator" class="w-3 h-3 rounded-full bg-yellow-400"></div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <form method="POST" action="{{ route('settings.instance.connect') }}"
                            id="connect-form" class="{{ $initialIsConnected ? 'hidden' : '' }}">
                            @csrf
                            <button type="submit"
                                class="px-5 py-2 bg-emerald-500 hover:bg-emerald-600 text-white font-medium rounded-lg shadow-sm transition-colors"
                                id="connect-button">
                                Conectar WhatsApp
                            </button>
                        </form>

                        <form method="POST" action="{{ route('settings.instance.disconnect') }}"
                            id="disconnect-form" class="{{ $initialIsConnected ? '' : 'hidden' }}">
                            @csrf
                            <button type="submit"
                                class="px-5 py-2 bg-rose-500 hover:bg-rose-600 text-white font-medium rounded-lg shadow-sm transition-colors"
                                id="disconnect-button">
                                Desconectar WhatsApp
                            </button>
                        </form>

                        <form method="POST" action="{{ route('settings.instance.delete') }}"
                            class="inline-flex"
                            onsubmit="return confirm('Tem certeza que deseja excluir esta instância? Essa ação não pode ser desfeita.');">
                            @csrf
                            <button type="submit"
                                class="px-5 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg shadow-sm transition-colors">
                                Excluir instância
                            </button>
                        </form>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-800">Conecte seu WhatsApp</h3>
                    <p class="text-gray-600">Escaneie o QR Code abaixo no aplicativo WhatsApp para vincular esta instância.</p>

                    <div id="qr-code-wrapper" class="mt-4 flex flex-col items-center gap-3">
                        @if ($qrCode)
                            <img id="qr-code-image" src="{{ $qrCode }}" alt="QR Code do WhatsApp" class="border border-gray-200 rounded-xl shadow-lg max-w-xs">
                        @else
                            <div class="text-gray-500 text-sm" id="qr-code-placeholder">QR Code indisponível no momento. Aguarde enquanto carregamos as informações.</div>
                        @endif
                        <span class="text-xs text-gray-500">O QR Code é atualizado automaticamente enquanto o status estiver pendente.</span>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    @if ($user->whatsapp_instance_uuid)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const statusText = document.getElementById('instance-status-text');
                const statusIndicator = document.getElementById('status-indicator');
                let qrImage = document.getElementById('qr-code-image');
                let qrPlaceholder = document.getElementById('qr-code-placeholder');
                const qrWrapper = document.getElementById('qr-code-wrapper');
                const connectForm = document.getElementById('connect-form');
                const disconnectForm = document.getElementById('disconnect-form');

                const statusClasses = {
                    connected: 'bg-emerald-500',
                    authenticated: 'bg-emerald-500',
                    open: 'bg-emerald-500',
                    qr_code: 'bg-yellow-400',
                    qrcode: 'bg-yellow-400',
                    connecting: 'bg-yellow-400',
                    loading: 'bg-yellow-400',
                    reconnecting: 'bg-yellow-400',
                    disconnecting: 'bg-yellow-400',
                    disconnected: 'bg-rose-500',
                    close: 'bg-rose-500',
                    refused: 'bg-rose-500'
                };

                const connectedStatuses = @json($connectedStatuses);

                const resolveConnectionState = (status, meta) => {
                    if (meta && typeof meta.connected === 'boolean') {
                        return meta.connected;
                    }

                    if (!status) {
                        return false;
                    }

                    return connectedStatuses.includes(status);
                };

                const updateQrCode = (qrCode) => {
                    if (!qrWrapper) {
                        return;
                    }

                    if (qrCode) {
                        if (!qrImage) {
                            qrImage = document.createElement('img');
                            qrImage.id = 'qr-code-image';
                            qrImage.alt = 'QR Code do WhatsApp';
                            qrImage.className = 'border border-gray-200 rounded-xl shadow-lg max-w-xs';

                            if (qrPlaceholder) {
                                qrWrapper.replaceChild(qrImage, qrPlaceholder);
                                qrPlaceholder = null;
                            } else {
                                qrWrapper.prepend(qrImage);
                            }
                        }

                        qrImage.src = qrCode;
                    } else {
                        if (qrImage) {
                            qrImage.remove();
                            qrImage = null;
                        }

                        if (!qrPlaceholder) {
                            qrPlaceholder = document.createElement('div');
                            qrPlaceholder.id = 'qr-code-placeholder';
                            qrPlaceholder.className = 'text-gray-500 text-sm';
                            qrPlaceholder.textContent = 'QR Code indisponível no momento. Aguarde enquanto carregamos as informações.';
                            qrWrapper.prepend(qrPlaceholder);
                        }
                    }
                };

                const applyStatus = (status, label, meta = {}) => {
                    statusText.textContent = label ?? 'Status indisponível';

                    statusIndicator.className = 'w-3 h-3 rounded-full transition-colors duration-300';
                    const indicatorClass = status ? (statusClasses[status] ?? 'bg-gray-400') : 'bg-gray-400';
                    statusIndicator.classList.add(indicatorClass);

                    const isConnected = resolveConnectionState(status, meta);

                    if (connectForm && disconnectForm) {
                        if (isConnected) {
                            connectForm.classList.add('hidden');
                            disconnectForm.classList.remove('hidden');
                        } else {
                            connectForm.classList.remove('hidden');
                            disconnectForm.classList.add('hidden');
                        }
                    }

                    const qrCode = meta && typeof meta === 'object' ? meta.qr_code_base64 ?? null : null;

                    if (['qr_code', 'qrcode'].includes(status)) {
                        updateQrCode(qrCode);
                    } else {
                        updateQrCode(null);
                    }
                };

                const fetchStatus = () => {
                    fetch('{{ route('settings.instance.status') }}', {
                        headers: {
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response
                                    .json()
                                    .catch(() => ({}))
                                    .then(body => Promise.reject({ status: response.status, body }));
                            }

                            return response.json();
                        })
                        .then(data => {
                            applyStatus(data.status, data.status_label, data);
                        })
                        .catch(error => {
                            if (error && error.status === 404 && error.body) {
                                applyStatus(error.body.status, error.body.status_label, { connected: false });
                                return;
                            }

                            applyStatus(null, 'Não foi possível atualizar o status.', { connected: false });
                        });
                };

                applyStatus(
                    @json($initialStatus),
                    @json($initialStatusLabel ?? 'Status indisponível'),
                    {
                        connected: @json($initialIsConnected),
                        qr_code_base64: @json($qrCode)
                    }
                );

                fetchStatus();
                setInterval(fetchStatus, 5000);
            });
        </script>
    @endif
@endpush
