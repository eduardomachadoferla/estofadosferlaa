<?php
require_once __DIR__ . '/../config/admin_lib.php';
exigir_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método não permitido.'); }
exigir_csrf();
$id=(int)($_POST['id'] ?? 0);
if ($id<=0) { http_response_code(400); exit('Produto inválido.'); }
try {
    $pdo->beginTransaction();
    $stmt=$pdo->prepare('SELECT imagem FROM produtos WHERE id=?'); $stmt->execute([$id]); $imgPrincipal=$stmt->fetchColumn();
    $stmt=$pdo->prepare('SELECT caminho FROM produto_imagens WHERE produto_id=?'); $stmt->execute([$id]);$imagens=$stmt->fetchAll(PDO::FETCH_COLUMN);
    $pdo->prepare('DELETE FROM produto_imagens WHERE produto_id=?')->execute([$id]);
    $pdo->prepare('DELETE FROM produto_cores WHERE produto_id=?')->execute([$id]);
    $pdo->prepare('DELETE FROM produtos WHERE id=?')->execute([$id]);
    $pdo->commit();
    foreach (array_unique([...$imagens,...($imgPrincipal ? [$imgPrincipal]:[])]) as $img) eliminar_imagem($img);
    flash('sucesso','Produto excluído do catálogo.');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('Excluir: '.$e->getMessage());flash('erro','Não foi possível excluir o produto.');
}
admin_redirect('admin/index.php#lista');
