<?php
session_start(); 

require_once '../startup/connectBD.php';

// Recebendo e filtrando os dados do formulário
$nome_jogador = addslashes($_POST['username']);
$email = addslashes($_POST['email']);
$senha = addslashes($_POST['password']);

if (!empty($nome_jogador) && !empty($email) && !empty($senha)) {
    $query_email_check = "SELECT * FROM jogadores WHERE email_jogador = '$email'";
    $result_email = $mysqli->query($query_email_check);
    
    $query_username_check = "SELECT * FROM jogadores WHERE nome_jogador = '$nome_jogador'";
    $result_username = $mysqli->query($query_username_check);
    
    if ($result_email->num_rows > 0) {
        $_SESSION['aviso'] = "Este e-mail já está cadastrado. Tente outro.";
        header("Location: ../cadastrar.php"); 
        exit();
    } elseif ($result_username->num_rows > 0) {
        $_SESSION['aviso'] = "Este nome de usuário já está em uso. Escolha outro.";
        header("Location: ../cadastrar.php"); 
        exit();
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        $query = "INSERT INTO jogadores (`id_jogador`, `nome_jogador`, `email_jogador`, `senha_jogador`) 
                  VALUES (null, '$nome_jogador', '$email', '$senha_hash')";

        if ($mysqli->query($query)) {
            $_SESSION['aviso'] = "Cadastro realizado com sucesso! Você pode fazer login agora.";
            header("Location: ../login.php");
            exit();
        } else {
            $_SESSION['aviso'] = "Erro ao inserir registro: " . $mysqli->error;
            header("Location: ../cadastrar.php"); 
            exit();
        }
    }
} else {
    $_SESSION['aviso'] = "Preencha todos os campos!";
    header("Location: ../cadastrar.php");
    exit();
}
?>
