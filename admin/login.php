<?php
require_once __DIR__ . '/../config/admin_lib.php';
if (admin_logado()) admin_redirect();
$erro='';
$usuarios=(int)$pdo->query('SELECT COUNT(*) FROM admin_usuarios')->fetchColumn();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigir_csrf();
    $usuario=trim((string)($_POST['usuario'] ?? ''));
    $senha=(string)($_POST['senha'] ?? '');
    if (($_SESSION['tentativas_login'] ?? 0)>=8 && time()-($_SESSION['ultimo_login_falho']??0)<900) {
        $erro='Muitas tentativas. Aguarde 15 minutos.';
    } else {
        $stmt=$pdo->prepare('SELECT * FROM admin_usuarios WHERE usuario=? LIMIT 1');$stmt->execute([$usuario]);$admin=$stmt->fetch();
        if ($admin && password_verify($senha,$admin['senha_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id']=(int)$admin['id'];
            $_SESSION['tentativas_login']=0;
            admin_redirect();
        }
        $_SESSION['tentativas_login']=($_SESSION['tentativas_login']??0)+1;
        $_SESSION['ultimo_login_falho']=time();
        $erro='Usuário ou senha inválidos.';
    }
}
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Entrar | Administração Ferla</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(site_url('admin/css/painel.css')) ?>"></head>
<body class="login-page"><div class="login-card"><div class="login-mark">FERLA<span>ESTOFADOS</span></div><div class="login-heading"><h1>Acesso administrativo</h1><p>Entre para gerenciar seu catálogo de produtos.</p></div>
<?php if ($erro): ?><p class="alert alert-error" role="alert"><?= e($erro) ?></p><?php endif; ?>
<?php if (!$usuarios): ?><p class="alert alert-error">Ainda não existe um administrador. Configure o primeiro acesso conforme as instruções no README.</p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><div class="field"><label for="usuario">Usuário</label><input id="usuario" name="usuario" autocomplete="username" required autofocus></div><div class="field"><label for="senha">Senha</label><input type="password" id="senha" name="senha" autocomplete="current-password" required></div><button type="submit" class="btn-primary login-submit">Entrar →</button></form>
<p class="login-back"><a href="<?= e(site_url('index.php')) ?>">← Voltar ao site</a></p></div></body></html>
