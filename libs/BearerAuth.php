<?php
if (!isset($_SERVER['PHP_AUTH_USER'])) {
    header('WWW-Authenticate: Basic realm="ldap.clinabs.com"');
    header('HTTP/1.0 401 Unauthorized');
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'error',
        'text' => 'No Bearer Auth Schema Supplied.'
    ]);
    exit;
}