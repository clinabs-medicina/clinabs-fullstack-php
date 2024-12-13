<?php
header('Content-Type: application/json');


//$ram_total = intval(preg_replace('/[^0-9]+/', '', shell_exec("free | grep 'Mem:' | awk '{ print \$2}'")));
//$ram_used = intval(preg_replace('/[^0-9]+/', '', shell_exec("free | grep 'Mem:' | awk '{ print \$3}'")));

$server = [
    'cpu' => [
        'percentage' => 0//round(sys_getloadavg()[0])
    ],
    'memory' => [
        'total' => 0,//$ram_total,
        'used' => 0,//$ram_used,
        'percentage' => 0,//round(($ram_used * 100) / $ram_total)
    ],
    'storage' => [
        'total' => 0,//shell_exec("python3 -c 'import psutil; print(psutil.virtual_memory().total)'"),
        'used' => 0,//shell_exec("python3 -c 'import psutil; print(psutil.virtual_memory().used)'"),
        'percent' => 0//shell_exec("python3 -c 'import psutil; print(psutil.virtual_memory().percent)'"),
    ]
];


//echo json_encode($server, JSON_PRETTY_PRINT);