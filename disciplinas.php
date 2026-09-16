<?php
require_once __DIR__ . '/config.php';
$tituloPagina = 'Disciplinas';
$mensagem = '';
$mensagemTipo = 'sucesso';
$disciplinaEdicao = null;

if (isset($_GET['excluir'])) {
    $id = (int)$_GET['excluir'];
    $stmt = $mysqli->prepare("DELETE FROM disciplinas WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $mensagem = 'Disciplina excluída com sucesso.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $nome = trim($_POST['nome'] ?? '');
    $codigo = trim($_POST['codigo'] ?? '');
    $cargaHoraria = (int)($_POST['carga_horaria'] ?? 0);

    if ($nome === '' || $codigo === '') {
        $mensagem = 'Nome e código são obrigatórios.';
        $mensagemTipo = 'erro';
    } else {
        if ($id > 0) {
            $stmt = $mysqli->prepare("UPDATE disciplinas SET nome=?, codigo=?, carga_horaria=? WHERE id=?");
            $stmt->bind_param('ssii', $nome, $codigo, $cargaHoraria, $id);
        } else {
            $stmt = $mysqli->prepare("INSERT INTO disciplinas (nome, codigo, carga_horaria) VALUES (?, ?, ?)");
            $stmt->bind_param('ssi', $nome, $codigo, $cargaHoraria);
        }
        if ($stmt->execute()) {
            $mensagem = $id > 0 ? 'Disciplina atualizada com sucesso.' : 'Disciplina cadastrada com sucesso.';
        } else {
            $mensagem = 'Erro ao salvar: código já pode estar em uso.';
            $mensagemTipo = 'erro';
        }
    }
}

if (isset($_GET['editar'])) {
    $id = (int)$_GET['editar'];
    $stmt = $mysqli->prepare("SELECT * FROM disciplinas WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $disciplinaEdicao = $stmt->get_result()->fetch_assoc();
}

$disciplinas = $mysqli->query("SELECT * FROM disciplinas ORDER BY nome");

require_once __DIR__ . '/includes/header.php';
?>

<div class="painel">
  <h2><?php echo $disciplinaEdicao ? 'Editar Disciplina' : 'Nova Disciplina'; ?></h2>
  <form method="post">
    <input type="hidden" name="id" value="<?php echo h($disciplinaEdicao['id'] ?? ''); ?>">
    <div class="linha">
      <div class="campo">
        <label>Nome *</label>
        <input type="text" name="nome" required value="<?php echo h($disciplinaEdicao['nome'] ?? ''); ?>">
      </div>
      <div class="campo">
        <label>Código *</label>
        <input type="text" name="codigo" required value="<?php echo h($disciplinaEdicao['codigo'] ?? ''); ?>">
      </div>
      <div class="campo">
        <label>Carga horária (horas)</label>
        <input type="number" name="carga_horaria" min="0" value="<?php echo h($disciplinaEdicao['carga_horaria'] ?? '0'); ?>">
      </div>
    </div>
    <button type="submit"><?php echo $disciplinaEdicao ? 'Salvar alterações' : 'Cadastrar disciplina'; ?></button>
    <?php if ($disciplinaEdicao): ?>
      <a class="botao secundario" href="disciplinas.php">Cancelar</a>
    <?php endif; ?>
  </form>
</div>

<div class="painel">
  <h2>Disciplinas cadastradas</h2>
  <?php if ($disciplinas->num_rows === 0): ?>
    <p class="vazio">Nenhuma disciplina cadastrada ainda.</p>
  <?php else: ?>
  <table>
    <thead>
      <tr><th>Nome</th><th>Código</th><th>Carga Horária</th><th>Ações</th></tr>
    </thead>
    <tbody>
      <?php while ($d = $disciplinas->fetch_assoc()): ?>
      <tr>
        <td><?php echo h($d['nome']); ?></td>
        <td><?php echo h($d['codigo']); ?></td>
        <td><?php echo h($d['carga_horaria']); ?>h</td>
        <td class="acoes">
          <a href="disciplinas.php?editar=<?php echo (int)$d['id']; ?>">Editar</a>
          <a href="disciplinas.php?excluir=<?php echo (int)$d['id']; ?>" onclick="return confirm('Excluir esta disciplina e todas as suas notas?');">Excluir</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
