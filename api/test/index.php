<?php

$request = [
    'headers' => getallheaders(),
    'data' => file_get_contents('php://input') ?? $_REQUEST,
];

header('Content-Type: application/json');
echo json_encode($request, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
