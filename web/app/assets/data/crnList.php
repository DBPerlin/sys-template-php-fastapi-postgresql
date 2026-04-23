<?php

$crnList = [
    ["id" => 1, "nome" => "CRN 1 - DF, GO, MT, TO"],
    ["id" => 2, "nome" => "CRN 2 - RS"],
    ["id" => 3, "nome" => "CRN 3 - SP, MS"],
    ["id" => 4, "nome" => "CRN 4 - RJ, ES"],
    ["id" => 5, "nome" => "CRN 5 - BA, SE"],
    ["id" => 6, "nome" => "CRN 6 - PE, AL, PB, RN"],
    ["id" => 7, "nome" => "CRN 7 - AC, PA, AP, AM, RR"],
    ["id" => 8, "nome" => "CRN 8 - PR"],
    ["id" => 9, "nome" => "CRN 9 - MG"],
    ["id" => 10, "nome" => "CRN 10 - SC"],
    ["id" => 11, "nome" => "CRN 11 - CE, MA, PI"]
];

$estadoList = [
    ["id" => 1, "nome" => "DF"],
    ["id" => 2, "nome" => "GO"],
    ["id" => 3, "nome" => "MT"],
    ["id" => 4, "nome" => "TO"],
    ["id" => 5, "nome" => "RS"],
    ["id" => 6, "nome" => "SP"],
    ["id" => 7, "nome" => "MS"],
    ["id" => 8, "nome" => "ES"],
    ["id" => 9, "nome" => "RJ"],
    ["id" => 10, "nome" => "BA"],
    ["id" => 11, "nome" => "SE"],
    ["id" => 12, "nome" => "AL"],
    ["id" => 13, "nome" => "PB"],
    ["id" => 14, "nome" => "PE"],
    ["id" => 15, "nome" => "RN"],
    ["id" => 16, "nome" => "AC"],
    ["id" => 17, "nome" => "AM"],
    ["id" => 18, "nome" => "AP"],
    ["id" => 19, "nome" => "PA"],
    ["id" => 20, "nome" => "RO"],
    ["id" => 21, "nome" => "RR"],
    ["id" => 22, "nome" => "PR"],
    ["id" => 23, "nome" => "MG"],
    ["id" => 24, "nome" => "SC"],
    ["id" => 25, "nome" => "CE"],
    ["id" => 26, "nome" => "MA"],
    ["id" => 27, "nome" => "PI"],

];

$estadoMap = [];

foreach($estadoList as $estado){
    $estadoMap[$estado["id"]] = $estado["nome"];
}