<?php
session_start();
require_once 'db.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $tipo = $_POST['tipo']; // 'aluno' ou 'professor'

    if (empty($nome) || empty($email) || empty($password) || empty($tipo)) {
        $erro = 'Por favor, preencha todos os campos.';
    } elseif (!in_array($tipo, ['aluno', 'professor'])) {
        $erro = 'Tipo de utilizador inválido.';
    } else {
        // Verificar se o email já existe
        $stmt = $pdo->prepare("SELECT id_utilizador FROM UtilizadoresPF WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $erro = 'Este email já está registado.';
        } else {
            // Inserir novo utilizador
            $stmt = $pdo->prepare("INSERT INTO UtilizadoresPF (nome, email, password, tipo) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$nome, $email, $password, $tipo])) {
                header('Location: login.php?registado=1');
                exit;
            } else {
                $erro = 'Ocorreu um erro ao criar a conta.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>ProjectFlow - Criar Conta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center vh-100">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h3 class="card-title text-center mb-4">Criar Conta</h3>
                    
                    <?php if ($erro): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($erro) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nome Completo</label>
                            <input type="text" name="nome" class="form-control" placeholder="Ex: Afonso Lourenço" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="exemplo@etpc.pt" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Palavra-passe</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Utilizador</label>
                            <select name="tipo" class="form-select" required>
                                <option value="" disabled selected>Selecione uma opção...</option>
                                <option value="aluno">Aluno</option>
                                <option value="professor">Professor</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 mt-2">Criar Conta</button>
                    </form>

                    <div class="text-center mt-3">
                        <small>Já tem conta? <a href="login.php" class="text-decoration-none">Inicie sessão aqui</a></small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>