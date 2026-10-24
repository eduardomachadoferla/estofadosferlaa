<?php
$editar = !empty($produto);
$val = fn($field)=> e((string)($produto[$field] ?? ''));
$token = csrf_token();
?>
<form class="product-form" method="post" enctype="multipart/form-data" action="<?= e(site_url($editar ? 'admin/atualizar_produto.php':'admin/salvar_produto.php')) ?>">
<input type="hidden" name="csrf" value="<?= e($token) ?>">
<?php if ($editar): ?><input type="hidden" name="id" value="<?= (int)$produto['id'] ?>"><?php endif; ?>
<div class="form-grid">
  <div class="field"><label for="categoria">Categoria <b>*</b></label><select name="categoria" id="categoria" required><option value="">Selecionar categoria</option>
  <?php foreach ($categorias as $cat): ?><option value="<?= e($cat['slug']) ?>" <?= ($produto['categoria']??'')===$cat['slug']?'selected':'' ?>><?= e($cat['nome']) ?></option><?php endforeach; ?></select></div>
  <div class="field"><label for="nome">Nome do produto <b>*</b></label><input id="nome" name="nome" required maxlength="180" value="<?= $val('nome') ?>" placeholder="Ex.: Sofá retrátil 3 lugares"></div>
  <div class="field full"><label for="subtitulo">Subtítulo</label><input id="subtitulo" name="subtitulo" value="<?= $val('subtitulo') ?>" placeholder="Uma frase breve sobre o modelo"></div>
  <div class="field full"><label for="descricao">Descrição <b>*</b></label><textarea id="descricao" name="descricao" required placeholder="Descreva o produto..."><?= $val('descricao') ?></textarea></div>
  <div class="field"><label for="preco">Preço (R$) <b>*</b></label><input id="preco" name="preco" required value="<?= $val('preco') ?>" placeholder="Ex.: 3.299,90"></div>
  <div class="field"><label for="tecido">Tecido / revestimento</label><input id="tecido" name="tecido" value="<?= $val('tecido') ?>" placeholder="Ex.: Suede"></div>
  <div class="field"><label for="material">Material</label><input id="material" name="material" value="<?= $val('material') ?>" placeholder="Ex.: Madeira e espuma"></div>
  <div class="field"><label for="estilo">Estilo</label><input id="estilo" name="estilo" value="<?= $val('estilo') ?>" placeholder="Ex.: Moderno"></div>
  <div class="field full"><label for="dimensoes">Dimensões</label><input id="dimensoes" name="dimensoes" value="<?= $val('dimensoes') ?>" placeholder="Ex.: 220 × 90 × 85 cm"></div>
  <div class="field full"><label for="especificacoes">Especificações adicionais</label><textarea id="especificacoes" name="especificacoes" placeholder="Estrutura, densidade da espuma, garantia..."><?= $val('especificacoes') ?></textarea></div>
</div>
<div class="form-divider"><h3>Fotos do produto</h3><p>Selecione várias fotos. Formatos JPG, PNG ou WebP, até 8 MB por foto.</p></div>
<?php if ($editar && !empty($imagens)): ?>
<div class="existing-images">
<?php foreach ($imagens as $img): ?>
  <div class="image-manage"><img loading="lazy" src="<?= e(image_url($img['caminho'])) ?>" alt="Imagem cadastrada">
  <label><input type="radio" name="imagem_principal_id" value="<?= (int)$img['id'] ?>" <?= $img['caminho'] === $produto['imagem'] ? 'checked':'' ?>> Capa</label>
  <label class="danger-option"><input type="checkbox" name="excluir_imagens[]" value="<?= (int)$img['id'] ?>"> Excluir</label></div>
<?php endforeach; ?></div>
<?php endif; ?>
<div class="field"><label for="imagens"><?= $editar ? 'Adicionar mais fotos':'Fotos principais *' ?></label><input type="file" id="imagens" name="imagens[]" accept="image/jpeg,image/png,image/webp" multiple <?= $editar ? '' : 'required' ?>></div>
<div class="form-divider"><h3>Cores e variações</h3><p>Adicione a cor e as fotos reais correspondentes. Ao clicar na bolinha, o visitante verá essas fotos.</p></div>
<?php if ($editar && $cores): ?><div class="existing-colors">
<?php foreach ($cores as $c): ?>
<div class="color-row color-existing">
  <div class="field"><label>Nome da cor</label><input name="cores_existentes[<?= (int)$c['id'] ?>][nome]" value="<?= e($c['nome']) ?>" required></div>
  <div class="field color-input"><label>Amostra</label><input type="color" name="cores_existentes[<?= (int)$c['id'] ?>][hex]" value="<?= e($c['hex']) ?>"></div>
  <div class="field"><label>Mais fotos desta cor</label><input type="file" name="cores_existentes_imagens[<?= (int)$c['id'] ?>][]" accept="image/jpeg,image/png,image/webp" multiple></div>
  <label class="danger-option"><input type="checkbox" name="excluir_cores[]" value="<?= (int)$c['id'] ?>"> Remover cor e suas fotos</label>
  <div class="color-photo-count"><?php $q=array_filter($imagens, fn($img)=>(int)$img['cor_id']===(int)$c['id']); ?><?= count($q) ?> foto(s) vinculada(s)</div>
</div>
<?php endforeach; ?></div><?php endif; ?>
<div id="novas-cores"></div><button type="button" class="btn-secondary" id="add-cor">＋ Adicionar cor</button>
<div class="settings-row">
  <label class="toggle-check"><input type="checkbox" name="visivel" value="1" <?= !$editar || !empty($produto['visivel']) ? 'checked':'' ?>><span><b>Produto visível</b><small>Exibir no catálogo público</small></span></label>
  <label class="toggle-check"><input type="checkbox" name="destaque" value="1" <?= !$editar || !empty($produto['destaque']) ? 'checked':'' ?>><span><b>Em destaque</b><small>Marcar como destaque no cadastro</small></span></label>
</div>
<div class="form-submit"><button class="btn-primary" type="submit"><?= $editar ? 'Salvar alterações':'Cadastrar produto' ?> →</button><?php if ($editar): ?><a class="btn-secondary" href="<?= e(site_url('admin/index.php')) ?>">Cancelar</a><?php endif; ?></div>
</form>
