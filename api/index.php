<?php
error_reporting(0);
ini_set('display_error', 0);

$token = 'Bearer 9fiaz0uanaa7gmhmtrlg4zfbr2hqi7wh';

$realm = 'Área Restrita';


function strip_str(string $str, string $sep) {
  return trim(end(explode($sep, strip_tags($str))));
}

if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
  header('WWW-Authenticate: Basic realm="'.$realm.'"');
  header('HTTP/1.0 401 Unauthorized');

  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(['status' => 'Error', 'message' => 'Access denied. You did not enter a Bearer Token'], JSON_PRETTY_PRINT);
  exit;
}else {
  $auth = $_SERVER['HTTP_AUTHORIZATION'];
  $api = [];

  header('Content-Type: application/json; charset=utf-8');
  
  if($auth == $token){
    $api['os_info'] = [
      'cpu' => array (
        'usage' => system("python3.11 -c 'import psutil;print(round(psutil.virtual_memory().percent))'")
       )
    ];

    
    print(json_encode($api, JSON_PRETTY_PRINT));
  }else {
    print(json_encode(['status' => 'Error', 'message' => 'Access denied. You Bearer Token is invalid'], JSON_PRETTY_PRINT));
  }
  exit;
}