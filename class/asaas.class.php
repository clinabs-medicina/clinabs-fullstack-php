<?php
class ASAAS
{
  private string $url;
  private string $api_key;
  private PDO $db;
  private string $wallet_id = '978b015e-064e-4e4e-b7ef-f11cc3572dc4';

  public function __construct(PDO $pdo, string $api_key, string $wallet_id, bool $sandbox = true)
  {
    $this->api_key = $api_key;
    $this->wallet_id = $wallet_id;
    $this->db = $pdo;

    if ($sandbox) {
      $this->url = 'https://sandbox.asaas.com/api/v3';
    } else {
      $this->url = 'https://api.asaas.com/v3';
    }
  }

  public function getAllPendings()
  {
    return $this->get('lean/payments?status=PENDING');
  }

  public function refund($id, $valor, $description)
  {
    return $this->post("payments/$id/refund", [
      'value' => preg_replace('/[^0-9]/', '', $valor),
      'description' => $description
    ]);
  }

  public function get_status($code)
  {
    return strtr($code, array(
      'RECEIVED' => 'PAGAMENTO RECEBIDO',
      'PENDING' => 'PAGAMENTO PENDENTE',
      'UNDEFINED' => 'NÃO DEFINIDO PELO CLIENTE',
      'BOLETO' => 'BOLETO BANCÁRIO',
      'RECEIVED_IN_CASH' => 'RECEBIMENTO EXTERNO',
      'REFUND_REQUESTED' => 'ESTORNO SOLICITADO',
      'CREDIT_CARD' => 'CARTÃO DE CRÉDITO',
      'DEBIT_CARD' => 'CARTÃO DE DÉBITO',
      'CONFIRMED' => 'PAGAMENTO CONFIRMADO',
      'REFUNDED' => 'PAGAMENTO ESTORNADO',
      'OVERDUE' => 'PAGAMENTO VENCIDO',
      'PAYMENT_RECEIVED' => 'PAGAMENTO RECEBIDO',
      'PAYMENT_FEE' => 'TAXA DE SERVIÇO',
      'TRANSFER' => 'TRANSFERÊNCIA TED/PIX'
    ));
  }

  public function getPix($id)
  {
    return $this->get("payments/$id/pixQrCode");
  }

  public function extrato()
  {
    return $this->get('financialTransactions');
  }

  public function novoCliente(string $token, string $nome, string $cpf, string $email, string $celular)
  {
    $data = [
      'name' => trim(strtoupper($nome)),
      'cpfCnpj' => trim(preg_replace('/[^A-Za-z0-9]/', '', $cpf)),
      'email' => trim(strtolower($email)),
      'mobilePhone' => trim(preg_replace('/[^A-Za-z0-9]/', '', $celular)),
      'notificationDisabled' => true,
      'groupName' => 'PACIENTES',
      'company' => 'CLINABS',
      'externalReference' => trim($token)
    ];

    $clientes = $this->listarClientes();

    $exists = false;
    $tk = '';

    foreach ($clientes->data as $cliente) {
      if ($data['cpfCnpj'] == $cliente->cpfCnpj) {
        $exists = true;
        $tk = $cliente->id;
        break;
      }
    }

    if (!$exists) {
      return $this->post('customers', $data);
    } else {
      return $this->getCliente($tk);
    }
  }

  public function editarCliente(string $id, string $token, string $nome, string $cpf, string $email, string $celular)
  {
    $data = [
      'name' => trim(strtoupper($nome)),
      'cpfCnpj' => trim(preg_replace('/[^A-Za-z0-9]/', '', $cpf)),
      'email' => trim(strtolower($email)),
      'mobilePhone' => trim(preg_replace('/[^A-Za-z0-9]/', '', $celular)),
      'notificationDisabled' => true,
      'groupName' => 'PACIENTES',
      'company' => 'CLINABS',
      'externalReference' => trim($token)
    ];

    try {
      return $this->put("customers/$id", $data);
    } catch (Exception $ex) {
      return $ex->getMessage();
    }
  }

  public function listarClientes()
  {
    return $this->get('customers?limit=1000');
  }

  public function getCliente($id)
  {
    return $this->get("customers/$id");
  }

