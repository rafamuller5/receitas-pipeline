<?php
// includes/auth.php
function iniciarSessao() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function estaLogado() {
    iniciarSessao();
    return isset($_SESSION['usuario_id']);
}

function exigirLogin() {
    if (!estaLogado()) {
        header('Location: login.php');
        exit;
    }
}

function nomeUsuario() {
    return $_SESSION['usuario_nome'] ?? 'Usuário';
}

function logout() {
    iniciarSessao();
    session_destroy();
    header('Location: login.php');
    exit;
}
?>
