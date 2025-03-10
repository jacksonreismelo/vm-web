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
$sql = "SELECT * FROM atividades WHERE id = ?";
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
    <title>Editar Atividade</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="css/logo.png" type="image/png">
    <script>
        function calcularTotal() {
            var carga = document.getElementById('carga').value;
            var repeticao = document.getElementById('repeticao').value;
            var serie = document.getElementById('serie').value;
            var total = carga * repeticao;
            var total_geral = total * serie;
            document.getElementById('total').value = total;
            document.getElementById('total_geral').value = total_geral;
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="images/logo_inicial.png" alt="Logo" class="logo">
            <h1>Editar Atividade</h1>
            <a href="logout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
        <div class="menu">
            <a href="dashboard.php" class="menu-item"><i class="fas fa-home"></i> Início</a>
            <a href="alunos.php" class="menu-item"><i class="fas fa-users"></i> Meus Alunos</a>
        </div>
        <div class="content">
            <form method="POST" action="salvar_atividade.php" class="atividade-form">
                <input type="hidden" name="id" value="<?php echo $atividade['id']; ?>">
                <input type="hidden" name="aluno_id" value="<?php echo $atividade['aluno_id']; ?>">
                <div class="form-group">
                    <label for="data"><i class="fas fa-calendar"></i> Data:</label>
                    <input type="text" id="data" name="data" value="<?php echo date('d/m/Y', strtotime($atividade['data'])); ?>" required>
                </div>
                <div class="form-group">
                    <label for="dia"><i class="fas fa-calendar-day"></i> Dia:</label>
                    <select id="dia" name="dia" required>
                        <option value="Segunda-Feira" <?php echo ($atividade['dia'] == 'Segunda-Feira') ? 'selected' : ''; ?>>Segunda-Feira</option>
                        <option value="Terça-Feira" <?php echo ($atividade['dia'] == 'Terça-Feira') ? 'selected' : ''; ?>>Terça-Feira</option>
                        <option value="Quarta-Feira" <?php echo ($atividade['dia'] == 'Quarta-Feira') ? 'selected' : ''; ?>>Quarta-Feira</option>
                        <option value="Quinta-Feira" <?php echo ($atividade['dia'] == 'Quinta-Feira') ? 'selected' : ''; ?>>Quinta-Feira</option>
                        <option value="Sexta-Feira" <?php echo ($atividade['dia'] == 'Sexta-Feira') ? 'selected' : ''; ?>>Sexta-Feira</option>
                        <option value="Sábado" <?php echo ($atividade['dia'] == 'Sábado') ? 'selected' : ''; ?>>Sábado</option>
                        <option value="Domingo" <?php echo ($atividade['dia'] == 'Domingo') ? 'selected' : ''; ?>>Domingo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="id_atividade"><i class="fas fa-dumbbell"></i> Atividade:</label>
                    <select id="id_atividade" name="id_atividade" required>
                        <?php foreach ($atividades_maquinas as $atividade_maquina): ?>
                            <option value="<?php echo $atividade_maquina['id']; ?>" <?php echo ($atividade['id_atividade'] == $atividade_maquina['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($atividade_maquina['nome']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="carga"><i class="fas fa-weight-hanging"></i> Carga:</label>
                    <input type="number" id="carga" name="carga" value="<?php echo $atividade['carga']; ?>" oninput="calcularTotal()" required>
                </div>
                <div class="form-group">
                    <label for="repeticao"><i class="fas fa-sync-alt"></i> Repetição:</label>
                    <input type="number" id="repeticao" name="repeticao" value="<?php echo $atividade['repeticao']; ?>" oninput="calcularTotal()" required>
                </div>
                <div class="form-group">
                    <label for="serie"><i class="fas fa-layer-group"></i> Série:</label>
                    <input type="number" id="serie" name="serie" value="<?php echo $atividade['serie']; ?>" oninput="calcularTotal()" required>
                </div>
                <div class="form-group">
                    <label for="cadencia"><i class="fas fa-stopwatch"></i> Cadência:</label>
                    <select id="cadencia" name="cadencia" required>
                        <option value="Lenta" <?php echo ($atividade['cadencia'] == 'Lenta') ? 'selected' : ''; ?>>Lenta</option>
                        <option value="Moderada" <?php echo ($atividade['cadencia'] == 'Moderada') ? 'selected' : ''; ?>>Moderada</option>
                        <option value="Rápida" <?php echo ($atividade['cadencia'] == 'Rápida') ? 'selected' : ''; ?>>Rápida</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="total"><i class="fas fa-equals"></i> Total:</label>
                    <input type="number" id="total" name="total" value="<?php echo $atividade['total']; ?>" readonly required>
                </div>
                <div class="form-group">
                    <label for="total_geral"><i class="fas fa-calculator"></i> Total Geral:</label>
                    <input type="number" id="total_geral" name="total_geral" value="<?php echo $atividade['total_geral']; ?>" class="total-geral" readonly required>
                </div>
                <button type="submit" class="save-button"><i class="fas fa-save"></i> Salvar</button>
            </form>
            <a href="aluno_detalhes.php?id=<?php echo $atividade['aluno_id']; ?>" class="back-button"><i class="fas fa-arrow-left"></i> Voltar</a>