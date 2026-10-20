<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] !== 'professor') {
    header('Location: login.php');
    exit;
}

$id_professor = $_SESSION['id_utilizador'];

// Processar a submissão de feedback
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_feedback'])) {
    $id_projeto = $_POST['id_projeto'];
    $comentario = $_POST['comentario'];
    $conclusao = $_POST['conclusao'];

    $stmt = $pdo->prepare("INSERT INTO FeedbackPF (conclusao, comentario, id_projeto, id_professor) VALUES (?, ?, ?, ?)");
    $stmt->execute([$conclusao, $comentario, $id_projeto, $id_professor]);
    header('Location: professor_dashboard.php');
    exit;
}

// Obter os projetos associados ao professor
$stmt = $pdo->prepare("
    SELECT p.*, u.nome AS aluno_nome, u.email AS aluno_email 
    FROM ProjetoPF p 
    JOIN UtilizadoresPF u ON p.id_aluno = u.id_utilizador 
    WHERE p.id_professor = ?
");
$stmt->execute([$id_professor]);
$projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel do Professor - ProjectFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">ProjectFlow - Professor</a>
        <div class="d-flex align-items-center text-white">
            <span class="me-3">Olá, Prof. <?= htmlspecialchars($_SESSION['nome']) ?></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Sair</a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <h3 class="mb-4">Projetos em Acompanhamento</h3>
    <?php if (empty($projetos)): ?>
        <p class="text-muted">Ainda não existem projetos atribuídos a este perfil.</p>
    <?php else: ?>
        <?php foreach ($projetos as $p): ?>
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between">
                    <span><strong>Aluno:</strong> <?= htmlspecialchars($p['aluno_nome']) ?> (<?= htmlspecialchars($p['aluno_email']) ?>)</span>
                    <span><strong>Data Limite:</strong> <?= htmlspecialchars($p['data_fim']) ?></span>
                </div>
                <div class="card-body">
                    <h5>Tema: <?= htmlspecialchars($p['tema']) ?></h5>
                    <p class="text-muted">Área: <?= htmlspecialchars($p['area']) ?></p>

                    <hr>
                    <h6>Registar Feedback / Avaliação</h6>
                    <form method="POST">
                        <input type="hidden" name="novo_feedback" value="1">
                        <input type="hidden" name="id_projeto" value="<?= $p['id_projeto'] ?>">
                        <div class="mb-2">
                            <textarea name="comentario" class="form-control" placeholder="Escreva as observações para o aluno..." rows="2" required></textarea>
                        </div>
                        <div class="mb-2">
                            <input type="text" name="conclusao" class="form-control" placeholder="Conclusão ou nota do estado do trabalho">
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">Enviar Feedback</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>