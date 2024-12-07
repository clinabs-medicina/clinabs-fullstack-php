<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';
$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.invertexto.com/v1/currency/USD_BRL?token=8158%7CHrkxbopDabJciIvre0lZQ0S3Biwvbjr6',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$dollar = json_decode(curl_exec($curl), true);


$stmt1 = $pdo->query("SELECT * FROM `PRODUTOS` WHERE moeda = 'USD';");
$produtos = $stmt1->fetchAll(PDO::FETCH_OBJ);

foreach($produtos as $produto) {
    $valor = ($produto->valor_venda * round($dollar['USD_BRL']['price']));
    
    $stmt = $pdo->prepare('UPDATE PRODUTOS SET valor = :valor WHERE id = :id');
    $stmt->bindValue(':valor', $valor / 100);
    $stmt->bindValue(':id', $produto->id);
    
    $stmt->execute();
}

file_put_contents('../dollar.json', json_encode($dollar, JSON_PRETTY_PRINT));
header('Content-Type: application/json');
curl_close($curl);
echo json_encode($dollar, JSON_PRETTY_PRINT);