<?php
    session_start();
    require '../config.php';
    require '../DBFunctions.php';

    $conn = DbConect();
    if($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
         // Método seguro usando prepared statements
         $query = "SELECT email AS [Mail], senha AS [pass],nome AS [nomeUsuario] FROM dados.stakeholdersPessoa WHERE email = '$username'";
         $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");

         if(DbNumRows($result) > 0 ){
            extract(DBFetchAssoc($result));
            $hashedPassword = hash('sha256', $password);
            if((string)trim(strtoupper($pass)) == (string)trim(strtoupper($hashedPassword))){                
                session_regenerate_id(true);
                $_SESSION['username'] = $username;
                $_SESSION['def']['usuNome'] = $nomeUsuario;
                $_SESSION['nameUser'] = $nomeUsuario;
                $_SESSION['loggedin'] = true;
                $_SESSION['login_time'] = time();
                
                header('Location: ../home.html');
                exit();
            }else{
                header('Location: ../login.html?1');
                exit(); 
            }
         }else{
            header('Location: ../login.html?2');
            exit();
         }
    }else{
       
    }
?>