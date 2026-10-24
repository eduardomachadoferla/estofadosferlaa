<?php
require_once __DIR__ . '/../config/admin_lib.php';
if ((int)$pdo->query('SELECT COUNT(*) FROM admin_usuarios')->fetchColumn() !== 0) {
    http_response_code(404); exit('Configuração inicial indisponível.');
}
$config = is_file(__DIR__ . '/../config/local.php') ? require __DIR__ . '/../config/local.php' : [];
$installToken = getenv('FERLA_INSTALL_TOKEN') ?: (string)($config['install_token'] ?? '');
if (strlen($installToken) < 24) $installToken = '';
$erro = ''; $sucesso = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_csrf();
    $token = trim((string)($_POST['token'] ?? ''));
    $usuario = trim((string)($_POST['usuario'] ?? ''));
    $senha = (string)($_POST['senha'] ?? '');
    if ($installToken === '' || !hash_equals($installToken, $token)) $erro='Chave de instalação incorreta.';
    elseif (!preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $usuario)) $erro='Usuário: 3 a 50 caracteres, apenas letras, números, _, - ou ponto.';
    elseif (strlen($senha) < 12) $erro='Use uma senha com pelo menos 12 caracteres.';
    else {
        try {
            $pdo->prepare('INSERT INTO admin_usuarios(usuario,senha_hash) VALUES(?,?)')->execute([$usuario,password_hash($senha,PASSWORD_DEFAULT)]);
            $sucesso=true;
        } catch (Throwable $e) { $erro='Não foi possível criar o administrador.'; }
    }
}
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Primeiro acesso — Ferla</title><link rel="stylesheet" href="<?= e(site_url('admin/css/painel.css')) ?>"></head>
<body class="login-page"><section class="login-card"><div class="login-mark">FERLA<span>ESTOFADOS</span></div><div class="login-heading"><h1>Configurar primeiro acesso</h1><p>Esse formulário só funciona antes da criação do primeiro administrador.</p></div>
<?php if ($sucesso): ?><p class="alert alert-success">Administrador criado! Apague a chave de instalação do arquivo de configuração e faça login.</p><a class="btn-primary" href="<?= e(site_url('admin/login.php')) ?>">Ir para o login →</a>
<?php elseif (!$installToken): ?><p class="alert alert-error">Antes de continuar, copie <strong>config/local.example.php</strong> para <strong>config/local.php</strong> e defina uma chave de instalação exclusiva. Instruções no README.</p>
<?php else: ?><?php if ($erro): ?><p class="alert alert-error"><?= e($erro) ?></p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<div class="field"><label>Chave de instalação</label><input type="password" name="token" required autocomplete="off"></div>
<div class="field"><label>Nome de usuário</label><input name="usuario" required minlength="3" maxlength="50" autocomplete="username"></div>
<div class="field"><label>Senha forte (mínimo 12 caracteres)</label><input type="password" name="senha" required minlength="12" autocomplete="new-password"></div>
<button type="submit" class="btn-primary login-submit">Criar administrador →</button></form>
<?php endif; ?></section></body></html>
