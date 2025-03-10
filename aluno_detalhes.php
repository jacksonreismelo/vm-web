<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.html");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: alunos.php");
    exit();
}

$host = '15.235.9.156';
$user = 'jacks4995_admin';
$password = 'vm102030!';
$database = 'jacks4995_vm_db';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = $_GET['id'];
$sql = "SELECT * FROM alunos WHERE id = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    $conn->close();
    header("Location: alunos.php");
    exit();
}

$aluno = $result->fetch_assoc();
$stmt->close();

$sql_atividades = "SELECT a.*, am.nome AS atividade_nome, am.imagem, am.tipo 
                   FROM atividades a 
                   LEFT JOIN atividades_maquinas am ON a.id_atividade = am.id 
                   WHERE a.aluno_id = ?";
$stmt_atividades = $conn->prepare($sql_atividades);
if (!$stmt_atividades) {
    die("Prepare failed: " . $conn->error);
}
$stmt_atividades->bind_param("i", $id);
$stmt_atividades->execute();
$result_atividades = $stmt_atividades->get_result();

$atividades = [];
if ($result_atividades->num_rows > 0) {
    while ($row = $result_atividades->fetch_assoc()) {
        $atividades[] = $row;
    }
}

$stmt_atividades->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Aluno</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="images/logo_inicial.png" alt="Logo" class="logo">
            <h1>Detalhes do Aluno</h1>
            <a href="logout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
        <div class="menu">
            <a href="dashboard.php" class="menu-item"><i class="fas fa-home"></i> Início</a>
            <a href="alunos.php" class="menu-item active"><i class="fas fa-users"></i> Meus Alunos</a>
        </div>
        <div class="content">
            <h2><i class="fas fa-user"></i> <?php echo htmlspecialchars($aluno['nome']); ?></h2>
            <p><strong><i class="fas fa-user-circle"></i> Login:</strong> <?php echo htmlspecialchars($aluno['login']); ?></p>
            <p><strong><i class="fas fa-birthday-cake"></i> Idade:</strong> <?php echo htmlspecialchars($aluno['idade']); ?> anos</p>
            <p><strong><i class="fas fa-weight"></i> Peso:</strong> <?php echo htmlspecialchars($aluno['peso']); ?> kg</p>
            <p><strong><i class="fas fa-ruler-vertical"></i> Altura:</strong> <?php echo htmlspecialchars($aluno['altura']); ?> cm</p>
            <p><strong><i class="fas fa-bullseye"></i> Objetivo:</strong> <?php echo htmlspecialchars($aluno['objetivo']); ?></p>

            <h3><i class="fas fa-running"></i> Atividades</h3>
            <?php if (count($atividades) > 0): ?>
                <ul class="atividades-lista">
                    <?php foreach ($atividades as $atividade): ?>
                        <li class="atividade-frame">
                            <a href="atividade_detalhes.php?id=<?php echo $atividade['id']; ?>">
                                <div class="atividade-item">
                                    <img src="<?php echo htmlspecialchars($atividade['imagem']); ?>" alt="Imagem da Atividade" class="atividade-img-mini">
                                    <div class="atividade-info">
                                        <p class="atividade-nome"><?php echo htmlspecialchars($atividade['atividade_nome']); ?></p>
                                        <p class="dia"><i class="fas fa-calendar-day"></i> <?php echo htmlspecialchars($atividade['dia']); ?></p>
                                        <p class="data"><i class="fas fa-calendar-alt"></i> <?php echo date("d/m/Y", strtotime($atividade['data'])); ?></p>
                                        <p class="tipo"><i class="fas fa-dumbbell"></i> <?php echo htmlspecialchars($atividade['tipo']); ?></p>
                                    </div>
                                </div>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Nenhuma atividade encontrada para este aluno.</p>
            <?php endif; ?>

            <a href="alunos.php" class="back-button"><i class="fas fa-arrow-left"></i> Voltar</a>
            <a href="lancar_atividade.php?aluno_id=<?php echo $aluno['id']; ?>" class="launch-button"><i class="fas fa-plus-circle"></i> Lançar Atividades</a>
        </div>
    </div>
</body>
</html>