<?php
session_start();

$host = '15.235.9.156';
$user = 'jacks4995_admin';
$password = 'vm102030!';
$database = 'jacks4995_vm_db';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_POST['username'];
$password = $_POST['password'];
$user_type = $_POST['user_type'];

if ($user_type == 'professor') {
    $sql = "SELECT * FROM users WHERE login = ?";
} else {
    $sql = "SELECT * FROM alunos WHERE login = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['senha'])) {
        $_SESSION['user'] = $user;
        if ($user_type == 'professor') {
            header("Location: dashboard.php");
        } else {
            header("Location: dashboard_aluno.php");
        }
    } else {
        echo "<script>
                alert('Usuário ou senha inválidos');
                window.history.back();
              </script>";
    }
} else {
    echo "<script>
            alert('Usuário ou senha inválidos');
            window.history.back();
          </script>";
}

$stmt->close();
$conn->close();
?>
