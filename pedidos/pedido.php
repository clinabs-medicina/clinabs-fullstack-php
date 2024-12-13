<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(isset($_SESSION['userObj'])) {
	    $user = (object) $_SESSION['userObj'];
    }
?>
<section class="main">
    <section>
        <h1 class="titulo-h1">Detalhes do Pedido</h1>
    </section>
    <div class="flex-container produtos-flex">
        <?php
          $sql = "SELECT *,(SELECT amount FROM VENDAS WHERE code = '".$_REQUEST['pedido_code']."') AS valor_pago FROM `FARMACIA` WHERE token = '".$_REQUEST['pedido_code']."'";

          $stmt = $pdo->query($sql);
          $pedido = $stmt->fetch(PDO::FETCH_OBJ);
          $produtos = [];
          
          foreach(json_decode($pedido->produtos) as $prod) {
              $stmt = $pdo->prepare('SELECT * FROM PRODUTOS WHERE id = :id');
              $stmt->bindValue(':id', $prod->id);
              $stmt->execute();
          
              $produtos[] = $stmt->fetch(PDO::FETCH_OBJ);
          }
          
          $pedido->produtos2 = $produtos;
          
          $pedido->paciente = $pacientes->getPacienteByToken($pedido->paciente_token);
          $pedido->medico = $medicos->getMedicoByToken($pedido->medico_token);
          $pedido->funcionario = $funcionarios->getFuncionarioByToken($pedido->funcionario_token);
          
          
          $stmtx = $pdo->prepare('SELECT
        	nome, 
        	cep, 
        	logradouro, 
        	numero, 
        	complemento, 
        	cidade, 
        	bairro, 
        	uf
        FROM
        	ENDERECOS
        WHERE
          token = :token');
          $stmtx->bindValue(':token', $pedido->endereco_entrega);
          $stmtx->execute();
          
          $endereco = $stmtx->fetch(PDO::FETCH_OBJ);

        

          if($pedido->doc_receita != "" && $pedido->paciente->doc_rg_frente != "" && $pedido->paciente->doc_rg_verso != "" && $pedido->paciente->doc_cpf_frente != "" && $pedido->paciente->doc_cpf_verso != "") {

          }else{
           
          }
        ?>
        
        <div class="list-paciente-flex">
          <div class="list-paciente-box-esq" style="width: 100% !important">
              <div class="list-paciente-box-esq-user">
                  <div>
                      <img
                          src="/data/images/profiles/<?=($pedido->paciente->token)?>.jpg"
                          height="140px"
                          class="imagem-user"
                      />
                  </div>
              </div>
              <div class="list-paciente-box-dir-user">
                  <span class="crm-bg"><?=($pedido->celular)?></span>
                  <h3><?=($pedido->paciente->nome_completo)?></h3>
                  <hr />
                  <p class="list-paciente-box-dir-subtitle" style="text-align: left !important"><?=($pedido->paciente->tipo)?></p>
                  <div class="list-paciente-box-dir-ico">
                      <img src="/assets/images/ico-map.svg" />
                      <?php
                      if(isset($endereco->logradouro)) {
                        ?>
                            <p><?=($endereco->logradouro)?>, <?=($endereco->numero)?> <br><?=($endereco->cidade)?>/<?=($endereco->uf)?> | CEP: <?=($endereco->cep)?></p>
                        <?php
                      }else {
                        echo '<p>Sem Endereço Cadastrado</p>';
                      }
                      ?>
                  </div>
              </div>
          </div>
        </div>
        
        <table width="100%"></table>
        <div class="head-title">
            <h4 class="titulo-h2">Detalhes dos Produtos</h4>
        </div>
        <table width="100%" id="tableProdutos" class="table" data-table="<?=($pedido->paciente->objeto)?>S" data-user="<?=($pedido->paciente->token)?>">
          <thead>
              <tr>
                <th width="32px">#</th>
                <th>Nome do Medicamento</th>
                <th width="128px">Valor Unitário</th>
                <th width="128px">Valor Total</th>
                <th width="80px">Qtde.</th>
              </tr>
          </thead>
          <tbody>
            <?php
            $i = 1;
                foreach(json_decode($pedido->produtos) as $ped) {
                    $valor_unitario = number_format($ped->valor / $ped->qtde, 2, ',', '.');
                    $valor_total = number_format($ped->valor, 2, ',', '.');
                    $qtde = round($ped->qtde);

                    $stmtz = $pdo->query('SELECT nome,image,marca FROM PRODUTOS WHERE id = '.$ped->id);
                    $prod = $stmtz->fetch(PDO::FETCH_OBJ);

                    echo "<tr class=\"fb-center\" >";
                        echo "<td>{$i}</td>";
                        echo "<td><div class=\"box-left-txt-center\"><img src=\"/data/images/produtos/{$prod->image}\" height=\"64px\"> {$prod->nome}</div></td>";
                        echo "<td>R$ {$valor_unitario}</td>";
                        echo "<td>R$ {$valor_total}</td>";
                        echo "<td>x{$qtde}</td>";
                    echo "</tr>";

                        $i++;
              
                }

            ?>
          </tbody>
        </table>
        <h2 class="titulo-h1">Documentos do Paciente</h2>
        <div class="container-flex1">
           <div class="d-flex">
                <input autocomplete="off" type="checkbox" id="doc_validation" name="doc_validation" checked="<?=$_user->doc_cnh == 'on' ? 'checked':''?>"> <strong>Minha CNH contem CPF,RG inclusos</strong>
           </div>
        </div>
        <div class="toolbar toolbar-btns-right">
            <a onclick="unlock_upload(this, '.pacientes-doc')" data-user="<?=($pedido->paciente_token)?>" data-token="<?=($pedido->token)?>" class="send-docs"><i class="fa fa-upload"></i> Enviar Documentos</a>
            <a download="<?=($pedido->token)?>.zip" href="/docs/pedido/<?=($pedido->token)?>/<?=($pedido->paciente->token)?>" class="download-docs"><i class="fa fa-download"></i> Baixar Todos</a>
        </div>
                        <section class="form-grid area-doc pacientes-doc">
                                <section class="form-group">
                                    <label for="doc_rg_frente" data-attachment="<?=$pedido->paciente->doc_rg_frente?>" class="anexo-item download-anexo-item ped-docs" data-token="<?=($_REQUEST['pedido_code'])?>" data-file="doc_rg_frente" title="clique para Baixar este Documento">
                                        <img data-title="Obrigatório" src="<?=(Modules::getDoc($pedido->paciente->doc_rg_frente))?>">
                                        <strong>Anexo RG (Frente)</strong>
                                    </label>
                                </section>
                                <section class="form-group">
                                    <label for="doc_rg_verso" data-attachment="<?=$pedido->paciente->doc_rg_verso?>" class="anexo-item download-anexo-item ped-docs" data-token="<?=($_REQUEST['pedido_code'])?>" data-file="doc_rg_verso" title="clique para Baixar este Documento">
                                        <img data-title="Obrigatrio" src="<?=(Modules::getDoc($pedido->paciente->doc_rg_verso))?>">
                                        <strong>Anexo RG (Verso)</strong>
                                    </label>
                                </section>
                                <section class="form-group">
                                    <label for="doc_cpf_frente" data-attachment="<?=$pedido->paciente->doc_cpf_frente?>" class="anexo-item download-anexo-item ped-docs" data-token="<?=($_REQUEST['pedido_code'])?>" data-file="doc_cpf_frente" title="clique para Baixar este Documento">
                                        <img data-title="Obrigatrio" src="<?=(Modules::getDoc($pedido->paciente->doc_cpf_frente))?>">
                                        <strong>Anexo CPF (Frente)</strong>
                                    </label>
                                </section>
                                <section class="form-group">
                                    <label for="doc_cpf_verso" data-attachment="<?=$pedido->paciente->doc_cpf_verso?>" class="anexo-item download-anexo-item ped-docs" data-token="<?=($_REQUEST['pedido_code'])?>" data-file="doc_cpf_verso" title="clique para Baixar este Documento">
                                        <img data-title="Obrigatório" src="<?=(Modules::getDoc($pedido->paciente->doc_cpf_verso))?>">
                                        <strong>Anexo CPF (Verso)</strong>
                                    </label>
                                </section>

                                <section class="form-group">
                                    <label for="doc_comp_residencia" data-attachment="<?=$pedido->paciente->doc_comp_residencia?>" class="anexo-item download-anexo-item ped-docs" data-token="<?=($_REQUEST['pedido_code'])?>" data-file="doc_procuracao" title="clique para Baixar este Documento">
                                        <img data-title="Obrigatório" src="<?=(Modules::getDoc($pedido->paciente->doc_comp_residencia))?>">
                                        <strong>Comprovante de Residencia</strong>
                                    </label>
                                </section>
                                <section class="form-group">
                                    <label for="doc_procuracao" data-attachment="<?=$pedido->paciente->doc_procuracao?>" class="anexo-item download-anexo-item ped-docs" data-token="<?=($_REQUEST['pedido_code'])?>" data-file="doc_procuracao" title="clique para Baixar este Documento">
                                    <img data-title="Obrigatrio" src="<?=(Modules::getDoc($pedido->paciente->doc_procuracao))?>">
                                    <strong>Procuração</strong>
                                    </label>
                                </section>
                                <section class="form-group">
                                    <label for="doc_anvisa" class="anexo-item download-anexo-item ped-docs" data-token="<?=($_REQUEST['pedido_code'])?>" data-file="doc_anvisa" title="clique para Baixar este Documento">
                                        <img data-title="Obrigatório" data-attachment="<?=$pedido->paciente->doc_anvisa?>" src="<?=(Modules::getDoc($pedido->paciente->doc_anvisa))?>">
                                        <strong>Documento Anvisa</strong>
                                    </label>
                                </section>
                                <section class="form-group">
                                <label for="doc_termos" data-attachment="<?=$pedido->paciente->doc_termos?>" class="anexo-item download-anexo-item ped-docs" data-token="<?=($_REQUEST['pedido_code'])?>" data-file="doc_termos" title="clique para Baixar este Documento">
                                        <img src="<?=(Modules::getDoc($pedido->paciente->doc_termos))?>">
                                        <strong>Termo de Consentimento</strong>
                                    </label>
                                </section>
                                <section class="form-group">
                                <label for="doc_receita" data-attachment="<?=$pedido->doc_receita?>" class="anexo-item download-anexo-item ped-docs" data-token="<?=($_REQUEST['pedido_code'])?>" data-file="doc_receita" title="clique para Baixar este Documento">
                                        <img src="<?=(Modules::getDoc($pedido->doc_receita))?>">
                                        <strong>Receita Médica</strong>
                                    </label>
                                </section>
                            </section>

      <h2 class="titulo-h1">Rastreamento do Pedido</h2>
        <table width="100%" id="tableRastreio" data-token="<?=($_REQUEST['pedido_code'])?>" data-user="<?=($pedido->paciente_token)?>">
          <thead>
              <tr>
                <th width="32px">#</th>
                <th>Evento</th>
                <th width="128px">Data/Hora</th>
                <th width="180px">Usurio</th>
              </tr>
          </thead>
          <tbody>
                <?php
                $stmt = $pdo->prepare('SELECT *,
(SELECT nome_completo FROM PACIENTES WHERE token = `user`
UNION (SELECT nome_completo  FROM FUNCIONARIOS WHERE token = `user`)
UNION  (SELECT nome_completo  FROM MEDICOS WHERE token = `user`)) AS nome_completo FROM RASTREAMENTO WHERE token = :token');
                $stmt->bindValue(':token', $_REQUEST['pedido_code']);
                
                $stmt->execute();
                
                
                $i = 1;
                
                foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $obj) {
                     if($user->tipo == 'USUARIO') {
                    $p = json_decode($pedido->produtos)[$x];
                
                    $prod = $c->getProdByPromo($p->id, $p->qtde);
        
                    $vt = number_format(preg_replace('/[^0-9]/', '', $prod['valor_total']) / 100, 2, ',', '.');
                    $v = number_format(preg_replace('/[^0-9]/', '', $prod['valor']) / 100, 2, ',', '.');
                  
 
                    $dt = date('d/m/Y, H:m:s', strtotime($obj->timestamp));
                    
                    echo "<tr>
                            <td>{$i}</td>
                            <td>{$obj->desc}</td>
                            <td>{$dt}</td>
                            <td>{$obj->nome_completo}</td>
                        </tr>";
                        $i++;
                  } else {
                      echo "<tr>
                            <td>{$i}</td>
                            <td>{$obj->desc}</td>
                            <td>{$dt}</td>
                            <td>{$obj->nome_completo}</td>
                        </tr>";
                        $i++;
                    }
                  }
                
                ?>
          </tbody> 
        </table>
    </div>
   
  </section>