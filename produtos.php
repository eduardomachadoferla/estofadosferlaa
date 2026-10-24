<?php
// Mantém links antigos de produto funcionando, sem duplicar a página de detalhes.
require_once __DIR__ . '/config/app.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
header('Location: ' . site_url('produto.php') . '?id=' . $id, true, 301);
exit;
