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
$sql_rec = "SELECT id, link, observacoes, status FROM rec_atividades WHERE id_atividade = ?";
$stmt_rec = $conn->prepare($sql_rec);
if (!$stmt_rec) {
    die("Prepare failed: " . $conn->error);
}
$stmt_rec->bind_param("i", $id);
$stmt_rec->execute();
$result_rec = $stmt_rec->get_result();

$videos = [];
if ($result_rec->num_rows > 0) {
    while ($row = $result_rec->fetch_assoc()) {
        $videos[] = $row;
    }
}

$stmt_rec->close();
$conn->close();

function obterDiaDaSemana($data) {
    $diasDaSemana = ['Domingo', 'Segunda-Feira', 'Terça-Feira', 'Quarta-Feira', 'Quinta-Feira', 'Sexta-Feira', 'Sábado'];
    $timestamp = strtotime($data);
    $diaSemana = date('w', $timestamp);
    return $diasDaSemana[$diaSemana];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Atividade</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .video-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }
        .video-item {
            position: relative;
            width: 100%;
            max-width: 320px;
        }
        .video-item video {
            width: 100%;
            border-radius: 10px;
            border: 3px solid;
        }
        .video-item.aprovado video {
            border-color: green;
        }
        .video-item.corrigir video {
            border-color: yellow;
        }
        .tag-aprovado, .tag-corrigir {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
            color: white;
        }
        .tag-aprovado {
            background-color: green;
        }
        .tag-corrigir {
            background-color: yellow;
            color: black;
        }
        .observacoes {
            margin-top: 5px;
            font-size: 14px;
            color: #555;
        }
        .select-status {
            margin-top: 10px;
        }
         .logo {
            float: left;
            margin-right: 20px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <button class="menu-toggle" onclick="toggleMenu()"><i class="fas fa-bars"></i> Menu</button>
                <img src="images/logo_inicial.png" alt="Logo" class="logo">
            </div>
           
        </div>
         <?php include 'menu.php'; ?>
        <div class="content">
            <h2><?php echo htmlspecialchars($atividade['atividade_nome']); ?></h2>
            <p><i class="fas fa-calendar-alt"></i> <strong>Data:</strong> <?php echo date("d/m/Y", strtotime($atividade['data'])); ?></p>
            <p><i class="fas fa-calendar-day"></i> <strong>Dia:</strong> <?php echo obterDiaDaSemana($atividade['data']); ?></p>
            <p><i class="fas fa-dumbbell"></i> <strong>Tipo:</strong> <?php echo htmlspecialchars($atividade['tipo']); ?></p>
            <p><i class="fas fa-weight-hanging"></i> <strong>Carga:</strong> <?php echo htmlspecialchars($atividade['carga']); ?></p>
            <p><i class="fas fa-sync-alt"></i> <strong>Repetição:</strong> <?php echo htmlspecialchars($atividade['repeticao']); ?></p>
            <p><i class="fas fa-layer-group"></i> <strong>Série:</strong> <?php echo htmlspecialchars($atividade['serie']); ?></p>
            <p><i class="fas fa-stopwatch"></i> <strong>Cadência:</strong> <?php echo htmlspecialchars($atividade['cadencia']); ?></p>
            <img src="<?php echo htmlspecialchars($atividade['imagem']); ?>" alt="Imagem da Atividade" class="atividade-detalhe-img">
            <p><i class="fas fa-equals"></i> <strong>Total:</strong> <?php echo htmlspecialchars($atividade['total']); ?></p>
            <p><i class="fas fa-calculator"></i> <strong>Total Geral:</strong> <?php echo htmlspecialchars($atividade['total_geral']); ?></p>
            
            <h3>Execução da Atividade</h3>
            <?php if (!empty($videos)): ?>
                <div class="video-container">
                    <?php foreach ($videos as $video): ?>
                        <div class="video-item <?php echo $video['status'] === 'aprovado' ? 'aprovado' : 'corrigir'; ?>">
                            <video class="responsive-video" controls>
                                <source src="<?php echo htmlspecialchars($video['link']); ?>" type="video/mp4">
                                Seu navegador não suporta o elemento de vídeo.
                            </video>
                            <div class="observacoes"><?php echo htmlspecialchars($video['observacoes']); ?></div>
                            <span class="tag-<?php echo $video['status'] === 'aprovado' ? 'aprovado' : 'corrigir'; ?>">
                                <?php echo $video['status'] === 'aprovado' ? 'Corrigido!' : 'A Corrigir'; ?>
                            </span>
                            <div class="select-status">
                                <form method="post" action="atualizar_status.php">
                                    <input type="hidden" name="id_video" value="<?php echo $video['id']; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="corrigir" <?php echo $video['status'] === 'corrigir' ? 'selected' : ''; ?>>A Corrigir</option>
                                        <option value="aprovado" <?php echo $video['status'] === 'aprovado' ? 'selected' : ''; ?>>Aprovado</option>
                                    </select>
                                </form>
                            </div>
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
