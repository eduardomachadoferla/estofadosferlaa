<?php
// php tools/verificar.php (para validar a hospedagem sem divulgar dados sensíveis)
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
$checks = [
    'PHP 8.1+' => PHP_VERSION_ID >= 80100,
    'pdo_sqlite' => extension_loaded('pdo_sqlite'),
    'fileinfo' => extension_loaded('fileinfo'),
    'zlib' => extension_loaded('zlib'),
    'upload de arquivos habilitado' => (bool)ini_get('file_uploads'),
    'pasta storage gravável' => is_writable(dirname(__DIR__) . '/storage'),
    'pasta uploads gravável' => is_writable(dirname(__DIR__) . '/assets/uploads'),
];
foreach ($checks as $label=>$pass) echo ($pass ? '[OK] ' : '[FALHA] ') . $label . "\n";
if (!in_array(false,$checks,true)) {
    require dirname(__DIR__) . '/config/db.php';
    echo '[OK] SQLite conectado; produtos: '.(int)$pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn()."\n";
}
exit(in_array(false,$checks,true) ? 1 : 0);
