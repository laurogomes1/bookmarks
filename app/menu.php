<?php

function getMenuItems() {
    return [
        [
            'title' => 'Dashboard',
            'url' => '/bookmarks/dashboard',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>'
        ],
        [
            'title' => 'Usuários',
            'url' => '/bookmarks/usuarios',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25v-1.5A2.25 2.25 0 016.75 16.5h10.5a2.25 2.25 0 012.25 2.25v1.5" /></svg>'
        ],
        [
            'title' => 'Configurações',
            'url' => '/bookmarks/configuracoes',
            'icon' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>'
        ],
    ];
}

function renderMenu($isCollapsed = false) {
    $output = '<div class="flex flex-col">'; 
    $output .= '<nav class="space-y-1">';
    $menuItems = getMenuItems();
    $requestUri = $_SERVER['REQUEST_URI'];
    foreach ($menuItems as $item) {
        $isActive = strpos($requestUri, $item['url']) === 0;
        $activeClass = $isActive ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900';
        $output .= sprintf(
            '<a href="%s" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md %s transition-colors duration-150 ease-in-out" title="%s">
                <div class="mr-3 %s flex-shrink-0">%s</div>
                <span class="menu-text transition-opacity duration-150 ease-in-out %s %s">%s</span>
            </a>',
            htmlspecialchars($item['url']),
            $activeClass,
            htmlspecialchars($item['title']),
            $isActive ? 'text-gray-900' : 'text-gray-400 group-hover:text-gray-500',
            $item['icon'],
            $isCollapsed ? 'opacity-0 hidden' : 'opacity-100',
            $isActive ? 'text-gray-900' : 'text-gray-600',
            htmlspecialchars($item['title'])
        );
    }
    $output .= '</nav>';
    $output .= sprintf(
        '<div class="border-t pt-4 mt-6 mb-4">
            <a href="/bookmarks/logout" class="group flex items-center px-3 py-2 text-sm font-medium rounded-md text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-150 ease-in-out" title="Sair">
                <div class="mr-3 text-red-500 group-hover:text-red-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                </div>
                <span class="menu-text transition-opacity duration-150 ease-in-out %s text-red-600">Sair</span>
            </a>
        </div>',
        $isCollapsed ? 'opacity-0 hidden' : 'opacity-100'
    );
    $output .= '</div>';
    return $output;
}
?>