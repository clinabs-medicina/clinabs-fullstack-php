<?php
class WhereByMeet {
  private $api_key = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJodHRwczovL2FjY291bnRzLmFwcGVhci5pbiIsImF1ZCI6Imh0dHBzOi8vYXBpLmFwcGVhci5pbi92MSIsImV4cCI6OTAwNzE5OTI1NDc0MDk5MSwiaWF0IjoxNzAyMjk2MTQ4LCJvcmdhbml6YXRpb25JZCI6MjAzOTA5LCJqdGkiOiJlOTIxYWNlMS00YTJjLTQxNzgtOTEwOC01ZTczOWU2NmU4NzAifQ.7nWckqL_kw7YicJFv2-Nteknly9RDbRNc8PVtrQcd9Y';
  
  public function createRoom($displayName) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.whereby.dev/v1/meetings');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{
      "endDate": "2099-12-13T17:00:00.000Z",
      "fields": ["hostRoomUrl"]}');

    $headers = [
      'Authorization: Bearer ' . $this->api_key,
      'Content-Type: application/json'
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return json_decode($response);
  }




  public function getRooms() {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://api.whereby.dev/v1/meetings');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    //curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '{
      "startDate": "2023-12-12T16:40:00.300Z",
      "endDate": "2023-12-12T16:50:00.300Z",
      "fields": ["hostRoomUrl"]
      }'
    );

    $headers = [
      'Authorization: Bearer ' . $this->api_key,
      'Content-Type: application/json'
    ];

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return json_decode($response);
  }
}