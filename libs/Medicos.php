<?php
class Medicos
{
  private $pdo;
  public $lastException;

  public function __construct($pdo)
  {
    $this->lastException = null;

    $this->pdo = $pdo;
  }

  public function Add($medico): bool
  {
    $sql = 'INSERT INTO MEDICOS (tipo_conselho, uf_conselho, num_conselho, nome_completo, nacionalidade, nome_preferencia, identidade_genero, cpf, rg, data_nascimento, telefone, celular, email, senha, termos, receber_emails, doc_rg_frente, doc_rg_verso, doc_cpf_frente, doc_cpf_verso, doc_comp_residencia, token, perm) 
    VALUES (:tipo_conselho, :uf_conselho, :num_conselho, :nome_completo, :nacionalidade, :nome_preferencia, :identidade_genero, :cpf, :rg, :data_nascimento, :telefone, :celular, :email, :senha, :termos, :receber_emails, :doc_rg_frente, :doc_rg_verso, :doc_cpf_frente, :doc_cpf_verso, :doc_comp_residencia, :token, :perm)';

    $stmt = $this->pdo->prepare($sql);

    $stmt->bindValue(':tipo_conselho', $medico->tipo_conselho);
    $stmt->bindValue(':uf_conselho', $medico->uf_conselho);
    $stmt->bindValue(':num_conselho', $medico->num_conselho);
    $stmt->bindValue(':nome_completo', trim($medico->nome_completo));
    $stmt->bindValue(':nacionalidade', $medico->nacionalidade);
    $stmt->bindValue(':nome_preferencia', $medico->nome_preferencia);
    $stmt->bindValue(':identidade_genero', $medico->identidade_genero);
    $stmt->bindValue(':cpf', $medico->cpf);
    $stmt->bindValue(':rg', $medico->rg);
    $stmt->bindValue(':data_nascimento', $medico->data_nascimento);
    $stmt->bindValue(':telefone', $medico->telefone);
    $stmt->bindValue(':celular', $medico->celular);
    $stmt->bindValue(':email', $medico->email);
    $stmt->bindValue(':senha', $medico->senha);
    $stmt->bindValue(':termos', $medico->termos);
    $stmt->bindValue(':receber_emails', $medico->receber_emails);
    $stmt->bindValue(':doc_rg_frente', $medico->doc_rg_frente);
    $stmt->bindValue(':doc_rg_verso', $medico->doc_rg_verso);
    $stmt->bindValue(':doc_cpf_frente', $medico->doc_cpf_frente);
    $stmt->bindValue(':doc_cpf_verso', $medico->doc_cpf_verso);
    $stmt->bindValue(':doc_comp_residencia', $medico->doc_comp_residencia);
    $stmt->bindValue(':token', $medico->token);
    $stmt->bindValue(':perm', 3);

    try {
      $stmt->execute();
      return true;
    } catch (PDOException $error) {
      $this->lastException = $error;
      return false;
    }
  }

  public function Update($medico): bool
  {
    $sql = 'UPDATE MEDICOS SET nome_completo = :nome_completo, nacionalidade = :nacionalidade, nome_preferencia = :nome_preferencia, identidade_genero = :identidade_genero, cpf = :cpf, rg = :rg, data_nascimento = :data_nascimento, telefone = :telefone, cep = :cep, numero = :numero, endereco = :endereco, complemento = :complemento, cidade = :cidade, bairro = :bairro, uf = :uf, celular = :celular, email = :email WHERE token = :token';

    $stmt = $this->pdo->prepare($sql);

    $stmt->bindValue(':nome_completo', $medico->nome_completo);
    $stmt->bindValue(':nacionalidade', $medico->nacionalidade);
    $stmt->bindValue(':nome_preferencia', $medico->nome_preferencia);
    $stmt->bindValue(':identidade_genero', $medico->identidade_genero);
    $stmt->bindValue(':cpf', $medico->cpf);
    $stmt->bindValue(':rg', $medico->rg);
    $stmt->bindValue(':data_nascimento', $medico->data_nascimento);
    $stmt->bindValue(':telefone', $medico->telefone);
    $stmt->bindValue(':cep', $medico->cep);
    $stmt->bindValue(':numero', $medico->numero);
    $stmt->bindValue(':complemento', $medico->complemento);
    $stmt->bindValue(':endereco', $medico->endereco);
    $stmt->bindValue(':cidade', $medico->cidade);
    $stmt->bindValue(':bairro', $medico->bairro);
    $stmt->bindValue(':uf', $medico->uf);
    $stmt->bindValue(':celular', $medico->celular);
    $stmt->bindValue(':email', $medico->email);

    $stmt->bindValue(':token', $medico->token);

    try {
      $stmt->execute();
      $this->lastException = false;

      return $stmt->rowCount() > 0;
    } catch (PDOException $error) {
      $this->lastException = $error;
      return false;
    }
  }

  public function Delete($medico): bool
  {
    $sql = 'DELETE FROM MEDICOS WHERE id = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $medico->id);
    $stmt->execute();
    return $stmt->rowCount() > 0;
  }

  public function getAll($limit = 1000, $shuffle = false)
  {
    $sql = "SELECT *,(SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS esp FROM MEDICOS ORDER BY nome_completo ASC LIMIT {$limit}";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function getGroups()
  {
    $sql = 'SELECT id,nome FROM GRUPO_ESPECIALIDADES ORDER BY id ASC';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    $res = $stmt->fetchAll(PDO::FETCH_OBJ);

    $grupos = [];

    foreach ($res as $item) {
      $grupos[$item->id] = $item->nome;
    }

    return $grupos;
  }

  public function getAllWithGroup($limit = 1000, $shuffle = false)
  {
    $sql = "SELECT *,(SELECT nome FROM `GRUPO_ESPECIALIDADES` WHERE id = grupo_especialidades) as group_name,(SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS esp FROM MEDICOS ORDER BY grupo_especialidades ASC LIMIT {$limit}";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();

    $results = [];

    foreach ($stmt->fetchAll(PDO::FETCH_OBJ) as $item) {
      $results[$item->grupo_especialidades][] = $item;
    }

    return $results;
  }

  public function getArrayById(int $id)
  {
    $sql = 'SELECT * FROM MEDICOS WHERE id = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getById(int $id)
  {
    $sql = 'SELECT * FROM MEDICOS WHERE id = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getMedicoByCpf(string $cpf)
  {
    $sql = 'SELECT * FROM MEDICOS WHERE cpf = :cpf';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':cpf', $cpf);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getMedicoByEmail(string $email)
  {
    $sql = 'SELECT * FROM MEDICOS WHERE email = :email';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getMedicoByEmailAndSenha(string $email, string $senha)
  {
    $sql = 'SELECT * FROM MEDICOS WHERE email = :email AND senha = :senha';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':senha', md5(sha1(md5($senha))));
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getMedicoByNome(string $nome)
  {
    $sql = 'SELECT * FROM MEDICOS WHERE nome_completo = :nome';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':nome', $nome);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getMedicoByToken($token)
  {
    $sql = 'SELECT * FROM MEDICOS WHERE token = :token';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':token', $token);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getMedicoByNomePreferencia(string $nome)
  {
    $sql = 'SELECT * FROM MEDICOS WHERE nome_preferencia = :nome';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':nome', $nome);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }
}
