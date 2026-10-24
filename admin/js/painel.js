(() => {
  const holder = document.getElementById('novas-cores');
  const add = document.getElementById('add-cor');
  let index = 0;
  add?.addEventListener('click', () => {
    const row = document.createElement('div');
    row.className = 'color-row';
    row.innerHTML = `<div class="field"><label>Nome da cor</label><input name="cores_nome[${index}]" placeholder="Ex.: Bege" required></div>
      <div class="field color-input"><label>Amostra visual</label><input type="color" name="cores_hex[${index}]" value="#d3b99d"></div>
      <div class="field"><label>Fotos nessa cor</label><input type="file" name="cores_imagens[${index}][]" accept="image/jpeg,image/png,image/webp" multiple></div>
      <button type="button" class="remove-color" aria-label="Remover esta cor">✕</button>`;
    row.querySelector('.remove-color').addEventListener('click', () => row.remove());
    holder.append(row);
    index++;
  });
})();
