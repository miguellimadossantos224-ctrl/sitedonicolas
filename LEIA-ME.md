# Sistema Escolar — PHP + MySQL

Aplicação para cadastro de alunos, disciplinas, lançamento de notas por
bimestre e emissão de relatórios por aluno e por disciplina.

## Estrutura dos arquivos

```
config.php               -> conexão com o banco (EDITAR antes de subir)
db.sql                    -> script para criar as tabelas
index.php                 -> painel inicial
alunos.php                -> cadastro/edição/exclusão de alunos
disciplinas.php           -> cadastro/edição/exclusão de disciplinas
notas.php                 -> lançamento de notas por bimestre
relatorio_aluno.php       -> relatório de notas de um aluno (todas disciplinas)
relatorio_disciplina.php  -> relatório de notas de uma disciplina (todos alunos)
includes/header.php       -> cabeçalho e menu
includes/footer.php       -> rodapé
assets/style.css          -> estilo visual
.htaccess                 -> configuração básica do Apache
```

## 1. Testar localmente (opcional, recomendado)

Se quiser testar antes de publicar, use XAMPP, WAMP ou Laragon:

1. Copie a pasta `escola_app` para a pasta `htdocs` (XAMPP) ou `www` (WAMP).
2. Crie um banco chamado `escola` no phpMyAdmin (`http://localhost/phpmyadmin`).
3. Importe o arquivo `db.sql` nesse banco.
4. Em `config.php`, ajuste:
   ```php
   $DB_HOST = 'localhost';
   $DB_NAME = 'escola';
   $DB_USER = 'root';
   $DB_PASS = '';
   ```
5. Acesse `http://localhost/escola_app/`.

## 2. Publicar no InfinityFree

### Passo 1 — Criar a conta e o site
1. Crie uma conta em https://infinityfree.com (ou app.infinityfree.net).
2. No painel (Client Area), clique em **Create Account** e escolha um
   subdomínio gratuito (ex: `meuescola.infinityfreeapp.com`) ou um domínio
   próprio, se tiver.
3. Aguarde a conta de hospedagem ficar com status **Active** (pode levar
   alguns minutos).

### Passo 2 — Criar o banco de dados MySQL
1. Dentro do painel do site (vCP / Control Panel), acesse **MySQL Databases**.
2. Crie um novo banco de dados (ex: `escola`). O InfinityFree vai gerar um
   nome final parecido com `if0_00000000_escola`.
3. Anote os dados exibidos:
   - **Hostname** (ex: `sql200.infinityfree.com`)
   - **Database name** (ex: `if0_00000000_escola`)
   - **Username** (ex: `if0_00000000`)
   - **Password** (a que você definiu)

### Passo 3 — Importar as tabelas
1. No painel, abra o **phpMyAdmin** apontando para o banco criado.
2. Vá em **Importar**, selecione o arquivo `db.sql` deste projeto e execute.
3. Confirme que as tabelas `alunos`, `disciplinas` e `notas` foram criadas
   (você pode remover os `INSERT` de exemplo no `db.sql` antes de importar,
   se não quiser dados de teste).

### Passo 4 — Configurar o config.php
Edite o arquivo `config.php` com os dados do Passo 2:
```php
$DB_HOST = 'sql200.infinityfree.com';   // o hostname do seu painel
$DB_NAME = 'if0_00000000_escola';
$DB_USER = 'if0_00000000';
$DB_PASS = 'sua_senha_do_banco';
```

### Passo 5 — Enviar os arquivos (FTP ou Gerenciador de Arquivos)
**Opção A — Gerenciador de Arquivos (mais simples):**
1. No painel, abra **Online File Manager**.
2. Entre na pasta `htdocs`.
3. Envie (upload) todos os arquivos e pastas deste projeto para dentro de
   `htdocs` (mantendo a estrutura: `config.php`, `index.php`, pasta
   `includes/`, pasta `assets/`, etc., direto dentro de `htdocs`, sem
   subpasta extra).

**Opção B — FTP (para muitos arquivos, mais rápido):**
1. Baixe um cliente FTP como o FileZilla.
2. No painel do InfinityFree, veja em **FTP Details** o host, usuário e
   senha do FTP.
3. Conecte-se e envie todo o conteúdo da pasta `escola_app` para dentro de
   `htdocs` no servidor.

### Passo 6 — Testar
Acesse seu domínio, ex:
```
http://meuescola.infinityfreeapp.com
```
Você deve ver o painel inicial do Sistema Escolar. Teste:
1. Cadastrar um aluno em **Alunos**.
2. Cadastrar uma disciplina em **Disciplinas**.
3. Lançar uma nota em **Lançar Notas**.
4. Conferir os relatórios em **Relatório por Aluno** e **Relatório por Disciplina**.

## Observações importantes sobre o InfinityFree

- O plano gratuito não suporta certas extensões/recursos avançados de PHP,
  mas tudo usado aqui (`mysqli`, formulários, sessões) é compatível.
- O ativação do domínio/hospedagem pode demorar até ~30 minutos após a
  criação da conta.
- Sites gratuitos do InfinityFree podem exibir uma tela de propaganda ou
  "powered by" ocasionalmente; isso é uma limitação da hospedagem gratuita,
  não da aplicação.
- Sempre faça backup do banco (Exportar no phpMyAdmin) antes de mudanças
  grandes.

## Segurança (recomendações)

- Nunca deixe o `config.php` com credenciais reais em repositórios públicos
  (GitHub, etc.).
- Se quiser evoluir o sistema, considere adicionar login de usuário
  (professor/administrador) antes de liberar o acesso publicamente, já que
  esta versão não possui autenticação.
