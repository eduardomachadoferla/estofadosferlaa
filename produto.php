<?php
require_once __DIR__ . '/config/db.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$stmt = $pdo->prepare('SELECT p.*, c.nome AS categoria_titulo FROM produtos p LEFT JOIN categorias c ON c.slug=p.categoria WHERE p.id=? AND p.visivel=1');
$stmt->execute([$id]);
$produto = $stmt->fetch();
if (!$produto) { http_response_code(404); exit('Produto não encontrado.'); }
$stmt=$pdo->prepare('SELECT * FROM produto_imagens WHERE produto_id=? ORDER BY ordem, id');
$stmt->execute([$id]); $imagens=$stmt->fetchAll();
$stmt=$pdo->prepare('SELECT * FROM produto_cores WHERE produto_id=? ORDER BY id');
$stmt->execute([$id]); $cores=$stmt->fetchAll();
$galeria = ['padrao'=>[]];
foreach ($imagens as $img) {
    $chave = $img['cor_id'] ? 'cor_'.$img['cor_id'] : 'padrao';
    $galeria[$chave][] = image_url($img['caminho']);
}
// Em produtos legados, a imagem principal pode não constar da galeria.
$padrao = image_url($produto['imagem']);
if (!$galeria['padrao']) $galeria['padrao'] = [$padrao];
if (!in_array($padrao, $galeria['padrao'], true)) array_unshift($galeria['padrao'], $padrao);
$titulo = $produto['nome'] . ' | Ferla Estofados';
$extraCss = 'assets/catalogo.css';
include __DIR__ . '/includes/header.php';
?>
<main class="product-page">
    <div class="container">
        <a href="<?= e(site_url('index.php')) ?>#produtos" class="back-link">← Voltar aos produtos</a>
        <section class="product-layout">
            <div class="product-gallery">
                <div class="product-image-box">
                    <img id="imagem-principal" src="<?= e($padrao) ?>" alt="<?= e($produto['nome']) ?>">
                </div>
                <div class="product-thumbs" id="miniaturas" aria-label="Fotos do produto"></div>
            </div>
            <div class="product-info">
                <span class="product-category"><?= e($produto['categoria_titulo'] ?: categoria_nome($produto['categoria'])) ?></span>
                <h1><?= e($produto['nome']) ?></h1>
                <?php if ($produto['subtitulo']): ?><p class="product-subtitle"><?= e($produto['subtitulo']) ?></p><?php endif; ?>
                <p class="product-description"><?= nl2br(e($produto['descricao'])) ?></p>
                <?php if ($cores): ?>
                <div class="product-colors">
                    <h3>Cores disponíveis <small id="cor-atual">— escolha uma cor</small></h3>
                    <div class="color-options">
                        <button type="button" class="color-option selected" data-cor="padrao" aria-label="Fotos padrão" title="Fotos padrão"><span class="swatch-default">↺</span></button>
                        <?php foreach ($cores as $c): ?>
                        <button type="button" class="color-option" data-cor="cor_<?= (int)$c['id'] ?>" aria-label="<?= e($c['nome']) ?>" title="<?= e($c['nome']) ?>">
                            <span class="swatch" style="background-color:<?= e($c['hex']) ?>"></span>
                        </button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="specs">
                    <h3>ESPECIFICAÇÕES</h3>
                    <ul>
                        <li>Categoria: <?= e($produto['categoria_titulo'] ?: categoria_nome($produto['categoria'])) ?></li>
                        <?php foreach (['tecido'=>'Tecido','material'=>'Material','estilo'=>'Estilo','dimensoes'=>'Dimensões'] as $key=>$label): ?>
                            <?php if ($produto[$key]): ?><li><?= e($label) ?>: <?= e($produto[$key]) ?></li><?php endif; ?>
                        <?php endforeach; ?>
                        <li>Referência: PROD-<?= str_pad((string)$produto['id'], 4, '0', STR_PAD_LEFT) ?></li>
                    </ul>
                    <?php if ($produto['especificacoes']): ?><p class="specs-extra"><?= nl2br(e($produto['especificacoes'])) ?></p><?php endif; ?>
                </div>
                <div class="buy-box">
                    <div class="price-line"></div>
                    <div class="product-price"><?= e(money_display($produto['preco'])) ?></div>
                    <a class="whatsapp-btn" target="_blank" rel="noopener noreferrer"
                        href="https://wa.me/554598175947?text=<?= rawurlencode('Olá, tenho interesse no produto ' . $produto['nome'] . ' (PROD-' . $produto['id'] . ')') ?>">COMPRE AQUI</a>
                </div>
            </div>
        </section>
    </div>
</main>
<script>
const galerias = <?= json_encode($galeria, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
const imagemPrincipal = document.getElementById('imagem-principal');
const thumbs = document.getElementById('miniaturas');
function mostrarGaleria(chave) {
    const fotos = galerias[chave]?.length ? galerias[chave] : galerias.padrao;
    imagemPrincipal.src = fotos[0];
    thumbs.replaceChildren();
    fotos.forEach((url, index) => {
        const btn = document.createElement('button');
        btn.type='button'; btn.className='thumb-btn' + (index===0?' is-active':'');
        btn.setAttribute('aria-label','Visualizar imagem '+(index+1));
        const img=document.createElement('img'); img.src=url; img.alt='Miniatura '+(index+1);
        btn.append(img);
        btn.addEventListener('click',()=>{
            imagemPrincipal.src=url;
            thumbs.querySelectorAll('.thumb-btn').forEach(x=>x.classList.remove('is-active'));
            btn.classList.add('is-active');
        });
        thumbs.append(btn);
    });
}
mostrarGaleria('padrao');
document.querySelectorAll('.color-option').forEach(btn=>btn.addEventListener('click',()=>{
    document.querySelectorAll('.color-option').forEach(x=>x.classList.remove('selected'));
    btn.classList.add('selected');
    const label=document.getElementById('cor-atual');
    if (label) label.textContent = btn.dataset.cor==='padrao'?'— fotos padrão':'— '+btn.getAttribute('aria-label');
    mostrarGaleria(btn.dataset.cor);
}));
</script>
<?php include __DIR__ . '/includes/footer.php'; ?>
