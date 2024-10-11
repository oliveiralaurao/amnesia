<?php
session_start();
require_once '../startup/connectBD.php';

$email = addslashes($_POST['email']);
$senha = addslashes($_POST['password']);

if (!empty($email) && !empty($senha)) {
    $query = "SELECT * FROM jogadores WHERE email_jogador = '$email'";
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if (password_verify($senha, $user['senha_jogador'])) {
            $_SESSION['user_id'] = $user['id_jogador'];
            $_SESSION['user_name'] = $user['nome_jogador'];
            $_SESSION['aviso'] = "Bem-vindo, " . $user['nome_jogador'] . "!";
            
            header("Location: ../jogo.php");
            exit();
        } else {
            $_SESSION['aviso'] = "Senha incorreta. Tente novamente.";
            header("Location: ../login.php");
            exit();
        }
    } else {
        $_SESSION['aviso'] = "E-mail não cadastrado.";
        header("Location: ../login.php");
        exit();
    }
} else {
    $_SESSION['aviso'] = "Preencha todos os campos.";
    header("Location: ../login.php");
    exit();
}
?>
