<?php
// Execute no terminal: php tools/criar_admin.php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once dirname(__DIR__) . '/config/db.php';
fwrite(STDOUT,"Nome de usuário: "); $usuario=trim((string)fgets(STDIN));
fwrite(STDOUT,"Nova senha (mínimo 12 caracteres): ");
if (function_exists('shell_exec')) @shell_exec('stty -echo');
$senha=trim((string)fgets(STDIN));
if (function_exists('shell_exec')) @shell_exec('stty echo');
fwrite(STDOUT,"\n");
if (!preg_match('/^[a-zA-Z0-9_.-]{3,50}$/',$usuario) || strlen($senha)<12) { fwrite(STDERR,"Usuário inválido ou senha muito curta.\n"); exit(1); }
$hash=password_hash($senha,PASSWORD_DEFAULT);
$pdo->prepare('INSERT INTO admin_usuarios (usuario,senha_hash) VALUES (?,?) ON CONFLICT(usuario) DO UPDATE SET senha_hash=excluded.senha_hash')->execute([$usuario,$hash]);
fwrite(STDOUT,"Administrador configurado! Abra /admin/login.php\n");
