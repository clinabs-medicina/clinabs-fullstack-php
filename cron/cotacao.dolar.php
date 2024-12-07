<?php
require_once '../config.inc.php';

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.invertexto.com/v1/currency/USD_BRL?token=14871|upV9AIHLjtXl9NWELOeoXnKk8bPIjMPD',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
));

$response = curl_exec($curl);

$dollar = json_decode($response, true);
$cotacao = $dollar['USD_BRL']['price'];


$pdo->query("UPDATE `ENVONRONMENT_VARIABLES` SET valor = '{$cotacao}' WHERE id = 1");
curl_close($curl);


echo $response;