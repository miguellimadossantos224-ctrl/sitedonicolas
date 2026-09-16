-- =========================================================
-- Banco de dados: Sistema Escolar (Alunos, Disciplinas, Notas)
-- Compatível com MySQL 5.7+ / MariaDB (InfinityFree)
-- =========================================================

-- Se estiver rodando localmente (XAMPP/WAMP), pode criar o banco:
-- CREATE DATABASE IF NOT EXISTS escola CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE escola;

-- No InfinityFree, o banco já é criado pelo painel: apenas rode o restante
-- deste script dentro do banco criado, via phpMyAdmin.

CREATE TABLE IF NOT EXISTS alunos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  matricula VARCHAR(30) NOT NULL UNIQUE,
  email VARCHAR(150) NULL,
  data_nascimento DATE NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS disciplinas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  codigo VARCHAR(30) NOT NULL UNIQUE,
  carga_horaria INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS notas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  aluno_id INT NOT NULL,
  disciplina_id INT NOT NULL,
  bimestre TINYINT NOT NULL,
  nota DECIMAL(4,2) NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uk_nota (aluno_id, disciplina_id, bimestre),
  CONSTRAINT fk_notas_aluno FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
  CONSTRAINT fk_notas_disciplina FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id) ON DELETE CASCADE,
  CONSTRAINT chk_bimestre CHECK (bimestre BETWEEN 1 AND 4),
  CONSTRAINT chk_nota CHECK (nota >= 0 AND nota <= 10)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dados de exemplo (opcional - remova se não quiser dados de teste)
INSERT INTO alunos (nome, matricula, email, data_nascimento) VALUES
('Ana Souza', '2026001', 'ana.souza@example.com', '2010-03-12'),
('Bruno Lima', '2026002', 'bruno.lima@example.com', '2010-07-25');

INSERT INTO disciplinas (nome, codigo, carga_horaria) VALUES
('Matemática', 'MAT01', 80),
('Português', 'POR01', 80),
('Ciências', 'CIE01', 60);

INSERT INTO notas (aluno_id, disciplina_id, bimestre, nota) VALUES
(1, 1, 1, 8.5),
(1, 2, 1, 7.0),
(2, 1, 1, 6.0);