  public function getPaciente(string $token, string $key = '*')
  {
    $clientes = $this->listarClientes();
    $result = ['error' => 'Cliente não Encontrado!'];
    $exists = false;

    foreach ($clientes->data as $cliente) {
      if ($cliente->externalReference == $token) {
        $result = $cliente;
        $exists = true;
        break;
      }
    }

    if ($key != '*' && $exists) {
      return $result->$key;
    } else {
      return $result;
    }
  }

  public function deleteCliente(string $token)
  {
    $response = $this->delete('customers/' . $token);
    return $response;
  }

  public function cobrarCliente(string $id, string $reference, int $valor, string $descricao, $type = 'UNDEFINED')
  {
    $data = [
      'billingType' => $type,
      'customer' => $id,
      'value' => $valor,
      'dueDate' => date('Y-m-d'),
      'description' => $descricao,
      'externalReference' => $reference,
      'callback' => [
        'successUrl' => 'https://' . $_SERVER['HTTP_HOST'] . '/agenda/',
        'autoRedirect' => true
      ]
    ];

    $response = $this->request('POST', 'payments', $data);
    return $response;
  }

  public function cobrarPIX($id, $valor, $reference)
  {
    $data = [
      'billingType' => 'PIX',
      'customer' => $id,
      'value' => $valor,
      'dueDate' => date('Y-m-d'),
      'description' => "TELECONSULTA #{$reference}",
      'externalReference' => $reference,
      'totalValue' => $valor
    ];

    return $this->post('payments', $data);
  }

  public function cobrarCredito($id, $valor, $reference)
  {
    $data = [
      'billingType' => 'CREDIT_CARD',
      'chargeType' => 'DETACHED',
      'name' => "CONSULTA MÉDICA #{$reference}",
      'description' => 'TELECONSULTA CLINABS',
      'endDate' => date('Y-m-d', strtotime(date('Y-m-d') . ' + 1 days')),
      'dueDateLimitDays' => 2,
      'maxInstallmentCount' => 2,
    ];

    return $this->post('payments', $data);
  }

  public function cobrarCc($id, $valor, $reference, $cardHolderName, $cardNumber, $expiryMonth, $expiryYear, $ccv,
    $name, $cpf, $email, $mobilePhone, $postalCode, $addressNumber, $addressComplement)
  {
    $data = [
      'billingType' => 'CREDIT_CARD',
      'creditCard' => [
        'holderName' => $cardHolderName,
        'number' => $cardNumber,
        'expiryMonth' => $expiryMonth,
        'expiryYear' => $expiryYear,
        'ccv' => $ccv
      ],
      'creditCardHolderInfo' => [
        'name' => $name,
        'cpfCnpj' => $cpf,
        'email' => $email,
        'postalCode' => $postalCode,
        'addressNumber' => $addressNumber,
        'addressComplement' => $addressComplement,
        'phone' => $mobilePhone,
        'mobilePhone' => $mobilePhone
      ],
      'customer' => $id,
      'value' => $valor,
      'dueDate' => date('Y-m-d'),
      'description' => "TELECONSULTA #{$reference}",
      'externalReference' => $reference
    ];

    return $this->post('payments', $data);
  }

  public function listarCobrancas($status = null, $dueDate = null)
  {
    if ($status != null && $dueDate != null) {
      return $this->get("payments?limit=10000&status=PENDING&dueDate[le]={$dueDate}")->data;
    } else {
      return $this->get('payments?limit=10000')->data;
    }
  }

  public function getCobranca($id)
  {
    return $this->get("payments/{$id}");
  }

  public function deleteCobranca($id)
  {
    return $this->delete("payments/{$id}");
  }

  public function desfazerCobrancaRemovida($id)
  {
    return $this->post("payments/{$id}/restore");
  }

  public function getPixInfo($id)
  {
    return $this->get("payments/$id/pixQrCode");
  }

  public function accountBalance()
  {
    $response = $this->get('finance/balance')->balance;
    return $response;
  }

  public function paymentsBalance()
  {
    return $this->get('finance/payment/statistics');
  }

  public function receber_dinheiro($code)
  {
    $payment = $this->getCobranca($code);

    return $this->post("payments/{$code}/receiveInCash", [
      'paymentDate' => date('Y-m-d'),
      'value' => $payment->value,
      'notifyCustomer' => false
    ]);
  }

