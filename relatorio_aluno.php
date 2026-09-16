<?php
require_once __DIR__ . '/config.php';
$tituloPagina = 'Relatório por Aluno';
$mensagem = '';

$alunoId = (int)($_GET['aluno_id'] ?? 0);
$alunos = $mysqli->query("SELECT * FROM alunos ORDER BY nome");

$alunoSelecionado = null;
$linhas = [];

if ($alunoId > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM alunos WHERE id = ?");
    $stmt->bind_param('i', $alunoId);
    $stmt->execute();
    $alunoSelecionado = $stmt->get_result()->fetch_assoc();

    if ($alunoSelecionado) {
        // Para cada disciplina, buscamos as notas dos 4 bimestres (podendo ser nulas)
        $stmt2 = $mysqli->prepare(
            "SELECT d.id, d.nome,
                    MAX(CASE WHEN n.bimestre = 1 THEN n.nota END) AS b1,
                    MAX(CASE WHEN n.bimestre = 2 THEN n.nota END) AS b2,
                    MAX(CASE WHEN n.bimestre = 3 THEN n.nota END) AS b3,
                    MAX(CASE WHEN n.bimestre = 4 THEN n.nota END) AS b4
             FROM disciplinas d
             LEFT JOIN notas n ON n.disciplina_id = d.id AND n.aluno_id = ?
             GROUP BY d.id, d.nome
             ORDER BY d.nome"
        );
        $stmt2->bind_param('i', $alunoId);
        $stmt2->execute();
        $res = $stmt2->get_result();
        while ($row = $res->fetch_assoc()) {
            $notasValidas = array_filter([$row['b1'], $row['b2'], $row['b3'], $row['b4']], fn($v) => $v !== null);
            $media = count($notasValidas) > 0 ? array_sum($notasValidas) / count($notasValidas) : null;
            $row['media'] = $media;
            $linhas[] = $row;
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="painel">
  <h2>Selecionar Aluno</h2>
  <form method="get">
    <div class="linha">
      <div class="campo">
        <label>Aluno</label>
        <select name="aluno_id" onchange="this.form.submit()">
          <option value="">Selecione um aluno...</option>
          <?php mysqli_data_seek($alunos, 0); while ($a = $alunos->fetch_assoc()): ?>
            <option value="<?php echo (int)$a['id']; ?>" <?php echo $a['id'] == $alunoId ? 'selected' : ''; ?>>
              <?php echo h($a['nome']); ?> (<?php echo h($a['matricula']); ?>)
            </option>
          <?php endwhile; ?>
        </select>
      </div>
    </div>
    <noscript><button type="submit">Ver relatório</button></noscript>
  </form>
</div>

<?php if ($alunoSelecionado): ?>
<div class="painel">
  <h2>Boletim de <?php echo h($alunoSelecionado['nome']); ?></h2>
  <p>Matrícula: <?php echo h($alunoSelecionado['matricula']); ?></p>

  <?php if (empty($linhas)): ?>
    <p class="vazio">Nenhuma disciplina cadastrada no sistema.</p>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Disciplina</th>
        <th>1º Bim.</th><th>2º Bim.</th><th>3º Bim.</th><th>4º Bim.</th>
        <th>Média</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($linhas as $l): ?>
      <tr>
        <td><?php echo h($l['nome']); ?></td>
        <td><?php echo $l['b1'] !== null ? number_format($l['b1'], 2, ',', '.') : '-'; ?></td>
        <td><?php echo $l['b2'] !== null ? number_format($l['b2'], 2, ',', '.') : '-'; ?></td>
        <td><?php echo $l['b3'] !== null ? number_format($l['b3'], 2, ',', '.') : '-'; ?></td>
        <td><?php echo $l['b4'] !== null ? number_format($l['b4'], 2, ',', '.') : '-'; ?></td>
        <td>
          <?php if ($l['media'] !== null): ?>
            <span class="<?php echo $l['media'] >= 6 ? 'media-boa' : 'media-ruim'; ?>">
              <?php echo number_format($l['media'], 2, ',', '.'); ?>
            </span>
          <?php else: ?>
            -
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<?php elseif ($alunoId > 0): ?>
  <div class="painel"><p class="vazio">Aluno não encontrado.</p></div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
