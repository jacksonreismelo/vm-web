<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: index.html");
    exit();
}

if (!isset($_GET['aluno_id'])) {
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

$aluno_id = $_GET['aluno_id'];

// Seleção de todas as atividades/máquinas
$result_atividades_maquinas = $conn->query("SELECT * FROM atividades_maquinas");
$atividades_maquinas = [];
if ($result_atividades_maquinas->num_rows > 0) {
    while ($row = $result_atividades_maquinas->fetch_assoc()) {
        $atividades_maquinas[] = $row;
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lançar Atividade</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="images/logo_inicial.png" alt="Logo" class="logo">
            <h1>Lançar Atividade</h1>
            <a href="logout.php" class="logout-button">Sair</a>
        </div>
        <div class="menu">
            <a href="dashboard.php" class="menu-item">Início</a>
            <a href="alunos.php" class="menu-item">Meus Alunos</a>
        </div>
        <div class="content">
            <form method="POST" action="salvar_atividade.php" class="atividade-form">
                <input type="hidden" name="aluno_id" value="<?php echo $aluno_id; ?>">
                <div class="form-group">
                    <label for="id_atividade">Atividade:</label>
                    <select id="id_atividade" name="id_atividade" required>
                        <?php foreach ($atividades_maquinas as $atividade_maquina): ?>
                            <option value="<?php echo $atividade_maquina['id']; ?>">
                                <?php echo htmlspecialchars($atividade_maquina['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="repeticao">Repetição:</label>
                    <input type="number" id="repeticao" name="repeticao" required>
                </div>
                <div class="form-group">
                    <label for="data">Data:</label>
                    <input type="date" id="data" name="data" required>
                </div>
                <div class="form-group">
                    <label for="dia">Dia:</label>
                    <select id="dia" name="dia" required>
                        <option value="Segunda-Feira">Segunda-Feira</option>
                        <option value="Terça-Feira">Terça-Feira</option>
                        <option value="Quarta-Feira">Quarta-Feira</option>
                        <option value="Quinta-Feira">Quinta-Feira</option>
                        <option value="Sexta-Feira">Sexta-Feira</option>
                        <option value="Sábado">Sábado</option>
                        <option value="Domingo">Domingo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="serie">Série:</label>
                    <input type="number" id="serie" name="serie" required>
                </div>
                <div class="form-group">
                    <label for="carga">Carga:</label>
                    <input type="number" id="carga" name="carga" required>
                </div>
                <div class="form-group">
                    <label for="total">Total:</label>
                    <input type="number" id="total" name="total" required>
                </div>
                <div class="form-group">
                    <label for="total_geral">Total Geral:</label>
                    <input type="number" id="total_geral" name="total_geral" required>
                </div>
                <button type="submit">Salvar</button>
            </form>
            <a href="aluno_detalhes.php?id=<?php echo $aluno_id; ?>" class="back-button">Voltar</a>
        </div>
    </div>
</body>
</html>