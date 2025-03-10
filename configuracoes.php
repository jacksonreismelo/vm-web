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

// Conexão com o banco de dados
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lógica para alterar a senha dos alunos
if (isset($_POST['alterar_senha'])) {
    $aluno_id = $_POST['aluno_id'];
    $nova_senha = password_hash($_POST['nova_senha'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE alunos SET senha = ? WHERE id = ?");
    $stmt->bind_param("si", $nova_senha, $aluno_id);
    $stmt->execute();
    $stmt->close();
    echo "Senha alterada com sucesso!";
}

// Lógica para adicionar/editar atividades/máquinas
if (isset($_POST['salvar_atividade'])) {
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $tipo = $_POST['tipo'];
    $imagem_path = '';

    // Verifica se um arquivo foi enviado
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['imagem']['tmp_name'];
        $fileName = $_FILES['imagem']['name'];
        $fileSize = $_FILES['imagem']['size'];
        $fileType = $_FILES['imagem']['type'];
        $fileNameCmps = pathinfo($fileName, PATHINFO_FILENAME);
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = $fileNameCmps . '_' . time() . '.' . $fileExtension;
        $uploadFileDir = './images/exercicios/';
        $dest_path = $uploadFileDir . $newFileName;

        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $imagem_path = 'https://vm.jacks4995.c44.integrator.host/images/exercicios/' . $newFileName;
        } else {
            echo "Houve um erro ao enviar o arquivo.";
        }
    }

    if ($id) {
        if (!empty($imagem_path)) {
            $stmt = $conn->prepare("UPDATE atividades_maquinas SET nome = ?, imagem = ?, tipo = ? WHERE id = ?");
            $stmt->bind_param("sssi", $nome, $imagem_path, $tipo, $id);
        } else {
            $stmt->prepare("UPDATE atividades_maquinas SET nome = ?, tipo = ? WHERE id = ?");
            $stmt->bind_param("ssi", $nome, $tipo, $id);
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO atividades_maquinas (nome, imagem, tipo) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nome, $imagem_path, $tipo);
    }
    $stmt->execute();
    $stmt->close();
    echo "Atividade/Máquina salva com sucesso!";
}

// Seleção de todas as atividades/máquinas
$result = $conn->query("SELECT * FROM atividades_maquinas");
$atividades_maquinas = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $atividades_maquinas[] = $row;
    }
}

// Seleção de todos os alunos
$result_alunos = $conn->query("SELECT id, nome FROM alunos");
$alunos = [];
if ($result_alunos->num_rows > 0) {
    while ($row = $result_alunos->fetch_assoc()) {
        $alunos[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="icon" href="css/logo.png" type="image/png">
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="images/logo_inicial.png" alt="Logo" class="logo">
            <h1>Configurações</h1>
            <a href="logout.php" class="logout-button"><i class="fas fa-sign-out-alt"></i> Sair</a>
        </div>
        <div class="menu">
            <a href="dashboard.php" class="menu-item"><i class="fas fa-home"></i> Início</a>
            <a href="alunos.php" class="menu-item"><i class="fas fa-users"></i> Meus Alunos</a>
            <a href="configuracoes.php" class="menu-item active"><i class="fas fa-cog"></i> Configurações</a>
        </div>
        <div class="content">
            <h2><i class="fas fa-key"></i> Alterar Senha dos Alunos</h2>
            <form method="POST" action="configuracoes.php" class="alterar-senha-form">
                <div class="form-group">
                    <label for="aluno_id">Selecionar Aluno:</label>
                    <select id="aluno_id" name="aluno_id" required>
                        <?php foreach ($alunos as $aluno): ?>
                            <option value="<?php echo $aluno['id']; ?>"><?php echo htmlspecialchars($aluno['nome']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="nova_senha">Nova Senha:</label>
                    <input type="password" id="nova_senha" name="nova_senha" required>
                </div>
                <button type="submit" name="alterar_senha"><i class="fas fa-exchange-alt"></i> Alterar Senha</button>
            </form>

            <h2><i class="fas fa-dumbbell"></i> Gerenciar Atividades/Máquinas</h2>
            <form method="POST" action="configuracoes.php" enctype="multipart/form-data" class="atividade-form">
                <input type="hidden" id="id" name="id">
                <div class="form-group">
                    <label for="nome">Nome:</label>
                    <input type="text" id="nome" name="nome" required>
                </div>
                <div class="form-group">
                    <label for="imagem">Imagem:</label>
                    <input type="file" id="imagem" name="imagem">
                </div>
                <div class="form-group">
                    <label for="tipo">Tipo:</label>
                    <select id="tipo" name="tipo" required>
                        <option value="Atividade">Atividade</option>
                        <option value="Maquina">Máquina</option>
                    </select>
                </div>
                <button type="submit" name="salvar_atividade"><i class="fas fa-save"></i> Salvar</button>
            </form>

            <h2><i class="fas fa-list"></i> Atividades/Máquinas</h2>
            <ul class="atividades-lista">
                <?php foreach ($atividades_maquinas as $atividade_maquina): ?>
                    <li>
                        <p><strong>Nome:</strong> <?php echo htmlspecialchars($atividade_maquina['nome']); ?></p>
                        <p><strong>Tipo:</strong> <?php echo htmlspecialchars($atividade_maquina['tipo']); ?></p>
                        <p><strong>Imagem:</strong> <img src="<?php echo htmlspecialchars($atividade_maquina['imagem']); ?>" alt="Imagem da Atividade/Máquina" class="atividade-img-mini"></p>
                        <a href="manage_activities.php?id=<?php echo $atividade_maquina['id']; ?>" class="edit-button"><i class="fas fa-edit"></i> Editar</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</body>
</html>