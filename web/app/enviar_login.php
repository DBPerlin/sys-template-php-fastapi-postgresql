<?php

session_start();

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $api_url = "http://backend:8000/core/validar_usuario";

    $data = [
        "nome" => $_POST["nome"] ?? "",
        "senha" => $_POST["senha"] ?? ""
    ];

    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json"
        ],
        CURLOPT_TIMEOUT => 10
    ]);

    $response = curl_exec($ch);

    if($response === false){
        echo "Erro ao conectar com a API: " . curl_error($ch);
        curl_close($ch);
        exit;
    }

    curl_close($ch);

    $result = json_decode($response, true);

    if(isset($result["sucesso"]) && $result["sucesso"]){

        $_SESSION["token"] = "autenticado";
        $_SESSION["usuario"] = $result["usuario"] ?? null;

        header("Location: interna.php");
        exit;

    }

    echo "Usuário ou senha inválidos";

}