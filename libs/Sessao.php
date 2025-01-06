<?php
require_once 'Medicos.php';
require_once 'Pacientes.php';

class Sessao {
  private $pdo;

  public function __construct($pdo) {
    $this->pdo = $pdo;
  }
  
    public function Login($usuario, $senha) {
      $sql = "SELECT * FROM USUARIOS WHERE usuario = :usuario AND senha = :senha";
      $stmt = $this->pdo->prepare($sql);

      $stmt->bindValue(':usuario', $usuario);
      $stmt->bindValue(':senha', md5(sha1(md5($senha))));

      $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /*
  public function getUser($token) {
    $sql = "SELECT * FROM USUARIOS WHERE senha = :senha";

      $stmt = $this->pdo->prepare($sql);

      $stmt->bindValue(':senha', $token);

      $stmt->execute();

     $res =  $stmt->fetch(PDO::FETCH_ASSOC);

    if($res['tipo'] == 'MEDICO')
    {
      return $this->prepareUser($res['usuario'], 'medico');
    }else{
      return $this->prepareUser($res['usuario'], 'paciente');
    }
  }

  private function prepareUser($usuario, $type = 'medico') {

    if($type == 'medico'){
      $medicos = new Medicos($this->pdo);
      try{
        return $medicos->getMedicoByEmail($usuario);
      }catch {
        return false;
      }
    }else{
      $pacientes = new Pacientes($this->pdo);
      
      try{
        return $medicos->getPacienteByEmail($usuario);
      }catch {
        return false;
      }
    }
  }
  */
    public function send_ping($session_id) {
          $stmt = $this->pdo->prepare('UPDATE SESSIONS  SET last_ping = :session_ts WHERE session_id = :session_id');
              $stmt->bindValue(':session_ts', date('Y-m-d H:i:s'));
                  $stmt->bindValue(':session_id', $session_id);

                      try {
                            $stmt->execute();

                                  return [
                                          'status' => true,
                                                  'message' => 'Ping recebido com sucesso'
                                                        ];
                                                            } catch(PDOException $e) {
                                                                  return [
                                                                          'status' => false,
                                                                                  'message' => $e->getMessage()
                                                                                        ];
                                                                                            }
                                                                                          }
                                                                                        }