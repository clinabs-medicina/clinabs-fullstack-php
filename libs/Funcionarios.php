<?php
class Funcionarios
{
  private $pdo;
  public $lastException;

  public function __construct($pdo)
  {
    $this->lastException = null;

    $this->pdo = $pdo;
  }

  public function Add($funcionario): bool
  {
    $sql = 'INSERT INTO FUNCIONARIOS (nome_completo, nacionalidade, nome_preferencia, identidade_genero, cpf, rg, data_nascimento, telefone, enderecos, celular, email, senha, doc_rg_frente, doc_rg_verso, doc_cpf_frente, doc_cpf_verso, doc_comp_residencia, termos, receber_emails, token) 
    VALUES (:nome_completo, :nacionalidade, :nome_preferencia, :identidade_genero, :cpf, :rg, :data_nascimento, :telefone, :enderecos, :celular, :email, :senha, :doc_rg_frente, :doc_rg_verso, :doc_cpf_frente, :doc_cpf_verso, :doc_comp_residencia, :termos, :receber_emails, :token)';

    $stmt = $this->pdo->prepare($sql);

    $stmt->bindValue(':nome_completo', $funcionario->nome_completo);
    $stmt->bindValue(':nacionalidade', $funcionario->nacionalidade);
    $stmt->bindValue(':nome_preferencia', $funcionario->nome_preferencia);
    $stmt->bindValue(':identidade_genero', $funcionario->identidade_genero);
    $stmt->bindValue(':cpf', $funcionario->cpf);
    $stmt->bindValue(':rg', $funcionario->rg);
    $stmt->bindValue(':data_nascimento', $funcionario->data_nascimento);
    $stmt->bindValue(':telefone', $funcionario->telefone);
    $stmt->bindValue(':enderecos', $funcionario->enderecos);
    $stmt->bindValue(':celular', $funcionario->celular);
    $stmt->bindValue(':email', $funcionario->email);
    $stmt->bindValue(':senha', $funcionario->senha);
    $stmt->bindValue(':doc_rg_frente', $funcionario->doc_rg_frente);
    $stmt->bindValue(':doc_rg_verso', $funcionario->doc_rg_verso);
    $stmt->bindValue(':doc_cpf_frente', $funcionario->doc_cpf_frente);
    $stmt->bindValue(':doc_cpf_verso', $funcionario->doc_cpf_verso);
    $stmt->bindValue(':doc_comp_residencia', $funcionario->doc_comp_residencia);
    $stmt->bindValue(':termos', $funcionario->termos);
    $stmt->bindValue(':receber_emails', $funcionario->receber_emails);
    $stmt->bindValue(':token', uniqid());

    try {
      $stmt->execute();
      return $stmt->rowCount() > 0;
    } catch (PDOException $error) {
      $this->lastException = $error;
      return false;
    }
  }

  public function Update($funcionario): bool
  {
    $sql = 'UPDATE FUNCIONARIOS SET nome_completo = :nome_completo, nacionalidade = :nacionalidade, nome_preferencia = :nome_preferencia, identidade_genero = :identidade_genero, cpf = :cpf, rg = :rg, data_nascimento = :data_nascimento, telefone = :telefone, cep = :cep, numero = :numero, endereco = :endereco, complemento = :complemento, cidade = :cidade, uf = :uf, celular = :celular, email = :email WHERE token = :token';

    $stmt = $this->pdo->prepare($sql);

    $stmt->bindValue(':nome_completo', $funcionario->nome_completo);
    $stmt->bindValue(':nacionalidade', $funcionario->nacionalidade);
    $stmt->bindValue(':nome_preferencia', $funcionario->nome_preferencia);
    $stmt->bindValue(':identidade_genero', $funcionario->identidade_genero);
    $stmt->bindValue(':cpf', $funcionario->cpf);
    $stmt->bindValue(':rg', $funcionario->rg);
    $stmt->bindValue(':data_nascimento', $funcionario->data_nascimento);
    $stmt->bindValue(':telefone', $funcionario->telefone);
    $stmt->bindValue(':cep', $funcionario->cep);
    $stmt->bindValue(':numero', $funcionario->numero);
    $stmt->bindValue(':endereco', $funcionario->endereco);
    $stmt->bindValue(':complemento', $funcionario->complemento);
    $stmt->bindValue(':cidade', $funcionario->cidade);
    $stmt->bindValue(':uf', $funcionario->uf);
    $stmt->bindValue(':celular', $funcionario->celular);
    $stmt->bindValue(':email', $funcionario->email);

    $stmt->bindValue(':token', $funcionario->token);

    try {
      $stmt->execute();

      return $stmt->rowCount() > 0;
    } catch (PDOException $error) {
      $this->lastException = $error;

      return null;
    }
  }

  public function Delete($token)
  {
    $sql = "UPDATE FUNCIONARIOS SET `status` = 'DELETADO' WHERE token = :token";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':token', $token);
    $stmt->execute();
    return $stmt->rowCount() > 0;
  }

  public function getAll()
  {
    $sql = "SELECT * FROM FUNCIONARIOS WHERE `status` = 'ATIVO'";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function getAllNames()
  {
    $sql = 'SELECT nome_completo FROM FUNCIONARIOS';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function getArrayById(int $id)
  {
    $sql = 'SELECT * FROM FUNCIONARIOS WHERE id = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getDocuments(int $id)
  {
    $sql = 'SELECT
    TO_BASE64 ( profileImage ) AS profileImage,
    TO_BASE64 ( doc_rg_frente ) AS doc_rg_frente,
    TO_BASE64 ( doc_rg_verso ) AS doc_rg_verso,
    TO_BASE64 ( doc_cpf_frente ) AS doc_cpf_frente,
    TO_BASE64 ( doc_cpf_verso ) AS doc_cpf_verso,
    TO_BASE64 ( doc_comp_residencia ) AS doc_comp_residencia
  FROM
    FUNCIONARIOS 
  WHERE
    id = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getById(int $id)
  {
    $sql = 'SELECT * FROM FUNCIONARIOS WHERE id = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getFuncionarioByCpf(string $cpf)
  {
    $sql = 'SELECT * FROM FUNCIONARIOS WHERE cpf = :cpf';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':cpf', $cpf);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getFuncionarioByToken(string $token)
  {
    $sql = 'SELECT * FROM FUNCIONARIOS WHERE token = :token';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':token', $token);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getFuncionarioByEmail(string $email)
  {
    $sql = 'SELECT * FROM FUNCIONARIOS WHERE email = :email';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getFuncionarioByEmailAndSenha(string $email, string $senha)
  {
    $sql = 'SELECT * FROM FUNCIONARIOS WHERE email = :email AND senha = :senha';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':senha', md5(sha1(md5($senha))));
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getFuncionarioByNome(string $nome)
  {
    $sql = 'SELECT * FROM FUNCIONARIOS WHERE nome_completo = :nome';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':nome', $nome);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getFuncionarioByNomePreferencia(string $nome)
  {
    $sql = 'SELECT * FROM FUNCIONARIOS WHERE nome_preferencia = :nome';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':nome', $nome);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }
}
