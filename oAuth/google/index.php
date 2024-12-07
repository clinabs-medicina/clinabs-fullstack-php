<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setAuthConfig($_SERVER['DOCUMENT_ROOT'].'/google_credentials.json');
$client->setRedirectUri('https://clinabs.com/oAuth/google/index.php');
$client->addScope(Google_Service_Calendar::CALENDAR_READONLY);

if (!isset($_GET['code'])) {
    $authUrl = $client->createAuthUrl();
    header('Location: ' .$authUrl);
    exit();
} else {
  file_put_contents('data.json', json_encode([
                                             'method' => $_SERVER['REQUEST_METHOD'],
                                             'request' => $_REQUEST ?? file_get_contents('php://input')
                                             ]));
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $_SESSION['access_token'] = $token;
    
    $client->setAccessToken($token['access_token']);

    $service = new Google_Service_Calendar($client);
    $events = $service->events->listEvents('primary', ['maxResults' => 1000]);

    $eventos = [];

    foreach ($events['items'] as $event) {
        $eventos[] = [
            'id' => $event->id,
            'title' => $event->summary,
            'start' => $event->start->dateTime,
            'end' => $event->end->dateTime,
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($eventos, JSON_PRETTY_PRINT);
}
