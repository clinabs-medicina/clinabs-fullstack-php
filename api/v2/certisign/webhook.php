<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/config.inc.php');
$token = "d29fca2043fc41f0ab07d48a11b00d13";
$data = json_decode(file_get_contents("php://input"));

if($data->action == 'SIGNATURE-DIGITAL') {
    $key = substr($data->apiDownload, strlen($data->apiDownload) - 16, strlen($data->apiDownload));
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.portaldeassinaturas.com.br/api/v2/document/package?key={$key}&includeOriginal=true&IncludeManifest=true&Zipped=true",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'token: d29fca2043fc41f0ab07d48a11b00d13'
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    $response = json_decode($response);
    $ZipName = uniqid().".zip";

    if(isset($response->bytes)) {
        $zipBytes = base64_decode($response->bytes);
        file_put_contents($ZipName, $zipBytes);
    }


    $zip = new ZipArchive;
    if ($zip->open($ZipName) === true) {
        $filename = $zip->getNameIndex(0);
        $fileinfo = pathinfo($filename);
        $path = "/data/receitas/assinadas/".str_replace('-Manifesto', '', $fileinfo['basename']);

        copy("zip://$ZipName#".$filename, $_SERVER['DOCUMENT_ROOT'].$path);
                        
        $zip->close();
        
        unlink($ZipName);

        $ag_token = trim(str_replace("-Manifesto", "", basename(end(explode('_', $fileinfo['basename'])), ".pdf")));

        $xstmt = $pdo->query("SELECT file_signed,historico_receitas,medico_token FROM AGENDA_MED WHERE token = '{$ag_token}'");
        $xstmt = $xstmt->fetch();

        $result = [
            'agenda_token' => trim(str_replace("-Manifesto", "", basename(end(explode('_', $fileinfo['basename'])), ".pdf"))),
            'filename' => basename($path),
            'medico_token' => $xstmt->medico_token,
            'path' => $path,
            'timestamp' => date('Y-m-d H:i:s')
        ];


        $receitas = [];

        if($xstmt->historico_receitas != null || $xstmt->historico_receitas != '[]') {
            try {
                $receitas[] = json_decode($xstmt->historico_receitas, true);
            } catch(Exception $ex) {
                
            }
        }

        $receitas[] = [
            'timestamp' => date('Y-m-d H:i:s'),
            'file' => basename($path),
            'medico_token' => $xstmt->medico_token
        ];


        $res = [];


        foreach($receitas as $receita) {
            if(is_array($receita)) {
                $res[] = $receita;
            }
        }

        $stmt = $pdo->prepare("UPDATE AGENDA_MED SET file_signed = :file_name,historico_receitas = :json_data WHERE token = :token_ag");
        $stmt->bindValue(":file_name", basename($path));
        $stmt->bindValue(":token_ag", $ag_token);
        $stmt->bindValue(":json_data", json_encode($res, JSON_PRETTY_PRINT));

        $stmt->execute();

        header('Content-Type: application/json');
        echo json_encode($result, JSON_PRETTY_PRINT);
    }
} 

else {
    $result = ['no document received from CertiSign'];
    header('Content-Type: application/json');
    echo json_encode($result, JSON_PRETTY_PRINT);
}