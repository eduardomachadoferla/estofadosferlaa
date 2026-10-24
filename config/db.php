<?php
declare(strict_types=1);
require_once __DIR__ . '/app.php';

// Em produção, FERLA_DB_PATH pode apontar para um diretório fora do site.
try {
    $dbPath = getenv('FERLA_DB_PATH') ?: dirname(__DIR__) . '/storage/database.sqlite';
    $dir = dirname($dbPath);
    if (!is_dir($dir) && !mkdir($dir, 0750, true) && !is_dir($dir)) {
        throw new RuntimeException('Não foi possível criar o diretório do banco de dados.');
    }
    if (!is_file($dbPath)) {
        $seed = dirname(__DIR__) . '/data/catalogo-inicial.sqlite.gz';
        if (is_file($seed)) {
            $tempPath = $dbPath . '.' . bin2hex(random_bytes(4)) . '.tmp';
            $in = gzopen($seed, 'rb');
            $out = fopen($tempPath, 'wb');
            if (!$in || !$out) throw new RuntimeException('Falha ao preparar banco inicial.');
            try {
                while (!gzeof($in)) {
                    $data = gzread($in, 8192);
                    if ($data === false || fwrite($out, $data) !== strlen($data)) throw new RuntimeException('Falha ao restaurar catálogo inicial.');
                }
            } finally {
                gzclose($in); fclose($out);
            }
            if (!is_file($dbPath)) {
                if (!rename($tempPath, $dbPath)) throw new RuntimeException('Falha ao instalar catálogo inicial.');
            } else { @unlink($tempPath); }
        }
    }
    $pdo = new PDO('sqlite:' . $dbPath, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON');
    require_once __DIR__ . '/schema.php';
    ensure_schema($pdo);
} catch (Throwable $e) {
    error_log('Erro na conexão/migração do catálogo: ' . $e->getMessage());
    http_response_code(500);
    exit('Não foi possível acessar o catálogo. Confira a extensão pdo_sqlite, os arquivos e as permissões do servidor.');
}
