<?php
require_once __DIR__ . '/config.php';
$tituloPagina = 'Alunos';
$mensagem = '';
$mensagemTipo = 'sucesso';
$alunoEdicao = null;

// Excluir aluno
if (isset($_GET['excluir'])) {
    $id = (int)$_GET['excluir'];
    $stmt = $mysqli->prepare("DELETE FROM alunos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $mensagem = 'Aluno excluído com sucesso.';
}

// Salvar (inserir ou atualizar)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $matricula = trim($_POST['matricula'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $dataNascimento = trim($_POST['data_nascimento'] ?? '');
    $dataNascimento = $dataNascimento !== '' ? $dataNascimento : null;

    if ($nome === '' || $matricula === '') {
        $mensagem = 'Nome e matrícula são obrigatórios.';
        $mensagemTipo = 'erro';
    } else {
        if ($id > 0) {
            $stmt = $mysqli->prepare("UPDATE alunos SET nome=?, matricula=?, email=?, data_nascimento=? WHERE id=?");
            $stmt->bind_param('ssssi', $nome, $matricula, $email, $dataNascimento, $id);
        } else {
            $stmt = $mysqli->prepare("INSERT INTO alunos (nome, matricula, email, data_nascimento) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('ssss', $nome, $matricula, $email, $dataNascimento);
        }
        if ($stmt->execute()) {
            $mensagem = $id > 0 ? 'Aluno atualizado com sucesso.' : 'Aluno cadastrado com sucesso.';
        } else {
            $mensagem = 'Erro ao salvar: matrícula já pode estar em uso.';
            $mensagemTipo = 'erro';
        }
    }
}

// Carregar aluno para edição
if (isset($_GET['editar'])) {
    $id = (int)$_GET['editar'];
    $stmt = $mysqli->prepare("SELECT * FROM alunos WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $alunoEdicao = $stmt->get_result()->fetch_assoc();
}

$alunos = $mysqli->query("SELECT * FROM alunos ORDER BY nome");

require_once __DIR__ . '/includes/header.php';
?>

<div class="painel">
  <h2><?php echo $alunoEdicao ? 'Editar Aluno' : 'Novo Aluno'; ?></h2>
  <form method="post">
    <input type="hidden" name="id" value="<?php echo h($alunoEdicao['id'] ?? ''); ?>">
    <div class="linha">
      <div class="campo">
        <label>Nome completo *</label>
        <input type="text" name="nome" required value="<?php echo h($alunoEdicao['nome'] ?? ''); ?>">
      </div>
      <div class="campo">
        <label>Matrícula *</label>
        <input type="text" name="matricula" required value="<?php echo h($alunoEdicao['matricula'] ?? ''); ?>">
      </div>
    </div>
    <div class="linha">
      <div class="campo">
        <label>E-mail</label>
        <input type="email" name="email" value="<?php echo h($alunoEdicao['email'] ?? ''); ?>">
      </div>
      <div class="campo">
        <label>Data de nascimento</label>
        <input type="date" name="data_nascimento" value="<?php echo h($alunoEdicao['data_nascimento'] ?? ''); ?>">
      </div>
    </div>
    <button type="submit"><?php echo $alunoEdicao ? 'Salvar alterações' : 'Cadastrar aluno'; ?></button>
    <?php if ($alunoEdicao): ?>
      <a class="botao secundario" href="alunos.php">Cancelar</a>
    <?php endif; ?>
  </form>
</div>

<div class="painel">
  <h2>Alunos cadastrados</h2>
  <?php if ($alunos->num_rows === 0): ?>
    <p class="vazio">Nenhum aluno cadastrado ainda.</p>
  <?php else: ?>
  <table>
    <thead>
      <tr><th>Nome</th><th>Matrícula</th><th>E-mail</th><th>Nascimento</th><th>Ações</th></tr>
    </thead>
    <tbody>
      <?php while ($a = $alunos->fetch_assoc()): ?>
      <tr>
        <td><?php echo h($a['nome']); ?></td>
        <td><?php echo h($a['matricula']); ?></td>
        <td><?php echo h($a['email']); ?></td>
        <td><?php echo h($a['data_nascimento']); ?></td>
        <td class="acoes">
          <a href="alunos.php?editar=<?php echo (int)$a['id']; ?>">Editar</a>
          <a href="alunos.php?excluir=<?php echo (int)$a['id']; ?>" onclick="return confirm('Excluir este aluno e todas as suas notas?');">Excluir</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
