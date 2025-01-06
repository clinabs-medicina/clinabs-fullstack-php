<?php
header('Content-Type: application/json');


$ram_total = intval(preg_replace('/[^0-9]+/', '', shell_exec("free | grep 'Mem:' | awk '{ print \$2}'")));
$ram_used = intval(preg_replace('/[^0-9]+/', '', shell_exec("free | grep 'Mem:' | awk '{ print \$3}'")));

$server = [
    'cpu' => [
        'percentage' => round(sys_getloadavg()[0])
    ],
    'memory' => [
        'total' => $ram_total,
        'used' => $ram_used,
        'percentage' => round(($ram_used * 100) / $ram_total)
    ],
    'storage' => [
        'total' => shell_exec("python3 -c 'import psutil; print(psutil.virtual_memory().total)'"),
        'used' => shell_exec("python3 -c 'import psutil; print(psutil.virtual_memory().used)'"),
        'percent' => shell_exec("python3 -c 'import psutil; print(psutil.virtual_memory().percent)'"),
    ]
];


echo json_encode($server, JSON_PRETTY_PRINT);