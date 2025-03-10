<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.html");
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

$sql = "SELECT * FROM alunos";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Alunos</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="images/logo_inicial.png" alt="Logo" class="logo">
            <h1>Meus Alunos</h1>
            <a href="logout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
        <div class="menu">
            <a href="dashboard.php" class="menu-item"><i class="fas fa-home"></i> Início</a>
            <a href="alunos.php" class="menu-item active"><i class="fas fa-users"></i> Meus Alunos</a>
        </div>
        <div class="content">
            <h2>Lista de Alunos</h2>
            <ul class="aluno-lista">
                <?php while($row = $result->fetch_assoc()): ?>
                    <li>
                        <a href="aluno_detalhes.php?id=<?php echo $row['id']; ?>">
                            <i class="fas fa-user"></i> <?php echo htmlspecialchars($row['nome']); ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>