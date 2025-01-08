<?php
class Agenda
{
  private $pdo;
  public $lastException;
  public $responseArray = [];

  public function __construct($pdo)
  {
    $this->lastException = null;

    $this->pdo = $pdo;
  }

  public function getAll($token = null)
  {
    if ($token == null) {
      $sql = 'SELECT AGENDA_MED.data_agendamento, (SELECT nome_completo FROM PACIENTES WHERE token = AGENDA_MED.paciente_token) AS paciente, (SELECT nome FROM ANAMNESE WHERE id = AGENDA_MED.anamnese) AS anamnese, (SELECT nome_completo FROM MEDICOS WHERE token = AGENDA_MED.medico_token) AS medico, AGENDA_MED.duracao_agendamento, AGENDA_MED.descricao, AGENDA_MED.data_efetivacao, AGENDA_MED.`status`, AGENDA_MED.token, AGENDA_MED.`timestamp`, AGENDA_MED.prescricao FROM AGENDA_MED';
      $stmt = $this->pdo->prepare($sql);
    } else {
      $sql = 'SELECT AGENDA_MED.data_agendamento, (SELECT nome_completo FROM PACIENTES WHERE token = AGENDA_MED.paciente_token) AS paciente, (SELECT nome FROM ANAMNESE WHERE id = AGENDA_MED.anamnese) AS anamnese, (SELECT nome_completo FROM MEDICOS WHERE token = AGENDA_MED.medico_token) AS medico, AGENDA_MED.duracao_agendamento, AGENDA_MED.descricao, AGENDA_MED.data_efetivacao, AGENDA_MED.`status`, AGENDA_MED.token, AGENDA_MED.`timestamp`, AGENDA_MED.prescricao FROM AGENDA_MED WHERE token = :token';
      $stmt = $this->pdo->prepare($sql);
      $stmt->bindValue(':token', $token);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_OBJ);
  }

  public function get($token)
  {
    $sql = 'SELECT
      AGENDA_MED.*, 
      AGENDA_MED.data_agendamento, 
      AGENDA_MED.paciente_token, 
      (SELECT nome FROM ANAMNESE WHERE id = AGENDA_MED.anamnese) as anamnese, 
      AGENDA_MED.medico_token, 
      AGENDA_MED.duracao_agendamento, 
      AGENDA_MED.descricao, 
      AGENDA_MED.data_efetivacao, 
      AGENDA_MED.`status`, 
      AGENDA_MED.token, 
      AGENDA_MED.`timestamp`, 
      AGENDA_MED.prescricao, 
      AGENDA_MED.meet, 
      AGENDA_MED.valor, 
      AGENDA_MED.modalidade, 
      PACIENTES.nome_completo, 
      PACIENTES.nacionalidade, 
      PACIENTES.nome_preferencia, 
      PACIENTES.identidade_genero, 
      PACIENTES.cpf, 
      PACIENTES.rg, 
      PACIENTES.data_nascimento, 
      PACIENTES.telefone, 
      PACIENTES.celular, 
      PACIENTES.email, 
      PACIENTES.senha, 
      PACIENTES.local_encaminhamento, 
      PACIENTES.local_acolhimento, 
      PACIENTES.tipo_atendimento, 
      PACIENTES.queixa_principal, 
      PACIENTES.termos, 
      PACIENTES.receber_emails, 
      PACIENTES.`status`, 
      MEDICOS.nome_completo AS medico_nome,
      PACIENTES.nome_completo AS paciente_nome,
      MEDICOS.tipo_conselho, 
      MEDICOS.uf_conselho, 
      MEDICOS.especialidade, 
      MEDICOS.num_conselho,
      MEDICOS.nacionalidade AS medico_nacionalidade,
      MEDICOS.identidade_genero AS medico_sexo,
      PACIENTES.token AS paciente_token
    FROM
      AGENDA_MED,
      PACIENTES,
      MEDICOS
    WHERE
      AGENDA_MED.paciente_token = PACIENTES.token AND
      AGENDA_MED.medico_token = MEDICOS.token AND
      AGENDA_MED.token = :token';

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':token', $token);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function updateStatus($agenda): bool
  {
    $sql = 'UPDATE `AGENDA_MED` SET `status` = :sts WHERE `AGENDA_MED`.`token` = :token';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':token', $agenda->token);
    $stmt->bindValue(':sts', $agenda->status);

