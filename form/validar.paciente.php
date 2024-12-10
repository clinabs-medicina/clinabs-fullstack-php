<?php
function isValidCPF($cpf) {
    // Remove non-numeric characters
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    
    // Check if CPF has 11 digits
    if (strlen($cpf) !== 11) {
        return false;
    }

    // Check for repeated CPF numbers (e.g., 111.111.111-11)
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Validate the first checksum digit
    $sum1 = 0;
    for ($i = 0; $i < 9; $i++) {
        $sum1 += (int)$cpf[$i] * (10 - $i);
    }
    $digit1 = $sum1 % 11 < 2 ? 0 : 11 - $sum1 % 11;
    if ((int)$cpf[9] !== $digit1) {
        return false;
    }

    // Validate the second checksum digit
    $sum2 = 0;
    for ($i = 0; $i < 10; $i++) {
        $sum2 += (int)$cpf[$i] * (11 - $i);
    }
    $digit2 = $sum2 % 11 < 2 ? 0 : 11 - $sum2 % 11;
    if ((int)$cpf[10] !== $digit2) {
        return false;
    }

    return true;
}

// Example usage
if(isset($_GET['cpf'])) {
    $cpf = $_REQUEST['cpf'];


    if (isValidCPF($cpf)) {
        $payload = ['status' => 'valid'];
    } else {
        $payload = ['status' => 'invalid'];
    }
} else if(isset($_GET['email'])) {
    $email = $_REQUEST['email'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $payload = ['status' => 'valid'];
    } else {
        $payload = ['status' => 'invalid'];
    }


    if (isValidEmail($cpf)) {
        $payload = ['status' => 'valid'];
    } else {
        $payload = ['status' => 'invalid'];
    }
}

header('Content-Type: application/json');
echo json_encode(payload, JSON_PRETTY_PRINT);