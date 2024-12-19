<?php
require_once "../config.inc.php";

ini_set("display_errors", 1);
@ini_set( 'upload_max_size' , '256M' );
@ini_set( 'post_max_size', '256M');
@ini_set( 'max_execution_time', '300' );
error_reporting(1);

$request = $_POST;

if (isset($request["enderecos"])) {
    $user_token = $request["token"];
   
    foreach($request["enderecos"] as $item) {
        $endereco  = json_decode(str_replace("'", '"', $item));
        $token = isset($endereco->token) ? $endereco->token : uniqid();
        
    if(isset($endereco->delete)) {
        $stmt = $pdo->prepare("DELETE FROM `ENDERECOS` WHERE token = :token");
        $stmt->bindValue(':token', $endereco->token);
    
        try {
          $stmt->execute();
        } catch(PDOException $ex) {

        }
    }else {
        $stmt = $pdo->prepare("INSERT INTO `ENDERECOS` ( 
     `nome`, 
     `cep`,
     `logradouro`, 
	 `numero`, 
	 `complemento`, 
	 `cidade`, 
	 `bairro`, 
	 `uf`, 
	 `user_token`, 
     `tipo_endereco`,                                 
	 `isDefault`,
     `inicio_expediente`,
     `fim_expediente`,
     `tipo_atendimento`,
     `unidade_status`,
	 `token`)
    VALUES
	(
	:nome, 
    :cep,
    :logradouro, 
	:numero, 
	:complemento, 
	:cidade, 
	:bairro, 
	:uf, 
	:user_token, 
	:tipo_endereco,
	:isDefault,
    :inicio_expediente,
    :fim_expediente,
    :tipo_atendimento,
    :unidade_status,
	:token
	) ON DUPLICATE KEY 
	UPDATE
	nome = :nome, 
    cep = :cep,
    logradouro = :logradouro,
	numero = :numero, 
	complemento = :complemento, 
	cidade = :cidade, 
	bairro = :bairro, 
	uf = :uf, 
	tipo_endereco = :tipo_endereco,
    inicio_expediente = :inicio_expediente,
    fim_expediente = :fim_expediente,
    tipo_atendimento = :tipo_atendimento,
    unidade_status = :unidade_status,
	isDefault = :isDefault");
	
	$stmt->bindValue(':nome', $endereco->nome); 
    $stmt->bindValue(':cep', $endereco->cep);
    $stmt->bindValue(':logradouro', $endereco->logradouro);
    $stmt->bindValue(':numero', $endereco->numero);
    $stmt->bindValue(':complemento', $endereco->complemento);
    $stmt->bindValue(':cidade', $endereco->cidade);
    $stmt->bindValue(':bairro', $endereco->bairro);
    $stmt->bindValue(':uf', $endereco->uf);
    $stmt->bindValue(':user_token', $user_token);
    $stmt->bindValue(':tipo_endereco', $endereco->tipo_endereco);
    $stmt->bindValue(':isDefault', (!empty($endereco->isDefault) && $endereco->isDefault == true) ? 1 : 0);
    $stmt->bindValue(':tipo_endereco', $endereco->tipo_endereco);
    $stmt->bindValue(':inicio_expediente', $endereco->inicio_expediente);
    $stmt->bindValue(':fim_expediente', $endereco->fim_expediente);
    $stmt->bindValue(':tipo_atendimento', $endereco->tipo_atendimento);
    $stmt->bindValue(':unidade_status', $endereco->unidade_status);
    $stmt->bindValue(':token', $token);
    
        try {
          $stmt->execute();
        } catch(PDOException $ex) {

        }
    }
  }
    
}

