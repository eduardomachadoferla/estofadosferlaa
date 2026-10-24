<?php
require_once __DIR__ . '/../config/admin_lib.php';
exigir_login();
$flash = ler_flash();
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= e($tituloAdmin ?? 'Administração') ?> | Ferla Estofados</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(site_url('admin/css/painel.css')) ?>"></head>
<body><div class="admin-layout">
<aside class="sidebar">
  <div><a class="admin-logo" href="<?= e(site_url('admin/index.php')) ?>">FERLA<span>ESTOFADOS</span></a>
  <nav class="side-nav" aria-label="Administração"><a href="<?= e(site_url('admin/index.php')) ?>">▦ &nbsp; Visão geral</a><a href="<?= e(site_url('admin/index.php')) ?>#cadastro">＋ &nbsp; Cadastrar produto</a><a href="<?= e(site_url('admin/index.php')) ?>#lista">▤ &nbsp; Produtos</a><a href="<?= e(site_url('admin/index.php')) ?>#categorias">◇ &nbsp; Categorias</a><a href="<?= e(site_url('index.php')) ?>" target="_blank" rel="noopener">↗ &nbsp; Visualizar site</a></nav></div>
  <form method="post" action="<?= e(site_url('admin/logout.php')) ?>"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><button type="submit" class="side-out logout-button">Sair do painel →</button></form>
</aside>
<main class="admin-main">
<header class="admin-header"><div><span class="eyebrow">PAINEL ADMINISTRATIVO</span><h1><?= e($tituloAdmin ?? 'Gerenciar catálogo') ?></h1><p>Ferla Estofados · Gerenciamento de catálogo</p></div><a href="<?= e(site_url('index.php')) ?>" class="header-site" target="_blank" rel="noopener">Ver loja ↗</a></header>
<?php if ($flash): ?><div class="alert <?= $flash['tipo'] === 'sucesso' ? 'alert-success' : 'alert-error' ?>" role="alert"><?= e($flash['mensagem']) ?></div><?php endif; ?>
