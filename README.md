# 🔒 Sistema de Gerenciamento Seguro

Sistema web robusto e seguro para gerenciamento de usuários e configurações, com implementações avançadas de segurança.

## Requisitos

- PHP 7.4+
- MySQL 5.7+
- Servidor web (Apache/Nginx)
- MAMP ou ambiente similar

## Instalação

1. Clone o repositório

```bash
git clone git@github.com:laurogomes1/painel_perfilpro.git
```

2. Configure o banco de dados

- Importe o esquema do banco de dados (se disponível)
- Configure as credenciais do banco em `app/data.php`

3. Configure o ambiente

- Certifique-se que o PHP está configurado corretamente
- Habilite as extensões necessárias (PDO, mysqli)

## 📁 Estrutura do Projeto

```
/
├── index.php              # Página de login com proteções
├── auth.php               # Autenticação segura
├── logout.php             # Logout seguro
├── verify_credentials.php # Verificação AJAX
├── app/
│   ├── config.php         # Configurações de segurança
│   ├── User.php           # Classe de usuário
│   ├── data.php           # Conexão com banco
│   ├── layout/
│   │   └── common.php     # Layout com verificações
│   └── modules/
│       ├── dashboard/     # Dashboard simplificado
│       ├── usuarios/      # Gestão de usuários
│       └── configuracoes/ # Configurações do sistema
├── assets/                # Arquivos estáticos
│   ├── css/              # Estilos
│   └── js/               # JavaScript
└── images/               # Imagens do sistema
```

## 🛡️ Segurança

### **Proteções Implementadas:**

- ✅ **CSRF Protection** - Proteção contra Cross-Site Request Forgery
- ✅ **Rate Limiting** - Proteção contra força bruta (5 tentativas/15min)
- ✅ **Session Security** - Regeneração de ID de sessão
- ✅ **Security Headers** - Headers de segurança configurados
- ✅ **Password Hashing** - PINs e senhas criptografados com `password_hash()`
- ✅ **Security Logging** - Logs detalhados de eventos de segurança
- ✅ **SQL Injection Protection** - Uso de PDO com prepared statements
- ✅ **Session Validation** - Verificação de autenticação em todas as páginas

### **Configurações de Segurança:**

- **Máximo de tentativas de login**: 5
- **Tempo de bloqueio**: 15 minutos
- **Tamanho do token CSRF**: 64 caracteres
- **Regeneração de sessão**: Após cada login
- **Logs de segurança**: Ativos em `app/logs/`

### **Sistema de Autenticação:**

- Login com email e senha
- Verificação de PIN de segurança (6 dígitos)
- Logout seguro com limpeza completa de sessão

## Desenvolvimento

Para contribuir com o projeto:

1. Crie um branch para sua feature

```bash
git checkout -b feature/nome-da-feature
```

2. Faça commit das mudanças

```bash
git commit -m "Descrição da mudança"
```

3. Envie para o repositório

```bash
git push origin feature/nome-da-feature
```

## 📊 Status do Sistema

### **Versão:** 2.0 (Seguro)

### **Status:** ✅ Produção

### **Última atualização:** Janeiro 2025

## 📞 Suporte

Para dúvidas sobre implementações de segurança ou problemas técnicos, consulte:

- **Logs de segurança:** `app/logs/`
- **Documentação completa:** `DOCUMENTACAO_SEGURANCA.md`

## Licença

Todos os direitos reservados - Sistema de Gerenciamento
