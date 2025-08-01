<?php
/**
 * Arquivo de layout comum para o Sistema de Gerenciamento
 * Contém funções e configurações compartilhadas entre todas as páginas
 */

// Inclui o arquivo de configuração principal da aplicação
require_once __DIR__ . '/../config.php';

// Função para verificar se o usuário está logado
function checkAuth() {
    // MODIFICAÇÃO: Ajustado para usar a variável de sessão correta que definimos ('user')
    if (!isset($_SESSION['user'])) {
        // MODIFICAÇÃO: Ajustado o redirecionamento para a raiz do projeto.
        header('Location: /bookmarks/index.php');
        exit;
    }
}

// Função para renderizar o cabeçalho HTML comum
function renderHeader($title = 'Sistema de Gerenciamento', $additionalStyles = '') {
    // Configura headers de segurança
    if (function_exists('setSecurityHeaders')) {
        setSecurityHeaders();
    }
    
    $html = '<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title) . ' - Sistema de Gerenciamento</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="/bookmarks/assets/css/styles.css" rel="stylesheet">
    <style>
        /* Garante que o body e html ocupem toda a altura, prevenindo problemas com 100vh em alguns browsers mobile */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* Prevenir scroll horizontal caso algo escape */
        }
        
        /* Estilos para o menu mobile */
        @media (max-width: 767px) {
            #sidebarContainer {
                transform: translateX(-100%); /* Esconde o menu por padrão em dispositivos móveis */
                transition: transform 0.3s ease-in-out;
                width: 256px !important; /* Largura fixa quando visível no mobile */
            }
            
            #sidebarContainer.mobile-visible {
                transform: translateX(0); /* Mostra o menu quando a classe mobile-visible é adicionada */
            }
            
            /* Garante que o logo no menu lateral seja visível no mobile */
            #sidebarContainer.mobile-visible #logoContainer img {
                opacity: 1 !important;
                transform: scale(1) !important;
            }
        }
        
        /* SOLUÇÃO DEFINITIVA PARA A COR DO TEXTO DO MENU */
        /* Estas regras são aplicadas apenas quando o menu está visível no mobile ou expandido no desktop */
        #sidebarContainer.mobile-visible #sidebar .menu-text,
        #sidebar:not(.collapsed) .menu-text {
            color: #000000 !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: inline !important;
        }
        
        /* Garantir que o botão Sair também tenha o texto visível */
        #sidebarContainer.mobile-visible #sidebar a[href="/bookmarks/logout.php"] .menu-text,
        #sidebar:not(.collapsed) a[href="/bookmarks/logout.php"] .menu-text {
            color: #DC2626 !important; /* text-red-600 */
            opacity: 1 !important;
            visibility: visible !important;
            display: inline !important;
        }
        
        /* Garantir que o texto do Dashboard seja visível no mobile */
        #sidebarContainer.mobile-visible #sidebar a[href="/bookmarks/app/modules/dashboard/"] .menu-text {
            color: #000000 !important;
            opacity: 1 !important;
            visibility: visible !important;
            display: inline !important;
        }
        
        /* Esconder o texto quando o menu está recolhido */
        #sidebar.collapsed .menu-text {
            display: none !important;
            opacity: 0 !important;
            visibility: hidden !important;
        }
        
        /* Estilos adicionais para o menu e botão Sair */
        #sidebar {
            overflow-y: auto; /* Permite rolagem se o conteúdo for muito grande */
        }
        
        /* Garantir que o botão Sair seja visível no mobile */
        @media (max-width: 767px) {
            #sidebarContainer.mobile-visible #sidebar {
                overflow-y: auto;
                padding-bottom: 20px;
            }
        }
        ' . $additionalStyles . '
    </style>
</head>
<body class="bg-gray-50 antialiased">';

    return $html;
}

