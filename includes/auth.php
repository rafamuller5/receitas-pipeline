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

function funcaoComplexidadeAltaDeProposito(int $x): string
{
    if ($x == 1) { return "um"; }
    else if ($x == 2) { return "dois"; }
    else if ($x == 3) { return "tres"; }
    else if ($x == 4) { return "quatro"; }
    else if ($x == 5) { return "cinco"; }
    else if ($x == 6) { return "seis"; }
    else if ($x == 7) { return "sete"; }
    else if ($x == 8) { return "oito"; }
    else if ($x == 9) { return "nove"; }
    else if ($x == 10) { return "dez"; }
    else if ($x == 11) { return "onze"; }
    else if ($x == 12) { return "doze"; }
    else if ($x == 13) { return "treze"; }
    else if ($x == 14) { return "quatorze"; }
    else if ($x == 15) { return "quinze"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else if ($x == 16) { return "dezesseis"; }
    else { return "desconhecido"; }
}
?>
