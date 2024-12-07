<?php
require_once '../config.inc.php';

header('content-type: application/json');
echo json_encode($pacientes->getAllWithoutAttachments(),16 | 64 | 128);