    try {
      $stmt->execute();
      $this->responseArray = ['ref' => $agenda->status];

      $this->lastException = false;

      return $stmt->rowCount() > 0;
    } catch (PDOException $ex) {
      $this->lastException = $ex;
      return false;
    }
  }

  public function Add($ag): bool
  {
    $sql = 'INSERT INTO `AGENDA_MED` (`cupom`,`valor`,`paciente_token`, `medico_token`, `modalidade`, `anamnese`, `data_agendamento`, `duracao_agendamento`, `descricao`, `meet`, `token`, `payment_method`) 
      VALUES (:cupom, :valor, :paciente_token, :medico_token, :modalidade, :anamnese, :data_agendamento, :duracao_agendamento, :descricao, :meet, :token, :payment_method);';

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':cupom', $ag->cupom);
    $stmt->bindValue(':valor', $ag->valor);
    $stmt->bindValue(':paciente_token', $ag->paciente_token);
    $stmt->bindValue(':medico_token', $ag->medico_token);
    $stmt->bindValue(':modalidade', strtoupper($ag->modalidade));
    $stmt->bindValue(':anamnese', $ag->anamnese);
    $stmt->bindValue(':data_agendamento', $ag->data_agendamento);
    $stmt->bindValue(':duracao_agendamento', $ag->duracao_agendamento);
    $stmt->bindValue(':descricao', $ag->descricao);
    $stmt->bindValue(':meet', $ag->meet);
    $stmt->bindValue(':token', $ag->token);
    $stmt->bindValue(':payment_method', $ag->payment_method);

    try {
      $stmt->execute();

      return $stmt->rowCount() > 0;
    } catch (PDOException $ex) {
      $this->lastException = $ex;

      return false;
    }
  }

  public function getByToken($token)
  {
    $sql = 'SELECT
      MEDICOS.nome_completo AS medico_nome, 
      MEDICOS.cpf as medico_cpf,
      MEDICOS.tipo_conselho, 
      MEDICOS.uf_conselho, 
      MEDICOS.especialidade, 
      MEDICOS.num_conselho,
      MEDICOS.email AS medico_email,
      AGENDA_MED.data_agendamento, 
      AGENDA_MED.medico_token,
      AGENDA_MED.paciente_token, 
      ( SELECT nome FROM ANAMNESE WHERE id = AGENDA_MED.anamnese ) AS anamnese, 
      AGENDA_MED.duracao_agendamento, 
      AGENDA_MED.descricao, 
      AGENDA_MED.token, 
      AGENDA_MED.prescricao, 
      AGENDA_MED.`status`, 
      PACIENTES.nome_completo, 
      PACIENTES.data_nascimento, 
      PACIENTES.identidade_genero, 
      PACIENTES.telefone, 
      PACIENTES.email, 
      PACIENTES.celular,
      AGENDA_MED.file_signed
    FROM
      MEDICOS,
      AGENDA_MED,
      PACIENTES
    WHERE
      MEDICOS.token = AGENDA_MED.medico_token
      AND 
      PACIENTES.token = AGENDA_MED.paciente_token
       AND
    AGENDA_MED.token = :token';

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':token', $token);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_OBJ);
  }

  public function Update($ag): bool
  {
    $sql = 'UPDATE `AGENDA_MED` SET medico_token = :medico_token, anamnese = :anamnese, data_agendamento = :data_agendamento, duracao_agendamento = :duracao_agendamento, descricao = :descricao, prescricao = :prescricao WHERE token = :token';

    $stmt = $this->pdo->prepare($sql);

    $stmt->bindValue(':medico_token', $ag->medico_token);
    $stmt->bindValue(':anamnese', $ag->anamnese);
    $stmt->bindValue(':data_agendamento', $ag->data_agendamento);
    $stmt->bindValue(':duracao_agendamento', $ag->duracao_agendamento);
    $stmt->bindValue(':descricao', $ag->descricao);
    $stmt->bindValue(':prescricao', $ag->prescricao);
    $stmt->bindValue(':token', $ag->token);

    try {
      $stmt->execute();
      $this->lastException = null;
      return $stmt->rowCount() > 0;
    } catch (PDOException $ex) {
      $this->lastException = $ex;
      return false;
    }
  }
}
