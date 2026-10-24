<?php
require_once __DIR__ . '/../config/admin_lib.php';
exigir_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método não permitido.'); }
$id=(int)($_POST['id'] ?? 0);
try { salvar_produto($pdo,true); flash('sucesso','Produto atualizado com sucesso!'); }
catch (Throwable $e) { error_log('Editar: '.$e->getMessage()); flash('erro', $e instanceof RuntimeException ? $e->getMessage() : 'Erro ao atualizar produto.'); }
admin_redirect('admin/editar_produto.php?id='.$id);