  public function desfazer_recebimento_dinheiro($code)
  {
    $payment = $this->getCobranca($code);

    return $this->post("payments/{$code}/undoReceivedInCash");
  }

  public function total_receber()
  {
    $response = $this->get('finance/payment/statistics?status=PENDING');
    return $response;
  }

  public function total_receber_boleto()
  {
    $response = $this->get('finance/payment/statistics?billingType=BOLETO&status=PENDING');
    return $response;
  }

  public function total_receber_ccredito()
  {
    $response = $this->get('finance/payment/statistics?billingType=CREDIT_CARD&status=RECEIVED');
    return $response;
  }

  public function total_receber_split()
  {
    $response = $this->get('finance/split/statistic');
    return $response;
  }

  public function account_info()
  {
    $response = $this->get('myAccount/accountNumber');
    return $response;
  }

  public function account_taxas()
  {
    $response = $this->get('myAccount/fees');
    return $response;
  }

  public function importPaciente($token)
  {
    $stmt = $this->db->prepare('SELECT nome_completo,cpf,email,celular FROM PACIENTES WHERE token = :token');
    $stmt->bindValue(':token', $token);

    try {
      $stmt->execute();

      $paciente = $stmt->fetch(PDO::FETCH_OBJ);

      $user = [];

      if (!empty($paciente->nome_completo) && !empty($paciente->cpf) && !empty($paciente->email) && !empty($paciente->celular)) {
        $add = $this->novoCliente(
          token: $token,
          nome: $paciente->nome_completo,
          cpf: $paciente->cpf,
          email: $paciente->email,
          celular: $paciente->celular
        );

        return $add;
      } else {
        return ['status' => 'warning', 'reason' => 'Verifique se os Dados do paciente [nome_completo ,cpf ,email, celular] está preenchidos corretamente!'];
      }
    } catch (Exception $ex) {
      return ['error' => $ex->getMessage()];
    }
  }

  public function cobrar(string $id, string $tipo, string $valor, string $reference, string $descricao, string $paymentDue)
  {
    $data = [
      'billingType' => $tipo,
      'customer' => $id,
      'value' => $valor,
      'dueDate' => $paymentDue,
      'description' => $descricao,
      'externalReference' => $reference,
      'callback' => [
        'successUrl' => 'https://' . $_SERVER['HTTP_HOST'] . '/agenda/',
        'autoRedirect' => true
      ]
    ];

    $response = $this->request('POST', 'payments', $data);

    if ($tipo == 'PIX') {
      $response->pixPayload = $this->getPixInfo($response->id);
    }

    return $response;
  }

  public function getClients()
  {
    $response = $this->request('GET', 'customers', ['limit' => 1000]);
    if (isset($response->data)) {
      return $response->data;
    } else {
      return $response;
    }
  }

  public function getPayments()
  {
    $response = $this->request('GET', 'payments', ['limit' => 1000]);
    if (isset($response->data)) {
      return $response->data;
    } else {
      return $response;
    }
  }

  public function getClient(string $token)
  {
    $response = $this->request('GET', "customers/$token");
    return $response;
  }

  public function getClientByReference(string $token)
  {
    $clients = $this->getClients();

    $client = [];
    $exists = false;

    foreach ($clients as $item) {
      if ($item->externalReference == $token) {
        $client = $item;
        $exists = true;
        break;
      }
    }

    if ($exists) {
      return $client;
    } else {
      return [
        'errors' => [
          [
            'code' => 'NOT_FOUND',
            'message' => 'Cliente não encontrado!'
          ]
        ]
      ];
    }
  }

  public function create_or_get_client(string $token, string $nome, string $cpf, string $email, string $celular)
  {
    try {
      $data = [
        'name' => trim(strtoupper($nome)),
        'cpfCnpj' => trim(preg_replace('/[^A-Za-z0-9]/', '', $cpf)),
        'email' => trim(strtolower($email)),
        'mobilePhone' => trim(preg_replace('/[^A-Za-z0-9]/', '', $celular)),
        'notificationDisabled' => true,
        'groupName' => 'PACIENTES',
        'company' => 'CLINABS',
        'externalReference' => trim($token)
      ];

      $clients = $this->getClients();
      $c = [];

      $exists = false;

      foreach ($clients as $client) {
        if ($client->externalReference == trim($token) || $client->cpfCnpj == trim(preg_replace('/[^A-Za-z0-9]/', '', $cpf))) {
          $exists = true;
          $c = $client;
          break;
        }
      }

      if ($exists) {
        return $c;
      } else {
        try {
          $response = $this->request('POST', 'customers', $data);

          return $response;
        } catch (Exception $ex) {
          return null;
        }
      }
    } catch (Exception $ex) {
      return null;
    }
  }

