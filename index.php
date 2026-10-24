<?php
require_once __DIR__ . '/config/db.php';
$titulo = 'Ferla Estofados';

$busca = trim((string)($_GET['busca'] ?? ''));
$categoria = trim((string)($_GET['categoria'] ?? ''));
$cor = trim((string)($_GET['cor'] ?? ''));
$tecido = trim((string)($_GET['tecido'] ?? ''));
$material = trim((string)($_GET['material'] ?? ''));
$estilo = trim((string)($_GET['estilo'] ?? ''));
$valorMin = isset($_GET['preco_min']) && $_GET['preco_min'] !== '' ? money_value((string)$_GET['preco_min']) : null;
$valorMax = isset($_GET['preco_max']) && $_GET['preco_max'] !== '' ? money_value((string)$_GET['preco_max']) : null;
$ordem = (string)($_GET['ordem'] ?? 'recentes');
$ordenacoes = ['recentes'=>'p.id DESC', 'menor_preco'=>'p.preco_valor ASC, p.id DESC',
    'maior_preco'=>'p.preco_valor DESC, p.id DESC', 'nome'=>'p.nome COLLATE NOCASE ASC'];
if (!isset($ordenacoes[$ordem])) $ordem = 'recentes';
$sql = "SELECT p.*, cat.nome AS categoria_titulo FROM produtos p LEFT JOIN categorias cat ON cat.slug = p.categoria WHERE p.visivel = 1";
$params = [];
if ($busca !== '') {
    $sql .= " AND (p.nome LIKE :busca OR p.descricao LIKE :busca OR p.categoria LIKE :busca OR p.subtitulo LIKE :busca OR p.tecido LIKE :busca OR p.material LIKE :busca OR p.estilo LIKE :busca OR p.dimensoes LIKE :busca)";
    $params[':busca'] = '%' . $busca . '%';
}
if ($categoria !== '') { $sql .= ' AND p.categoria = :categoria'; $params[':categoria'] = $categoria; }
if ($tecido !== '') { $sql .= ' AND p.tecido = :tecido'; $params[':tecido'] = $tecido; }
if ($material !== '') { $sql .= ' AND p.material = :material'; $params[':material'] = $material; }
if ($estilo !== '') { $sql .= ' AND p.estilo = :estilo'; $params[':estilo'] = $estilo; }
if ($cor !== '') {
    $sql .= ' AND EXISTS (SELECT 1 FROM produto_cores c WHERE c.produto_id = p.id AND c.nome = :cor)';
    $params[':cor'] = $cor;
}
if ($valorMin !== null) { $sql .= ' AND p.preco_valor >= :min'; $params[':min'] = $valorMin; }
if ($valorMax !== null) { $sql .= ' AND p.preco_valor <= :max'; $params[':max'] = $valorMax; }
$sql .= ' ORDER BY ' . $ordenacoes[$ordem];
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$produtos = $stmt->fetchAll();
$categorias = $pdo->query('SELECT * FROM categorias ORDER BY nome')->fetchAll();
$coresFiltro = $pdo->query('SELECT DISTINCT c.nome FROM produto_cores c JOIN produtos p ON p.id=c.produto_id WHERE p.visivel=1 ORDER BY c.nome')->fetchAll(PDO::FETCH_COLUMN);
$tecidosFiltro = $pdo->query("SELECT DISTINCT tecido FROM produtos WHERE visivel=1 AND TRIM(tecido) != '' ORDER BY tecido")->fetchAll(PDO::FETCH_COLUMN);
$materiaisFiltro = $pdo->query("SELECT DISTINCT material FROM produtos WHERE visivel=1 AND TRIM(material) != '' ORDER BY material")->fetchAll(PDO::FETCH_COLUMN);
$estilosFiltro = $pdo->query("SELECT DISTINCT estilo FROM produtos WHERE visivel=1 AND TRIM(estilo) != '' ORDER BY estilo")->fetchAll(PDO::FETCH_COLUMN);

$extraCss = 'assets/catalogo.css';
include __DIR__ . '/includes/header.php';
?>