switch ($request["tabela"]) {
    case "USUARIOS": {
        $stmt = $pdo->prepare("UPDATE `USUARIOS` 
        SET 
        `nome_completo` = :nome_completo,
        `nacionalidade` = :nacionalidade,
        `nome_preferencia` = :nome_preferencia,
        `identidade_genero` = :identidade_genero,
        `cpf` = :cpf,
        `rg` = :rg,
        `data_nascimento` = :data_nascimento,
        `telefone` = :telefone,
        `celular` = :celular,
        `email` = :email,
        `marcas` = :marcas,
        `status` = :status
        WHERE
            `token` = :token");

        $stmt->bindValue(":nome_completo", $request["nome_completo"]);
        $stmt->bindValue(":nacionalidade", $request["nacionalidade"]);
        $stmt->bindValue(":nome_preferencia", $request["nome_preferencia"]);
        $stmt->bindValue(":identidade_genero", $request["identidade_genero"]);
        $stmt->bindValue(":cpf", preg_replace('/[^0-9]+/', '', $request["cpf"]));
        $stmt->bindValue(":rg", preg_replace('/[^0-9]+/', '', $request["rg"]));
        $stmt->bindValue(":data_nascimento", Modules::parseDate($request["data_nascimento"]));
        $stmt->bindValue(":telefone", preg_replace('/[^0-9]+/', '', $request["telefone"]));
        $stmt->bindValue(":celular", preg_replace('/[^0-9]+/', '', $request["celular"]));
        $stmt->bindValue(":email", $request["email"]);
        $stmt->bindValue(":token", $request["token"]);
        $stmt->bindValue(":marcas", json_encode($request["marcas"]));
        $stmt->bindValue(":status", $request["situacao"]);

        try {
            $stmt->execute();
            
            if(isset($request['senha']) && isset($request['confirm_senha']) && (!empty($request['senha']) && !empty($request['confirm_senha']))) {
                $stmtx = $pdo->prepare("UPDATE USUARIOS SET senha = :pwd WHERE token = :token");
                $stmtx->bindValue(':pwd', md5(sha1(md5($request['senha']))));
                $stmtx->bindValue(':token', $request["token"]);
                
               try{
                    $stmtx->execute();
               } catch (Exception $ex) {
   
               }
            }
                $json = json_encode([
                    "title" => "Atenção",
                    "text" => "Cadastro Atualizado Com Sucesso!",
                    "status" => "success",
                ]);
        } catch (Exception $ex) {
            $json = json_encode([
                "title" => "Atenção",
                "text" => 'Falha ao Atualizar o Cadastro \n\n'.$ex->getMessage(),
                "status" => "error",
                "action" => $ex->getCode() == 23000 ? 'focus':''
            ]);
        }
        break;
    }
    
    case "PACIENTES": {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE `PACIENTES` 
        SET 
        `nome_completo` = :nome_completo,
        `nacionalidade` = :nacionalidade,
        `nome_preferencia` = :nome_preferencia,
        `identidade_genero` = :identidade_genero,
        `cpf` = :cpf,
        `rg` = :rg,
        `data_nascimento` = :data_nascimento,
        `telefone` = :telefone,
        `celular` = :celular,
        `email` = :email,
        `medico_token` = :medico_token,
        `doc_cnh` = :doc_cnh,
        `status` = :sts,
        `updateTime` = :ts,
        `responsavel_nome` = :responsavel_nome,
        `responsavel_cpf` = :responsavel_cpf,
        `responsavel_contato` = :responsavel_contato,
        `responsavel_rg` = :responsavel_rg

        WHERE
            `token` = :token");

        $stmt->bindValue(":nome_completo", trim(strtoupper($request["nome_completo"])));
        $stmt->bindValue(":nacionalidade", $request["nacionalidade"]);
        $stmt->bindValue(":nome_preferencia", $request["nome_preferencia"]);
        $stmt->bindValue(":identidade_genero", $request["identidade_genero"]);
        $stmt->bindValue(":cpf", preg_replace('/[^0-9]+/', '', $request["cpf"]));
        $stmt->bindValue(":rg", preg_replace('/[^0-9]+/', '', $request["rg"]));
        $stmt->bindValue(":data_nascimento", Modules::parseDate($request["data_nascimento"]));
        $stmt->bindValue(":telefone", preg_replace('/[^0-9]+/', '', $request["telefone"]));
        $stmt->bindValue(":celular", preg_replace('/[^0-9]+/', '', $request["celular"]));
        $stmt->bindValue(":email", $request["email"]);
        $stmt->bindValue(":medico_token", $request["medico_token"]);
        $stmt->bindValue(":doc_cnh", $request["doc_validation"]);
		$stmt->bindValue(":ts", date('Y-m-d H:i:s'));
        $stmt->bindValue(":token", $request["token"]);
        $stmt->bindValue(":sts", $request["situacao"]);
        $stmt->bindValue(":responsavel_nome", strtoupper($request["responsavel_nome"]));
        $stmt->bindValue(":responsavel_cpf", preg_replace('/[^0-9]+/', '', $request["responsavel_cpf"]));
        $stmt->bindValue(":responsavel_contato", preg_replace('/[^0-9]+/', '', $request["responsavel_contato"]));
        $stmt->bindValue(":responsavel_rg", preg_replace('/[^0-9]+/', '', $request["responsavel_rg"]));

        try {
            $stmt->execute();
            $pdo->commit();

            
            
            if(isset($request['senha']) && isset($request['confirm_senha']) && (!empty($request['senha']) && !empty($request['confirm_senha']))) {
                $stmtx = $pdo->prepare("UPDATE PACIENTES SET senha = :pwd WHERE token = :token");
                $stmtx->bindValue(':pwd', md5(sha1(md5($request['senha']))));
                $stmtx->bindValue(':token', $request["token"]);
                
               try{
                    $stmtx->execute();
               } catch (Exception $ex) {

               }
            }
            
            if($stmt->rowCount() > 0) {
                $json = json_encode([
                    "title" => "Atenção",
                    "text" => "Cadastro Atualizado Com Sucesso!",
                    "status" => "success",
                ]);
            }else {
                $json = json_encode([
                    "title" => "Atenção",
                    "text" => 'Falha ao Atualizar o Cadastro!',
                    "status" => "error",
                ]);
            }
        } catch (Exception $ex) {
            $pdo->rollBack();
            $json = json_encode([
                "title" => "Atenção",
                "text" => 'Falha ao Atualizar o Cadastro \n\n'.$ex->getMessage(),
                "status" => "error",
                "action" => $ex->getCode() == 23000 ? 'focus':''
            ]);
        }
        break;
    }

    case "FUNCIONARIOS": {
        $stmt = $pdo->prepare("UPDATE `FUNCIONARIOS` 
        SET 
        `nome_completo` = :nome_completo,
        `nacionalidade` = :nacionalidade,
        `nome_preferencia` = :nome_preferencia,
        `identidade_genero` = :identidade_genero,
        `cpf` = :cpf,
        `rg` = :rg,
        `data_nascimento` = :data_nascimento,
        `telefone` = :telefone,
        `celular` = :celular,
        `email` = :email,
        `status` = :status
        WHERE
            `token` = :token");

        $stmt->bindValue(":nome_completo", $request["nome_completo"]);
        $stmt->bindValue(":nacionalidade", $request["nacionalidade"]);
        $stmt->bindValue(":nome_preferencia", $request["nome_preferencia"]);
        $stmt->bindValue(":identidade_genero", $request["identidade_genero"]);
        $stmt->bindValue(":cpf", $request["cpf"]);
        $stmt->bindValue(":rg", $request["rg"]);
        $stmt->bindValue(":data_nascimento", Modules::parseDate($request["data_nascimento"]));
        $stmt->bindValue(":telefone", preg_replace('/[^0-9]+/', '', $request["telefone"]));
        $stmt->bindValue(":celular", preg_replace('/[^0-9]+/', '', $request["celular"]));
        $stmt->bindValue(":email", $request["email"]);
        $stmt->bindValue(":token", $request["token"]);
        $stmt->bindValue(":status", $request['situacao']);

        try {
            $stmt->execute();
            
           if(isset($request['senha']) && isset($request['confirm_senha']) && (!empty($request['senha']) && !empty($request['confirm_senha']))) {
                $stmtx = $pdo->prepare("UPDATE FUNCIONARIOS SET senha = :pwd WHERE token = :token");
                $stmtx->bindValue(':pwd', md5(sha1(md5($request['senha']))));
                $stmtx->bindValue(':token', $request["token"]);
                
                $stmtx->execute();
            }
            
            $json = json_encode([
                "title" => "Atenão",
                "text" => "Cadastro Atualizado Com Sucesso!",
                "status" => "success",
            ]);
        } catch (Exception $ex) {
            $json = json_encode([
                "title" => "Atenão",
                "text" => 'Falha ao Atualizar o Cadastro\n' . $ex->getMessage(),
                "status" => "error",
            ]);
        }
        break;
    }

    case "MEDICOS": {
        $stmt = $pdo->prepare("UPDATE `MEDICOS` 
            SET 
            `nome_completo` = :nome_completo,
            `nacionalidade` = :nacionalidade,
            `nome_preferencia` = :nome_preferencia,
            `identidade_genero` = :identidade_genero,
            `cpf` = :cpf,
            `rg` = :rg,
            `data_nascimento` = :data_nascimento,
            `telefone` = :telefone,
            `celular` = :celular,
            `email` = :email,
            `tipo_conselho` = :tipo_conselho,
            `uf_conselho` = :uf_conselho,
            `num_conselho` = :num_conselho,
            `anamnese` = :anamnese,
            `especialidade` = :especialidade,
            `descricao` = :descricao,
            `descricao_html` = :descricao_completa,
            `valor_consulta` = :valor_consulta,
            `valor_consulta_online` = :valor_consulta_online,
            `duracao_atendimento` = :duracao_atendimento,
            `status` = :status,
            `nome_clinica` = :nome_clinica,
            `inicio_ag` = :inicio_ag,
            `fim_ag` = :fim_ag,
            `google_agenda_link` = :google_agenda_link,
            `google_agenda_sync` = :google_agenda_sync,
            `disponibilizar_agenda` = :disponibilizar_agenda,
            `tempo_limite_online` = :tempo_limite_online,
            `tempo_limite_presencial` = :tempo_limite_presencial,
            `grupo_especialidades` = :grupo_especialidades,
            `age_min` = :age_min,
            `age_max` = :age_max
            WHERE
	            `token` = :token"); 

        $stmt->bindValue(":nome_completo", $request["nome_completo"]);
        $stmt->bindValue(":nacionalidade", $request["nacionalidade"]);
        $stmt->bindValue(":nome_preferencia", $request["nome_preferencia"]);
        $stmt->bindValue(":identidade_genero", $request["identidade_genero"]);
        $stmt->bindValue(":cpf", $request["cpf"]);
        $stmt->bindValue(":rg", $request["rg"]);
        $stmt->bindValue(":data_nascimento", Modules::parseDate($request["data_nascimento"]));
        $stmt->bindValue(":telefone", preg_replace('/[^0-9]+/', '', $request["telefone"]));
        $stmt->bindValue(":celular", preg_replace('/[^0-9]+/', '', $request["celular"]));
        $stmt->bindValue(":email", $request["email"]);
        $stmt->bindValue(":token", $request["token"]);
        $stmt->bindValue(":tipo_conselho", $request["tipo_conselho"]);
        $stmt->bindValue(":uf_conselho", $request["uf_conselho"]);
        $stmt->bindValue(":num_conselho", $request["num_conselho"]);
        $stmt->bindValue(":anamnese", is_array($request["anamnese"]) ? json_encode($request["anamnese"]):'[]');
        $stmt->bindValue(":especialidade", preg_replace('/[^0-9]+/', '', json_encode($request["especialidades"])));
        $stmt->bindValue(":descricao", $request["descricao"]);
        $stmt->bindValue(":descricao_completa", $request["descricao_completa"]);
        $stmt->bindValue(":valor_consulta", str_replace(",", ".", $request["valor_consulta"]));
        $stmt->bindValue(":valor_consulta_online", str_replace(",", ".", $request["valor_consulta_online"]));
        $stmt->bindValue(":duracao_atendimento", $request["duracao_atendimento"]);
        $stmt->bindValue(":status", $request['situacao']);
        $stmt->bindValue(":nome_clinica", $request['nome_clinica']);
        $stmt->bindValue(":inicio_ag", $request['inicio_ag']);
        $stmt->bindValue(":fim_ag", $request['fim_ag']);
        $stmt->bindValue(":google_agenda_link", $request['google_agenda_link']);
        $stmt->bindValue(":google_agenda_sync", $request['google_agenda_sync']);
        $stmt->bindValue(":disponibilizar_agenda", $request['disponibilizar_agenda']);
        $stmt->bindValue(":tempo_limite_online", $request['tempo_limite_online']);
        $stmt->bindValue(":tempo_limite_presencial", $request['tempo_limite_presencial']);
        $stmt->bindValue(":grupo_especialidades", $request['grupo_especialidades']);
        $stmt->bindValue(":age_min", preg_replace('/[^0-9]+/', '',$request['age_min']));
        $stmt->bindValue(":age_max", preg_replace('/[^0-9]+/', '',$request['age_max']));

        try {
            $stmt->execute();
            $json = json_encode([
                "title" => "Atenção",
                "text" => "Cadastro Atualizado Com Sucesso!",
                "status" => "success",
            ]);
          
          if(isset($request["descricao_html"])){
               $desc =  utf8_encode(base64_decode($request["descricao_html"]));
            }
            
            if(isset($request['senha']) && isset($request['confirm_senha']) && (!empty($request['senha']) && !empty($request['confirm_senha']))) {
                $stmtx = $pdo->prepare("UPDATE MEDICOS SET senha = :pwd WHERE token = :token");
                $stmtx->bindValue(':pwd', md5(sha1(md5($request['senha']))));
                $stmtx->bindValue(':token', $request["token"]);
                
                $stmtx->execute();
            }
        } catch (Exception $ex) {
            $json = json_encode([
                "title" => "Atenção",
                "text" => 'Falha ao Atualizar o Cadastro\n' . $ex->getMessage(),
                "status" => "error",
            ]);
        } finally {
            if(isset($request["do_payment"])){
                try {
                    $stmts = $pdo->prepare("UPDATE `MEDICOS` 
                    SET 
                        do_payment = :do_payment, 
                    	chave_pix = :chave_pix, 
                    	payment_link = :payment_link, 
                    	beneficiario_nome = :beneficiario_nome, 
                    	wa_notificacao = :wa_notificacao
                    WHERE token = :medico_token");
                    	
                $stmts->bindValue(':do_payment', $request["do_payment"]);
                $stmts->bindValue(':chave_pix', $request["chave_pix"]);
                $stmts->bindValue(':payment_link', $request["payment_link"]);
                $stmts->bindValue(':beneficiario_nome', $request["beneficiario_nome"]);
                $stmts->bindValue(':wa_notificacao', $request["wa_notificacao"]);
                $stmts->bindValue(':medico_token', $request["token"]);
                
                $stmts->execute();
                
                }catch(Exception $ex) {
                    
                }
            }
        }
        break;
    }
}

header("content-type: application/json");
echo $json;