<?php
class Carrinho {
  private $pdo;
  public $lastException;

  public function __construct($pdo) {
    $this->lastException = null;

    $this->pdo = $pdo;
  }

  public function add($product_id, $pid, $user_doc,$user_ref, $qtde=null) {
    $stmt = $this->pdo->prepare("INSERT INTO CARRINHO (product_id, user_doc, pid, user_ref) VALUES (:product_id, :user_doc, :pid, :user_ref) ON DUPLICATE KEY UPDATE qtde = qtde + 1");
    
    $stmt->bindValue(":product_id", $product_id);
    $stmt->bindValue(":user_doc", $user_doc);
    $stmt->bindValue(":pid", $pid);
    $stmt->bindValue(":user_ref", $user_ref);
   
    try {
      $stmt->execute();
      return $stmt->rowCount() > 0 ? ['status' => 'success'] : ['status' => 'danger'];
    }catch(PDOException $error) {
      file_put_contents('carrinho.error.log', print_r($error, true));
      return ['status' => 'danger'];
    }
    
    
  }

  public function getAll($ref) {
    $sql = "SELECT CARRINHO.id AS `index`,CARRINHO.user_doc, PRODUTOS.nome, PRODUTOS.descricao, PRODUTOS.valor_venda, PRODUTOS.image, PRODUTOS.id, CARRINHO.pid, CARRINHO.qtde FROM CARRINHO, PRODUTOS WHERE CARRINHO.product_id = PRODUTOS.id AND CARRINHO.user_ref = :user_ref ORDER BY CARRINHO.`timestamp` ASC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':user_ref', $ref);
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_OBJ);

    return $produtos;
  }

  public function Sum($ref) {
    $sql = "SELECT SUM(PRODUTOS.valor_venda) AS total FROM PRODUTOS, CARRINHO WHERE PRODUTOS.id = CARRINHO.product_id AND CARRINHO.user_ref = :user_ref GROUP BY CARRINHO.user_doc";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':user_ref', $ref);
    $stmt->execute();
    $produtos = $stmt->fetch(PDO::FETCH_OBJ);

    return $produtos->total;
  }

  public function getById($id, $cpf) {
    $sql = "SELECT CARRINHO.id AS `index`,CARRINHO.user_doc, PRODUTOS.nome, PRODUTOS.descricao, PRODUTOS.valor_venda, PRODUTOS.image, PRODUTOS.id, CARRINHO.pid, CARRINHO.qtde FROM CARRINHO, PRODUTOS WHERE CARRINHO.product_id = PRODUTOS.id AND CARRINHO.user_doc = :user_doc AND CARRINHO.product_id = :id ORDER BY CARRINHO.`timestamp` ASC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':user_doc', $cpf);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $produtos = $stmt->fetch(PDO::FETCH_OBJ);

    return $produtos;
  }

  public function getByPid($pid, $cpf) {
    $sql = "SELECT CARRINHO.id AS `index`,PRODUTOS.codigo AS sku,CARRINHO.user_doc, PRODUTOS.nome, PRODUTOS.descricao, PRODUTOS.valor_venda, PRODUTOS.image, PRODUTOS.id, CARRINHO.pid, CARRINHO.qtde FROM CARRINHO, PRODUTOS WHERE CARRINHO.product_id = PRODUTOS.id AND CARRINHO.user_doc = :user_doc AND CARRINHO.pid = :pid ORDER BY CARRINHO.`timestamp` ASC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':user_doc', $cpf);
    $stmt->bindValue(':pid', $pid);
    $stmt->execute();
    $produtos = $stmt->fetch(PDO::FETCH_OBJ);

    return $produtos;
  }


  public function removeItem($pid, $ref) {
    if($pid == 'all'){
      $sql = "DELETE FROM CARRINHO WHERE CARRINHO.user_ref = :user_ref";

      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':user_ref', $ref);
    }else {
      $sql = "DELETE FROM CARRINHO WHERE CARRINHO.user_ref = :user_ref AND CARRINHO.pid = :pid";
      
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':user_ref', $ref);
      $stmt->bindValue(':pid', $pid);
    }
    
    try{
      $stmt->execute();
      return true;
   }catch(PDOException $error){
     $this->lastException = $error;
     return false;
   }
  }

  public function update($pid, $qtde) {
    $sql = "UPDATE CARRINHO SET qtde = :qtde WHERE pid = :pid";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':qtde', $qtde);
    $stmt->bindValue(':pid', $pid);
    $stmt->execute();

    return $stmt->rowCount() > 0;
  }
}