<header class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
    <div>
        <h2 class="text-3xl font-semibold text-[#2d3a4d]">{{ $title ?? 'Painel de Monitoramento' }}</h2>
        @isset($subtitle)
            <p class="text-sm text-[#6b5b51] mt-1">{{ $subtitle }}</p>
        @endisset
    </div>
    <div class="flex items-center gap-4">
        <img src="{{ asset('img/doctor.jpg') }}" class="w-10 h-10 rounded-full border-2 border-[#9fc5e8] object-cover" alt="avatar">
        <div class="flex items-center gap-3">
            <span class="font-medium text-[#4b3f36]">{{ auth()->user()->name ?? 'Profissional' }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="px-3 py-1.5 rounded-md bg-[#2d3a4d] text-white text-sm hover:opacity-90">Sair</button>
            </form>
        </div>
    </div>
</header>
