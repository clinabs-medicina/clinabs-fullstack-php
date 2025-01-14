<?php
require_once "bkp-sync.php";

$headers = getallheaders();
$servername = $headers["X-Database-Server"];
$username = $headers["X-Database-User"];
$password = $headers["X-Database-Password"];
$dbname = $headers["X-Database-Name"];

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$allTablesData = [];
$input = file_get_contents('php://input');
$tables = json_decode($input, true)['tables'];

foreach ($tables as $table) {
    $sql = "SELECT * FROM $table";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $tableData = [];
        while ($row = $result->fetch_assoc()) {
            if(isset($headers['X-Skip-Column'])) {
                unset($row[$headers['X-Skip-Column']]);
            }

           $tableData[] = fill_table_row($table, $row);

           
        }
        $allTablesData[$table] = $tableData;

        file_put_contents($headers['X-Database-Backup']."/{$table}.json", json_encode($tableData, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION));
    }
}

$conn->close();


if(isset($headers['X-Database-Backup'])) {
    if(!is_dir($headers['X-Database-Backup'])) {
        mkdir($headers['X-Database-Backup'], 0777, true);
    }

    $fileName = date('Y-m-d');
    file_put_contents($headers['X-Database-Backup']."/{$fileName}.json", json_encode($allTablesData, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION));
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode(['status' => 'success'], JSON_FORCE_OBJECT | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION);