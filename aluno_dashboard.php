<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['id_utilizador']) || $_SESSION['tipo'] !== 'aluno') {
    header('Location: login.php');
    exit;
}

$id_aluno = $_SESSION['id_utilizador'];

// --- 1. AÇÕES DO PROJETO ---

// Criar Projeto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_projeto'])) {
    $tema = trim($_POST['tema']);
    $area = trim($_POST['area']);
    $data_fim = $_POST['data_fim'];
    $id_prof = $_POST['id_professor'];

    $stmt = $pdo->prepare("INSERT INTO ProjetoPF (tema, area, data_fim, id_aluno, id_professor) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$tema, $area, $data_fim, $id_aluno, $id_prof]);
    $novo_id = $pdo->lastInsertId();
    header("Location: aluno_dashboard.php?id_projeto=$novo_id");
    exit;
}

// Editar Projeto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_projeto'])) {
    $id_p = (int)$_POST['id_projeto'];
    $tema = trim($_POST['tema']);
    $area = trim($_POST['area']);
    $data_fim = $_POST['data_fim'];
    $id_prof = $_POST['id_professor'];

    $stmt = $pdo->prepare("UPDATE ProjetoPF SET tema = ?, area = ?, data_fim = ?, id_professor = ? WHERE id_projeto = ? AND id_aluno = ?");
    $stmt->execute([$tema, $area, $data_fim, $id_prof, $id_p, $id_aluno]);
    header("Location: aluno_dashboard.php?id_projeto=$id_p");
    exit;
}

// Apagar Projeto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apagar_projeto'])) {
    $id_p = (int)$_POST['id_projeto'];
    $stmt = $pdo->prepare("DELETE FROM ProjetoPF WHERE id_projeto = ? AND id_aluno = ?");
    $stmt->execute([$id_p, $id_aluno]);
    header("Location: aluno_dashboard.php");
    exit;
}

// --- 2. AÇÕES DE TAREFAS ---

// Criar Tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nova_tarefa'])) {
    $id_p = (int)$_POST['id_projeto'];
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $estado = $_POST['estado'] ?? 'Pendente';
    $data_final = $_POST['data_final'];

    $stmt = $pdo->prepare("INSERT INTO TarefasPF (titulo, descricao, estado, data_final, id_projeto) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$titulo, $descricao, $estado, $data_final, $id_p]);
    header("Location: aluno_dashboard.php?id_projeto=$id_p");
    exit;
}

// Editar Tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_tarefa'])) {
    $id_p = (int)$_POST['id_projeto'];
    $id_t = (int)$_POST['id_tarefa'];
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $estado = $_POST['estado'];
    $data_final = $_POST['data_final'];

    $stmt = $pdo->prepare("UPDATE TarefasPF SET titulo = ?, descricao = ?, estado = ?, data_final = ? WHERE id_tarefa = ? AND id_projeto = ?");
    $stmt->execute([$titulo, $descricao, $estado, $data_final, $id_t, $id_p]);
    header("Location: aluno_dashboard.php?id_projeto=$id_p");
    exit;
}

// Apagar Tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apagar_tarefa'])) {
    $id_p = (int)$_POST['id_projeto'];
    $id_t = (int)$_POST['id_tarefa'];

    $stmt = $pdo->prepare("DELETE FROM TarefasPF WHERE id_tarefa = ? AND id_projeto = ?");
    $stmt->execute([$id_t, $id_p]);
    header("Location: aluno_dashboard.php?id_projeto=$id_p");
    exit;
}

// --- 3. AÇÕES DO REGISTO DIÁRIO ---

// Criar Diário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_diario'])) {
    $id_p = (int)$_POST['id_projeto'];
    $descricao = trim($_POST['descricao']);
    $data = $_POST['data'] ?: date('Y-m-d');

    $stmt = $pdo->prepare("INSERT INTO RegistoDiarioPF (data, descricao, id_projeto) VALUES (?, ?, ?)");
    $stmt->execute([$data, $descricao, $id_p]);
    header("Location: aluno_dashboard.php?id_projeto=$id_p");
    exit;
}

