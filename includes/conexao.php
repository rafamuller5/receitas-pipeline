<?php
// includes/conexao.php
// Configurações do banco de dados
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'db_receitas');

function conectar() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    $conn->set_charset('utf8mb4');

    if ($conn->connect_error) {
        die('<div style="font-family:sans-serif;padding:2rem;color:#c0392b;">
            <h2>❌ Erro de Conexão</h2>
            <p>' . $conn->connect_error . '</p>
            <p>Verifique as configurações em <strong>includes/conexao.php</strong></p>
        </div>');
    }
    return $conn;
}
?>
