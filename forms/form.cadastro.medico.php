<?php
global $medicos;
require_once $_SERVER['DOCUMENT_ROOT'].'/config.inc.php';

$token = md5(preg_replace("/[^0-9]/", "", $_REQUEST["cpf"]).uniqid());
$pwd = md5(sha1(md5('Anna@2025!')));
$stmt = $pdo->prepare('INSERT INTO `MEDICOS` (`cpf`,`nome_completo`,`nome_preferencia`,`identidade_genero`,`data_nascimento`,`tipo_conselho`,`uf_conselho`,`num_conselho`,`email`,`telefone`,`celular`,`rg`,`doc_rg_frente`,`doc_rg_verso`,`doc_cpf_frente`,`doc_cpf_verso`,`doc_comp_residencia`, `anamnese`, `senha`, `token`) VALUES (:cpf ,:nome_completo ,:nome_preferencia ,:identidade_genero ,:data_nascimento ,:tipo_conselho ,:uf_conselho ,:num_conselho ,:email ,:telefone ,:celular,:rg ,:doc_rg_frente ,:doc_rg_verso ,:doc_cpf_frente ,:doc_cpf_verso ,:doc_comp_residencia, :anamnese, :senha, :token);');

        $stmt->bindValue(":cpf", $_REQUEST["cpf"]);
        $stmt->bindValue(":nome_completo", trim(strtoupper($_REQUEST["nome_completo"])));
        $stmt->bindValue(":nome_preferencia", strtoupper($_REQUEST["nome_preferencia"]));
        $stmt->bindValue(":identidade_genero", $_REQUEST["identidade_genero"]);
        $stmt->bindValue(":data_nascimento", $_REQUEST["data_nascimento"]);
        $stmt->bindValue(":tipo_conselho", strtoupper($_REQUEST["tipo_conselho"]));
        $stmt->bindValue(":uf_conselho", strtoupper($_REQUEST["uf_conselho"]));
        $stmt->bindValue(":num_conselho", $_REQUEST["num_conselho"]);
        $stmt->bindValue(":email", strtolower($_REQUEST["email"]));
        $stmt->bindValue(":telefone", $_REQUEST["telefone"]);
        $stmt->bindValue(":celular", $_REQUEST["celular"]);
        $stmt->bindValue(":rg", $_REQUEST["rg"]);
        $stmt->bindValue(":doc_rg_frente", $_REQUEST["doc_rg_frente"]);
        $stmt->bindValue(":doc_rg_verso", $_REQUEST["doc_rg_verso"]);
        $stmt->bindValue(":doc_cpf_frente", $_REQUEST["doc_cpf_frente"]);
        $stmt->bindValue(":doc_cpf_verso", $_REQUEST["doc_cpf_verso"]);
        $stmt->bindValue(":doc_comp_residencia", $_REQUEST["doc_comp_residencia"]);
        $stmt->bindValue(":senha", $pwd);
        $stmt->bindValue(":anamnese", "[]");


        $stmtx = $pdo->prepare("INSERT INTO `clinabs_db`.`ENDERECOS` ( 
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
	 `token` 
	)
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
	:token
	);");

        $stmtx->bindValue(':nome', 'ATENDIMENTO');
        $stmtx->bindValue(':cep', $_REQUEST["cep"]);
        $stmtx->bindValue(':logradouro', $_REQUEST["endereco"]);
        $stmtx->bindValue(':numero', $_REQUEST["numero"]);
        $stmtx->bindValue(':complemento', $_REQUEST["complemento"]);
        $stmtx->bindValue(':cidade', $_REQUEST["cidade"]);
        $stmtx->bindValue(':bairro', $_REQUEST["bairro"]);
        $stmtx->bindValue(':uf', $_REQUEST["uf"]);
        $stmtx->bindValue(':user_token', $token);
        $stmtx->bindValue(':tipo_endereco', 'ATENDIMENTO');
        $stmtx->bindValue(':isDefault', true);
        $stmtx->bindValue(':token', uniqid());

        try {
            $stmtx->execute();
        } catch(PDOException $ex) {

        }


$stmt->bindValue(":token", $token);


        
        try {
            $stmt->execute();

            if($stmt->rowCount() > 0)
            {
                $json = json_encode([
                    'status' => 'success', 
                    'text' => 'Cadastro Realizado Com Sucesso!',
                ], JSON_PRETTY_PRINT);
            }else {
                $json = json_encode([
                    'status' => 'danger', 
                    'text' => 'Erro Desconhecido'
                ], JSON_PRETTY_PRINT);
            }
        } catch(Exception $ex) {
            $exception = $ex;
            if(strpos($ex->getMessage(), '1062')) {
                $json = json_encode([
                    'status' => 'danger', 
                    'text' => 'Já Cadastrado'
                ], JSON_PRETTY_PRINT);
            }else {
                 $json = json_encode([
                    'status' => 'danger', 
                    'text' => 'Erro Desconhecido'
                ], JSON_PRETTY_PRINT);
            }
        }

header('Content-Type: application/json; charset=utf-8');
echo $json;