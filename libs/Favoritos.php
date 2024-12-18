<?php
class Favoritos {
  private $pdo;
  public $lastException;

  public function __construct($pdo) {
    $this->lastException = null;

    $this->pdo = $pdo;
  }

  public function add($product_id, $pid, $user_doc) {
    $stmt = $this->pdo->prepare("INSERT INTO FAVORITOS (product_id, user_doc, pid) VALUES (:product_id, :user_doc, :pid)");
    
    $stmt->bindValue(":product_id", $product_id);
    $stmt->bindValue(":user_doc", $user_doc);
    $stmt->bindValue(":pid", $pid);
    $stmt->execute();
    
    return $stmt->rowCount() > 0 ? ['status' => 'success'] : ['status' => 'danger'];
  }

  public function getAll($cpf) {
    $sql = "SELECT FAVORITOS.id AS `index`,FAVORITOS.user_doc, PRODUTOS.nome, PRODUTOS.descricao, PRODUTOS.valor_venda, PRODUTOS.image, PRODUTOS.id, FAVORITOS.pid FROM FAVORITOS, PRODUTOS WHERE FAVORITOS.product_id = PRODUTOS.id AND FAVORITOS.user_doc = :user_doc ORDER BY FAVORITOS.`timestamp` ASC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':user_doc', $cpf);
    $stmt->execute();
    $produtos = $stmt->fetchAll(PDO::FETCH_OBJ);

    return $produtos;
  }

  public function Sum($cpf) {
    $sql = "SELECT SUM(FAVORITOS.valor_venda) AS total FROM PRODUTOS, CARRINHO WHERE FAVORITOS.id = FAVORITOS.product_id AND FAVORITOS.user_doc = :user_doc GROUP BY FAVORITOS.user_doc";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':user_doc', $cpf);
    $stmt->execute();
    $produtos = $stmt->fetch(PDO::FETCH_OBJ);

    return $produtos->total;
  }

  public function getById($id, $cpf) {
    $sql = "SELECT FAVORITOS.id AS `index`,FAVORITOS.user_doc, FAVORITOS.nome, FAVORITOS.descricao, FAVORITOS.valor_venda, FAVORITOS.image, FAVORITOS.id, FAVORITOS.pid, FAVORITOS.qtde FROM CARRINHO, PRODUTOS WHERE FAVORITOS.product_id = FAVORITOS.id AND FAVORITOS.user_doc = :user_doc AND FAVORITOS.product_id = :id ORDER BY FAVORITOS.`timestamp` ASC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':user_doc', $cpf);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $produtos = $stmt->fetch(PDO::FETCH_OBJ);

    return $produtos;
  }

  public function getByPid($pid, $cpf) {
    $sql = "SELECT FAVORITOS.id AS `index`,FAVORITOS.user_doc, FAVORITOS.nome, FAVORITOS.descricao, FAVORITOS.valor_venda, FAVORITOS.image, FAVORITOS.id, FAVORITOS.pid, FAVORITOS.qtde FROM CARRINHO, PRODUTOS WHERE FAVORITOS.product_id = FAVORITOS.id AND FAVORITOS.user_doc = :user_doc AND FAVORITOS.pid = :pid ORDER BY FAVORITOS.`timestamp` ASC";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':user_doc', $cpf);
    $stmt->bindValue(':pid', $pid);
    $stmt->execute();
    $produtos = $stmt->fetch(PDO::FETCH_OBJ);

    return $produtos;
  }


  public function removeItem($pid, $cpf) {
    if($pid == 'all'){
      $sql = "DELETE FROM CARRINHO WHERE FAVORITOS.user_doc = :user_doc";

      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':user_doc', $cpf);
    }else {
      $sql = "DELETE FROM CARRINHO WHERE FAVORITOS.user_doc = :user_doc AND FAVORITOS.pid = :pid";
      
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':user_doc', $cpf);
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
}