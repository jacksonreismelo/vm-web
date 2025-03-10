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
$sql = "SELECT a.*, am.nome AS atividade_nome, am.imagem 
        FROM atividades a 
        LEFT JOIN atividades_maquinas am ON a.id_atividade = am.id 
        WHERE a.id = ?";
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

$atividade = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Atividade</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="css/logo.png" type="image/png">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="images/logo_inicial.png" alt="Logo" class="logo">
            <h1>Detalhes da Atividade</h1>
            <a href="logout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
        <div class="menu">
            <a href="dashboard.php" class="menu-item"><i class="fas fa-home"></i> Início</a>
            <a href="alunos.php" class="menu-item"><i class="fas fa-users"></i> Meus Alunos</a>
        </div>
        <div class="content">
            <h2><i class="fas fa-dumbbell"></i> <?php echo htmlspecialchars($atividade['atividade_nome']); ?></h2>
            <p><strong><i class="fas fa-weight-hanging"></i> Carga:</strong> <?php echo htmlspecialchars($atividade['carga']); ?> kg</p>
            <p><strong><i class="fas fa-sync-alt"></i> Repetição:</strong> <?php echo htmlspecialchars($atividade['repeticao']); ?></p>
            <p><strong><i class="fas fa-layer-group"></i> Série:</strong> <?php echo htmlspecialchars($atividade['serie']); ?></p>
            <p><strong><i class="fas fa-stopwatch"></i> Cadência:</strong> <?php echo htmlspecialchars($atividade['cadencia']); ?></p>
            <p><strong><i class="fas fa-image"></i> Imagem:</strong> <img src="<?php echo htmlspecialchars($atividade['imagem']); ?>" alt="Imagem da Atividade" class="atividade-detalhe-img"></p>
            <p><strong><i class="fas fa-equals"></i> Total:</strong> <?php echo htmlspecialchars($atividade['total']); ?></p>
            <p><strong><i class="fas fa-calculator"></i> Total Geral:</strong> <span class="total-geral"><?php echo htmlspecialchars($atividade['total_geral']); ?></span></p>
            <a href="editar_atividade.php?id=<?php echo $atividade['id']; ?>" class="edit-button"><i class="fas fa-edit"></i> Editar</a>
            <a href="excluir_atividade.php?id=<?php echo $atividade['id']; ?>" class="delete-button" onclick="return confirm('Tem certeza que deseja excluir esta atividade?');"><i class="fas fa-trash-alt"></i> Excluir</a>
            <a href="aluno_detalhes.php?id=<?php echo $atividade['aluno_id']; ?>" class="back-button"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
</body>
</html>