  private function request($method, $path, $data = [])
  {
    $request_headers = [
      'Content-Type: application/json',
      'Accept: application/json',
      "access_token: {$this->api_key}",
      'User-Agent: PostmanRuntime/7.40.0'
    ];

    if (strtoupper($method) == 'GET') {
      $query = '?' . http_build_query($data);
    } else {
      $query = '';
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "{$this->url}/{$path}{$query}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);

    if (strtoupper($method) != 'GET') {
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
      return curl_error($ch);
    } else {
      curl_close($ch);

      return json_decode($response);
    }
  }

  private function put($path, $data = [])
  {
    $client = new \GuzzleHttp\Client();

    try {
      $response = $client->request('PUT', "{$this->url}/{$path}", [
        'body' => json_encode($data),
        'headers' => [
          'accept' => 'application/json',
          'access_token' => $this->api_key,
          'content-type' => 'application/json',
        ],
      ]);

      return json_decode($response->getBody());
    } catch (Exception $e) {
      return 'Ocorreu um Erro ao Processar a Solicitação';
    }
  }

  private function post($path, $data = [])
  {
    $client = new \GuzzleHttp\Client();

    try {
      $response = $client->request('POST', "{$this->url}/{$path}", [
        'body' => json_encode($data),
        'headers' => [
          'accept' => 'application/json',
          'access_token' => $this->api_key,
          'content-type' => 'application/json',
        ],
      ]);

      return json_decode($response->getBody());
    } catch (Exception $e) {
      // Find the position of the first curly brace `{` and the last closing square bracket `]`
      $startPos = strpos($e->getMessage(), '{');
      $endPos = strrpos($e->getMessage(), ']');

      // Ensure valid positions are found
      if ($startPos !== false && $endPos !== false && $endPos > $startPos) {
        // Extract the substring from { to ]
        $partialData = substr($e->getMessage(), $startPos, $endPos - $startPos + 1);

        return json_decode($partialData . '}')->errors[0];
      } else {
        return 'Ocorreu um Erro ao Processar a Solicitação';
      }
    }
  }

  private function get($path, $data = [])
  {
    $client = new \GuzzleHttp\Client();

    try {
      $response = $client->request('GET', "{$this->url}/{$path}", [
        'headers' => [
          'accept' => 'application/json',
          'access_token' => $this->api_key,
        ],
      ]);

      return json_decode($response->getBody());
    } catch (Exception $e) {
      // Find the position of the first curly brace `{` and the last closing square bracket `]`
      $startPos = strpos($e->getMessage(), '{');
      $endPos = strrpos($e->getMessage(), ']');

      // Ensure valid positions are found
      if ($startPos !== false && $endPos !== false && $endPos > $startPos) {
        // Extract the substring from { to ]
        $partialData = substr($e->getMessage(), $startPos, $endPos - $startPos + 1);

        return json_decode($partialData . '}')->errors[0];
      } else {
        return 'Ocorreu um Erro ao Processar a Solicitação';
      }
    }
  }

  private function delete($path, $data = [])
  {
    $client = new \GuzzleHttp\Client();

    try {
      $response = $client->request('DELETE', "{$this->url}/{$path}", [
        'headers' => [
          'accept' => 'application/json',
          'access_token' => $this->api_key,
          'content-type' => 'application/json;charset=utf-8'
        ],
      ]);

      return json_decode($response->getBody());
    } catch (\Exception $e) {
      return $this->parse_error($e->getMessage());
    }
  }

  private function parse_error($error)
  {
    $ex = explode('response:', $error)[1];
    $ex = str_replace('\n', '', $ex);

    $errors = [
      'invalid_cpfCnpj' => 'CPF Inválido'
    ];

    $data = json_decode($ex, true);

    $result = [];

    foreach ($data['errors'] as $item) {
      $result[] = [
        'code' => $item['code'],
        'description' => $errors[$item['code']]
      ];
    }

    return json_decode($ex);
  }
}
