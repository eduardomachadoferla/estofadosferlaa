<?php
declare(strict_types=1);
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
function categorias(PDO $pdo): array {
    return $pdo->query('SELECT slug, nome FROM categorias ORDER BY nome')->fetchAll();
}
function validar_produto(PDO $pdo, array $post): array {
    $result = [];
    foreach (['categoria','nome','descricao','preco','subtitulo','tecido','material','estilo','dimensoes','especificacoes'] as $f) {
        $result[$f] = trim((string)($post[$f] ?? ''));
    }
    if ($result['categoria'] === '' || $result['nome'] === '' || $result['descricao'] === '' || $result['preco'] === '') {
        throw new RuntimeException('Preencha categoria, nome, descrição e preço.');
    }
    $cat = $pdo->prepare('SELECT 1 FROM categorias WHERE slug = ?'); $cat->execute([$result['categoria']]);
    if (!$cat->fetchColumn()) throw new RuntimeException('Selecione uma categoria válida.');
    if (strlen($result['nome']) > 180) throw new RuntimeException('Nome muito longo.');
    $price = money_value($result['preco']);
    if ($price === null || $price < 0 || $price > 100000000) throw new RuntimeException('Informe um preço válido.');
    $result['preco_valor'] = $price;
    $result['destaque'] = !empty($post['destaque']) ? 1 : 0;
    $result['visivel'] = !empty($post['visivel']) ? 1 : 0;
    return $result;
}
function upload_items(?array $files, ?int $group = null): array {
    if (!$files || !isset($files['name'])) return [];
    $result = [];
    $names = $group === null ? $files['name'] : ($files['name'][$group] ?? []);
    if (!is_array($names)) return [];
    foreach ($names as $i=>$name) {
        $result[] = [
            'name' => $name,
            'tmp_name' => $group === null ? ($files['tmp_name'][$i] ?? '') : ($files['tmp_name'][$group][$i] ?? ''),
            'error' => $group === null ? ($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) : ($files['error'][$group][$i] ?? UPLOAD_ERR_NO_FILE),
            'size' => $group === null ? ($files['size'][$i] ?? 0) : ($files['size'][$group][$i] ?? 0),
        ];
    }
    return $result;
}
function upload_imagens(array $items, array &$criados): array {
    $saved = [];
    $destdir = dirname(__DIR__) . '/assets/uploads';
    if (!is_dir($destdir) && !mkdir($destdir, 0755, true)) throw new RuntimeException('Não foi possível criar a pasta de imagens.');
    $mimeTypes = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    foreach ($items as $file) {
        $err = (int)$file['error'];
        if ($err === UPLOAD_ERR_NO_FILE) continue;
        if ($err !== UPLOAD_ERR_OK) throw new RuntimeException('Falha no envio de uma imagem (código ' . $err . ').');
        if ((int)$file['size'] > 8 * 1024 * 1024 || (int)$file['size'] <= 0) throw new RuntimeException('Cada imagem deve ter até 8 MB.');
        $mime = $finfo->file($file['tmp_name']);
        if (!isset($mimeTypes[$mime]) || @getimagesize($file['tmp_name']) === false) {
            throw new RuntimeException('Arquivo inválido: envie imagens JPG, PNG ou WebP.');
        }
        $filename = 'produto_' . bin2hex(random_bytes(16)) . '.' . $mimeTypes[$mime];
        $dest = $destdir . '/' . $filename;
        if (!is_uploaded_file($file['tmp_name']) || !move_uploaded_file($file['tmp_name'], $dest)) {
            throw new RuntimeException('Não foi possível salvar a imagem no servidor.');
        }
        $criados[] = $dest;
        $saved[] = 'assets/uploads/' . $filename;
    }
    return $saved;
}
function eliminar_imagem(?string $caminho): void {
    $filename = basename((string)$caminho);
    if (!preg_match('/^produto_[a-zA-Z0-9_.-]+\.(jpg|jpeg|png|webp)$/', $filename)) return;
    $path = dirname(__DIR__) . '/assets/uploads/' . $filename;
    if (is_file($path)) @unlink($path);
}
function galerias_atualizar(PDO $pdo, int $produtoId, array $post, array $files, array &$criados, array &$remover): void {
    // Remover cores selecionadas e suas respectivas imagens.
    foreach (($post['excluir_cores'] ?? []) as $corId) {
        $stmt=$pdo->prepare('SELECT caminho FROM produto_imagens WHERE produto_id=? AND cor_id=?');
        $stmt->execute([$produtoId, (int)$corId]);
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $c) $remover[]=$c;
        $pdo->prepare('DELETE FROM produto_imagens WHERE produto_id=? AND cor_id=?')->execute([$produtoId, (int)$corId]);
        $pdo->prepare('DELETE FROM produto_cores WHERE produto_id=? AND id=?')->execute([$produtoId, (int)$corId]);
    }
    foreach (($post['excluir_imagens'] ?? []) as $imagemId) {
        $st=$pdo->prepare('SELECT caminho FROM produto_imagens WHERE produto_id=? AND id=?');
        $st->execute([$produtoId, (int)$imagemId]);
        $img=$st->fetchColumn();
        if ($img !== false) {
            $pdo->prepare('DELETE FROM produto_imagens WHERE produto_id=? AND id=?')->execute([$produtoId, (int)$imagemId]);
            $remover[]=$img;
        }
    }
    foreach (($post['cores_existentes'] ?? []) as $id=>$row) {
        $id=(int)$id;
        if (in_array((string)$id, array_map('strval', $post['excluir_cores'] ?? []), true)) continue;
        $nome=trim((string)($row['nome'] ?? ''));
        $hex=(string)($row['hex'] ?? '');
        if ($nome === '' || !preg_match('/^#[a-f0-9]{6}$/i', $hex)) throw new RuntimeException('Confira as cores cadastradas.');
        $pdo->prepare('UPDATE produto_cores SET nome=?, hex=? WHERE id=? AND produto_id=?')->execute([$nome,$hex,$id,$produtoId]);
        foreach (upload_imagens(upload_items($files['cores_existentes_imagens'] ?? null, $id), $criados) as $caminho) {
            $pdo->prepare('INSERT INTO produto_imagens(produto_id,caminho,cor_id) VALUES(?,?,?)')->execute([$produtoId,$caminho,$id]);
        }
    }
    $nomes=$post['cores_nome'] ?? [];
    foreach ($nomes as $i=>$nome) {
        $nome=trim((string)$nome); $hex=(string)($post['cores_hex'][$i] ?? '');
        $uploaded=upload_items($files['cores_imagens'] ?? null, (int)$i);
        if ($nome === '' && !$uploaded) continue;
        if ($nome === '' || !preg_match('/^#[a-f0-9]{6}$/i', $hex)) throw new RuntimeException('Informe nome e cor válida nas variações.');
        $pdo->prepare('INSERT INTO produto_cores(produto_id,nome,hex) VALUES(?,?,?)')->execute([$produtoId,$nome,$hex]);
        $corId=(int)$pdo->lastInsertId();
        foreach (upload_imagens($uploaded, $criados) as $caminho) {
            $pdo->prepare('INSERT INTO produto_imagens(produto_id,caminho,cor_id) VALUES(?,?,?)')->execute([$produtoId,$caminho,$corId]);
        }
    }
    foreach (upload_imagens(upload_items($files['imagens'] ?? null), $criados) as $caminho) {
        $pdo->prepare('INSERT INTO produto_imagens(produto_id,caminho,cor_id) VALUES(?,?,NULL)')->execute([$produtoId,$caminho]);
    }
    // A imagem principal escolhida prevalece; senão preserva a atual; ou usa a primeira disponível.
    $st=$pdo->prepare('SELECT imagem FROM produtos WHERE id=?'); $st->execute([$produtoId]);
    $atual=$st->fetchColumn();
    $st=$pdo->prepare('SELECT id,caminho FROM produto_imagens WHERE produto_id=? ORDER BY CASE WHEN cor_id IS NULL THEN 0 ELSE 1 END, id');
    $st->execute([$produtoId]); $imgs=$st->fetchAll();
    if (!$imgs) throw new RuntimeException('O produto precisa ter ao menos uma imagem.');
    $principal=$imgs[0]['caminho'];
    $escolhida=(int)($post['imagem_principal_id'] ?? 0);
    foreach ($imgs as $img) {
        if ($escolhida > 0 && (int)$img['id'] === $escolhida) { $principal=$img['caminho']; break; }
        if (!$escolhida && $img['caminho'] === $atual) $principal=$atual;
    }
    $pdo->prepare('UPDATE produtos SET imagem=? WHERE id=?')->execute([$principal,$produtoId]);
}
function salvar_produto(PDO $pdo, bool $edit): int {
    exigir_csrf();
    $data=validar_produto($pdo, $_POST);
    $id=$edit ? (int)($_POST['id'] ?? 0) : 0;
    if ($edit) {
        $st=$pdo->prepare('SELECT id FROM produtos WHERE id=?'); $st->execute([$id]);
        if (!$st->fetchColumn()) throw new RuntimeException('Produto não encontrado.');
    }
    $criados=[]; $remover=[];
    try {
        $pdo->beginTransaction();
        $colunas = ['categoria','nome','descricao','preco','subtitulo','preco_valor','tecido','material','estilo','dimensoes','especificacoes','destaque','visivel'];
        if ($edit) {
            $sql='UPDATE produtos SET ' . implode(', ', array_map(fn($x)=>"$x = ?", $colunas)) . ' WHERE id = ?';
            $pdo->prepare($sql)->execute([...array_map(fn($x)=>$data[$x], $colunas), $id]);
        } else {
            $sql='INSERT INTO produtos (' . implode(',', $colunas) . ',imagem) VALUES(' . implode(',', array_fill(0,count($colunas),'?')) . ',?)';
            $pdo->prepare($sql)->execute([...array_map(fn($x)=>$data[$x], $colunas),'']);
            $id=(int)$pdo->lastInsertId();
        }
        galerias_atualizar($pdo,$id,$_POST,$_FILES,$criados,$remover);
        $pdo->commit();
        foreach ($remover as $f) eliminar_imagem($f);
        return $id;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        foreach ($criados as $path) @unlink($path);
        throw $e;
    }
}
