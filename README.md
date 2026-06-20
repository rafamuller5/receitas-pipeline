# 🍽️ Sistema de Receitas

Aplicação web simples para gerenciamento de receitas doces e salgadas.
Desenvolvida em **PHP + MySQL**, sem frameworks, fácil de instalar em qualquer VM com LAMP/WAMP.

---

## 📋 Requisitos

- PHP 7.4 ou superior
- MySQL 5.7 ou superior (ou MariaDB 10+)
- Servidor web: Apache ou Nginx

> **Dica rápida para VM:** Instale o pacote LAMP de uma só vez:
> ```bash
> sudo apt update && sudo apt install -y apache2 mysql-server php php-mysqli
> ```

---

## 🚀 Instalação Passo a Passo

### 1. Copiar os arquivos

Copie a pasta `receitas/` para o diretório do servidor web:

```bash
# Apache no Ubuntu/Debian:
sudo cp -r receitas/ /var/www/html/receitas

# Permissões
sudo chown -R www-data:www-data /var/www/html/receitas
```

### 2. Criar o banco de dados

Acesse o MySQL e execute o script:

```bash
mysql -u root -p < /var/www/html/receitas/banco.sql
```

Ou manualmente:
```sql
mysql -u root -p
source /var/www/html/receitas/banco.sql;
```

### 3. Configurar a conexão

Edite o arquivo `includes/conexao.php` com seus dados:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');    // seu usuário MySQL
define('DB_PASS', '');        // sua senha MySQL
define('DB_NAME', 'db_receitas');
```

### 4. Acessar o sistema

Abra o navegador e acesse:
```
http://localhost/receitas/
```

---

## 🔐 Credenciais de Acesso

| Usuário | Login   | Senha    |
|---------|---------|----------|
| Administrador | `admin` | `admin123` |
| Maria Silva   | `maria` | `maria123` |

---

## 📁 Estrutura do Projeto

```
receitas/
├── banco.sql              ← Script SQL (tabelas + dados)
├── login.php              ← Tela de login
├── logout.php             ← Encerrar sessão
├── index.php              ← Listagem de receitas
├── receita_form.php       ← Criar / Editar receita (CRUD)
├── receita_excluir.php    ← Excluir receita
├── includes/
│   ├── conexao.php        ← Configuração do banco
│   └── auth.php           ← Controle de sessão
└── assets/
    └── css/
        └── style.css      ← Estilos da aplicação
```

---

## ✨ Funcionalidades

- ✅ Login e controle de sessão
- ✅ Listagem de receitas com filtros (nome, tipo)
- ✅ Estatísticas (total, doces, salgadas, custo médio)
- ✅ **CRUD completo** de receitas:
  - Criar nova receita
  - Editar receita existente
  - Excluir com confirmação
- ✅ Banco populado com 10 receitas + 2 usuários

---

## 🗄️ Estrutura do Banco de Dados

### Tabela `receita`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT AUTO_INCREMENT | Chave primária |
| nome | VARCHAR(150) | Nome da receita |
| descricao | TEXT | Descrição da receita |
| data_registro | DATE | Data de cadastro |
| custo | DECIMAL(10,2) | Custo de produção |
| tipo_receita | ENUM('doce','salgada') | Tipo |

### Tabela `usuario`
| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT AUTO_INCREMENT | Chave primária |
| nome | VARCHAR(100) | Nome completo |
| login | VARCHAR(50) | Login único |
| senha | VARCHAR(255) | Senha MD5 |
| situacao | ENUM('ativo','inativo') | Status |

---

## ⚠️ Observações

- As senhas são armazenadas com **MD5** para simplicidade. Em produção, use `password_hash()` do PHP.
- O sistema usa sessões PHP nativas, sem dependências externas.
