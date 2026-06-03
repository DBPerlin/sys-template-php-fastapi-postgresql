<?php

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $api_url = "https://cnn.cfn.org.br/application/front-resource/get-nutrir";

    $crn = $_POST["crn_id"] ?? "";
    $inscricao = $_POST["inscricao"] ?? "";
    
    $inscricao_numeros = preg_replace('/\D/', '', $inscricao);

    $params_array = [
        "comando" => "get-nutricionista",
        "options[crn]" => $crn,
        "options[registro]" => $inscricao,
        "options[nome]" => "", 
        "options[cpf]" => "",  
        "options[tecnico]" => "",
        "options[situacao]" => ""
    ];

    $params = http_build_query($params_array);

    $options = [
        "http" => [
            "method"  => "POST",
            "header"  => "Content-Type: application/x-www-form-urlencoded\r\n" .
                         "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n" .
                         "Referer: https://proid.cfn.org.br/\r\n",
            "content" => $params,
            "ignore_errors" => true
        ]
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($api_url, false, $context);

    if ($response === false) {
        echo json_encode([
            "sucesso" => false,
            "erro" => "Falha de conexão com a API do Conselho."
        ]);
        exit;
    }

    $result = json_decode($response, true);

    // Retorno final da API
    if (isset($result["success"]) && $result["success"] === true && !empty($result["data"])) {
        
        $profissional = $result["data"][0];
        
        //checkando o nome
        $nome_digitado = strtoupper(trim($_POST["nome"] ?? ""));
        $nome_api = strtoupper(trim($profissional["nome"] ?? ""));
        
        if (strpos($nome_api, $nome_digitado) !== false || strpos($nome_digitado, $nome_api) !== false || similar_text($nome_digitado, $nome_api) > 10) {
            echo json_encode([
                "sucesso" => true,
                "dados" => $profissional
            ]);
        } else {
             echo json_encode([
                "sucesso" => false,
                "erro" => "O Nome digitado não bate com o Registro do CRN fornecido."
            ]);
        }

    } else {
        echo json_encode([
            "sucesso" => false,
            "erro" => "Nenhum profissional encontrado com esse CRN e Registro."
        ]);
    }
}