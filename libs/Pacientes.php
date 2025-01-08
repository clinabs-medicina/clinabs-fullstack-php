<?php
class Pacientes
{
  private $pdo;
  public $lastException;

  public function __construct($pdo)
  {
    $this->lastException = null;

    $this->pdo = $pdo;
  }

  public function Add($paciente): bool
  {
    $sql = 'INSERT IGNORE INTO PACIENTES (
      nome_completo,
      nacionalidade,
      nome_preferencia,
      identidade_genero,
      cpf,
      rg,
      data_nascimento,
      telefone,
      celular,
      email,
      senha,
      doc_rg_frente,
      doc_rg_verso,
      doc_cpf_frente,
      doc_cpf_verso,
      doc_comp_residencia,
      doc_procuracao,
      doc_anvisa,
      doc_termos,
      termos,
      receber_emails,
      token,
      fid,
      medico_token,
      anamnese,
      responsavel_nome,
      responsavel_cpf,
      responsavel_rg,
      responsavel_contato,
      queixa_principal
    )
    VALUES
      (
      :nome_completo,
      :nacionalidade,
      :nome_preferencia,
      :identidade_genero,
      :cpf,
      :rg,
      :data_nascimento,
      :telefone,
      :celular,
      :email,
      :senha,
      :doc_rg_frente,
      :doc_rg_verso,
      :doc_cpf_frente,
      :doc_cpf_verso,
      :doc_comp_residencia,
      :doc_procuracao,
      :doc_anvisa,
      :doc_termos,
      :termos,
      :receber_emails,
      :token,
      :fid,
      :medico_token,
      :anamnese,
      :responsavel_nome,
      :responsavel_cpf,
      :responsavel_rg,
      :responsavel_contato,
      :queixa_principal
      )';

    $stmt = $this->pdo->prepare($sql);

    $stmt->bindValue(':nome_completo', $paciente->nome_completo);
    $stmt->bindValue(':nacionalidade', $paciente->nacionalidade);
    $stmt->bindValue(':nome_preferencia', $paciente->nome_preferencia);
    $stmt->bindValue(':identidade_genero', $paciente->identidade_genero);
    $stmt->bindValue(':cpf', preg_replace('/[^A-Za-z0-9]/', '', $paciente->cpf));
    $stmt->bindValue(':rg', preg_replace('/[^A-Za-z0-9]/', '', $paciente->rg));
    $stmt->bindValue(':data_nascimento', $paciente->data_nascimento);
    $stmt->bindValue(':telefone', preg_replace('/[^A-Za-z0-9]/', '', $paciente->telefone));
    $stmt->bindValue(':celular', preg_replace('/[^A-Za-z0-9]/', '', $paciente->celular));
    $stmt->bindValue(':email', $paciente->email);
    $stmt->bindValue(':senha', $paciente->senha);
    $stmt->bindValue(':doc_rg_frente', $paciente->doc_rg_frente);
    $stmt->bindValue(':doc_rg_verso', $paciente->doc_rg_verso);
    $stmt->bindValue(':doc_cpf_frente', $paciente->doc_cpf_frente);
    $stmt->bindValue(':doc_cpf_verso', $paciente->doc_cpf_verso);
    $stmt->bindValue(':doc_comp_residencia', $paciente->doc_comp_residencia);
    $stmt->bindValue(':doc_procuracao', $paciente->doc_procuracao);
    $stmt->bindValue(':doc_anvisa', $paciente->doc_anvisa);
    $stmt->bindValue(':doc_termos', $paciente->doc_termos);
    $stmt->bindValue(':termos', $paciente->termos ?? 'on');
    $stmt->bindValue(':receber_emails', $paciente->receber_emails ?? 'on');
    $stmt->bindValue(':token', $paciente->token);
    $stmt->bindValue(':fid', $paciente->fid);
    $stmt->bindValue(':medico_token', $paciente->medico_token);
    $stmt->bindValue(':anamnese', $paciente->anamnese);
    $stmt->bindValue(':responsavel_nome', $paciente->responsavel_nome ?? '');
    $stmt->bindValue(':responsavel_cpf', $paciente->responsavel_cpf ?? '');
    $stmt->bindValue(':responsavel_rg', $paciente->responsavel_rg ?? '');
    $stmt->bindValue(':responsavel_contato', $paciente->responsavel_celular ?? '');
    $stmt->bindValue(':queixa_principal', $paciente->queixa_principal ?? '');

