<?php
$ag = $agenda->get($_GET['token']);
$wb = json_decode($ag->meet);

$stmt2 = $pdo->prepare("SELECT * FROM PACIENTES WHERE token = :token");
$stmt2->bindValue(':token', $ag->paciente_token);

$stmt2->execute();
$_user = $stmt2->fetch(PDO::FETCH_OBJ);

$presc = [];
?>
<div class="container">
    <div class="row-wb" id="row-wb">
        <div class="column-wb">
            <iframe src="<?= ($wb->hostRoomUrl) ?>&displayName=<?= $_user->nome_completo ?>" width="350px" height="600px"></iframe>
        </div>

    </div>
</div>