<?php
require_once "../config.inc.php";
$request = $_POST;

function getBlob($fileData)
{
    return base64_decode(explode(";base64,", $fileData)[1]);
}

if (isset($request["image"])) {
    $blob = getBlob($request["image"]);
    $fileName = $_SERVER["DOCUMENT_ROOT"] . "/tmp/" . uniqid() . ".png";

    file_put_contents( $_SERVER["DOCUMENT_ROOT"] ."/data/images/profiles/". $request["token"] . ".jpg", $blob);

    $json = json_encode([
        "title" => "Atenção",
        "text" => "Cadastro Atualizado Com Sucesso!",
        "status" => "success",
    ]);

    header("content-type: application/json");
    echo $json;
}