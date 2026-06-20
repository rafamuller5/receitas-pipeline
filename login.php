<?php
// login.php
require_once 'includes/conexao.php';
require_once 'includes/auth.php';

iniciarSessao();

// Se já logado, redireciona
if (estaLogado()) {
    header('Location: index.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if ($login && $senha) {
        $conn = conectar();
        $stmt = $conn->prepare("SELECT id, nome, senha FROM usuario WHERE login = ? AND situacao = 'ativo' LIMIT 1");
        $stmt->bind_param('s', $login);
        $stmt->execute();
        $res = $stmt->get_result();
        $user = $res->fetch_assoc();
        $stmt->close();
        $conn->close();

        if ($user && $user['senha'] === md5($senha)) {
            $_SESSION['usuario_id']   = $user['id'];
            $_SESSION['usuario_nome'] = $user['nome'];
            header('Location: index.php');
            exit;
        } else {
            $erro = 'Login ou senha inválidos.';
        }
    } else {
        $erro = 'Preencha todos os campos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistema de Receitaz</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="login-wrapper">
    <div class="login-box">
        <div class="login-logo">
            <div class="icon">🍽️</div>
            <h1>Sistema de Receitas</h1>
            <p>Acesse sua conta para continuar</p>
        </div>

        <?php if ($erro): ?>
            <div class="alert alert-danger">⚠️ <?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="login">Usuário</label>
                <input type="text" id="login" name="login" placeholder="Digite seu login"
                       value="<?= htmlspecialchars($_POST['login'] ?? '') ?>" autocomplete="username" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary">🔐 Entrar</button>
        </form>

        <div class="login-hint">
            Acesso padrão: <b>admin</b> / <b>admin123</b>
        </div>
    </div>
</div>
</body>
</html>
