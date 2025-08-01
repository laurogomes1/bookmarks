// Script para definir o ano atual no rodapé
document.getElementById("currentYear").textContent = new Date().getFullYear();

// Script para lógica de login e PIN
const loginForm = document.getElementById("loginForm");
const credentialsSection = document.getElementById("credentialsSection");
const pinSection = document.getElementById("pinSection");
const pinSectionHeader = document.getElementById("pinSectionHeader");
const backToLoginButton = document.getElementById("backToLoginButton");

const emailInput = document.getElementById("email");
const passwordInput = document.getElementById("password");
const entrarButton = document.getElementById("entrarButton");
const loginError = document.getElementById("loginError");

const mainTitle = document.getElementById("mainTitle");
const mainSubtitle = document.getElementById("mainSubtitle");
const pinInput = document.getElementById("pin");

const originalTitle = "Bem-vindo de volta!";
const originalSubtitle = "Faça login para acessar seu painel.";

entrarButton.addEventListener("click", async function (event) {
	event.preventDefault();

	// Validação básica do formato do email e senha preenchida
	if (emailInput.value.trim() === "" || !emailInput.value.includes("@")) {
		loginError.textContent = "Por favor, insira um email válido.";
		loginError.classList.remove("hidden");
		emailInput.focus();
		return;
	}

	if (passwordInput.value.trim() === "") {
		loginError.textContent = "Por favor, insira sua senha.";
		loginError.classList.remove("hidden");
		passwordInput.focus();
		return;
	}

	// Verifica as credenciais via AJAX
	try {
		const formData = new FormData();
		formData.append("email", emailInput.value);
		formData.append("password", passwordInput.value);

		console.log("Dados enviados:", {
			email: emailInput.value,
			senha_digitada: passwordInput.value,
		});

		const response = await fetch("verify_credentials.php", {
			method: "POST",
			body: formData,
		});

		const data = await response.json();
		console.log(
			"Resposta completa do servidor:",
			JSON.stringify(data, null, 2)
		);

		if (data.success) {
			loginError.classList.add("hidden");

			// Mostra a seção do PIN
			if (mainTitle) mainTitle.textContent = "Verificação de Segurança";
			if (mainSubtitle)
				mainSubtitle.textContent = "Por favor, insira seu PIN de 6 dígitos.";

			credentialsSection.classList.add("hidden");
			pinSection.classList.remove("hidden");
			pinSectionHeader.classList.remove("hidden");

			if (pinInput) {
				pinInput.value = "";
				pinInput.focus();
			}
		} else {
			console.log("Detalhes da falha de autenticação:", {
				mensagem: data.message,
				debug: data.debug,
			});
			loginError.textContent = data.message || "Email ou senha inválidos.";
			if (data.debug) {
				console.log("Debug info:", data.debug);
			}
			loginError.classList.remove("hidden");
			passwordInput.value = "";
			passwordInput.focus();
		}
	} catch (error) {
		console.error("Erro na requisição:", error);
		loginError.textContent = "Erro ao verificar credenciais. Tente novamente.";
		loginError.classList.remove("hidden");
	}
});

if (backToLoginButton) {
	backToLoginButton.addEventListener("click", function () {
		if (mainTitle) mainTitle.textContent = originalTitle;
		if (mainSubtitle) mainSubtitle.textContent = originalSubtitle;

		pinSection.classList.add("hidden");
		pinSectionHeader.classList.add("hidden");
		credentialsSection.classList.remove("hidden");
		loginError.classList.add("hidden");

		if (pinInput) pinInput.value = "";
		if (emailInput) emailInput.focus();
	});
}

// Esconde a mensagem de erro se o usuário começar a digitar
emailInput.addEventListener("input", function () {
	loginError.classList.add("hidden");
});
passwordInput.addEventListener("input", function () {
	loginError.classList.add("hidden");
});

// Adiciona listeners para capturar Enter nos campos de email e senha
emailInput.addEventListener("keypress", function (e) {
	if (e.key === "Enter") {
		e.preventDefault();
		entrarButton.click(); // Simula o clique no botão Entrar
	}
});

passwordInput.addEventListener("keypress", function (e) {
	if (e.key === "Enter") {
		e.preventDefault();
		entrarButton.click(); // Simula o clique no botão Entrar
	}
});

if (pinInput) {
	pinInput.addEventListener("input", function (e) {
		let value = e.target.value.replace(/\D/g, "");
		if (value.length > 6) {
			value = value.slice(0, 6);
		}
		e.target.value = value;
		// Remove a mensagem de erro quando o usuário começa a digitar novamente
		document.getElementById("pinError")?.classList.add("hidden");
	});

	// Adiciona listener para capturar o Enter no campo do PIN
	pinInput.addEventListener("keypress", function (e) {
		if (e.key === "Enter") {
			e.preventDefault();
			const currentPin = pinInput.value;
			if (currentPin.length === 6) {
				loginForm.submit(); // Envia o formulário para validação no backend
			} else {
				document.getElementById("pinError")?.classList.remove("hidden");
				pinInput.focus();
			}
		}
	});
}

const confirmarPinButton = document.getElementById("confirmarPinButton");
if (confirmarPinButton) {
	confirmarPinButton.addEventListener("click", function () {
		const currentPin = pinInput.value;
		if (currentPin.length === 6) {
			loginForm.submit(); // Envia o formulário para validação no backend
		} else {
			document.getElementById("pinError")?.classList.remove("hidden");
			pinInput.focus();
		}
	});
}
