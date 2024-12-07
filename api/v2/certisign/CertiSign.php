<?php
class CertiSign {
  private string $token;
  private string $url;
  //'https://api-sbx.portaldeassinaturas.com.br/api/v2':
  public function __construct(string $token, bool $sandbox = false) {
    $this->token = $token;
    $this->url = 'https://api.portaldeassinaturas.com.br/api/v2';
  }

  public function bind(string $event, array $fields) {
    if($event == 'signature') {
      $upload = $this->request(
        mode: 'POST', 
        path: 'document/upload',
        data: json_encode([
          "fileName" => basename($fields['file'], ".pdf"),
          "bytes" => $this->read_file($fields['file'], $fields['token'])
        ])
      );

      if(isset($upload->uploadId)) {
        $name = "RECEITA_".$fields['receitaId'].".pdf";

        $doc = $this->request(
          mode: 'POST', 
          path: 'document/create',
          data: 
          '{ 
              "document":{  
                  "name":"'.$name.'",
                  "upload":{  
                    "id":"'.$upload->uploadId.'",
                    "name":"'.$name.'"
                }
              },
              "sender":{  
                "name":"'.$fields['nome'].'",
                "email":"'.$fields['email'].'",
                "individualIdentificationCode":"'.$fields['cpf'].'"
              },
              "signers":[
                  {  
                    "step": 1,
                    "title":"Signer",
                    "name":"'.$fields['nome'].'",
                    "email":"'.$fields['email'].'",
                    "individualIdentificationCode":"'.$fields['cpf'].'"
                  }
              ],
              "tags": [
                "'.$fields['receitaId'].'",
                "'.$fields['medicoNome'].'",
                "'.$fields['pacienteNome'].'"
              ]
          }'
        );

        if(isset($doc->signUrl)) {
          return [
            'url' => $doc->signUrl,
            'chave' => $doc->chave
          ];
        } 
        
        else {
          return [
            'error' => 'Ocorreu um Erro [signUrl]'
          ];
        }
      }
      else {
        return [
          'error' => 'Ocorreu um Erro'
        ];
      }
    }
    
  }




  
  private function read_file(string $file, string $token){
    $tmpFile = $_SERVER['DOCUMENT_ROOT'].'/tmp/RECEITA_'.$token.'.pdf';

    $ch = curl_init($file);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    $data = curl_exec($ch);
    curl_close($ch);

  
    $fp = fopen($tmpFile, 'r');
    $file_content = stream_get_contents($fp);
    fclose($fp);

    
    if ($file_content === false) {
      return false;
    } else {
      $byte_array = [];
      for ($i = 0; $i < strlen($file_content); $i++) {
          $byte_array[] = ord($file_content[$i]);
      }
      
      return $byte_array;
    }
  }

  public function request(string $mode, string $path, string $data) {
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => "{$this->url}/{$path}",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => strtoupper($mode),
      CURLOPT_POSTFIELDS => $data,
      CURLOPT_HTTPHEADER => array(
          "token: {$this->token}",
          "Content-Type: application/json"
        ),
      ));

      try {
        $response = curl_exec($curl);

      } catch (Exception $e) {
        $response = ['error' => $e->getMessage()];
        
        print_r($response);
      }

      curl_close($curl);
    
    return json_decode($response);
  }
  
}