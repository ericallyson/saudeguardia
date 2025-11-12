<aside class="w-64 bg-gradient-to-b from-[#f3ede1] to-[#fdfbf7] border-r border-[#e3d7c3] p-6 flex flex-col">
    <a href="{{ route('dashboard') }}" class="flex items-center">
        <img src="{{ asset('images/logo-horizontal.svg') }}" alt="Saúde Guardiã" class="w-auto">
    </a>
    <nav class="mt-6">
        @php
            $linkBase = 'flex items-center px-6 py-2 mt-4 rounded-lg transition-colors';
        @endphp
        <a class="{{ $linkBase }} {{ request()->routeIs('dashboard') ? 'text-gray-700 bg-gray-200' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-700' }}" href="{{ route('dashboard') }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            <span class="mx-3">Dashboard</span>
        </a>
        <a class="{{ $linkBase }} {{ request()->routeIs('metas.*') ? 'text-gray-700 bg-gray-200' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-700' }}" href="{{ route('metas.index') }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V7a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2z"></path></svg>
            <span class="mx-3">Metas</span>
        </a>
        <a class="{{ $linkBase }} {{ request()->routeIs('pacientes.*') ? 'text-gray-700 bg-gray-200' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-700' }}" href="{{ route('pacientes.index') }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21a6 6 0 00-9-5.197m0 0A5.975 5.975 0 0112 13.489m0 0a5.975 5.975 0 015.403 2.316m0 0A7.47 7.47 0 0115 21"></path></svg>
            <span class="mx-3">Pacientes</span>
        </a>
        <a class="{{ $linkBase }} text-gray-500 hover:bg-gray-200 hover:text-gray-700" href="#">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V7a2 2 0 012-2h10a2 2 0 012 2v10a2 2 0 01-2 2z"></path></svg>
            <span class="mx-3">Relatórios</span>
        </a>
        <a class="{{ $linkBase }} {{ request()->routeIs('settings.*') ? 'text-gray-700 bg-gray-200' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-700' }}" href="{{ route('settings.index') }}">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span class="mx-3">Configurações</span>
        </a>
    </nav>
</aside>