// Editar Diário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_diario'])) {
    $id_p = (int)$_POST['id_projeto'];
    $id_r = (int)$_POST['id_registo'];
    $descricao = trim($_POST['descricao']);
    $data = $_POST['data'];

    $stmt = $pdo->prepare("UPDATE RegistoDiarioPF SET descricao = ?, data = ? WHERE id_registo = ? AND id_projeto = ?");
    $stmt->execute([$descricao, $data, $id_r, $id_p]);
    header("Location: aluno_dashboard.php?id_projeto=$id_p");
    exit;
}

// Apagar Diário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apagar_diario'])) {
    $id_p = (int)$_POST['id_projeto'];
    $id_r = (int)$_POST['id_registo'];

    $stmt = $pdo->prepare("DELETE FROM RegistoDiarioPF WHERE id_registo = ? AND id_projeto = ?");
    $stmt->execute([$id_r, $id_p]);
    header("Location: aluno_dashboard.php?id_projeto=$id_p");
    exit;
}

// --- 4. AÇÕES DE RECURSOS ---

// Criar Recurso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_recurso'])) {
    $id_p = (int)$_POST['id_projeto'];
    $titulo = trim($_POST['titulo']);
    $link = trim($_POST['link']);
    $descricao = trim($_POST['descricao']);
    $data = date('Y-m-d');

    $stmt = $pdo->prepare("INSERT INTO RecursoPF (titulo, link, data, descricao, id_projeto) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$titulo, $link, $data, $descricao, $id_p]);
    header("Location: aluno_dashboard.php?id_projeto=$id_p");
    exit;
}

// Editar Recurso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_recurso'])) {
    $id_p = (int)$_POST['id_projeto'];
    $id_rec = (int)$_POST['id_recurso'];
    $titulo = trim($_POST['titulo']);
    $link = trim($_POST['link']);
    $descricao = trim($_POST['descricao']);

    $stmt = $pdo->prepare("UPDATE RecursoPF SET titulo = ?, link = ?, descricao = ? WHERE id_recurso = ? AND id_projeto = ?");
    $stmt->execute([$titulo, $link, $descricao, $id_rec, $id_p]);
    header("Location: aluno_dashboard.php?id_projeto=$id_p");
    exit;
}

// Apagar Recurso
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apagar_recurso'])) {
    $id_p = (int)$_POST['id_projeto'];
    $id_rec = (int)$_POST['id_recurso'];

    $stmt = $pdo->prepare("DELETE FROM RecursoPF WHERE id_recurso = ? AND id_projeto = ?");
    $stmt->execute([$id_rec, $id_p]);
    header("Location: aluno_dashboard.php?id_projeto=$id_p");
    exit;
}

// --- 5. CARREGAR DADOS ---

// Obter todos os projetos do aluno
$stmt = $pdo->prepare("SELECT p.*, u.nome AS prof_nome FROM ProjetoPF p LEFT JOIN UtilizadoresPF u ON p.id_professor = u.id_utilizador WHERE p.id_aluno = ? ORDER BY p.id_projeto DESC");
$stmt->execute([$id_aluno]);
$projetos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Selecionar o projeto ativo
$id_projeto_ativo = isset($_GET['id_projeto']) ? (int)$_GET['id_projeto'] : ($projetos[0]['id_projeto'] ?? null);

$projeto_ativo = null;
foreach ($projetos as $p) {
    if ($p['id_projeto'] == $id_projeto_ativo) {
        $projeto_ativo = $p;
        break;
    }
}

if (!$projeto_ativo && !empty($projetos)) {
    $projeto_ativo = $projetos[0];
    $id_projeto_ativo = $projeto_ativo['id_projeto'];
}

