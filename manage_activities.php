<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.html");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: configuracoes.php");
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
$sql = "SELECT * FROM atividades_maquinas WHERE id = ?";
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
    header("Location: configuracoes.php");
    exit();
}

$atividade_maquina = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Atividade/Máquina</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="css/logo.png" type="image/png">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="images/logo_inicial.png" alt="Logo" class="logo">
            <h1>Gerenciar Atividade/Máquina</h1>
            <a href="logout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
        <div class="menu">
            <a href="dashboard.php" class="menu-item"><i class="fas fa-home"></i> Início</a>
            <a href="alunos.php" class="menu-item"><i class="fas fa-users"></i> Meus Alunos</a>
            <a href="configuracoes.php" class="menu-item"><i class="fas fa-cog"></i> Configurações</a>
        </div>
        <div class="content">
            <form method="POST" action="configuracoes.php" enctype="multipart/form-data" class="atividade-form">
                <input type="hidden" id="id" name="id" value="<?php echo $atividade_maquina['id']; ?>">
                <div class="form-group">
                    <label for="nome"><i class="fas fa-font"></i> Nome:</label>
                    <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($atividade_maquina['nome']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="imagem"><i class="fas fa-image"></i> Imagem:</label>
                    <input type="file" id="imagem" name="imagem">
                </div>
                <div class="form-group">
                    <label for="tipo"><i class="fas fa-tag"></i> Tipo:</label>
                    <select id="tipo" name="tipo" required>
                        <option value="Atividade" <?php echo ($atividade_maquina['tipo'] == 'Atividade') ? 'selected' : ''; ?>>Atividade</option>
                        <option value="Maquina" <?php echo ($atividade_maquina['tipo'] == 'Maquina') ? 'selected' : ''; ?>>Máquina</option>
                    </select>
                </div>
                <button type="submit" name="salvar_atividade" class="save-button"><i class="fas fa-save"></i> Salvar</button>
            </form>
            <a href="configuracoes.php" class="back-button"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
</body>
</html>