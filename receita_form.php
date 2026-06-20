<?php
// receita_form.php — Criar / Editar Receita
require_once 'includes/conexao.php';
require_once 'includes/auth.php';
exigirLogin();

$conn = conectar();
$id   = intval($_GET['id'] ?? 0);
$modo = $id ? 'editar' : 'criar';

// Carrega dados se editando
$dados = [
    'nome'         => '',
    'descricao'    => '',
    'data_registro'=> date('Y-m-d'),
    'custo'        => '',
    'tipo_receita' => 'doce',
];

if ($modo === 'editar') {
    $stmt = $conn->prepare("SELECT * FROM receita WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        header('Location: index.php');
        exit;
    }
    $dados = $row;
}

$erros = [];

// Processa POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome          = trim($_POST['nome'] ?? '');
    $descricao     = trim($_POST['descricao'] ?? '');
    $data_registro = trim($_POST['data_registro'] ?? '');
    $custo         = str_replace(',', '.', trim($_POST['custo'] ?? ''));
    $tipo_receita  = $_POST['tipo_receita'] ?? '';

    // Validações
    if (!$nome)          $erros[] = 'O nome é obrigatório.';
    if (!$data_registro) $erros[] = 'A data de registro é obrigatória.';
    if (!is_numeric($custo) || $custo < 0) $erros[] = 'O custo deve ser um valor válido.';
    if (!in_array($tipo_receita, ['doce', 'salgada'])) $erros[] = 'Tipo de receita inválido.';

    // Guarda para re-exibir
    $dados = compact('nome', 'descricao', 'data_registro', 'custo', 'tipo_receita');

    if (empty($erros)) {
        if ($modo === 'criar') {
            $stmt = $conn->prepare("INSERT INTO receita (nome, descricao, data_registro, custo, tipo_receita) VALUES (?,?,?,?,?)");
            $stmt->bind_param('sssds', $nome, $descricao, $data_registro, $custo, $tipo_receita);
            $stmt->execute();
            $stmt->close();
            header('Location: index.php?msg=criada');
        } else {
            $stmt = $conn->prepare("UPDATE receita SET nome=?, descricao=?, data_registro=?, custo=?, tipo_receita=? WHERE id=?");
            $stmt->bind_param('sssdsi', $nome, $descricao, $data_registro, $custo, $tipo_receita, $id);
            $stmt->execute();
            $stmt->close();
            header('Location: index.php?msg=editada');
        }
        $conn->close();
        exit;
    }
}

$conn->close();
$titulo = $modo === 'criar' ? '➕ Nova Receita' : '✏️ Editar Receita';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?> — Sistema de Receitas</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="index.php" class="brand"><span>🍽️</span> Sistema de Receitas</a>
    <div class="nav-links">
        <a href="index.php">📋 Receitas</a>
        <a href="receita_form.php" class="active">➕ Nova Receita</a>
        <span class="user-info">👤 <?= htmlspecialchars(nomeUsuario()) ?></span>
        <a href="logout.php">Sair</a>
    </div>
</nav>

<div class="container">
    <div class="page-header">
        <div>
            <h1><?= $titulo ?></h1>
            <p><?= $modo === 'criar' ? 'Preencha os dados para cadastrar uma nova receita.' : 'Altere os dados da receita.' ?></p>
        </div>
        <a href="index.php" class="btn btn-secondary">← Voltar</a>
    </div>

    <?php if ($erros): ?>
        <div class="alert alert-danger">
            ⚠️ <div>
            <?php foreach ($erros as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-card">
        <form method="POST" action="receita_form.php<?= $modo === 'editar' ? "?id=$id" : '' ?>">

            <div class="form-group">
                <label for="nome">Nome da Receita *</label>
                <input type="text" id="nome" name="nome"
                       value="<?= htmlspecialchars($dados['nome']) ?>"
                       placeholder="Ex: Bolo de Chocolate" required>
            </div>

            <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao"
                          placeholder="Descreva brevemente a receita, ingredientes principais..."><?= htmlspecialchars($dados['descricao']) ?></textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="tipo_receita">Tipo de Receita *</label>
                    <select id="tipo_receita" name="tipo_receita" required>
                        <option value="doce"    <?= $dados['tipo_receita'] === 'doce'    ? 'selected' : '' ?>>🍰 Doce</option>
                        <option value="salgada" <?= $dados['tipo_receita'] === 'salgada' ? 'selected' : '' ?>>🥐 Salgada</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="custo">Custo (R$) *</label>
                    <input type="number" id="custo" name="custo" step="0.01" min="0"
                           value="<?= htmlspecialchars($dados['custo']) ?>"
                           placeholder="0,00" required>
                </div>
            </div>

            <div class="form-group">
                <label for="data_registro">Data de Registro *</label>
                <input type="date" id="data_registro" name="data_registro"
                       value="<?= htmlspecialchars($dados['data_registro']) ?>" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <?= $modo === 'criar' ? '💾 Cadastrar Receita' : '💾 Salvar Alterações' ?>
                </button>
                <a href="index.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
