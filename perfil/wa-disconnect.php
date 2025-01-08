<?php
require_once '../config.inc.php';

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://app.hallo-api.com/v1/instance/GLCG-000629-i12u-aofB-X77N9XQ3GXZB/token/5RLU7PHL-zPBP-4AVw-BgNb-2B67W4L9VX85/instance',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('fLogin' => '9V60VP5I-SYAxyJ-AMbim7a2-ZHY47UPR3LCY', 'ACTION' => 'DISCONNECT'),
));

$result = curl_exec($curl);

curl_close($curl);
