<?php

session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.html?error=not_logged_in');
    exit();
}
require '../config.php';
require '../DBFunctions.php';
require '../Functions.php';
$conn = DbConect();

if(isset($_POST['cnpj']) && !empty($_POST['cnpj']) || isset($_GET['cnpj']) && !empty($_GET['cnpj'])) {
    if(isset($_POST['cnpj']) && !empty($_POST['cnpj'])){
        $cnpj = preg_replace('/[^0-9]/', '', $_POST['cnpj']); 
    }else{
        $cnpj = preg_replace('/[^0-9]/', '', $_GET['cnpj']); 
    }
    $query = "SELECT * FROM stakeholders WHERE cnpj = '$cnpj'";
    $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
    if(DbNumRows($result) == 0){    
        if(strlen($cnpj) === 14) {
            $url = "https://receitaws.com.br/v1/cnpj/" . $cnpj;

            $options = [
                'http' => [
                    'method' => 'GET',
                    'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36'
                ]
            ];
            $context = stream_context_create($options);

            try {
                $response = file_get_contents($url, false, $context);

                if($response === FALSE) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Erro ao acessar a API da ReceitaWS']);
                } else {
                    header('Content-Type: application/json');
                    $dados = json_decode($response, true);
                    $cnpj = $dados['cnpj'];
                    $razao = $dados['nome'];
                    $situacao = $dados['situacao'];
                    $tipo = $dados['tipo'];
                    $porte = $dados['porte'];
                    $naturezaJuridica = $dados['natureza_juridica'];
                    $logradouro = $dados['logradouro'];
                    $numero = $dados['numero'];
                    $complemento = $dados['complemento'];
                    $municipio = $dados['municipio'];
                    $bairro = $dados['bairro'];
                    $uf = $dados['uf'];
                    $cep = $dados['cep'];
                    $email = $dados['email'];
                    $telefone = $dados['telefone'];
                    $dataSituacao = $dados['data_situacao'];
                    $capitalSocial = $dados['capital_social'];

                    // Tratamento de dados \\
                    $razao = str_replace("'", "''", $razao);
                    $logradouro = str_replace("'", "''", $logradouro);
                    $complemento = str_replace("'", "''", $complemento);
                    $municipio = str_replace("'", "''", $municipio);
                    $bairro = str_replace("'", "''", $bairro);
                    $situacao =='ATIVA' ? $situacao = '1' : $situacao = '0';
                    $cnpj = desformataCNPJ($cnpj);
                    $dataSituacao = date('Y-m-d', strtotime($dataSituacao));
                    $cep = str_replace('.', '', str_replace('-', '', $cep));
                    $naturezaJuridica = utf8_decode($naturezaJuridica);
                    
                    if($cnpj != null){
                        $endereco = $logradouro.','.$numero.'-'.$bairro.','.$municipio.','.$uf.','.$cep.',Brazil';
                        // $apiKey = 'AIzaSyDyAGfE-AUL2a_bfWgX5XXYqS0wSKJqoto';
                        $apiKey = 'AIzaSyDdZUSIyaY-RihUMfNkR5o4cBTcoxLxdiw'; // API DO GOOGLE MAPS (starsgroup04@gmail.com)
                        $coordenadas = getCoordenadasGoogle($endereco, $apiKey);

                        if (isset($coordenadas['lat'])) {
                            $dados['latitude'] = $coordenadas['lat'] ?? null;
                            $dados['longitude'] = $coordenadas['lng'] ?? null;
                            $latitude = $coordenadas['lat'];
                            $longitude = $coordenadas['lng'];
                        } else {
                            $dados['latitude'] = null;
                            $dados['longitude'] = null;
                            $latitude = '';
                            $longitude = '';
                        } 

                        $query = "INSERT INTO stakeholders (cnpj, razao, situacao, tipo, porte, natureza_juridica, logradouro, numero, complemento, municipio, bairro, uf, cep, email, telefone, data_situacao, capital_social,latitude,longetude) 
                        VALUES ('$cnpj', '$razao', '$situacao', '$tipo', '$porte', '$naturezaJuridica', '$logradouro', '$numero', '$complemento', '$municipio', '$bairro', '$uf', '$cep', '$email', '$telefone', '$dataSituacao', '$capitalSocial', '$latitude', '$longitude')";
                        $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
                    }

                    // Processando atividade principal \\
                    if($cnpj != null){
                        foreach($dados['atividade_principal'] as $atividade){
                            $codigo = $atividade['code'];
                            $descricao = utf8_decode($atividade['text']);
                            $tipo_atividade = 'P';
                            $query = "INSERT INTO stakeholdersAtividades (cnpj,codigo,descricao,tipo) 
                            VALUES ('$cnpj', '$codigo', '$descricao', '$tipo_atividade')";
                            $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
                        }
                    }

                    // Processando atividade secundarias \\
                    if($cnpj != null){
                        foreach($dados['atividades_secundarias'] as $atividade){
                            $codigo = $atividade['code'];
                            $descricao = utf8_decode($atividade['text']);
                            $tipo_atividade = 'S';
                            $query = "INSERT INTO stakeholdersAtividades (cnpj,codigo,descricao,tipo) 
                            VALUES ('$cnpj', '$codigo', '$descricao', '$tipo_atividade')";
                            $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
                        }
                    }
                    // Processando Sócios \\
                    if($cnpj != null){
                        foreach($dados['qsa'] as $socios){
                            $socio = utf8_decode($socios['nome']);
                            $qsa = utf8_decode($socios['qual']);
                            if(isset($socios['nome_rep_legal'])){
                                $qsa_rep_legal = utf8_decode($socios['nome_rep_legal']);
                                $nome_rep_legal = utf8_decode($socios['qual_rep_legal']);
                            }else{
                                $qsa_rep_legal = null;
                                $nome_rep_legal = null;
                            }
                           
                            $query = "INSERT INTO stakeholdersSocios (cnpj,qsa,nome,qsa_rep_legal,nome_rep_legal) VALUES ('$cnpj', '$qsa', '$socio', '$qsa_rep_legal','$nome_rep_legal')";
                            $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
                        }
                    }
                    $dados['latitude'] = $coordenadas['lat'] ?? null;
                    $dados['longitude'] = $coordenadas['lng'] ?? null;
                    $dados['endereco_formatado'] = $endereco;

                    $query ="SELECT TOP 5 ID AS [IdConsulta],cast(A.SConsData AS DATE) AS [dataConsulta] FROM netfactor..nfConsultaSerasa AS A WHERE A.SConsCnpjCpf = '$cnpj' AND userConsulta > 0 AND cast(A.SConsData AS DATE) > '2025-01-01' ORDER BY A.SConsData DESC";
                    $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
                    while($r = DbFetchAssoc($result)) {
                        $dados['idDoSerasa'][] = [
                            'IdConsulta' => utf8_encode($r['IdConsulta']),
                            'dataConsulta' => utf8_encode($r['dataConsulta']) 
                        ];
                    }

                    echo json_encode($dados);

                    exit();
                }
            }catch(Exception $e){
                http_response_code(500);
                echo json_encode(['error' => 'Erro na requisição: ' . $e->getMessage()]);
            }
        }else{
            http_response_code(400);
            echo json_encode(['error' => 'CNPJ inválido. Deve conter 14 dígitos.']);
        }
    }else{
        //http_response_code(500);
        extract(DBFetchAssoc($result));
        $tipo_j = $tipo;

        $query ="SELECT * FROM stakeholdersAtividades WHERE cnpj = '$cnpj' AND tipo = 'P'";
        $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
        extract(DBFetchAssoc($result));
        $atividade_principal = $codigo.' '.utf8_encode($descricao);

        

        if($situacao == '1'){
            $situacao = 'Ativa';
        }else{
            $situacao = 'Inativa';
        }
        
        $naturezaJuridica = utf8_encode($natureza_juridica);
        $dados = [
            'cnpj' => $cnpj,
            'razao' => $razao,
            'situacao' => $situacao,
            'tipo' => $tipo_j,
            'porte' => $porte,
            'natureza_juridica' => "$naturezaJuridica",
            'logradouro' => $logradouro,
            'numero' => $numero,
            'complemento' => $complemento,
            'municipio' => $municipio,
            'bairro' => $bairro,
            'uf' => $uf,
            'cep' => $cep,
            'email' => $email,
            'telefone' => $telefone,
            'abertura' => $data_situacao,
            'capital_social' => $capital_social,
            'latitude' => $latitude,
            'longitude' => $longetude,
            'atividade_principal' => $atividade_principal,
            'bd' => '1',
            'socios' => [],
            'idDoSerasa' => []
        ];

        // Buscando sócios \\
        $query = "SELECT * FROM stakeholdersSocios WHERE cnpj = '$cnpj'";
        $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
        while($r = DbFetchAssoc($result)) {
            $dados['socios'][] = [
                'Nome' => utf8_encode($r['nome']),
                'Qualificacao' => utf8_encode($r['qsa']) 
            ];
        }
        //// Buscando CNAE \\
        $query = "SELECT * FROM stakeholdersAtividades WHERE cnpj = '$cnpj' AND tipo = 'S'";
        $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
        while($r = DbFetchAssoc($result)) {
            $dados['cnae'][] = [
                'codigo' => utf8_encode($r['codigo']),
                'descricao' => utf8_encode($r['descricao']) 
            ];
        }

        $query ="SELECT TOP 5 ID AS [IdConsulta],cast(A.SConsData AS DATE) AS [dataConsulta] FROM netfactor..nfConsultaSerasa AS A WHERE A.SConsCnpjCpf = '$cnpj' AND userConsulta > 0 AND cast(A.SConsData AS DATE) > '2025-01-01' ORDER BY A.SConsData DESC";
        $result = DBQuery($query, $conn) or die ("Erro: ".DbError($conn)." na query: <pre> $query </pre>");
        while($r = DbFetchAssoc($result)) {
            $dados['idDoSerasa'][] = [
                'IdConsulta' => utf8_encode($r['IdConsulta']),
                'dataConsulta' => utf8_encode($r['dataConsulta']) 
            ];
        }
        
            
        echo json_encode($dados, JSON_UNESCAPED_UNICODE);
    }
}else{
    http_response_code(400);
    echo json_encode(['error' => 'CNPJ não fornecido.']);
}
?>