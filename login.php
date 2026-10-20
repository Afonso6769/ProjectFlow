<?php
session_start();
require_once 'db.php';

$erro = '';
$sucesso = '';

if (isset($_GET['registado']) && $_GET['registado'] == 1) {
    $sucesso = 'Conta criada com sucesso! Já pode iniciar sessão.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM UtilizadoresPF WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && $password === $user['password']) {
        $_SESSION['id_utilizador'] = $user['id_utilizador'];
        $_SESSION['nome'] = $user['nome'];
        $_SESSION['tipo'] = $user['tipo'];

        if ($user['tipo'] === 'aluno') {
            header('Location: aluno_dashboard.php');
        } else {
            header('Location: professor_dashboard.php');
        }
        exit;
    } else {
        $erro = 'Credenciais inválidas.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>ProjectFlow - Autenticação</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="card-title text-center mb-4">ProjectFlow</h3>
                    
                    <?php if ($sucesso): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($sucesso) ?></div>
                    <?php endif; ?>

                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Palavra-passe</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>

                    <div class="text-center mt-3">
                        <small>Ainda não tem conta? <a href="registo.php" class="text-decoration-none">Registe-se aqui</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>