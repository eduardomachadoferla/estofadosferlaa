<?php
require_once __DIR__ . '/../config/admin_lib.php';
exigir_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método não permitido.'); }
exigir_csrf();
$nome=trim((string)($_POST['nome'] ?? ''));
if (strlen($nome)<2 || strlen($nome)>60) {
    flash('erro','Informe um nome de categoria entre 2 e 60 caracteres.');
} else {
    $slug=strtolower((string)iconv('UTF-8','ASCII//TRANSLIT//IGNORE',$nome));
    $slug=trim(preg_replace('/[^a-z0-9]+/','-',$slug),'-');
    if (!$slug) { flash('erro','Nome da categoria inválido.'); }
    else {
        try { $pdo->prepare('INSERT INTO categorias(slug,nome) VALUES(?,?)')->execute([$slug,$nome]); flash('sucesso','Categoria adicionada!'); }
        catch (PDOException $e) { flash('erro','Essa categoria já existe.'); }
    }
}
admin_redirect('admin/index.php#categorias');
