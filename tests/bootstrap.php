<?php
/**
 * Bootstrap para testes PHPUnit
 * GCS 2026/A — Sistema de Receitas
 */

// Autoloader do Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Definir constantes para o ambiente de testes
define('DB_HOST', getenv('MYSQL_TEST_HOST') ?: '127.0.0.1');
define('DB_USER', getenv('MYSQL_TEST_USER') ?: 'appuser');
define('DB_PASS', getenv('MYSQL_TEST_PASS') ?: 'App@2026');
define('DB_NAME', getenv('MYSQL_TEST_DB')   ?: 'db_receitas_test');
define('APP_ENV', 'testing');

/**
 * Função conectar() mockada para testes (sem dependência real de DB
 * nos testes unitários — os testes de integração usam DB real)
 */
if (!function_exists('conectar')) {
    function conectar(): \mysqli {
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        return $conn;
    }
}

if (!function_exists('nomeUsuario')) {
    function nomeUsuario(): string {
        return 'Testador';
    }
}

if (!function_exists('exigirLogin')) {
    function exigirLogin(): void {
        // No-op em testes
    }
}

if (!function_exists('estaLogado')) {
    function estaLogado(): bool {
        return true;
    }
}
