# 🛋️ Ferla Estofados

Site desenvolvido para a **Ferla Estofados**, com o objetivo de apresentar os produtos da empresa de forma organizada, facilitar a busca por móveis e permitir o gerenciamento do catálogo por meio de um painel administrativo.

🔗 **Acesse o site:** https://estofadosferla.wuaze.com/

## 💻 Sobre o projeto

O site foi desenvolvido utilizando **PHP, HTML, CSS e JavaScript**, com banco de dados SQLite.

A proposta é oferecer um catálogo online em que os clientes possam conhecer os produtos, visualizar fotos, conferir informações e explorar as opções disponíveis.

O projeto também conta com uma área administrativa, permitindo atualizar o catálogo sem precisar modificar o código do site.

## 🛠️ Funcionalidades

**Catálogo de produtos**

* Pesquisa de produtos pelo nome.
* Filtros por categoria, preço, tecido e cor.
* Organização por preço, nome ou produtos recentes.
* Página individual com informações e especificações.
* Galeria de imagens com diferentes opções de cores.
* Layout adaptado para computadores e celulares.

**Painel administrativo**

* Login para acesso à administração.
* Cadastro de novos produtos.
* Edição e exclusão de produtos existentes.
* Upload de várias imagens.
* Escolha da imagem principal.
* Cadastro de cores e suas respectivas fotos.
* Gerenciamento de preços, descrições e categorias.
* Opção de destacar ou ocultar produtos.

## 🚀 Tecnologias utilizadas

* **HTML5:** estrutura das páginas.
* **CSS3:** estilização e responsividade.
* **JavaScript:** interatividade, filtros e galerias.
* **PHP:** funcionamento do sistema e painel administrativo.
* **SQLite:** armazenamento dos produtos e informações.
* **GitHub:** versionamento e armazenamento do código.

## 📂 Estrutura do projeto

Os principais arquivos e diretórios são:

| Arquivo / Pasta   | Função                                  |
| ----------------- | --------------------------------------- |
| `index.php`       | Página inicial e catálogo               |
| `produto.php`     | Detalhes dos produtos                   |
| `sobre.php`       | Informações sobre a empresa             |
| `style.css`       | Estilos do site                         |
| `admin/`          | Painel administrativo                   |
| `assets/uploads/` | Imagens dos produtos                    |
| `config/`         | Configurações                           |
| `storage/`        | Banco de dados                          |
| `tools/`          | Ferramentas de instalação e verificação |

## ⚙️ Como instalar

Para executar o projeto, é necessário um servidor com PHP 8.1 ou superior e suporte a SQLite.

1. Baixe ou clone o repositório.
2. Coloque os arquivos em uma hospedagem com suporte a PHP.
3. Verifique se as pastas `storage/` e `assets/uploads/` possuem permissão de escrita.
4. Acesse o endereço do site pelo navegador.
5. Configure o administrador para começar a cadastrar os produtos.

O banco de dados é criado automaticamente na primeira execução, utilizando o catálogo inicial incluído no projeto.

**Importante:** se o site já estiver em uso, faça um backup antes de atualizar os arquivos para não perder produtos e imagens cadastrados.

## 🔐 Acesso administrativo

Para configurar o primeiro administrador:

1. Copie `config/local.example.php` para `config/local.php`.
2. Defina uma chave privada no campo `install_token`, com pelo menos 24 caracteres.
3. Acesse `/admin/instalar.php` pelo navegador.
4. Informe a chave e crie seu usuário e senha.
5. Após finalizar, remova o arquivo de configuração ou a chave utilizada na instalação.

O acesso ao painel é feito por:

`/admin/login.php`

O sistema utiliza senhas protegidas por hash, validação de sessões e proteção contra requisições não autorizadas.

## 📌 Observações

O projeto foi desenvolvido para funcionar em hospedagens com suporte a PHP e SQLite.

O GitHub é utilizado para armazenar o código, mas o site precisa de uma hospedagem que execute PHP para funcionar corretamente.

Também é importante manter backups do banco de dados e das imagens dos produtos.

---

### 🌐 Site oficial

**Ferla Estofados**

https://estofadosferla.wuaze.com/

Desenvolvido por **Eduardo Machado Ferla**.
