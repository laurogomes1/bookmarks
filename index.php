<?php
session_start();
require_once 'app/config.php';
require_once 'app/data.php';


// Configura headers de segurança
setSecurityHeaders();

// Verifica se há bloqueio por tentativas excessivas
if (!checkRateLimit('login')) {
    $error_message = 'Muitas tentativas de login. Tente novamente em 15 minutos.';
    $is_blocked = true;
} else {
    $is_blocked = false;
}
$logo_url = get_logo_url();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center antialiased px-4 py-8">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-xl p-8 md:p-10 border border-gray-200 relative"> 
            <div id="pinSectionHeader" class="hidden"> 
                <button id="backToLoginButton" title="Voltar" class="absolute top-6 left-6 text-gray-500 hover:text-sky-600 transition-colors duration-150 ease-in-out">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </button>
            </div>

            <div id="cardHeader" class="text-center mb-8">
                <div class="flex justify-center mb-6">
                    <img src="<?php echo htmlspecialchars($logo_url); ?>" alt="Logo PerfilPro.shop" class="h-10 md:h-12 w-auto" onerror="this.onerror=null; this.src='https://placehold.co/150x40/FFFFFF/1E293B?text=Logo&font=inter'; this.alt='Logo Alternativa';">
                </div>
                <h1 id="mainTitle" class="text-2xl font-semibold text-gray-900">Bem-vindo de volta!</h1>
                <p id="mainSubtitle" class="text-gray-600 mt-2 text-sm">Faça login para acessar seu painel.</p>
            </div>
           
            <form id="loginForm" action="auth.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo generateCSRFToken(); ?>">
                
                <div id="credentialsSection" class="space-y-6 <?php echo isset($_SESSION['pin_error']) ? 'hidden' : ''; ?>">
                    <div>
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-700 mb-1.5">Email</label>
                        <input type="email" id="email" name="email" required 
                               class="block w-full rounded-md border-0 py-2.5 px-3.5 bg-white text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-sky-500 sm:text-sm sm:leading-6 transition-colors duration-150 ease-in-out"
                               placeholder="seu@email.com">
                    </div>
                   
                    <div>
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-700 mb-1.5">Senha</label>
                        <input type="password" id="password" name="password" required 
                               class="block w-full rounded-md border-0 py-2.5 px-3.5 bg-white text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-sky-500 sm:text-sm sm:leading-6 transition-colors duration-150 ease-in-out"
                               placeholder="••••••••">
                    </div>
                   
                    <div class="flex items-center"> 
                        <div class="flex items-center">
                            <input type="checkbox" id="remember" name="remember" 
                                   class="h-4 w-4 rounded border-gray-300 bg-white text-sky-600 focus:ring-sky-500 focus:ring-offset-white cursor-pointer">
                            <label for="remember" class="ml-2 block text-sm text-gray-700 cursor-pointer">Lembrar-me</label>
                        </div>
                    </div>
                   
                    <button id="entrarButton" type="submit" 
                            class="flex w-full justify-center rounded-md bg-green-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 transition-colors duration-150 ease-in-out active:bg-green-700"
                            <?php echo $is_blocked ? 'disabled' : ''; ?>>
                        <?php echo $is_blocked ? 'Bloqueado' : 'Entrar'; ?>
                    </button>

                    <div id="loginError" class="<?php echo isset($_SESSION['login_error']) || $is_blocked ? '' : 'hidden'; ?> mt-4 text-sm text-red-600 bg-red-100 border border-red-300 rounded-md p-3 text-center">
                        <?php echo $is_blocked ? $error_message : 'Email ou senha inválidos. Por favor, tente novamente.'; ?>
                    </div>
                </div>

                <div id="pinSection" class="<?php echo isset($_SESSION['pin_error']) ? '' : 'hidden'; ?> space-y-6"> 
                    <div>
                        <label for="pin" class="block text-sm font-medium leading-6 text-gray-700 mb-1.5">PIN de Segurança</label>
                        <input type="password" id="pin" name="pin" maxlength="6" pattern="\d*" inputmode="numeric"
                               class="block w-full rounded-md border-0 py-2.5 px-3.5 bg-white text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-sky-500 sm:text-sm sm:leading-6 transition-colors duration-150 ease-in-out text-center tracking-[.5em]" 
                               placeholder="••••••">
                       
                        <div id="pinError" class="<?php echo isset($_SESSION['pin_error']) ? '' : 'hidden'; ?> mt-2 text-sm text-red-600 bg-red-100 border border-red-300 rounded-md p-3 text-center">
                            PIN inválido. Por favor, tente novamente.
                        </div>
                    </div>
                    <button id="confirmarPinButton" type="button"
                            class="flex w-full justify-center rounded-md bg-green-600 px-3 py-2.5 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 transition-colors duration-150 ease-in-out active:bg-green-700">
                        Confirmar PIN
                    </button>
                </div>

                <?php if (isset($_SESSION['temp_credentials'])): ?>
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['temp_credentials']['email']); ?>">
                    <input type="hidden" name="password" value="<?php echo htmlspecialchars($_SESSION['temp_credentials']['password']); ?>">
                <?php endif; ?>
            </form>
        </div>
       
        <p class="text-center mt-6 text-xs text-gray-500">
            &copy; <span id="currentYear"></span> PerfilPro.shop. Todos os direitos reservados.
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Lógica para o botão de voltar (se já não existir em main.js)
            const backToLoginButton = document.getElementById('backToLoginButton');
            const credentialsSection = document.getElementById('credentialsSection');
            const pinSection = document.getElementById('pinSection');
            const pinSectionHeader = document.getElementById('pinSectionHeader');
            const mainTitle = document.getElementById('mainTitle');
            const mainSubtitle = document.getElementById('mainSubtitle');

            if (backToLoginButton) {
                backToLoginButton.addEventListener('click', function() {
                    // Esconde a seção do PIN e mostra a de credenciais
                    pinSection.classList.add('hidden');
                    pinSectionHeader.classList.add('hidden');
                    credentialsSection.classList.remove('hidden');

                    // Restaura o título e subtitulo
                    mainTitle.textContent = 'Bem-vindo de volta!';
                    mainSubtitle.textContent = 'Faça login para acessar seu painel.';
                    
                    // Limpa o campo de PIN e erros
                    document.getElementById('pin').value = '';
                    document.getElementById('pinError').classList.add('hidden');
                    document.getElementById('loginError').classList.add('hidden');

                    // Limpa a sessão PHP via AJAX para não persistir o erro
                    fetch('app/clear_session.php', { method: 'POST' });
                });
            }

            // Lógica para a tecla Enter no campo PIN
            const pinInput = document.getElementById('pin');
            const confirmarPinButton = document.getElementById('confirmarPinButton');

            if (pinInput && confirmarPinButton) {
                pinInput.addEventListener('keydown', function(event) {
                    if (event.key === 'Enter') {
                        event.preventDefault(); 
                        confirmarPinButton.click(); 
                    }
                });
            }

            // Lógica para o ano atual no rodapé
            const currentYearSpan = document.getElementById('currentYear');
            if (currentYearSpan) {
                currentYearSpan.textContent = new Date().getFullYear();
            }
        });
    </script>
    <script src="assets/js/main.js"></script> </body>
</html>
<?php
// Limpa as mensagens de erro e credenciais temporárias após exibir
if (isset($_SESSION['pin_error'])) {
    unset($_SESSION['pin_error']);
}
if (isset($_SESSION['login_error'])) {
    unset($_SESSION['login_error']);
}
// Não remova 'temp_credentials' aqui, pois o formulário PIN pode precisar dele.
// A remoção deve ser feita no script auth.php após a validação final.
?>