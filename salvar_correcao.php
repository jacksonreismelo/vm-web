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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_atividade = $_POST['id_atividade'];
    $video_path = '';

    // Verifica se um arquivo foi enviado
    if (isset($_FILES['video']) && $_FILES['video']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['video']['tmp_name'];
        $fileName = $_FILES['video']['name'];
        $fileSize = $_FILES['video']['size'];
        $fileType = $_FILES['video']['type'];
        $fileNameCmps = pathinfo($fileName, PATHINFO_FILENAME);
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = $fileNameCmps . '_' . time() . '.' . $fileExtension;
        $uploadFileDir = './videos/alunos/';
        $dest_path = $uploadFileDir . $newFileName;

        if(move_uploaded_file($fileTmpPath, $dest_path)) {
            $video_path = 'https://vm.jacks4995.c44.integrator.host/videos/alunos/' . $newFileName;
        } else {
            echo "Houve um erro ao enviar o arquivo.";
            exit();
        }
    } else {
        echo "Arquivo não enviado ou ocorreu um erro.";
        exit();
    }

    if (!empty($video_path)) {
        $stmt = $conn->prepare("INSERT INTO rec_atividades (id_atividade, link, status) VALUES (?, ?, 'ativo')");
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("is", $id_atividade, $video_path);
        if ($stmt->execute() === false) {
            die("Execute failed: " . $stmt->error);
        }
        $stmt->close();
    }

    header("Location: atividade_detalhes.php?id=$id_atividade");
    exit();
}

$conn->close();
?>