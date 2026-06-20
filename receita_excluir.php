<?php
// receita_excluir.php
require_once 'includes/conexao.php';
require_once 'includes/auth.php';
exigirLogin();

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    $conn = conectar();
    $stmt = $conn->prepare("DELETE FROM receita WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

header('Location: index.php?msg=excluida');
exit;
