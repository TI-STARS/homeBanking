<?php
    session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
        // Redirecionar para a página de login com mensagem
        header('Location: ../login.html?error=not_logged_in');
        exit(); // Importante para parar a execução
    }
    require '../config.php';
    require '../DBFunctions.php';
    require '../Functions.php';
    $conn = DbConect();
    $name_user = $_SESSION['nameUser'];
    $login = $_SESSION['username'];

    $query = "SELECT COUNT(*) as totalCNPJ FROM stakeholdersProspect WHERE usuario = '$login'";
    $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
    extract(DBFetchAssoc($result));

    $query = "SELECT COUNT(*) as totalCNPJAnalise FROM stakeholdersProspect WHERE usuario = '$login' AND cadastro_status='1'";
    $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
    extract(DBFetchAssoc($result));

    $query = "SELECT COUNT(*) as totalCNPJPOC FROM stakeholdersProspect WHERE usuario = '$login' AND cadastro_status='3'";
    $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
    extract(DBFetchAssoc($result));
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script src='../js/functions.js'></script>
    
    <title> Home Banking</title>
</head>
<body>
    <div class="toggle-sidebar">☰
    
    </div>
    <div class="sidebar">
        
        <div class="logo-container" style="text-align: center; width: 100%; margin-top: 10px;">
            <img src="../img/logo_scad_2.png" style="width: 75%; height: 75%;" alt="Logo" class="logo" >
        </div>
        <div class="sidebar-header">
            <h3>Bem vindo, <?=utf8_encode($name_user);?></h3>
            <div class="toggle-sidebar">
                <i class="fas fa-bars"></i>
            </div>
        </div>
        
        <ul class="sidebar-nav">
            <li class="active">
                <a href="home.php">
                    <i class="fas fa-home"></i>
                    <span class="material-symbols-outlined">home</span>&ensp;<span>Home </span>
                </a>
            </li>
            <li>
                <a href="cadastro_lead.php">
                    <i class="fas fa-chart-bar"></i>
                    <span class="material-symbols-outlined">description</span>&ensp;<span>Cadastro Lead</span>
                </a>
            </li>
            <li>
                <a href="dashboard.php">
                    <i class="fas fa-chart-bar"></i>
                    <span class="material-symbols-outlined">bar_chart</span>&ensp;<span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="logout.php">
                    <i class="fas fa-icons"></i>
                    <span class="material-symbols-outlined">exit_to_app</span>&ensp;<span>Sair</span>
                </a>
            </li> 
    </div>
 
    <div class="container">
        <div class="header">
            <h1>Sistema de Auto atendimento</h1>
            <div class="breadcrumb">Home</div>
        </div>
        <div class="cards-container">
            <div class="card">
                <div class="card-header">
                    <span class="card-title">CNPJ (CADASTRADOS)</span>
                    <span class="card-period"></span>
                </div>
                <div class="card-body">
                    <span class="card-value"><?=$totalCNPJ;?></span>
                    <span class="card-change increase"></span>
                </div>
            </div>
        
            <div class="card">
                <div class="card-header">
                    <span class="card-title">CNPJ (EM ANÁLISE)</span>
                    <span class="card-period"></span>
                </div>
                <div class="card-body">
                    <span class="card-value"><?=$totalCNPJAnalise;?></span>
                    <span class="card-change increase"></span>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <span class="card-title">CNPJ (POC GERADA)</span>
                    <span class="card-period"></span>
                </div>
                <div class="card-body">
                    <span class="card-value"><?=$totalCNPJPOC;?></span>
                    <span class="card-change decrease"></span>
                </div>
            </div>
            
        </div>
        <br>
        <div class="cadastro-section">
            <h3>
                Lista de empresas em analise &nbsp;
            </h3>
           
            <table>
                <thead>
                    <tr>
                        <th style="text-align: center;">Empresa</th>
                        <th style="text-align: center;">CNPJ</th>
                        <th style="text-align: center;">Data de cadastro</th>
                        <th style="text-align: center;">Documentos</th>
                        <th style="text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                        $query = "  SELECT * ,
                                        ISNULL(c.typeFile,0) as [typeFile],
                                        c.ID as [IDFile],
                                        ISNULL(c.fileProcessDate,0) as [fileProcessDate],
                                        ISNULL(C.fileProcess,0) AS [fileProcess] FROM stakeholdersProspect as a
                                    INNER JOIN stakeholders AS b ON a.cnpj = b.cnpj
                                    LEFT JOIN stakeholdersProspectFile AS c on c.IDProspect = a.ID
                        WHERE usuario = '$login' ORDER BY cadastro_status ASC";
                        //print($query);
                        $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
                        while($row = DBFetchAssoc($result)){

                            if($row['cadastro_status'] == '1'){
                                $status = 'Em análise';
                               
                            }else if($row['cadastro_status'] == '2'){
                                $status = 'Disponivel para gerar POC';
                               
                            }else if($row['cadastro_status'] == '3'){
                                $status = 'POC gerada';
                               
                            }else if($row['cadastro_status'] == '4'){
                                $status = 'Cadastro Rejeitado';
                            }

                            $typeFile = $row['typeFile'];
                            $fileProcess = $row['fileProcess'];
                            $file_summary = $row['typeResumo'];
                            $idFile = $row['IDFile'];

                            if($typeFile == 0 && $fileProcess == 0){
                                $fileIcon = '<span title="Existe um arquivo para ser processado" class="material-symbols-outlined">update</span>';
                            }else if($typeFile != 0 && $fileProcess != 0){
                                $fileIcon = '<span href="#" style="color:green;" title="'.utf8_encode($file_summary).'" onclick="openDoc('.$idFile.');" class="material-symbols-outlined">check_circle</span>';
                            }else{
                                $fileIcon = '<span href="#" style="color:red;" title="'.utf8_encode($file_summary).'" class="material-symbols-outlined">check_circle</span>';
                            }

                            $cnpj = $row['cnpj'];
                            $cnpj = formataCNPJ($cnpj);
                        ?>
                        <tr style="background-color: <?=$line_color;?>;">
                            <td style="vertical-align: middle; text-align: center;" ><?=$line_icon;?><?=$row['razao'];?></td>
                            <td style="vertical-align: middle; text-align: center;" ><?=$cnpj;?></td>
                            <td style="vertical-align: middle; text-align: center;" ><?=date('d/m/Y', strtotime($row['inputDate']));?></td>
                            <td style="vertical-align: middle; text-align: center;" ><?=$fileIcon;?></td>
                            <td style="vertical-align: middle; text-align: center;" ><?=$status;?></td>
                        </tr>
                        <?
                        }
                    ?>
                    </tbody>
            </table>
        </div>
    </div>
    <script>

            function openDoc(IDFile){
              
                window.open('viwerFile.php?IDFile='+IDFile,'Visualizar Documento',500,500);
            }

            document.addEventListener('DOMContentLoaded', function() {
            // Toggle sidebar em telas pequenas
            const toggleBtn = document.querySelector('.toggle-sidebar');
            const sidebar = document.querySelector('.sidebar');
                
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('active');
                });
            }

            // Marcar item ativo baseado na URL atual
            const currentPage = window.location.pathname.split('/').pop();
            const navLinks = document.querySelectorAll('.sidebar-nav li a');

            navLinks.forEach(link => {
                const linkPage = link.getAttribute('href');
                if (linkPage === currentPage) {
                    link.parentElement.classList.add('active');
                }

                // Remover classe active de outros itens
                link.addEventListener('click', function() {
                    navLinks.forEach(item => {
                        item.parentElement.classList.remove('active');
                    });
                    this.parentElement.classList.add('active');
                });
            });
        });

        document.querySelector('.toggle-sidebar').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        })
        
    </script>
</body>
</html>