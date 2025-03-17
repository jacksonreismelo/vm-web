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

// Selecionar os links dos vídeos antes de excluir as correções
$sql_select_rec = "SELECT link FROM rec_atividades WHERE id_atividade = ?";
$stmt_select_rec = $conn->prepare($sql_select_rec);
if (!$stmt_select_rec) {
    die("Prepare failed: " . $conn->error);
}
$stmt_select_rec->bind_param("i", $id);
$stmt_select_rec->execute();
$result_select_rec = $stmt_select_rec->get_result();

$video_links = [];
if ($result_select_rec->num_rows > 0) {
    while ($row = $result_select_rec->fetch_assoc()) {
        $video_links[] = $row['link'];
    }
}
$stmt_select_rec->close();

// Excluir correções associadas à atividade
$sql_del_rec = "DELETE FROM rec_atividades WHERE id_atividade = ?";
$stmt_del_rec = $conn->prepare($sql_del_rec);
if (!$stmt_del_rec) {
    die("Prepare failed: " . $conn->error);
}
$stmt_del_rec->bind_param("i", $id);
$stmt_del_rec->execute();
$stmt_del_rec->close();

// Excluir atividade
$sql_del_atividade = "DELETE FROM atividades WHERE id = ?";
$stmt_del_atividade = $conn->prepare($sql_del_atividade);
if (!$stmt_del_atividade) {
    die("Prepare failed: " . $conn->error);
}
$stmt_del_atividade->bind_param("i", $id);
$stmt_del_atividade->execute();
$stmt_del_atividade->close();

// Excluir os arquivos de vídeo do armazenamento
foreach ($video_links as $video_link) {
    $file_path = str_replace('https://vm.jacks4995.c44.integrator.host/', './', $video_link);
    if (file_exists($file_path)) {
        unlink($file_path);
    }
}

$conn->close();

header("Location: alunos.php");
exit();
?>