// Função para renderizar o rodapé HTML comum
function renderFooter() {
    $html = '<script>
        const sidebarContainer = document.getElementById("sidebarContainer");
        const sidebar = document.getElementById("sidebar"); // O <aside>
        const toggleButton = document.getElementById("toggleSidebar");
        const menuTexts = document.querySelectorAll("#sidebar .menu-text"); // Seleciona os textos dentro do sidebar
        const sidebarLogo = document.getElementById("sidebarLogo");
        const logoContainer = document.getElementById("logoContainer");
        const sidebarOverlay = document.getElementById("sidebarOverlay");
        const mainContentArea = document.getElementById("mainContentArea"); // Referência à área de conteúdo principal
        const mobileMenuButton = document.getElementById("mobileMenuButton"); // Botão de menu mobile

        // Estado inicial do menu
        let isCollapsed = true; // Menu recolhido por padrão no desktop
        let isMobileMenuHidden = true; // Menu mobile sempre oculto por padrão

        // Função para ajustar o layout
        function adjustLayout() {
            const isMobile = window.innerWidth < 768; // Tailwind md breakpoint
            const collapsedWidth = "80px";
            const expandedWidth = "256px"; // Largura padrão do menu expandido
            
            let sidebarCurrentWidth = isCollapsed ? collapsedWidth : expandedWidth;
            
            // Lógica para dispositivos móveis
            if (isMobile) {
                // Configuração do container do sidebar para mobile
                sidebarContainer.classList.add("fixed", "top-0", "left-0", "z-20"); 
                sidebarContainer.classList.remove("md:relative");
                
                // No mobile, o menu fica oculto por padrão ou visível quando clicado
                if (isMobileMenuHidden) {
                    sidebarContainer.classList.remove("mobile-visible");
                    sidebarOverlay.classList.add("hidden");
                    mainContentArea.style.marginLeft = "0";
                } else {
                    // Quando o menu mobile está visível
                    sidebarContainer.classList.add("mobile-visible");
                    sidebarOverlay.classList.remove("hidden");
                    mainContentArea.style.marginLeft = "0";
                }
            } 
            // Lógica para desktop
            else { 
                // Resetar classes mobile para desktop
                sidebarContainer.classList.remove("mobile-visible");
                
                // Aplicar largura baseada no estado collapsed/expanded
                sidebarContainer.style.width = sidebarCurrentWidth;
                mainContentArea.style.marginLeft = sidebarCurrentWidth; // Ajusta a margem do conteúdo principal
                
                sidebarOverlay.classList.add("hidden"); // Overlay nunca no desktop
            }

            // Gerencia a visibilidade dos textos do menu e logo
            if (isCollapsed) {
                sidebar.classList.add("collapsed"); // Adiciona uma classe para estilização CSS se necessário
                menuTexts.forEach(text => {
                    text.classList.add("opacity-0", "hidden");
                    text.classList.remove("opacity-100");
                });
                toggleButton.querySelector("svg").style.transform = "rotate(180deg)";
                if (sidebarLogo) sidebarLogo.classList.add("opacity-0", "scale-0");
            } else {
                sidebar.classList.remove("collapsed");
                menuTexts.forEach(text => {
                    text.classList.remove("opacity-0", "hidden");
                    text.classList.add("opacity-100");
                });
                toggleButton.querySelector("svg").style.transform = "rotate(0deg)";
                if (sidebarLogo) sidebarLogo.classList.remove("opacity-0", "scale-0");
            }

            // Atualiza a visibilidade do logo na topbar
            const topbarLogoContainer = document.getElementById("topbarLogoContainer");
            if (topbarLogoContainer) {
                if (isCollapsed && !isMobile) { // Apenas mostra o logo na topbar em desktop quando o menu está recolhido
                    topbarLogoContainer.classList.remove("opacity-0");
                    topbarLogoContainer.classList.add("opacity-100");
                } else {
                    topbarLogoContainer.classList.remove("opacity-100");
                    topbarLogoContainer.classList.add("opacity-0");
                }
            }
        }

        // Fecha o menu quando clica no overlay
        sidebarOverlay.addEventListener("click", () => {
            const isMobile = window.innerWidth < 768;
            if (isMobile) {
                isMobileMenuHidden = true;
            } else {
                isCollapsed = true;
            }
            adjustLayout();
        });

        // Ação do botão de toggle no sidebar (funciona tanto no desktop quanto no mobile)
        toggleButton.addEventListener("click", () => {
            const isMobile = window.innerWidth < 768;
            if (isMobile) {
                isMobileMenuHidden = !isMobileMenuHidden;
            } else {
                isCollapsed = !isCollapsed;
            }
            adjustLayout();
        });
        
        // Ação do botão de menu mobile na topbar
        document.addEventListener("click", function(event) {
            if (event.target.closest("#mobileMenuButton")) {
                isMobileMenuHidden = !isMobileMenuHidden;
                adjustLayout();
            }
        });

        // Ajusta o layout inicial na carga da página
        function initializeLayout() {
            const isMobile = window.innerWidth < 768;
            if (isMobile) {
                isMobileMenuHidden = true;
            } else {
                // Para desktop, o estado inicial é recolhido
                isCollapsed = true;
            }
            adjustLayout();
        }
        
        initializeLayout(); // Aplica o layout inicial

        // Ajusta o layout quando a janela é redimensionada
        let resizeTimer;
        window.addEventListener("resize", () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(initializeLayout, 100); // Debounce para evitar chamadas excessivas
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    </body>
</html>';

    return $html;
}

// Função para renderizar a estrutura principal da página
function renderPageStructure($user, $content) {
    require_once __DIR__ . '/../menu.php'; // Contém a função renderMenu
    require_once __DIR__ . '/../topbar.php'; // Contém a função renderTopBar
    
    // MODIFICAÇÃO: Simplificado, já que a função get_logo_url não foi fornecida, usando um placeholder.
    $logo_url = '/bookmarks/images/logo.png'; // Caminho para seu logo

    $html = '<div class="min-h-screen flex">
        <div id="sidebarContainer" class="transition-all duration-300 ease-in-out fixed top-0 left-0 h-screen z-20">
            <aside id="sidebar" class="bg-white shadow-sm border-r h-screen flex flex-col collapsed">
                <div class="h-16 flex items-center justify-between px-4 border-b flex-shrink-0">
                    <div id="logoContainer" class="flex items-center justify-center flex-1 overflow-hidden transition-opacity duration-300">
                        <img src="' . htmlspecialchars($logo_url) . '" alt="Logo Sistema" 
                             id="sidebarLogo"
                             class="h-8 w-auto transition-all duration-300" 
                             onerror="this.onerror=null; this.src=\'https://placehold.co/120x32/FFFFFF/1E293B?text=Logo&font=inter\'; this.alt=\'Logo Alternativa\';">
                    </div>
                    <button id="toggleSidebar" class="p-2 rounded-md text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 transition-transform duration-300">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
                        </svg>
                    </button>
                </div>
                
                <div class="p-4 overflow-y-auto flex-grow">
                     ' . renderMenu($_SERVER['PHP_SELF'], true) . '
                </div>
            </aside>
        </div>

        <div id="sidebarOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 transition-opacity md:hidden hidden z-10" 
             aria-hidden="true"></div>

        <div id="mainContentArea" class="flex-1 flex flex-col min-w-0 transition-all duration-300 ease-in-out">
            ' . renderTopBar($user, true) . '

            <main class="flex-1 p-4 sm:p-6 lg:p-8 overflow-y-auto">
                <div class="max-w-7xl mx-auto w-full">
                    ' . $content . '
                </div>
            </main>
        </div>
    </div>';

    return $html;
}

// Função para exibir tempo relativo (ex: há 3 dias, há 2 meses)
function tempoDecorrido($data) {
    // Verificar se a data é válida
    if (empty($data)) {
        return '-';
    }
    
    $agora = new DateTime();
    $dataPedido = new DateTime($data);
    $diff = $agora->diff($dataPedido);
    if ($diff->y > 0) {
        return 'há ' . $diff->y . ' ' . ($diff->y == 1 ? 'ano' : 'anos');
    } elseif ($diff->m > 0) {
        return 'há ' . $diff->m . ' ' . ($diff->m == 1 ? 'mês' : 'meses');
    } elseif ($diff->d > 0) {
        return 'há ' . $diff->d . ' ' . ($diff->d == 1 ? 'dia' : 'dias');
    } elseif ($diff->h > 0) {
        return 'há ' . $diff->h . ' ' . ($diff->h == 1 ? 'hora' : 'horas');
    } elseif ($diff->i > 0) {
        return 'há ' . $diff->i . ' ' . ($diff->i == 1 ? 'minuto' : 'minutos');
    } else {
        return 'agora mesmo';
    }
}
?>