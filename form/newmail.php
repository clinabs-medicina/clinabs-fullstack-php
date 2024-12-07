<?php
require_once('../config.inc.php');

$email = $_GET['mail'];
$password = base64_decode($_GET['password']);
$token = uniqid().'.tmp';
$file = '/home/docker/mailserver/docker-data/dms/config/postfix-accounts.cf';


class PasswordHasher {
    const SALT_LENGTH = 16;
    const HASH_ALGORITHM = 'sha512';

    // Função para gerar uma senha criptografada
    public static function hashPassword($password) {
        $salt = self::generateSalt();
        $hashedPassword = crypt($password, '$6$' . $salt . '$');
        return $hashedPassword;
    }

    // Função para gerar um salt aleatório
    private static function generateSalt() {
        return substr(base64_encode(openssl_random_pseudo_bytes(self::SALT_LENGTH)), 0, self::SALT_LENGTH);
    }
}

// Função para adicionar uma conta ao arquivo postfix-accounts.cf
function addAccount($pdo, $file, $email, $hashedPassword) {
    // Linha a ser adicionada
    $email = strtolower($email);

    if(!account_exists($file, $email)) {
        if (is_writable($file)) {
            // Abre o arquivo em modo de escrita
            if (!$handle = fopen($file, 'w')) {
                return ['status' => 'error', 'icon' => 'error', 'text' => 'Acesso Negado ao Gerenente de Contas de E-mail'];
                exit;
            }
    
            // Escreve a linha no arquivo
            if (fwrite($handle, $line) === FALSE) {
                return ['status' => 'error', 'icon' => 'error', 'text' => 'Acesso Negado ao Gerenente de Contas de E-mail'];
                exit;
            }

            shell_exec("sudo docker restart mail-server");
    
            return ['status' => 'success', 'icon' => 'success',  'text' => 'Conta de E-mail Criada com Sucesso'];
    
            // Fecha o arquivo
            fclose($handle);
        } else {
            return ['status' => 'error','icon' => 'error',  'text' => 'Acesso Negado ao Gerenente de Contas de E-mail'];
        }
    } else {
        return ['status' => 'error', 'icon' => 'error', 'text' => 'Conta de E-mail já Existe'];
    }
    
}

function getAccounts($file) {
    $accounts = [];

    $handle = fopen($file, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
           $item = explode('|', $line);

           $accounts[strtolower($item[0])] = $item[1];
        }

        fclose($handle);
    }

    return $accounts;
}

function account_exists($file, $email) {
    $accounts = getAccounts($file);
    $exists = false;

    foreach($accounts as $k => $v) {
        if($k == strtolower($email)) {
            $exists = true;
            break;
        }
    }

    return $exists;
}

file_put_contents('mail.r', print_r($_GET, true));
// Adiciona a nova conta ao arquivo postfix-accounts.cf
if(isset($_GET['nome']) && isset($_GET['mail']) && isset($_GET['password'])) {
    $hashedPassword = PasswordHasher::hashPassword($password);

    $stmt = $pdo->prepare('INSERT INTO `CONTAS_EMAIL`(`nome`, `email`, `senha`) VALUES (:nome, :email, :senha)');
    $stmt->bindValue(':nome', strtoupper($_GET['nome']));
    $stmt->bindValue(':email', strtolower($_GET['mail']));
    $stmt->bindValue(':senha', $hashedPassword);

    
    try {
        $stmt->execute();
        $res = addAccount($pdo, $file, $email, $hashedPassword);
        $mail_accounts = $pdo->query('SELECT * FROM CONTAS_EMAIL');
        if($res['status'] == 'success') {

            unlink($file);
            chmod($file, 0777);

            foreach($mail_accounts->fetchAll(PDO::FETCH_OBJ) as $email){
                file_put_contents($file, "{$email->email}|{SHA512-CRYPT}{$email->senha}".PHP_EOL, FILE_APPEND);
            }
        }

        $json = json_encode($res);
    } catch(Exception $ex) {
       $json = json_encode(['status' => 'error','icon' => 'info', 'text' => 'Não foi Possível Adicionar Esta Conta.']);
    }
    
} else {
    $json = json_encode(['status' => 'error','icon' => 'info', 'text' => 'Nenhum dado Enviado']);
}

header('Content-Type: application/json');
echo $json;
?>
