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

$id = isset($_POST['id']) ? $_POST['id'] : null;
$aluno_id = $_POST['aluno_id'];
$id_atividade = $_POST['id_atividade'];
$carga = $_POST['carga'];
$repeticao = $_POST['repeticao'];
$serie = $_POST['serie'];
$cadencia = $_POST['cadencia'];
$dia = $_POST['dia'];
$data = DateTime::createFromFormat('d/m/Y', $_POST['data'])->format('Y-m-d');
$total = $carga * $repeticao;
$total_geral = $total * $serie;

if ($id) {
    $sql = "UPDATE atividades SET id_atividade = ?, carga = ?, repeticao = ?, serie = ?, cadencia = ?, dia = ?, total = ?, total_geral = ?, data = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiississi", $id_atividade, $carga, $repeticao, $serie, $cadencia, $dia, $total, $total_geral, $data, $id);
} else {
    $sql = "INSERT INTO atividades (aluno_id, id_atividade, carga, repeticao, serie, cadencia, dia, total, total_geral, data) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiiississs", $aluno_id, $id_atividade, $carga, $repeticao, $serie, $cadencia, $dia, $total, $total_geral, $data);
}

if ($stmt->execute()) {
    header("Location: aluno_detalhes.php?id=$aluno_id");
} else {
    echo "Erro ao salvar a atividade: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>