<?php
function renderTopBar($user, $isMenuCollapsed = false) {
    $logo_url = get_logo_url();
    $html = '<header class="bg-white shadow h-16 sticky top-0 z-10">
        <div class="h-full px-4 flex items-center justify-between">';    
    
    // Botão de menu mobile (visível apenas em dispositivos móveis)
    $html .= '<button id="mobileMenuButton" class="md:hidden p-2 rounded-md text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>';
    
    // Logo aparece no topbar apenas quando o menu está recolhido
    $html .= '<div id="topbarLogoContainer" class="flex items-center transition-opacity duration-300 md:ml-0 ml-3 ' . (!$isMenuCollapsed ? 'opacity-0 md:opacity-0' : 'opacity-100') . '">
        <img src="' . htmlspecialchars($logo_url) . '" alt="Logo Sistema" 
            class="h-8 w-auto transition-all duration-300" 
            id="topbarLogo"
            onerror="this.onerror=null; this.src=\'https://placehold.co/120x32/FFFFFF/1E293B?text=Logo&font=inter\'; this.alt=\'Logo Alternativa\';">
    </div>';

    // Área do usuário e botão de sair
    $html .= '<div class="flex items-center space-x-4">
            <span class="text-gray-700 hidden sm:inline">Olá, ' . htmlspecialchars($user['name']) . '</span>
            <span class="text-gray-700 sm:hidden">Olá!</span>
            <a href="/bookmarks/logout.php" 
               class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 sm:px-4 py-2 rounded-md text-sm font-medium transition-colors duration-150 ease-in-out">
                Sair
            </a>
        </div>
    </div>
</header>';

    return $html;
}
?>