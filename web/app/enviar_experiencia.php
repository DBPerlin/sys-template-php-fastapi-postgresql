<?php

$api_url = "http://backend:8000/vivencias/nova_experiencia";

$postFields = [
    "crn_id" => $_POST["crn_id"] ?? "",
    "nome_completo" => $_POST["nome_completo"] ?? "",
    "cpf" => $_POST["cpf"] ?? "",
    "inscricao" => $_POST["inscricao"] ?? "",
    "estado_id" => $_POST["estado_id"] ?? "",
    "titulo_trabalho" => $_POST["titulo_trabalho"] ?? "",
    "area_nutricao" => $_POST["area_nutricao"] ?? "",
    "telefone" => $_POST["telefone"] ?? "",
    "email" => $_POST["email"] ?? "",
    "objetivo_trabalho" => $_POST["objetivo_trabalho"] ?? "",
    "acoes_trabalho" => $_POST["acoes_trabalho"] ?? "",
    "resultados_trabalho" => $_POST["resultados_trabalho"] ?? ""
];

$ch = curl_init();

if(isset($_FILES['arquivo']) && $_FILES['arquivo']['tmp_name'] != ""){
    $postFields['arquivo'] = new CURLFile(
        $_FILES['arquivo']['tmp_name'],
        $_FILES['arquivo']['type'],
        $_FILES['arquivo']['name']
    );
}

curl_setopt_array($ch, [
    CURLOPT_URL => $api_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $postFields,
    CURLOPT_TIMEOUT => 30
]);

$response = curl_exec($ch);

curl_close($ch);

$result = json_decode($response, true);

if(isset($result["id"])){

    header("Location: index.php?sucesso=1");
    exit;

}else{

    echo "<h2>Erro ao enviar relato.</h2>";
    echo $response;

}

?>



