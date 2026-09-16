<?php
require_once __DIR__ . '/config.php';
$tituloPagina = 'Lançar Notas';
$mensagem = '';
$mensagemTipo = 'sucesso';

// Salvar nota (insere ou atualiza, graças à chave única aluno+disciplina+bimestre)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alunoId = (int)($_POST['aluno_id'] ?? 0);
    $disciplinaId = (int)($_POST['disciplina_id'] ?? 0);
    $bimestre = (int)($_POST['bimestre'] ?? 0);
    $nota = str_replace(',', '.', trim($_POST['nota'] ?? ''));

    if ($alunoId <= 0 || $disciplinaId <= 0 || $bimestre < 1 || $bimestre > 4 || !is_numeric($nota) || $nota < 0 || $nota > 10) {
        $mensagem = 'Preencha todos os campos corretamente. A nota deve ser entre 0 e 10.';
        $mensagemTipo = 'erro';
    } else {
        $stmt = $mysqli->prepare(
            "INSERT INTO notas (aluno_id, disciplina_id, bimestre, nota) VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE nota = VALUES(nota)"
        );
        $stmt->bind_param('iiid', $alunoId, $disciplinaId, $bimestre, $nota);
        if ($stmt->execute()) {
            $mensagem = 'Nota lançada com sucesso.';
        } else {
            $mensagem = 'Erro ao lançar a nota.';
            $mensagemTipo = 'erro';
        }
    }
}

// Excluir uma nota lançada
if (isset($_GET['excluir'])) {
    $id = (int)$_GET['excluir'];
    $stmt = $mysqli->prepare("DELETE FROM notas WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $mensagem = 'Nota removida com sucesso.';
}

$alunos = $mysqli->query("SELECT * FROM alunos ORDER BY nome");
$disciplinas = $mysqli->query("SELECT * FROM disciplinas ORDER BY nome");

// Últimas notas lançadas, para conferência rápida
$ultimasNotas = $mysqli->query(
    "SELECT n.id, n.bimestre, n.nota, a.nome AS aluno_nome, d.nome AS disciplina_nome
     FROM notas n
     JOIN alunos a ON a.id = n.aluno_id
     JOIN disciplinas d ON d.id = n.disciplina_id
     ORDER BY n.criado_em DESC
     LIMIT 15"
);

require_once __DIR__ . '/includes/header.php';
?>

<div class="painel">
  <h2>Lançar Nota</h2>
  <form method="post">
    <div class="linha">
      <div class="campo">
        <label>Aluno *</label>
        <select name="aluno_id" required>
          <option value="">Selecione...</option>
          <?php mysqli_data_seek($alunos, 0); while ($a = $alunos->fetch_assoc()): ?>
            <option value="<?php echo (int)$a['id']; ?>"><?php echo h($a['nome']); ?> (<?php echo h($a['matricula']); ?>)</option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="campo">
        <label>Disciplina *</label>
        <select name="disciplina_id" required>
          <option value="">Selecione...</option>
          <?php mysqli_data_seek($disciplinas, 0); while ($d = $disciplinas->fetch_assoc()): ?>
            <option value="<?php echo (int)$d['id']; ?>"><?php echo h($d['nome']); ?></option>
          <?php endwhile; ?>
        </select>
      </div>
    </div>
    <div class="linha">
      <div class="campo">
        <label>Bimestre *</label>
        <select name="bimestre" required>
          <option value="1">1º Bimestre</option>
          <option value="2">2º Bimestre</option>
          <option value="3">3º Bimestre</option>
          <option value="4">4º Bimestre</option>
        </select>
      </div>
      <div class="campo">
        <label>Nota (0 a 10) *</label>
        <input type="number" step="0.01" min="0" max="10" name="nota" required>
      </div>
    </div>
    <button type="submit">Salvar nota</button>
  </form>
  <p style="font-size:13px;color:#777;margin-top:10px;">
    Se já existir uma nota para esse aluno + disciplina + bimestre, ela será atualizada automaticamente.
  </p>
</div>

<div class="painel">
  <h2>Últimos lançamentos</h2>
  <?php if ($ultimasNotas->num_rows === 0): ?>
    <p class="vazio">Nenhuma nota lançada ainda.</p>
  <?php else: ?>
  <table>
    <thead>
      <tr><th>Aluno</th><th>Disciplina</th><th>Bimestre</th><th>Nota</th><th>Ações</th></tr>
    </thead>
    <tbody>
      <?php while ($n = $ultimasNotas->fetch_assoc()): ?>
      <tr>
        <td><?php echo h($n['aluno_nome']); ?></td>
        <td><?php echo h($n['disciplina_nome']); ?></td>
        <td><?php echo (int)$n['bimestre']; ?>º</td>
        <td><?php echo h(number_format((float)$n['nota'], 2, ',', '.')); ?></td>
        <td class="acoes">
          <a href="notas.php?excluir=<?php echo (int)$n['id']; ?>" onclick="return confirm('Remover esta nota?');">Excluir</a>
        </td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
