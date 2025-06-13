<?php

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

?>