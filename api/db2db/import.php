<?php
$servername = '68.183.159.246'; //'68.183.159.246';
$database = 'clinabs_app'; //'clinabs_app';
$username = 'clinabs_admin';
$password = 'GenP+s+J6Cisa^vB7visr@%3c0nCaOz#3Bb7jaGJ6pyOqC*C';

date_default_timezone_set('America/Sao_Paulo');

// Banco de Dados
try {
	$pdo = new PDO("mysql:host=$servername;dbname=$database;charset=utf8mb4", $username, $password);
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
	echo "Connection Failed: " . $e->getMessage();
}


$tableName = "AGENDA_MED";

$file = file_get_contents("{$tableName}.json");
$contents = json_decode($file, true);

$i = 0;
foreach ($contents as $id => $item) {

    $sql = "INSERT IGNORE INTO {$tableName} (`".implode('`,`', array_keys($item))."`) VALUES (:".implode(', :', array_keys($item)).")";
    $stmt = $pdo->prepare($sql);
    

    try {
        $stmt->execute($item);
    } catch (PDOException $e) { 

    } finally {
        $i++;
        $p = round($i * 100 / count($contents));
        echo "{$p}%\n";
    }
}