<?php
class Produtos
{
  private $pdo;
  public $lastException;

  public function __construct($pdo)
  {
    $this->lastException = null;

    $this->pdo = $pdo;
  }

  public function getAll()
  {
    $sql = 'SELECT * FROM PRODUTOS';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function getById($id)
  {
    $sql = 'SELECT * FROM PRODUTOS WHERE id = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    $produto = $stmt->fetch(PDO::FETCH_OBJ);
    return $produto;
  }

  public function getByToken($token)
  {
    $sql = 'SELECT * FROM PRODUTOS WHERE token = :token';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':token', $token);
    $stmt->execute();
    $produto = $stmt->fetch(PDO::FETCH_OBJ);
    return $produto;
  }

  public function Add($produto)
  {
    $sql = 'INSERT INTO PRODUTOS (nome, descricao, codigo, image, nacionalidade, valor_compra, valor_venda, valor, valor_frete_compra, unidade_medida, capacidade, fornecedor, nfe_ordem, lote, moeda, data_validade, marca, prazo_entrega, valor_frete_venda, numero_frascos, token, objeto, catalog_file) VALUES (:nome, :descricao, :codigo, :image, :nacionalidade, :valor_compra, :valor_venda, :valor, :valor_frete_compra, :unidade_medida, :capacidade, :fornecedor, :nfe_ordem, :lote, :moeda, :data_validade, :marca, :prazo_entrega, :valor_frete_venda, :numero_frascos, :token, :obj, :catalog_file)';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':nome', $produto->nome);
    $stmt->bindValue(':descricao', $produto->descricao);
    $stmt->bindValue(':codigo', $produto->codigo);
    $stmt->bindValue(':image', $produto->image);
    $stmt->bindValue(':nacionalidade', $produto->nacionalidade);
    $stmt->bindValue(':valor_compra', $produto->valor_compra);
    $stmt->bindValue(':valor_venda', $produto->valor_venda);
    $stmt->bindValue(':valor', $produto->valor_venda);
    $stmt->bindValue(':valor_frete_compra', $produto->valor_frete_compra);
    $stmt->bindValue(':unidade_medida', $produto->unidade_medida);
    $stmt->bindValue(':capacidade', $produto->capacidade);
    $stmt->bindValue(':fornecedor', $produto->fornecedor);
    $stmt->bindValue(':nfe_ordem', $produto->nfe);
    $stmt->bindValue(':lote', $produto->lote);
    $stmt->bindValue(':moeda', $produto->moeda);
    $stmt->bindValue(':data_validade', $produto->data_validade);
    $stmt->bindValue(':marca', $produto->marca);
    $stmt->bindValue(':prazo_entrega', $produto->prazo_entrega);
    $stmt->bindValue(':valor_frete_venda', $produto->valor_frete_venda);
    $stmt->bindValue(':numero_frascos', $produto->numero_frascos);
    $stmt->bindValue(':token', uniqid());
    $stmt->bindValue(':obj', 'PRODUTO');
    $stmt->bindValue(':catalog_file', $produto->catalog_file);

    try {
      $stmt->execute();
      return true;
    } catch (PDOException $error) {
      $this->lastException = $error;
      return false;
    }
  }

  public function Update($produto)
  {
    $sql = 'UPDATE PRODUTOS SET 
    nome = :nome,
    valor = :valor, 
    descricao = :descricao, 
    codigo = :codigo, 
    nacionalidade = :nacionalidade, 
    valor_compra = :valor_compra, 
    valor_venda = :valor_venda,
    valor = :valor_venda, 
    valor_frete_compra = :valor_frete_compra, 
    unidade_medida = :unidade_medida, 
    capacidade = :capacidade, 
    fornecedor = :fornecedor, 
    nfe_ordem = :nfe_ordem, 
    lote = :lote,
    moeda = :moeda,
    data_validade = :data_validade, 
    marca = :marca, 
    prazo_entrega = :prazo_entrega, 
    valor_frete_venda = :valor_frete_venda, 
    numero_frascos = :numero_frascos,
    catalog_file = :catalog_file,
    image = :image,
    status = :status,
    excluido = :excluir
    WHERE token = :token';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':nome', $produto->nome);
    $stmt->bindValue(':valor', $produto->valor);
    $stmt->bindValue(':descricao', $produto->descricao);
    $stmt->bindValue(':codigo', $produto->codigo);
    $stmt->bindValue(':nacionalidade', $produto->nacionalidade);
    $stmt->bindValue(':valor_compra', $produto->valor_compra);
    $stmt->bindValue(':valor_venda', $produto->valor_venda);
    $stmt->bindValue(':valor', $produto->valor_venda);
    $stmt->bindValue(':valor_frete_compra', $produto->valor_frete_compra);
    $stmt->bindValue(':unidade_medida', $produto->unidade_medida);
    $stmt->bindValue(':capacidade', $produto->capacidade);
    $stmt->bindValue(':fornecedor', $produto->fornecedor);
    $stmt->bindValue(':nfe_ordem', $produto->nfe);
    $stmt->bindValue(':lote', $produto->lote);
    $stmt->bindValue(':moeda', $produto->moeda);
    $stmt->bindValue(':data_validade', $produto->data_validade);
    $stmt->bindValue(':marca', $produto->marca);
    $stmt->bindValue(':prazo_entrega', $produto->prazo_entrega);
    $stmt->bindValue(':valor_frete_venda', $produto->valor_frete_venda);
    $stmt->bindValue(':numero_frascos', $produto->numero_frascos);
    $stmt->bindValue(':token', $produto->token);
    $stmt->bindValue(':image', $produto->image);
    $stmt->bindValue(':catalog_file', $produto->catalog_file);
    $stmt->bindValue(':status', $produto->status);
    $stmt->bindValue(':excluir', $produto->excluir);

    try {
      $stmt->execute();
      return true;
    } catch (PDOException $error) {
      $this->lastException = $error;
      return false;
    }
  }
}
