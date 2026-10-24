# Ferla Estofados — catálogo PHP + administração

Versão revisada do projeto original. **O visual da página inicial (`index.php` e `style.css`) foi preservado**; foram adicionados filtros discretos, novos recursos da página de produto e um painel administrativo renovado. O banco e as fotos do ZIP original foram preservados.

## Funcionalidades

- Site público com pesquisa, categorias, filtro por preço, tecido e cor, e ordenação por preço, nome ou recente.
- Fotos reais por cor na página do produto; clique nas amostras para trocar as fotos sem recarregar.
- Administração com cadastro, edição, exclusão, exibição/ocultação, destaque, categorias, preços, descrições, dimensões e especificações.
- Upload de várias fotos, escolha da capa, remoção de fotos e variantes de cor com fotos separadas.
- Login com senhas hash, proteção CSRF, validação do tipo real de imagens e controles de sessão.
- Migração automática do SQLite original preservando as informações existentes.

## Hospedagem

É necessário **PHP 8.1+**, extensões **PDO SQLite**, **fileinfo**, **mbstring não é necessária**, **zlib** e habilitação de upload de arquivos. Em cadastros com várias fotos, ajuste também `upload_max_filesize` e `post_max_size` na hospedagem. Hospedagem Apache com `.htaccess` é recomendada. O GitHub **armazena o repositório**, mas GitHub Pages **não executa PHP**. Use hospedagem PHP (por exemplo, hospedagem compartilhada com acesso ao SQLite).

1. Faça backup dos arquivos atuais e do banco antes de substituir uma instalação que já está em uso.
2. Publique os arquivos do projeto (ou envie-os a um repositório Git e configure o deploy numa hospedagem PHP). Defina a raiz do site para a pasta do projeto. Também funciona em subpasta; links se ajustam automaticamente.
3. Deixe `storage/` com permissão de escrita para o PHP e `assets/uploads/` com permissão de upload. Normalmente 755 para pastas com dono correto; nunca 777 em produção se puder evitar.
4. O arquivo `data/catalogo-inicial.sqlite.gz` é um **catálogo inicial preservado** do ZIP original e está preparado para o Git. Na primeira execução, o sistema cria `storage/database.sqlite` automaticamente se não existir. **Após o primeiro deploy, nunca sobrescreva esse banco sem backup.**
5. Se você já tiver um `storage/database.sqlite` com dados novos, mantenha-o durante as atualizações. Esse arquivo está no `.gitignore` para evitar publicar senhas hash e registros atualizados em Git.
6. Visite a página inicial e `/admin/login.php`. Configure o primeiro usuário conforme o item abaixo.
7. Teste cadastro de produto, upload de imagem, filtro e escolha de cor antes de divulgar o site.

### Criar o administrador — pelo navegador

1. Copie `config/local.example.php` para `config/local.php` **no servidor**.
2. Substitua o texto da `install_token` por uma chave aleatória privada de pelo menos 24 caracteres. Nunca suba `config/local.php` ao GitHub (já está no `.gitignore`).
3. Abra `https://seu-dominio/admin/instalar.php`, informe a chave, escolha usuário e uma senha forte (no mínimo 12 caracteres).
4. Após criar o administrador, **apague `config/local.php`** ou limpe a chave de instalação. O instalador bloqueia novas contas assim que já existe um administrador.
5. Entre em `/admin/login.php`. **A senha antiga em texto puro não é mais usada nem válida.**

### Criar ou redefinir administrador — pelo terminal (alternativa)

Com PHP CLI e PDO SQLite instalado, rode `php tools/criar_admin.php` na raiz do projeto. Ele solicita o usuário e a nova senha. Para um usuário existente, redefine a senha. Não envie senhas por mensagens nem as registre no repositório.

### Endereços principais

- `/` ou `/index.php`: catálogo, busca e filtros.
- `/produto.php?id=5`: página do produto.
- `/sobre.php`: página institucional original.
- `/admin/login.php`: login.
- `/admin/index.php`: painel administrativo.

### Banco, fotos e segurança

- Usa SQLite em `storage/database.sqlite`. É possível configurar `FERLA_DB_PATH` para um **caminho absoluto fora da pasta pública**, opção preferida em produção.
- O catálogo inicial (compactado) contém os produtos antigos como amostra/continuidade. Recomendamos revisar os nomes, preços e fotos antes da publicação.
- Para hospedar com **Nginx**, configure regras para NEGAR acesso HTTP a `/storage`, `/data`, `/config`, `/tools`, arquivos SQLite e PHP dentro de `/assets/uploads`. O `.htaccess` **só vale para Apache**.
- Faça backups periódicos do banco e da pasta `assets/uploads/` (não basta fazer backup só do código Git).
- Evite publicar dados de instalação, credenciais, cópias do banco ou arquivos de debug.
- Preços antigos, antes armazenados como texto (`4,000.00`, por exemplo), são normalizados internamente como números para filtragem; se houver ambiguidades, revise os preços dos produtos importados no painel.
- Se as imagens não aparecerem depois do deploy, confira permissões, conteúdo da pasta `assets/uploads` e caminhos relativos. Algumas imagens cadastradas na versão antiga podem já estar ausentes no ZIP original.

### Verificação manual antes de colocar no ar

Acesse o site na raiz **e** em uma subpasta se esse for seu caso. Verifique pesquisa, filtros combinados, preços, carregamento da página de detalhes, galerias e bolinhas de cores, login, cadastro, edição, exclusão e visualização no celular. A folha `style.css` original não foi modificada, e os elementos novos usam estilos separados.

### Diagnóstico no terminal

Se tiver PHP disponível no terminal, rode `php tools/verificar.php` para conferir os pré-requisitos da hospedagem e o acesso ao banco.
