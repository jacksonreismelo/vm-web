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
$sql = "SELECT a.*, am.nome AS atividade_nome, am.imagem, am.tipo 
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

// Seleção dos links dos vídeos da tabela rec_atividades
$sql_rec = "SELECT link FROM rec_atividades WHERE id_atividade = ? AND status = 'ativo'";
$stmt_rec = $conn->prepare($sql_rec);
if (!$stmt_rec) {
    die("Prepare failed: " . $conn->error);
}
$stmt_rec->bind_param("i", $id);
$stmt_rec->execute();
$result_rec = $stmt_rec->get_result();

$video_links = [];
if ($result_rec->num_rows > 0) {
    while ($row = $result_rec->fetch_assoc()) {
        $video_links[] = $row['link'];
    }
}

$stmt_rec->close();
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
    <script>
        function confirmarExclusao(id) {
            if (confirm("Tem certeza de que deseja excluir esta atividade e todas as correções associadas?")) {
                window.location.href = "excluir_atividade.php?id=" + id;
            }
        }
    </script>
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
            <h2><?php echo htmlspecialchars($atividade['atividade_nome']); ?></h2>
            <p><i class="fas fa-calendar-alt"></i> <strong>Data:</strong> <?php echo date("d/m/Y", strtotime($atividade['data'])); ?></p>
            <p><i class="fas fa-calendar-day"></i> <strong>Dia:</strong> <?php echo htmlspecialchars($atividade['dia']); ?></p>
            <p><i class="fas fa-dumbbell"></i> <strong>Tipo:</strong> <?php echo htmlspecialchars($atividade['tipo']); ?></p>
            <p><i class="fas fa-weight-hanging"></i> <strong>Carga:</strong> <?php echo htmlspecialchars($atividade['carga']); ?></p>
            <p><i class="fas fa-sync-alt"></i> <strong>Repetição:</strong> <?php echo htmlspecialchars($atividade['repeticao']); ?></p>
            <p><i class="fas fa-layer-group"></i> <strong>Série:</strong> <?php echo htmlspecialchars($atividade['serie']); ?></p>
            <p><i class="fas fa-stopwatch"></i> <strong>Cadência:</strong> <?php echo htmlspecialchars($atividade['cadencia']); ?></p>
            <img src="<?php echo htmlspecialchars($atividade['imagem']); ?>" alt="Imagem da Atividade" class="atividade-detalhe-img">
            <p><i class="fas fa-equals"></i> <strong>Total:</strong> <?php echo htmlspecialchars($atividade['total']); ?></p>
            <p><i class="fas fa-calculator"></i> <strong>Total Geral:</strong> <?php echo htmlspecialchars($atividade['total_geral']); ?></p>
            
            <h3>Execução da Atividade</h3>
            <?php if (!empty($video_links)): ?>
                <div class="video-container">
                    <?php foreach ($video_links as $video_link): ?>
                        <div class="video-item">
                            <video class="responsive-video" controls>
                                <source src="<?php echo htmlspecialchars($video_link); ?>" type="video/mp4">
                                Seu navegador não suporta o elemento de vídeo.
                            </video>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Nenhum vídeo disponível para esta atividade.</p>
            <?php endif; ?>

            <a href="editar_atividade.php?id=<?php echo $atividade['id']; ?>" class="edit-button"><i class="fas fa-edit"></i> Editar</a>
            <a href="lancar_correcao.php?id=<?php echo $atividade['id']; ?>" class="launch-button"><i class="fas fa-file-alt"></i> Lançar Correção</a>
            <button class="delete-button" onclick="confirmarExclusao(<?php echo $atividade['id']; ?>)"><i class="fas fa-trash"></i> Excluir Atividade</button>
            <a href="aluno_detalhes.php?id=<?php echo $atividade['aluno_id']; ?>" class="back-button"><i class="fas fa-arrow-left"></i> Voltar</a>
        </div>
    </div>
</body>
</html>