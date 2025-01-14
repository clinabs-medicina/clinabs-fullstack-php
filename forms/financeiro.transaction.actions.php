<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

ini_set('display_errors', 1);
error_reporting(1);


function translate($q, $sl, $tl){
  $curl = curl_init("https://translate.googleapis.com/translate_a/single?client=gtx&ie=UTF-8&oe=UTF-8&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=ss&dt=t&dt=at&sl=".$sl."&tl=".$tl."&hl=hl&q=".urlencode($q));
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, True);
  $return = curl_exec($curl);
  curl_close($curl);
  
  
  
  $res=json_decode($return);
  return $res[0][0][0];
}

switch($_REQUEST['action']) {
    case 'cancel': {
          $curl = curl_init();

          curl_setopt_array($curl, [
            CURLOPT_URL => "https://api.pagar.me/core/v5/charges/".$_REQUEST['charge_id'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => [
              "accept: application/json",
              "authorization: Basic ".$API_KEY_B64_PD,
              "content-type: application/json"
            ],
          ]);
          
          $response = json_decode(curl_exec($curl));
          $err = curl_error($curl);
          
          curl_close($curl);
          
          if ($err) {
              $json = json_encode([
                'status' => 'danger', 
                'text' => 'Falha ao Cancelar Transação'
            ], JSON_PRETTY_PRINT);

          } else {
            if($response->status == 'canceled')
            {
                $json = json_encode([
                  'status' => 'success',
                  'text' => 'Transação Cancelada Com Sucesso!'
              ], JSON_PRETTY_PRINT);
            }else{
              $json = json_encode([
                'status' => 'warning',
                'text' => translate($response->message, "en", "pt").'\n'.trim(end(exlode('|', $response->message)))
            ], JSON_PRETTY_PRINT);
          }
            
      }
    break;
    }
}


header('Content-Type: application/json; charset=utf-8');
echo $json;