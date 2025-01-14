<?php
require_once '../config.inc.php';

header('Content-Type: application/json; charset=utf-8');
echo json_encode($pacientes->getAllWithoutAttachments(),16 | 64 | 128);