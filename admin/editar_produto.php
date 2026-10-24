<?php
require_once __DIR__ . '/../config/admin_lib.php';
exigir_login();
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT) ?: 0;
$stmt=$pdo->prepare('SELECT * FROM produtos WHERE id=?'); $stmt->execute([$id]); $produto=$stmt->fetch();
if (!$produto) { http_response_code(404); exit('Produto não encontrado.'); }
$stmt=$pdo->prepare('SELECT * FROM produto_imagens WHERE produto_id=? ORDER BY ordem, id');$stmt->execute([$id]);$imagens=$stmt->fetchAll();
$stmt=$pdo->prepare('SELECT * FROM produto_cores WHERE produto_id=? ORDER BY id');$stmt->execute([$id]);$cores=$stmt->fetchAll();
$categorias=categorias($pdo); $tituloAdmin='Editar produto';
include __DIR__ . '/_cabecalho.php';
?>
<a href="<?= e(site_url('admin/index.php')) ?>#lista" class="back-admin">← Voltar aos produtos</a>
<section class="panel"><div class="panel-heading"><div><span class="eyebrow">EDIÇÃO</span><h2><?= e($produto['nome']) ?></h2><p>As alterações ficam disponíveis na loja assim que forem salvas.</p></div></div>
<?php include __DIR__ . '/_form_produto.php'; ?>
</section>
<?php include __DIR__ . '/_rodape.php'; ?>
