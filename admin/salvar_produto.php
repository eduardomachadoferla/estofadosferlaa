<?php
require_once __DIR__ . '/../config/admin_lib.php';
exigir_login();
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit('Método não permitido.'); }
try { $id=salvar_produto($pdo,false); flash('sucesso','Produto cadastrado com sucesso!'); }
catch (Throwable $e) { error_log('Cadastrar: '.$e->getMessage()); flash('erro', $e instanceof RuntimeException ? $e->getMessage() : 'Erro ao cadastrar produto. Verifique os campos e a pasta de imagens.'); }
admin_redirect('admin/index.php#cadastro');
