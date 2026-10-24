<?php
require_once __DIR__ . '/../config/admin_lib.php';
exigir_login();
$produtos=$pdo->query('SELECT p.*, c.nome AS categoria_titulo FROM produtos p LEFT JOIN categorias c ON c.slug = p.categoria ORDER BY p.id DESC')->fetchAll();
$categorias=categorias($pdo);
$tituloAdmin='Gerenciar produtos';
include __DIR__ . '/_cabecalho.php';
?>
<section class="stats-grid"><article class="metric"><span>PRODUTOS CADASTRADOS</span><strong><?= count($produtos) ?></strong><small>No catálogo</small></article>
<article class="metric"><span>PRODUTOS PUBLICADOS</span><strong><?= count(array_filter($produtos,fn($p)=>$p['visivel'])) ?></strong><small>Visíveis no site</small></article>
<article class="metric"><span>CATEGORIAS</span><strong><?= count($categorias) ?></strong><small>Disponíveis para cadastro</small></article></section>
<section class="panel" id="cadastro"><div class="panel-heading"><div><span class="eyebrow">NOVO ITEM</span><h2>Cadastrar produto</h2><p>Preencha as informações para adicionar um produto ao catálogo.</p></div></div>
<?php $produto=null;$imagens=[];$cores=[];include __DIR__ . '/_form_produto.php'; ?>
</section>
<section class="panel" id="lista"><div class="panel-heading"><div><span class="eyebrow">CATÁLOGO</span><h2>Produtos cadastrados</h2><p>Edite informações, imagens ou disponibilidade a qualquer momento.</p></div><span class="count-badge"><?= count($produtos) ?> itens</span></div>
<?php if (!$produtos): ?><div class="empty-state">Ainda não há produtos cadastrados. Use o formulário acima para começar.</div><?php else: ?>
<div class="admin-product-grid">
<?php foreach ($produtos as $item): ?>
<article class="admin-product-card"><img loading="lazy" src="<?= e(image_url($item['imagem'])) ?>" alt="<?= e($item['nome']) ?>"><div class="admin-product-info">
<div class="product-meta"><span class="badge"><?= e($item['categoria_titulo'] ?: categoria_nome($item['categoria'])) ?></span><span class="badge <?= $item['visivel'] ? 'badge-live':'badge-hidden' ?>"><?= $item['visivel'] ? 'Publicado':'Oculto' ?></span></div>
<h3><?= e($item['nome']) ?></h3><strong><?= e(money_display($item['preco'])) ?></strong>
<div class="card-actions"><a class="btn-edit" href="<?= e(site_url('admin/editar_produto.php')) ?>?id=<?= (int)$item['id'] ?>">Editar produto</a>
<form method="post" action="<?= e(site_url('admin/excluir_produto.php')) ?>" onsubmit="return confirm('Excluir este produto e todas as suas fotos? Essa ação não pode ser desfeita.');"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$item['id'] ?>"><button class="btn-delete" type="submit">Excluir</button></form></div>
</div></article>
<?php endforeach; ?>
</div><?php endif; ?></section>
<section class="panel" id="categorias"><div class="panel-heading"><div><span class="eyebrow">ORGANIZAÇÃO</span><h2>Categorias</h2><p>Adicione outras categorias além das cinco categorias iniciais.</p></div></div>
<div class="category-chips"><?php foreach ($categorias as $cat): ?><span><?= e($cat['nome']) ?></span><?php endforeach; ?></div>
<form class="category-add" method="post" action="<?= e(site_url('admin/salvar_categoria.php')) ?>"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><div class="field"><label for="nome-categoria">Nova categoria</label><input id="nome-categoria" name="nome" required maxlength="60" placeholder="Ex.: Recamiers"></div><button class="btn-primary" type="submit">Adicionar categoria</button></form>
</section>
<?php include __DIR__ . '/_rodape.php'; ?>
