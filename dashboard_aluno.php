<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.html");
    exit();
}

$aluno = $_SESSION['user'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Aluno</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="images/logo_inicial.png" alt="Logo" class="logo">
            <h1>Bem-vindo, <?php echo htmlspecialchars($aluno['nome']); ?></h1>
            <a href="logout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
        <div class="menu">
            <a href="atividades.php" class="menu-item">Minhas Atividades</a>
            <a href="perfil.php" class="menu-item">Meu Perfil</a>
        </div>
        <div class="content">
            <h2>Dashboard Aluno</h2>
            <p>Aqui você pode ver suas atividades e atualizar seu perfil.</p>
        </div>
    </div>
</body>
</html>