    try {
      $stmt->execute();
      $this->lastException = null;
      return $stmt->rowCount() > 0;
    } catch (PDOException $error) {
      $this->lastException = $error;
      return false;
    }
  }

  public function Update($paciente): bool
  {
    $sql = 'UPDATE PACIENTES SET 
    nome_completo = :nome_completo, 
    nacionalidade = :nacionalidade, 
    nome_preferencia = :nome_preferencia, 
    identidade_genero = :identidade_genero, 
    data_nascimento = :data_nascimento, 
    email = :email, 
    telefone = :telefone, 
    celular = :celular, 
    anamnese = :anamnese, 
    medico_token = :medico_token, 
    doc_rg_frente = :doc_rg_frente,
    doc_rg_verso = :doc_rg_verso,
    doc_cpf_frente = :doc_cpf_frente,
    doc_cpf_verso = :doc_cpf_verso,
    doc_comp_residencia = :doc_comp_residencia, 
    doc_procuracao = :doc_procuracao,
    doc_anvisa = :doc_anvisa, 
    doc_termos = :doc_termos 
    WHERE token = :token';

    $stmt = $this->pdo->prepare($sql);

    $stmt->bindValue(':nome_completo', $paciente->nome_completo);
    $stmt->bindValue(':nacionalidade', $paciente->nacionalidade);
    $stmt->bindValue(':nome_preferencia', $paciente->nome_preferencia);
    $stmt->bindValue(':identidade_genero', $paciente->identidade_genero);
    $stmt->bindValue(':data_nascimento', $paciente->data_nascimento);
    $stmt->bindValue(':telefone', $paciente->telefone);
    $stmt->bindValue(':celular', $paciente->celular);
    $stmt->bindValue(':email', $paciente->email);
    $stmt->bindValue(':medico_token', $paciente->medico_token);
    $stmt->bindValue(':anamnese', $paciente->anamnese);
    $stmt->bindValue(':doc_rg_frente', $paciente->doc_rg_frente);
    $stmt->bindValue(':doc_rg_verso', $paciente->doc_rg_verso);
    $stmt->bindValue(':doc_cpf_frente', $paciente->doc_cpf_frente);
    $stmt->bindValue(':doc_cpf_verso', $paciente->doc_cpf_verso);
    $stmt->bindValue(':doc_comp_residencia', $paciente->doc_comp_residencia);
    $stmt->bindValue(':doc_procuracao', $paciente->doc_procuracao);
    $stmt->bindValue(':doc_anvisa', $paciente->doc_anvisa);
    $stmt->bindValue(':doc_termos', $paciente->doc_termos);

    $stmt->bindValue(':token', $paciente->token);

    try {
      $stmt->execute();
      return $stmt->rowCount() > 0;
    } catch (PDOExcption $ex) {
      return false;
    }
  }

  public function basicUpdate($paciente): bool
  {
    $sql = 'UPDATE PACIENTES SET nome_completo = :nome_completo, nacionalidade = :nacionalidade, nome_preferencia = :nome_preferencia, identidade_genero = :identidade_genero, data_nascimento = :data_nascimento, email = :email, rg = :rg, celular = :celular, anamnese = :anamnese, medico_token = :medico_token WHERE token = :token';

    $stmt = $this->pdo->prepare($sql);

    $stmt->bindValue(':nome_completo', $paciente->nome_completo);
    $stmt->bindValue(':nacionalidade', $paciente->nacionalidade);
    $stmt->bindValue(':nome_preferencia', $paciente->nome_preferencia);
    $stmt->bindValue(':identidade_genero', $paciente->identidade_genero);
    $stmt->bindValue(':data_nascimento', $paciente->data_nascimento);
    $stmt->bindValue(':celular', $paciente->celular);
    $stmt->bindValue(':email', $paciente->email);
    $stmt->bindValue(':rg', $paciente->rg);
    $stmt->bindValue(':medico_token', $paciente->medico_token);
    $stmt->bindValue(':anamnese', $paciente->anamnese);
    $stmt->bindValue(':token', $paciente->token);

    try {
      $stmt->execute();
      return $stmt->rowCount() > 0;
    } catch (PDOExcption $ex) {
      return false;
    }
  }

  public function Delete($paciente): bool
  {
    $sql = 'DELETE FROM PACIENTES WHERE id = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $paciente->id);
    $stmt->execute();
    return $stmt->rowCount() > 0;
  }

  public function getAll()
  {
    $sql = 'SELECT * FROM PACIENTES';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function getAllWithoutAttachments()
  {
    $sql = 'SELECT objeto, id, nome_completo, nacionalidade, nome_preferencia, identidade_genero, cpf, rg, data_nascimento, telefone, cep, numero, endereco, cidade, bairro, uf, celular, email, senha, termos, receber_emails FROM PACIENTES';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function getAllNames()
  {
    $sql = 'SELECT nome_completo FROM PACIENTES';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function getArrayById(int $id)
  {
    $sql = 'SELECT * FROM PACIENTES WHERE id = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
  }

  public function getPacienteByToken($token)
  {
    $sql = 'SELECT * FROM PACIENTES WHERE token = :token';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':token', $token);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getById(int $id)
  {
    $sql = 'SELECT * FROM PACIENTES WHERE id = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getPacienteByCpf(string $cpf)
  {
    $sql = 'SELECT * FROM PACIENTES WHERE cpf = :cpf';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':cpf', $cpf);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getPacienteByEmail(string $email)
  {
    $sql = 'SELECT * FROM PACIENTES WHERE email = :email';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getPacienteByEmailAndSenha(string $email, string $senha)
  {
    $sql = 'SELECT * FROM PACIENTES WHERE email = :email AND senha = :senha';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':email', $email);
    $stmt->bindValue(':senha', md5(sha1(md5($senha))));
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getPacienteByNome(string $nome)
  {
    $sql = 'SELECT * FROM PACIENTES WHERE nome_completo = :nome';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':nome', trim($nome));
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function getPacienteByNomePreferencia(string $nome)
  {
    $sql = 'SELECT * FROM PACIENTES WHERE nome_preferencia = :nome';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':nome', $nome);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }
}
