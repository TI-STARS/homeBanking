<?
// Arquivo com funções gerais \\

function desformataCNPJ($cnpj){
    $cnpj = str_replace('.','',str_replace('-','',str_replace('/','',$cnpj)));
    return $cnpj;
}

function formataCNPJ($cnpj){
    $cnpj = substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
    return $cnpj;
}

function getCoordenadasLocationIQ($endereco, $apiKey) {
    $url = "https://us1.locationiq.com/v1/search.php?key=" . $apiKey . 
           "&q=" . urlencode($endereco) . "&format=json";
    
    // Adicionando tratamento de erros
    $context = stream_context_create([
        'http' => [
            'ignore_errors' => true // Para capturar erros HTTP
        ]
    ]);
    
    $response = @file_get_contents($url, false, $context);
    
    if ($response === FALSE) {
        return ['error' => 'Falha ao acessar a API'];
    }
    
    $data = json_decode($response, true);
    
    if (!empty($data) && isset($data[0]['lat']) && isset($data[0]['lon'])) {
        return [
            'latitude' => $data[0]['lat'],
            'longitude' => $data[0]['lon']
        ];
    }
    
    return ['error' => 'Endereço não encontrado ou dados inválidos'];
}

function getCoordenadasGoogle($endereco, $apiKey) {
    // Remove caracteres especiais e formata o endereço
    $endereco = preg_replace('/[^a-zA-Z0-9\s,.-]/', '', $endereco);
    $enderecoCodificado = urlencode($endereco);
    
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$enderecoCodificado}&key={$apiKey}";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Apenas para desenvolvimento
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        return ['error' => 'Erro na requisição cURL: ' . curl_error($ch)];
    }
    
    curl_close($ch);
    
    $data = json_decode($response, true);

    // Verifica os possíveis status da API
    switch ($data['status']) {
        case 'OK':
            // Retorna apenas as coordenadas do geometry->location
            return [
                'lat' => $data['results'][0]['geometry']['location']['lat'],
                'lng' => $data['results'][0]['geometry']['location']['lng']
            ];
            
        case 'ZERO_RESULTS':
            return ['error' => 'Endereço não encontrado'];
            
        case 'OVER_QUERY_LIMIT':
            return ['error' => 'Limite de consultas excedido'];
            
        case 'REQUEST_DENIED':
            return ['error' => 'API key inválida ou não autorizada'];
            
        case 'INVALID_REQUEST':
            return ['error' => 'Requisição inválida - verifique o endereço'];
            
        default:
            return ['error' => 'Erro desconhecido: ' . $data['status']];
    }
}

function getRandomColor() {
    $letters = '0123456789ABCDEF';
    $color = '#';
    for ($i = 0; $i < 6; $i++) {
        $color .= $letters[rand(0, 15)];
    }
    return $color;
}

function formataValor($valor){
    $valor = number_format($valor, 2, ',', '.');
    return $valor;
}

function gerarNomeUnicoParaArquivo($extensao = '') {
    $prefixoDia = date('d') . '_';
    $hash = substr(md5(uniqid(rand(), true)), 0, 8); 
    $nomeArquivo = $prefixoDia . $hash;
    
    if (!empty($extensao)) {
        $nomeArquivo .= '.' . trim($extensao, '.');
    }
    
    return $nomeArquivo;
}

?>