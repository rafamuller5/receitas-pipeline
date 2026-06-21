<?php
// index.php — Listagem de Receitas
require_once 'includes/conexao.php';
require_once 'includes/auth.php';
exigirLogin();

$conn = conectar();

// Filtros
$busca = trim($_GET['busca'] ?? '');
$tipo  = $_GET['tipo'] ?? '';

$where = "WHERE 1=1";
$params = [];
$types  = '';

if ($busca) {
    $where   .= " AND (nome LIKE ? OR descricao LIKE ?)";
    $like     = "%$busca%";
    $params[] = $like;
    $params[] = $like;
    $types   .= 'ss';
}
if ($tipo === 'doce' || $tipo === 'salgada') {
    $where   .= " AND tipo_receita = ?";
    $params[] = $tipo;
    $types   .= 's';
}

$sql  = "SELECT * FROM receita $where ORDER BY data_registro DESC";
$stmt = $conn->prepare($sql);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$receitas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Estatísticas
$stats = $conn->query("SELECT
    COUNT(*) AS total,
    SUM(tipo_receita='doce')    AS doces,
    SUM(tipo_receita='salgada') AS salgadas,
    AVG(custo) AS custo_medio
    FROM receita")->fetch_assoc();

$conn->close();

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receitas — Listagem</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <a href="index.php" class="brand"><span>🍽️</span> Sistema de Receitas</a>
    <div class="nav-links">
        <a href="index.php" class="active">📋 Receitas</a>
        <a href="receita_form.php">➕ Nova Receita</a>
        <span class="user-info">👤 <?= htmlspecialchars(nomeUsuario()) ?></span>
        <a href="logout.php">Sair</a>
    </div>
</nav>

<div class="container">

    <!-- ALERTAS -->
    <?php if ($msg === 'criada'): ?>
        <div class="alert alert-success">✅ Receita cadastrada com sucesso!</div>
    <?php elseif ($msg === 'editada'): ?>
        <div class="alert alert-success">✅ Receita atualizada com sucesso!</div>
    <?php elseif ($msg === 'excluida'): ?>
        <div class="alert alert-success">✅ Receita excluída com sucesso!</div>
    <?php endif; ?>

    <!-- HEADER -->
    <div class="page-header">
        <div>
            <h1>📋 Receitaz Cadastradas</h1>
            <p>Gerencie suas receitas doces e salgadas</p>
        </div>
        <a href="receita_form.php" class="btn btn-primary">➕ Nova Receita</a>
    </div>

    <!-- STATS -->
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-icon">📚</div>
            <div>
                <div class="stat-label">Total</div>
                <div class="stat-value"><?= $stats['total'] ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🍰</div>
            <div>
                <div class="stat-label">Doces</div>
                <div class="stat-value"><?= $stats['doces'] ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🥐</div>
            <div>
                <div class="stat-label">Salgadas</div>
                <div class="stat-value"><?= $stats['salgadas'] ?></div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div>
                <div class="stat-label">Custo Médio</div>
                <div class="stat-value">R$ <?= number_format($stats['custo_medio'] ?? 0, 2, ',', '.') ?></div>
            </div>
        </div>
    </div>

    <!-- FILTROS -->
    <form method="GET" action="index.php">
        <div class="filters">
            <label for="busca" class="sr-only">Buscar por nome ou descrição</label>
            <input type="text" id="busca" name="busca" placeholder="🔍 Buscar por nome ou descrição..."
                   value="<?= htmlspecialchars($busca) ?>">
            <label for="tipo" class="sr-only">Tipo de receita</label>
            <select id="tipo" name="tipo">
                <option value="">Todos os tipos</option>
                <option value="doce"    <?= $tipo === 'doce'    ? 'selected' : '' ?>>🍰 Doce</option>
                <option value="salgada" <?= $tipo === 'salgada' ? 'selected' : '' ?>>🥐 Salgada</option>
            </select>
            <button type="submit" class="btn btn-primary">Filtrar</button>
            <?php if ($busca || $tipo): ?>
                <a href="index.php" class="btn btn-secondary">Limpar</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- TABELA -->
    <div class="card">
        <?php if (empty($receitas)): ?>
            <div class="empty-state">
                <div class="icon">🍽️</div>
                <p>Nenhuma receita encontrada.</p>
            </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Custo</th>
                    <th>Data de Registro</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($receitas as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td>
                        <strong><?= htmlspecialchars($r['nome']) ?></strong>
                        <?php if ($r['descricao']): ?>
                            <br><small style="color:var(--text-muted)"><?= htmlspecialchars(mb_substr($r['descricao'], 0, 70)) ?>...</small>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($r['tipo_receita'] === 'doce'): ?>
                            <span class="badge badge-doce">🍰 Doce</span>
                        <?php else: ?>
                            <span class="badge badge-salgada">🥐 Salgada</span>
                        <?php endif; ?>
                    </td>
                    <td><strong>R$ <?= number_format($r['custo'], 2, ',', '.') ?></strong></td>
                    <td><?= date('d/m/Y', strtotime($r['data_registro'])) ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="receita_form.php?id=<?= $r['id'] ?>" class="btn btn-secondary btn-sm">✏️ Editar</a>
                            <a href="receita_excluir.php?id=<?= $r['id'] ?>" class="btn btn-danger btn-sm"
                               onclick="return confirm('Excluir a receita \'<?= htmlspecialchars(addslashes($r['nome'])) ?>\'?')">🗑️</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <p style="margin-top:1rem; font-size:0.82rem; color:var(--text-muted); text-align:right;">
        <?= count($receitas) ?> receita(s) exibida(s)
    </p>
</div>

</body>
</html>
