<?php
require_once __DIR__ . '/../../config.inc.php';

$ag = $agenda->get($_GET['room']);
$wb = json_decode($ag->meet);

$stmt2 = $pdo->prepare("SELECT * FROM PACIENTES WHERE token = :token");
$stmt2->bindValue(':token', $ag->paciente_token);

$stmt2->execute();
$_user = $stmt2->fetch(PDO::FETCH_OBJ);

$presc = [];
?>
<!DOCTYPE html>
<html>

<head>
    <title>My Meeting Room</title>
    <style>
        #whereby {
            display: flex;
            width: 600px;
            height: 600px;
            background-color: black;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src=" https://cdn.srv.whereby.com/embed/v2-embed.js" type="module"></script>
</head>

<body>
    <iframe src="https://clinabs.whereby.com/demo-c0a0f399-857b-45ba-82bb-c416f763d77b" allow="camera; microphone; fullscreen; speaker; display-capture; compute-pressure" style="height: 700px; width: 100%"></iframe>
</body>

</html>