<main>
    <section class="hero" id="inicio">
        <div class="hero-content container">
            <h1>Estofados Ferla</h1>
            <p>Conforto e sofisticação que transformam seu lar</p>
            <a href="#produtos" class="btn">VER PRODUTOS</a>
        </div>
    </section>

    <section class="section" id="produtos">
        <div class="container">
            <div class="section-header">
                <h2>Todos os Produtos</h2>
                <p>Encontre o estofado ideal para seu ambiente</p>
            </div>

            <form action="<?= e(site_url('index.php')) ?>#produtos" method="get" class="catalog-filters" aria-label="Filtrar produtos">
                <div class="filter-fields">
                    <label>Categoria
                        <select name="categoria"><option value="">Todas</option>
                            <?php foreach ($categorias as $cat): ?><option value="<?= e($cat['slug']) ?>" <?= $categoria === $cat['slug'] ? 'selected' : '' ?>><?= e($cat['nome']) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                    <label>Preço mínimo (R$)<input type="number" name="preco_min" min="0" step="0.01" placeholder="0,00" value="<?= e((string)($_GET['preco_min'] ?? '')) ?>"></label>
                    <label>Preço máximo (R$)<input type="number" name="preco_max" min="0" step="0.01" placeholder="Sem limite" value="<?= e((string)($_GET['preco_max'] ?? '')) ?>"></label>
                    <label>Cor<select name="cor"><option value="">Todas</option>
                        <?php foreach ($coresFiltro as $option): ?><option <?= $cor === $option ? 'selected' : '' ?> value="<?= e($option) ?>"><?= e($option) ?></option><?php endforeach; ?>
                    </select></label>
                    <label>Tecido<select name="tecido"><option value="">Todos</option>
                        <?php foreach ($tecidosFiltro as $option): ?><option <?= $tecido === $option ? 'selected' : '' ?> value="<?= e($option) ?>"><?= e($option) ?></option><?php endforeach; ?>
                    </select></label>
                    <label>Material<select name="material"><option value="">Todos</option>
                        <?php foreach ($materiaisFiltro as $option): ?><option <?= $material === $option ? 'selected' : '' ?> value="<?= e($option) ?>"><?= e($option) ?></option><?php endforeach; ?>
                    </select></label>
                    <label>Estilo<select name="estilo"><option value="">Todos</option>
                        <?php foreach ($estilosFiltro as $option): ?><option <?= $estilo === $option ? 'selected' : '' ?> value="<?= e($option) ?>"><?= e($option) ?></option><?php endforeach; ?>
                    </select></label>
                    <label>Ordenar<select name="ordem">
                        <option value="recentes" <?= $ordem==='recentes'?'selected':'' ?>>Mais recentes</option>
                        <option value="menor_preco" <?= $ordem==='menor_preco'?'selected':'' ?>>Menor preço</option>
                        <option value="maior_preco" <?= $ordem==='maior_preco'?'selected':'' ?>>Maior preço</option>
                        <option value="nome" <?= $ordem==='nome'?'selected':'' ?>>Nome (A-Z)</option>
                    </select></label>
                </div>
                <input type="hidden" name="busca" value="<?= e($busca) ?>">
                <div class="filter-actions"><button type="submit" class="btn">Aplicar filtros</button><a href="<?= e(site_url('index.php')) ?>#produtos" class="filter-reset">Limpar filtros</a><span><?= count($produtos) ?> produto(s)</span></div>
            </form>

            <div class="products-grid">
                <?php if (!empty($produtos)): ?>
                <?php foreach ($produtos as $produto): ?>
                <article class="card">
                    <div class="card-image">
                        <img src="<?= e(image_url($produto['imagem'])) ?>"
                            alt="<?= htmlspecialchars($produto['nome']) ?>">
                    </div>

                    <div class="card-body">
                        <span class="tag"><?= e($produto['categoria_titulo'] ?: categoria_nome($produto['categoria'])) ?></span>
                        <h3><?= htmlspecialchars($produto['nome']) ?></h3>
                        <p>
                            <?= htmlspecialchars($produto['descricao']) ?>
                        </p>

                        <div class="card-footer">
                            <strong class="price"><?= e(money_display($produto['preco'])) ?></strong>
                            <a class="outline-btn" href="<?= e(site_url('produto.php')) ?>?id=<?= (int)$produto['id'] ?>">Saber
                                mais</a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
                <?php else: ?>
                <p class="no-results">Nenhum produto encontrado para sua pesquisa.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- <section class="categories" id="sobre">
    <div class="container">
      <div class="section-header">
        <h2>Sobre Nós</h2>
        <p>Elegância, conforto e qualidade em cada detalhe.</p>
      </div>
    </div>
  </section> -->
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>