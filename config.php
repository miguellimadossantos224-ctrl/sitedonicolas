<?php
/**
 * Configuração de conexão com o banco de dados MySQL.
 *
 * IMPORTANTE (InfinityFree):
 * - Host normalmente é algo como "sql200.infinityfree.com" (veja no painel
 *   em MySQL Databases -> "Hostname").
 * - Usuário e nome do banco geralmente começam com "if0_XXXXXXX_".
 * - Crie o banco de dados no painel do InfinityFree ANTES de importar o db.sql.
 */

$DB_HOST = 'localhost';           // ex: sql200.infinityfree.com
$DB_NAME = 'if0_00000000_escola'; // nome do banco criado no InfinityFree
$DB_USER = 'if0_00000000';        // usuário do banco
$DB_PASS = 'sua_senha_aqui';      // senha do banco

mysqli_report(MYSQLI_REPORT_OFF);
$mysqli = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($mysqli->connect_errno) {
    die('Erro de conexão com o banco de dados: ' . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');

// Função utilitária para sanitizar saída em HTML
function h($valor) {
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}
