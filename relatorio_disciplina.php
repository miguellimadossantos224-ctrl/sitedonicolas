<?php
require_once __DIR__ . '/config.php';
$tituloPagina = 'Relatório por Disciplina';
$mensagem = '';

$disciplinaId = (int)($_GET['disciplina_id'] ?? 0);
$disciplinas = $mysqli->query("SELECT * FROM disciplinas ORDER BY nome");

$disciplinaSelecionada = null;
$linhas = [];

if ($disciplinaId > 0) {
    $stmt = $mysqli->prepare("SELECT * FROM disciplinas WHERE id = ?");
    $stmt->bind_param('i', $disciplinaId);
    $stmt->execute();
    $disciplinaSelecionada = $stmt->get_result()->fetch_assoc();

    if ($disciplinaSelecionada) {
        $stmt2 = $mysqli->prepare(
            "SELECT a.id, a.nome, a.matricula,
                    MAX(CASE WHEN n.bimestre = 1 THEN n.nota END) AS b1,
                    MAX(CASE WHEN n.bimestre = 2 THEN n.nota END) AS b2,
                    MAX(CASE WHEN n.bimestre = 3 THEN n.nota END) AS b3,
                    MAX(CASE WHEN n.bimestre = 4 THEN n.nota END) AS b4
             FROM alunos a
             LEFT JOIN notas n ON n.aluno_id = a.id AND n.disciplina_id = ?
             GROUP BY a.id, a.nome, a.matricula
             ORDER BY a.nome"
        );
        $stmt2->bind_param('i', $disciplinaId);
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
  <h2>Selecionar Disciplina</h2>
  <form method="get">
    <div class="linha">
      <div class="campo">
        <label>Disciplina</label>
        <select name="disciplina_id" onchange="this.form.submit()">
          <option value="">Selecione uma disciplina...</option>
          <?php mysqli_data_seek($disciplinas, 0); while ($d = $disciplinas->fetch_assoc()): ?>
            <option value="<?php echo (int)$d['id']; ?>" <?php echo $d['id'] == $disciplinaId ? 'selected' : ''; ?>>
              <?php echo h($d['nome']); ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
    </div>
    <noscript><button type="submit">Ver relatório</button></noscript>
  </form>
</div>

<?php if ($disciplinaSelecionada): ?>
<div class="painel">
  <h2>Notas de <?php echo h($disciplinaSelecionada['nome']); ?></h2>

  <?php if (empty($linhas)): ?>
    <p class="vazio">Nenhum aluno cadastrado no sistema.</p>
  <?php else: ?>
  <table>
    <thead>
      <tr>
        <th>Aluno</th><th>Matrícula</th>
        <th>1º Bim.</th><th>2º Bim.</th><th>3º Bim.</th><th>4º Bim.</th>
        <th>Média</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($linhas as $l): ?>
      <tr>
        <td><?php echo h($l['nome']); ?></td>
        <td><?php echo h($l['matricula']); ?></td>
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
<?php elseif ($disciplinaId > 0): ?>
  <div class="painel"><p class="vazio">Disciplina não encontrada.</p></div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