// Carregar conteúdos do projeto ativo
$tarefas = [];
$diarios = [];
$recursos = [];
$feedbacks = [];

if ($projeto_ativo) {
    $p_id = $projeto_ativo['id_projeto'];

    $stmt = $pdo->prepare("SELECT * FROM TarefasPF WHERE id_projeto = ? ORDER BY id_tarefa DESC");
    $stmt->execute([$p_id]);
    $tarefas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM RegistoDiarioPF WHERE id_projeto = ? ORDER BY data DESC, id_registo DESC");
    $stmt->execute([$p_id]);
    $diarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT * FROM RecursoPF WHERE id_projeto = ? ORDER BY id_recurso DESC");
    $stmt->execute([$p_id]);
    $recursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT f.*, u.nome AS prof_nome FROM FeedbackPF f JOIN UtilizadoresPF u ON f.id_professor = u.id_utilizador WHERE f.id_projeto = ? ORDER BY f.id_feedback DESC");
    $stmt->execute([$p_id]);
    $feedbacks = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Lista de professores para os seletores
$professores = $pdo->query("SELECT id_utilizador, nome FROM UtilizadoresPF WHERE tipo = 'professor'")->fetchAll(PDO::FETCH_ASSOC);
$mostrar_novo_form = isset($_GET['novo']) || empty($projetos);
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Painel do Aluno - ProjectFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="aluno_dashboard.php">ProjectFlow - Aluno</a>
        <div class="d-flex align-items-center text-white">
            <span class="me-3">Olá, <?= htmlspecialchars($_SESSION['nome']) ?></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Sair</a>
        </div>
    </div>
</nav>

<div class="container pb-5">

    <!-- SELEÇÃO DE PROJETOS E BOTÃO NOVO PROJETO -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <h4 class="m-0">Os Meus Projetos:</h4>
            <?php if (!empty($projetos)): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <?= htmlspecialchars($projeto_ativo['tema'] ?? 'Selecionar Projeto') ?>
                    </button>
                    <ul class="dropdown-menu">
                        <?php foreach ($projetos as $p): ?>
                            <li>
                                <a class="dropdown-item <?= ($p['id_projeto'] == $id_projeto_ativo) ? 'active' : '' ?>" href="aluno_dashboard.php?id_projeto=<?= $p['id_projeto'] ?>">
                                    <?= htmlspecialchars($p['tema']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
        <a href="aluno_dashboard.php?novo=1" class="btn btn-success">+ Criar Novo Projeto</a>
    </div>

    <!-- FORMULÁRIO DE CRIAR PROJETO -->
    <?php if ($mostrar_novo_form): ?>
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="m-0">Registar Novo Projeto da PAP</h5>
                <?php if (!empty($projetos)): ?>
                    <a href="aluno_dashboard.php?id_projeto=<?= $id_projeto_ativo ?>" class="btn-close btn-close-white"></a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="criar_projeto" value="1">
                    <div class="mb-3">
                        <label class="form-label">Tema do Projeto</label>
                        <input type="text" name="tema" class="form-control" placeholder="Ex: Sistema de Gestão Escolar" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Área</label>
                        <input type="text" name="area" class="form-control" placeholder="Ex: Desenvolvimento Web" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data de Conclusão Prevista</label>
                        <input type="date" name="data_fim" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Professor Orientador</label>
                        <select name="id_professor" class="form-select" required>
                            <option value="">Selecione um professor...</option>
                            <?php foreach ($professores as $prof): ?>
                                <option value="<?= $prof['id_utilizador'] ?>"><?= htmlspecialchars($prof['nome']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Guardar Projeto</button>
                    <?php if (!empty($projetos)): ?>
                        <a href="aluno_dashboard.php?id_projeto=<?= $id_projeto_ativo ?>" class="btn btn-secondary ms-2">Cancelar</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- CONTEÚDO DO PROJETO ATIVO -->
    <?php if ($projeto_ativo && !$mostrar_novo_form): ?>
        <!-- CABEÇALHO DO PROJETO -->
        <div class="card shadow mb-4">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h3 class="card-title mb-1"><?= htmlspecialchars($projeto_ativo['tema']) ?></h3>
                    <p class="text-muted mb-0">
                        <strong>Área:</strong> <?= htmlspecialchars($projeto_ativo['area']) ?> | 
                        <strong>Data Limite:</strong> <?= htmlspecialchars($projeto_ativo['data_fim']) ?> | 
                        <strong>Orientador:</strong> <?= htmlspecialchars($projeto_ativo['prof_nome'] ?? 'Não atribuído') ?>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditarProjeto">Editar Projeto</button>
                    <form method="POST" onsubmit="return confirm('Tem a certeza que pretende apagar este projeto e todos os seus itens?');" class="d-inline">
                        <input type="hidden" name="apagar_projeto" value="1">
                        <input type="hidden" name="id_projeto" value="<?= $projeto_ativo['id_projeto'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm">Apagar Projeto</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL EDITAR PROJETO -->
        <div class="modal fade" id="modalEditarProjeto" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="POST">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Projeto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="editar_projeto" value="1">
                            <input type="hidden" name="id_projeto" value="<?= $projeto_ativo['id_projeto'] ?>">
                            <div class="mb-3">
                                <label class="form-label">Tema</label>
                                <input type="text" name="tema" class="form-control" value="<?= htmlspecialchars($projeto_ativo['tema']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Área</label>
                                <input type="text" name="area" class="form-control" value="<?= htmlspecialchars($projeto_ativo['area']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Data de Conclusão Prevista</label>
                                <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($projeto_ativo['data_fim']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Professor Orientador</label>
                                <select name="id_professor" class="form-select" required>
                                    <?php foreach ($professores as $prof): ?>
                                        <option value="<?= $prof['id_utilizador'] ?>" <?= ($prof['id_utilizador'] == $projeto_ativo['id_professor']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($prof['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Guardar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- GRELHA DE QUADROS -->
        <div class="row g-4">
            
            <!-- 1. TAREFAS -->
            <div class="col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-secondary text-white"><h5 class="m-0">Tarefas</h5></div>
                    <div class="card-body">
                        <!-- Form Adicionar Tarefa -->
                        <form method="POST" class="mb-3 border p-2 rounded bg-light">
                            <input type="hidden" name="nova_tarefa" value="1">
                            <input type="hidden" name="id_projeto" value="<?= $projeto_ativo['id_projeto'] ?>">
                            <div class="mb-2"><input type="text" name="titulo" class="form-control" placeholder="Nova tarefa" required></div>
                            <div class="mb-2"><textarea name="descricao" class="form-control" placeholder="Descrição da tarefa" rows="2"></textarea></div>
                            <div class="row g-2 mb-2">
                                <div class="col-6">
                                    <select name="estado" class="form-select">
                                        <option value="Pendente">Pendente</option>
                                        <option value="Em Progresso">Em Progresso</option>
                                        <option value="Concluída">Concluída</option>
                                    </select>
                                </div>
                                <div class="col-6"><input type="date" name="data_final" class="form-control"></div>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary w-100">+ Adicionar Tarefa</button>
                        </form>

                        <!-- Lista de Tarefas -->
                        <ul class="list-group">
                            <?php foreach ($tarefas as $t): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="me-auto">
                                        <strong><?= htmlspecialchars($t['titulo']) ?></strong>
                                        <span class="badge bg-info text-dark ms-2"><?= htmlspecialchars($t['estado']) ?></span>
                                        <?php if ($t['descricao']): ?><br><small class="text-muted"><?= htmlspecialchars($t['descricao']) ?></small><?php endif; ?>
                                        <?php if ($t['data_final']): ?><br><small class="text-danger">Data limite: <?= htmlspecialchars($t['data_final']) ?></small><?php endif; ?>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editTask<?= $t['id_tarefa'] ?>">✏️</button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Apagar esta tarefa?');">
                                            <input type="hidden" name="apagar_tarefa" value="1">
                                            <input type="hidden" name="id_projeto" value="<?= $projeto_ativo['id_projeto'] ?>">
                                            <input type="hidden" name="id_tarefa" value="<?= $t['id_tarefa'] ?>">
                                            <button type="submit" class="btn btn-outline-danger">🗑️</button>
                                        </form>
                                    </div>
                                </li>

                                <!-- Modal Editar Tarefa -->
                                <div class="modal fade" id="editTask<?= $t['id_tarefa'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Tarefa</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="editar_tarefa" value="1">
                                                    <input type="hidden" name="id_projeto" value="<?= $projeto_ativo['id_projeto'] ?>">
                                                    <input type="hidden" name="id_tarefa" value="<?= $t['id_tarefa'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Título</label>
                                                        <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($t['titulo']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Descrição</label>
                                                        <textarea name="descricao" class="form-control" rows="2"><?= htmlspecialchars($t['descricao']) ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Estado</label>
                                                        <select name="estado" class="form-select">
                                                            <option value="Pendente" <?= ($t['estado'] === 'Pendente') ? 'selected' : '' ?>>Pendente</option>
                                                            <option value="Em Progresso" <?= ($t['estado'] === 'Em Progresso') ? 'selected' : '' ?>>Em Progresso</option>
                                                            <option value="Concluída" <?= ($t['estado'] === 'Concluída') ? 'selected' : '' ?>>Concluída</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Data Limite</label>
                                                        <input type="date" name="data_final" class="form-control" value="<?= htmlspecialchars($t['data_final']) ?>">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 2. REGISTO DIÁRIO -->
            <div class="col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-secondary text-white"><h5 class="m-0">Registo Diário</h5></div>
                    <div class="card-body">
                        <!-- Form Adicionar Diário -->
                        <form method="POST" class="mb-3 border p-2 rounded bg-light">
                            <input type="hidden" name="novo_diario" value="1">
                            <input type="hidden" name="id_projeto" value="<?= $projeto_ativo['id_projeto'] ?>">
                            <div class="mb-2"><textarea name="descricao" class="form-control" placeholder="Resumo do trabalho realizado hoje..." rows="2" required></textarea></div>
                            <div class="mb-2"><input type="date" name="data" class="form-control" value="<?= date('Y-m-d') ?>"></div>
                            <button type="submit" class="btn btn-sm btn-primary w-100">+ Adicionar Nota</button>
                        </form>

                        <!-- Lista de Diários -->
                        <ul class="list-group">
                            <?php foreach ($diarios as $d): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="me-auto">
                                        <small class="text-muted"><strong><?= htmlspecialchars($d['data']) ?></strong></small>
                                        <p class="mb-0"><?= htmlspecialchars($d['descricao']) ?></p>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editDiario<?= $d['id_registo'] ?>">✏️</button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Apagar este registo?');">
                                            <input type="hidden" name="apagar_diario" value="1">
                                            <input type="hidden" name="id_projeto" value="<?= $projeto_ativo['id_projeto'] ?>">
                                            <input type="hidden" name="id_registo" value="<?= $d['id_registo'] ?>">
                                            <button type="submit" class="btn btn-outline-danger">🗑️</button>
                                        </form>
                                    </div>
                                </li>

                                <!-- Modal Editar Diário -->
                                <div class="modal fade" id="editDiario<?= $d['id_registo'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Registo Diário</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="editar_diario" value="1">
                                                    <input type="hidden" name="id_projeto" value="<?= $projeto_ativo['id_projeto'] ?>">
                                                    <input type="hidden" name="id_registo" value="<?= $d['id_registo'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Data</label>
                                                        <input type="date" name="data" class="form-control" value="<?= htmlspecialchars($d['data']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Descrição</label>
                                                        <textarea name="descricao" class="form-control" rows="3" required><?= htmlspecialchars($d['descricao']) ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 3. RECURSOS E LINKS -->
            <div class="col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-secondary text-white"><h5 class="m-0">Recursos e Links</h5></div>
                    <div class="card-body">
                        <!-- Form Adicionar Recurso -->
                        <form method="POST" class="mb-3 border p-2 rounded bg-light">
                            <input type="hidden" name="novo_recurso" value="1">
                            <input type="hidden" name="id_projeto" value="<?= $projeto_ativo['id_projeto'] ?>">
                            <div class="mb-2"><input type="text" name="titulo" class="form-control" placeholder="Título do recurso" required></div>
                            <div class="mb-2"><input type="url" name="link" class="form-control" placeholder="https://..." required></div>
                            <div class="mb-2"><input type="text" name="descricao" class="form-control" placeholder="Descrição rápida"></div>
                            <button type="submit" class="btn btn-sm btn-primary w-100">+ Guardar Recurso</button>
                        </form>

                        <!-- Lista de Recursos -->
                        <ul class="list-group">
                            <?php foreach ($recursos as $r): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-start">
                                    <div class="me-auto">
                                        <a href="<?= htmlspecialchars($r['link']) ?>" target="_blank"><strong><?= htmlspecialchars($r['titulo']) ?></strong></a>
                                        <?php if ($r['descricao']): ?><p class="mb-0 text-muted"><small><?= htmlspecialchars($r['descricao']) ?></small></p><?php endif; ?>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editRecurso<?= $r['id_recurso'] ?>">✏️</button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('Apagar este recurso?');">
                                            <input type="hidden" name="apagar_recurso" value="1">
                                            <input type="hidden" name="id_projeto" value="<?= $projeto_ativo['id_projeto'] ?>">
                                            <input type="hidden" name="id_recurso" value="<?= $r['id_recurso'] ?>">
                                            <button type="submit" class="btn btn-outline-danger">🗑️</button>
                                        </form>
                                    </div>
                                </li>

                                <!-- Modal Editar Recurso -->
                                <div class="modal fade" id="editRecurso<?= $r['id_recurso'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Editar Recurso</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="editar_recurso" value="1">
                                                    <input type="hidden" name="id_projeto" value="<?= $projeto_ativo['id_projeto'] ?>">
                                                    <input type="hidden" name="id_recurso" value="<?= $r['id_recurso'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Título</label>
                                                        <input type="text" name="titulo" class="form-control" value="<?= htmlspecialchars($r['titulo']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Link</label>
                                                        <input type="url" name="link" class="form-control" value="<?= htmlspecialchars($r['link']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Descrição</label>
                                                        <input type="text" name="descricao" class="form-control" value="<?= htmlspecialchars($r['descricao']) ?>">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary">Guardar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- 4. FEEDBACK DO PROFESSOR -->
            <div class="col-md-6">
                <div class="card shadow h-100">
                    <div class="card-header bg-secondary text-white"><h5 class="m-0">Feedback do Professor</h5></div>
                    <div class="card-body">
                        <?php if (empty($feedbacks)): ?>
                            <p class="text-muted">Ainda não existe feedback registado para este projeto.</p>
                        <?php else: ?>
                            <?php foreach ($feedbacks as $fb): ?>
                                <div class="border p-3 mb-2 rounded bg-white">
                                    <strong>Prof. <?= htmlspecialchars($fb['prof_nome']) ?></strong>
                                    <p class="mb-1 mt-1"><strong>Comentário:</strong> <?= htmlspecialchars($fb['comentario']) ?></p>
                                    <?php if ($fb['conclusao']): ?>
                                        <small class="text-success"><strong>Conclusão:</strong> <?= htmlspecialchars($fb['conclusao']) ?></small>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>

</div>
</body>
</html>