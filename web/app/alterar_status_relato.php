<?php

session_start();

if(!isset($_SESSION["token"])){
    http_response_code(401);
    echo json_encode(["erro" => "Não autenticado"]);
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);

$api_url = "http://backend:8000/vivencias/alterar_status";

$headers = [
    "Content-Type: application/json",
    //"Authorization: Bearer " . $_SESSION["token"]
];

$options = [
    "http" => [
        "header" => implode("\r\n", $headers),
        "method" => "POST",
        "content" => json_encode([
            "id" => $data["id"],
            "status" => $data["status"]
        ])
    ]
];

$context = stream_context_create($options);

$response = @file_get_contents($api_url, false, $context);

if($response === false){
    echo json_encode([
        "erro" => "Erro ao conectar com API"
    ]);
    exit;
}

echo $response;