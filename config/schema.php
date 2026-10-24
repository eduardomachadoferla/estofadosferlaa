<?php
declare(strict_types=1);
function ensure_schema(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS produtos (
        id INTEGER PRIMARY KEY AUTOINCREMENT, categoria TEXT NOT NULL,
        nome TEXT NOT NULL, descricao TEXT NOT NULL,
        preco TEXT NOT NULL, imagem TEXT NOT NULL DEFAULT '',
        destaque INTEGER DEFAULT 1, criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS produto_imagens (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        produto_id INTEGER NOT NULL, caminho TEXT NOT NULL
    )");
    $cols = array_column($pdo->query('PRAGMA table_info(produtos)')->fetchAll(), 'name');
    $fields = [
      'subtitulo' => "TEXT NOT NULL DEFAULT ''", 'preco_valor' => 'REAL',
      'tecido' => "TEXT NOT NULL DEFAULT ''", 'material' => "TEXT NOT NULL DEFAULT ''",
      'estilo' => "TEXT NOT NULL DEFAULT ''", 'dimensoes' => "TEXT NOT NULL DEFAULT ''",
      'especificacoes' => "TEXT NOT NULL DEFAULT ''", 'visivel' => 'INTEGER NOT NULL DEFAULT 1',
    ];
    foreach ($fields as $col => $definition) {
        if (!in_array($col, $cols, true)) $pdo->exec('ALTER TABLE produtos ADD COLUMN ' . $col . ' ' . $definition);
    }
    $imgCols = array_column($pdo->query('PRAGMA table_info(produto_imagens)')->fetchAll(), 'name');
    if (!in_array('cor_id', $imgCols, true)) $pdo->exec('ALTER TABLE produto_imagens ADD COLUMN cor_id INTEGER');
    if (!in_array('ordem', $imgCols, true)) $pdo->exec('ALTER TABLE produto_imagens ADD COLUMN ordem INTEGER NOT NULL DEFAULT 0');
    $pdo->exec('CREATE TABLE IF NOT EXISTS produto_cores (
      id INTEGER PRIMARY KEY AUTOINCREMENT, produto_id INTEGER NOT NULL,
      nome TEXT NOT NULL, hex TEXT NOT NULL DEFAULT \'#888888\'
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS categorias (
      slug TEXT PRIMARY KEY, nome TEXT NOT NULL UNIQUE
    )');
    $pdo->exec('CREATE TABLE IF NOT EXISTS admin_usuarios (
      id INTEGER PRIMARY KEY AUTOINCREMENT,
      usuario TEXT NOT NULL UNIQUE, senha_hash TEXT NOT NULL
    )');
    foreach (['sofas'=>'Sofás', 'poltronas'=>'Poltronas', 'cadeiras'=>'Cadeiras', 'cabeceiras'=>'Cabeceiras', 'puffs'=>'Puffs'] as $slug=>$label) {
        $pdo->prepare('INSERT OR IGNORE INTO categorias (slug, nome) VALUES (?, ?)')->execute([$slug, $label]);
    }
    // Mantém as categorias dos registros existentes.
    $cats = $pdo->query('SELECT DISTINCT categoria FROM produtos')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($cats as $cat) {
        if ($cat === '') continue;
        $slug = strtolower((string)$cat);
        $pdo->prepare('INSERT OR IGNORE INTO categorias (slug, nome) VALUES (?, ?)')->execute([$slug, categoria_nome($slug)]);
    }
    $pending = $pdo->query('SELECT id, preco FROM produtos WHERE preco_valor IS NULL')->fetchAll();
    $upd = $pdo->prepare('UPDATE produtos SET preco_valor = ? WHERE id = ?');
    foreach ($pending as $row) $upd->execute([money_value($row['preco']), $row['id']]);
    // Produtos da versão anterior podiam ter apenas a foto de capa (sem galeria).
    $pdo->exec("INSERT INTO produto_imagens (produto_id, caminho)
        SELECT p.id, p.imagem FROM produtos p
        WHERE p.imagem != '' AND NOT EXISTS (
            SELECT 1 FROM produto_imagens i WHERE i.produto_id = p.id
        )");
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_produtos_filtro ON produtos(visivel, categoria, preco_valor)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_imagens_produto ON produto_imagens(produto_id, cor_id)');
    $pdo->exec('CREATE INDEX IF NOT EXISTS idx_cores_produto ON produto_cores(produto_id)');
}
