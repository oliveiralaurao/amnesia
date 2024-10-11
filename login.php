<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            max-width: 400px;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            color: #007bff;
        }
        .form-control {
            border-radius: 50px;
            padding: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 50px;
            padding: 10px;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        p.text-center {
            margin-top: 20px;
            color: #6c757d;
        }
        a {
            color: #007bff;
        }
        a:hover {
            color: #0056b3;
        }
    </style>
    <title>Login - Jogo da Memória</title>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Login</h1>

        <!-- Bloco PHP para exibir o aviso, se existir -->
        <?php
        session_start();
        if (isset($_SESSION['aviso'])) {
            echo '<div class="alert alert-warning text-center" role="alert">' . $_SESSION['aviso'] . '</div>';
            unset($_SESSION['aviso']);
        }
        ?>

        <form method="post" action="back/loginVerifica.php" class="mb-4">
            <div class="form-group">
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Senha" required>
            </div>
            <button type="submit" name="entrar" class="btn btn-primary btn-block">Entrar</button>
        </form>
        <p class="text-center">Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>
    </div>
</body>
</html>
