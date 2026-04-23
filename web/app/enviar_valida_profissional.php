<?php

header("Content-Type: application/json");

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $base_url = "http://backend:8000/core/consulta-profissional";

    $params = http_build_query([
        "regional" => $_POST["crn_id"] ?? "",
        "registro" => $_POST["inscricao"] ?? "",
        "nome" => strtoupper(trim($_POST["nome"] ?? "")),
        "cpf" => preg_replace('/\D/', '', $_POST["cpf"] ?? "")
    ]);

    $api_url = $base_url . "?" . $params;

    $response = file_get_contents($api_url);

    if($response === false){
        echo json_encode([
            "sucesso" => false,
            "erro" => "Erro ao consultar API"
        ]);
        exit;
    }

    $result = json_decode($response, true);

    if(!empty($result) && is_array($result)){
        echo json_encode([
            "sucesso" => true,
            "dados" => $result[0] ?? null
        ]);
    }else{
        echo json_encode([
            "sucesso" => false
        ]);
    }

}