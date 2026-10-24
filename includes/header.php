<?php require_once __DIR__ . '/../config/app.php'; if (!isset($titulo)) $titulo = 'Ferla Estofados'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($titulo) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(site_url('style.css')) ?>">
  <?php if (!empty($extraCss)): ?><link rel="stylesheet" href="<?= e(site_url($extraCss)) ?>"><?php endif; ?>
</head>
<body>
<header class="topbar">
  <div class="container navbar">
    
    <a href="<?= e(site_url('index.php')) ?>" class="logo">
      <strong>FERLA</strong>
      <span>ESTOFADOS</span>
    </a>

    <ul class="menu">
      <li><a href="<?= e(site_url('index.php')) ?>">Início</a></li>
      <li><a href="<?= e(site_url('index.php')) ?>#produtos">Produtos</a></li>
      <li><a href="<?= e(site_url('sobre.php')) ?>">Sobre Nós</a></li>
      <li><a href="https://wa.me/554598175947" target="_blank">WhatsApp</a></li>
    </ul>

    <form method="GET" action="<?= e(site_url('index.php')) ?>#produtos" class="search-form">
      <input
        type="text"
        name="busca"
        placeholder="Buscar produtos..."
        value="<?= e(is_string($_GET['busca'] ?? null) ? $_GET['busca'] : '') ?>"
      >

      <button type="submit">Buscar</button>

      <?php if (!empty($_GET['busca'] ?? '')): ?>
        <a href="<?= e(site_url('index.php')) ?>#produtos" class="outline-btn">Limpar</a>
      <?php endif; ?>
    </form>

  </div>
</header>