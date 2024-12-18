<?php
class CarrinhoCalc {
  public $pdo;
  
  public function __construct($pdo) {
    $this->pdo = $pdo;
  }
  
  public function getProdByPromo($pid, $qtde) {
    $stmt = $this->pdo->prepare("SELECT * FROM PRODUTOS_PROMOCOES WHERE produto_id = {$pid} AND {$qtde} >= frascos ORDER BY frascos DESC");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);


    if($stmt->rowCount() == 0) {
      $stmt2 = $this->pdo->prepare("SELECT * FROM PRODUTOS WHERE id = {$pid}");
      $stmt2->execute();
      $result = $stmt2->fetch(PDO::FETCH_ASSOC);

      return [
        'nome' => $result['nome'],
        'produto_id' => $result['id'],
        'frascos' => 1,
        'valor' => $result['valor'],
        'valor_frete' => $result['valor_frete_venda'],
        'valor_total' => (($result['valor']))
      ];
    }else {
      $stmt = $this->pdo->prepare("SELECT * FROM PRODUTOS_PROMOCOES WHERE produto_id = {$pid} AND {$qtde} >= frascos ORDER BY frascos DESC");
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $result['valor_total'] = (floatval($result['valor']) + $result['valor_frete']);
      return $result;
    }
  }
}
