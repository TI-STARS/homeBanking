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
    $cad_prospect = [];

    $query = "SELECT 
                COUNT(*) AS empresas_cadastro,
                p.nome AS usuario,
                CAST(inputDate as date) AS DATA,
                CAST(MONTH(cast(inputDate as date)) AS VARCHAR) + '/' + CAST(YEAR(cast(inputDate as date)) AS VARCHAR) AS MONTH_YEAR
              FROM stakeholdersProspect s
              LEFT JOIN dados.stakeholdersPessoa as p ON p.email = s.usuario
              GROUP BY p.nome, cast(inputDate as date)";
    
    $result = DBQuery($query, $conn) or die("Erro: ".DbError($conn)." na query: <pre>$query</pre>");
    
    // Organiza os dados para o gráfico
    $datasets = [];
    $labels = [];

    while($row = DBFetchAssoc($result)) {
        $usuario = utf8_encode($row['usuario']);
        $dados_grafico_barra[$usuario] += $row['empresas_cadastro'];
        $dados_grafico_barra_label['name'] = $usuario;

        // Dados do Grafico de linha \\
        
        $month_year = $row['MONTH_YEAR'];

        if (!in_array($month_year, $labels)) {
            $labels[] = $month_year;
        }

        if (!isset($datasets[$usuario])) {
            $datasets[$usuario] = [
                'label' => $usuario,
                'data' => [],
                'fill' => false,
                'borderColor' => getRandomColor(), 
                'tension' => 0.2
            ];
        }

        $index = array_search($month_year, $labels);
        $datasets[$usuario]['data'][$index] = (int)$row['empresas_cadastro'];
    }

    foreach ($datasets as &$dataset) {
        for ($i = 0; $i < count($labels); $i++) {
            if (!isset($dataset['data'][$i])) {
                $dataset['data'][$i] = 0;
            }
        }
        $dataset['data'] = array_values($dataset['data']); // Reindexa o array
    }



?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.min.js" integrity="sha512-L0Shl7nXXzIlBSUUPpxrokqq4ojqgZFQczTYlGjzONGTDAcLremjwaWv5A+EDLnxhQzY5xUZPWLOLqYRkY0Cbw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.js" integrity="sha512-7DgGWBKHddtgZ9Cgu8aGfJXvgcVv4SWSESomRtghob4k4orCBUTSRQ4s5SaC2Rz+OptMqNk0aHHsaUBk6fzIXw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js" integrity="sha512-ZwR1/gSZM3ai6vCdI+LVF1zSq/5HznD3ZSTk7kajkaj4D292NLuduDCO1c/NT8Id+jE58KYLKT7hXnbtryGmMg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js" integrity="sha512-CQBWl4fJHWbryGE+Pc7UAxWMUMNMWzWxF4SQo9CgkJIN1kx6djDQZjh3Y8SZ1d+6I+1zze6Z7kHXO7q3UyZAWw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/helpers.js" integrity="sha512-08S2icXl5dFWPl8stSVyzg3W14tTISlNtJekjsQplv326QtsmbEVqL4TFBrRXTdEj8QI5izJFoVaf5KgNDDOMA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/helpers.min.js" integrity="sha512-JG3S/EICkp8Lx9YhtIpzAVJ55WGnxT3T6bfiXYbjPRUoN9yu+ZM+wVLDsI/L2BWRiKjw/67d+/APw/CDn+Lm0Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <title> Home Banking</title>
</head>
<body>
    <div class="toggle-sidebar">☰</div>
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
            <li >
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
            <li class="active">
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
 <div class="dashboard-container">
    <!-- Primeira Linha - 2 Gráficos -->
    <div class="chart-row">
        <div class="chart-island">
            <h2>DESEMPENHO MENSAL</h2>
            <div class="chart-container">
                <canvas id="myChart"></canvas>
            </div>
        </div>
        
        <div class="chart-island">
            <h2>TOTAL DE CADASTROS POR USUÁRIO</h2>
            <div class="chart-container">
                <canvas id="myChart2"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Segunda Linha - 2 Gráficos Adicionais -->
    <div class="chart-row">
        <div class="chart-island">
            <h2>STATUS DOS CADASTROS</h2>
            <div class="chart-container">
                <canvas id="myChart3"></canvas>
            </div>
        </div>
        
        <div class="chart-island">
            <h2>DISTRIBUIÇÃO GEOGRÁFICA</h2>
            <div class="chart-container">
                <canvas id="myChart4"></canvas>
            </div>
        </div>
    </div>
</div>

    
    <script>

    // Definindo as cores que serão usadas em ambos os gráficos
        const coresCompartilhadas = {
            backgroundColor: [
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(255, 206, 86, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)',
                'rgba(199, 199, 199, 0.7)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)',
                'rgba(199, 199, 199, 1)'
            ]
        };

        // Primeiro gráfico (linha)
        const ctx = document.getElementById('myChart').getContext('2d');
        const myChart = new Chart(ctx, {
            type: 'line',
            data: { 
                labels: <?= json_encode($labels) ?>,
                datasets: <?= json_encode(array_values($datasets)) ?>.map((dataset, index) => ({
                    ...dataset,
                    backgroundColor: coresCompartilhadas.backgroundColor[index % coresCompartilhadas.backgroundColor.length],
                    borderColor: coresCompartilhadas.borderColor[index % coresCompartilhadas.borderColor.length],
                    borderWidth: 1
                }))
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Segundo gráfico (barras)
        const ctx2 = document.getElementById('myChart2').getContext('2d');
        const myChart2 = new Chart(ctx2, {
            type: 'bar',
            data: {
                labels: [<?php 
                    echo "'" . implode("','", array_keys($dados_grafico_barra)) . "'";
                ?>],
                datasets: [{
                    label: 'Total de empresas cadastradas',
                    data: [<?php 
                        echo implode(',', array_values($dados_grafico_barra));
                    ?>],
                    backgroundColor: coresCompartilhadas.backgroundColor,
                    borderColor: coresCompartilhadas.borderColor,
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
                    
        