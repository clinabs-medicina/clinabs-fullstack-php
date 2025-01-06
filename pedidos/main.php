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
        <h1 class="titulo-h1"><?=($user->tipo == 'FUNCIONARIO' ? 'Pedidos':'Meus Pedidos')?></h1>
    </section>
    <div class="flex-container produtos-flex">
        
        <table id="tablePedidos" class="display dataTable">
         <thead>
            <tr>
                <th class="hide-mb">Código</th>
                <th>Data <span class="hide-mb">Compra</span></th>
                <th>Paciente</th>
                <th class="hide-mb">Médico</th>
                <th class="hide-mb">Atendente</th>
                <th class="hide-mb">Status</th>
                <th>Ações</th>
            </tr>
         </thead>
        <tbody>
          <?php
          if(isset($_SESSION['token']) || $user->tipo == 'PACIENTE') {
            if(isset($_SESSION['token'])) {
                $query = "SELECT
                    FARMACIA.produtos, 
                    FARMACIA.valor_total, 
                    FARMACIA.timesptamp, 
                    FARMACIA.funcionario_token, 
                    FARMACIA.paciente_token, 
                    FARMACIA.token, 
                    FARMACIA.`status`, 
                    FARMACIA.payment_method, 
                    (SELECT PACIENTES.nome_completo FROM PACIENTES WHERE PACIENTES.token = FARMACIA.paciente_token) AS paciente_nome,
                    (SELECT FUNCIONARIOS.nome_completo FROM FUNCIONARIOS WHERE FUNCIONARIOS.token = FARMACIA.funcionario_token) AS funcionario_nome,
                    (SELECT MEDICOS.nome_completo FROM MEDICOS WHERE MEDICOS.token = FARMACIA.medico_token) AS medico_nome
                  FROM
                    FARMACIA
                  WHERE paciente_token = '".($_SESSION['token'])."'";
            }else {
                $query = "SELECT
                    FARMACIA.produtos, 
                    FARMACIA.valor_total, 
                    FARMACIA.timesptamp, 
                    FARMACIA.funcionario_token, 
                    FARMACIA.paciente_token, 
                    FARMACIA.token, 
                    FARMACIA.`status`, 
                    FARMACIA.payment_method, 
                    (SELECT PACIENTES.nome_completo FROM PACIENTES WHERE PACIENTES.token = FARMACIA.paciente_token) AS paciente_nome,
                    (SELECT FUNCIONARIOS.nome_completo FROM FUNCIONARIOS WHERE FUNCIONARIOS.token = FARMACIA.funcionario_token) AS funcionario_nome,
                    (SELECT MEDICOS.nome_completo FROM MEDICOS WHERE MEDICOS.token = FARMACIA.medico_token) AS medico_nome
                  FROM
                    FARMACIA
                  WHERE paciente_token = '".($user->token)."'";
            }
          }else {
            $query = "SELECT
            FARMACIA.produtos, 
            FARMACIA.valor_total, 
            FARMACIA.timesptamp, 
            FARMACIA.funcionario_token, 
            FARMACIA.paciente_token, 
            FARMACIA.token, 
            FARMACIA.`status`, 
            FARMACIA.payment_method, 
            (SELECT PACIENTES.nome_completo FROM PACIENTES WHERE PACIENTES.token = FARMACIA.paciente_token) AS paciente_nome,
            (SELECT FUNCIONARIOS.nome_completo FROM FUNCIONARIOS WHERE FUNCIONARIOS.token = FARMACIA.funcionario_token) AS funcionario_nome,
            (SELECT MEDICOS.nome_completo FROM MEDICOS WHERE MEDICOS.token = FARMACIA.medico_token) AS medico_nome
          FROM
            FARMACIA";
          }
            foreach($pdo->query($query)->fetchAll(PDO::FETCH_OBJ) as $column) 
            {
                $prods = json_decode($column->produtos, true);
                $produtos = [];
                
                $isIn = false;
                
                foreach($prods as $prod) {
                     $produtos[] = $prod['id'];
                }

                
                if($user->tipo == 'USUARIO') {
                    if(count($produtos) > 0) {
                     
                    $stmtz = $pdo->query('SELECT id,marca FROM `PRODUTOS` WHERE id IN('.implode(', ', $produtos).') GROUP BY marca');
                    
                    foreach($stmtz->fetchAll(PDO::FETCH_ASSOC) as $prod) {
                         if(in_array($prod['marca'], $user->marcas)) {
                            $isIn = true;
                            break;
                        }
                      }
                    }

                } else {
                    $isIn = true;
                }
                
               if($isIn) {
                  if($column->status == 'PAGO') {
                    try {
                    $stmt = $pdo->prepare('SELECT `desc` FROM `RASTREAMENTO` WHERE token = :token ORDER BY timestamp DESC LIMIT 1');
                    $stmt->bindValue(':token', $column->token);
                    
                    $stmt->execute();
                    
                    $sts = $stmt->fetch(PDO::FETCH_OBJ);
                    $sts = $sts->desc;
                } catch(Exception $ex) {
                    $sts = $column->status;
                }
               }else {
                   $sts = $column->status;
               }
                
              echo "<tr id=\"{$column->token}\">";
                echo "<td><a href=\"/pedidos/{$column->token}\">{$column->token}</a></td>";
                echo '<td>'.date('d/m/Y H:i', strtotime($column->timesptamp)).'</td>';
                echo "<td><img src=\"/assets/images/ico-pacienteblack.svg\" width=\"20px\" style=\"display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;\"><span class=\"paciente-nome\">{$column->paciente_nome}</span></td>";
                echo "<td><img src=\"/assets/images/ico-medicoblack.svg\" width=\"20px\" style=\"display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;\">{$column->medico_nome}</td>";
                echo "<td><img src=\"/assets/images/ico-pacienteblack.svg\" width=\"20px\" style=\"display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;\"><span class=\"paciente-nome\">{$column->funcionario_nome}</span></td>";
                echo "<td class=\"td-status\">{$sts}</td>";
                echo "<td>
                <div class=\"btns-act\">
                <div class=\"btns-table\">";
                
                

                  switch($column->status) {
                    case 'PAGO': {
                      echo "<button title=\"Confirmar Entrega\" class=\"btn-action\" onclick=\"action_btn_pedidos(this)\" data-token=\"{$column->token}\" data-action=\"order-accept\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                      echo "<button title=\"Cancelar Pedido\" class=\"btn-action\" onclick=\"action_btn_pedidos(this)\" data-token=\"{$column->token}\" data-action=\"order-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                      $allow_tc = false;
                      break;
                    }

                    case 'AGUARDANDO PAGAMENTO': {
                        echo "<button disabled title=\"Confirmar Entrega\" class=\"btn-action\" onclick=\"action_btn_pedidos(this)\" data-token=\"{$column->token}\" data-action=\"order-accept\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                        echo "<button title=\"Cancelar Pedido\" class=\"btn-action\" onclick=\"action_btn_pedidos(this)\" data-token=\"{$column->token}\" data-action=\"order-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                        $allow_tc = false;
                        break;
                    }

                    case 'CANCELADO': {
                        echo "<button disabled title=\"Confirmar Entrega\" class=\"btn-action\" onclick=\"action_btn_pedidos(this)\" data-token=\"{$column->token}\" data-action=\"order-accept\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                        echo "<button disabled title=\"Cancelar Pedido\" class=\"btn-action\" onclick=\"action_btn_pedidos(this)\" data-token=\"{$column->token}\" data-action=\"order-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                        $allow_tc = false;
                        break;
                    }

                    case 'ENTREGUE': {
                      echo "<button disabled title=\"Confirmar Entrega\" class=\"btn-action\" onclick=\"action_btn_pedidos(this)\" data-token=\"{$column->token}\" data-action=\"order-accept\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                      echo "<button disabled title=\"Cancelar Pedido\" class=\"btn-action\" onclick=\"action_btn_pedidos(this)\" data-token=\"{$column->token}\" data-action=\"order-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                      $allow_tc = false;
                      break;
                    }

                    case 'CANCELAMENTO PENDENTE': {
                      echo "<button disabled title=\"Confirmar Entrega\" class=\"btn-action\" onclick=\"action_btn_pedidos(this)\" data-token=\"{$column->token}\" data-action=\"order-accept\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-checked.svg\" height=\"28px\"></button>";
                      echo "<button disabled title=\"Cancelar Pedido\" class=\"btn-action\" onclick=\"action_btn_pedidos(this)\" data-token=\"{$column->token}\" data-action=\"order-cancel\" data-status=\"{$column->status}\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                      $allow_tc = false;
                      break;
                    }
                  }
                    
                echo "<a title=\"Baixar PDF de Resumo do Pedido\" href=\"/api/pdf/pedido.php?pedido_code={$column->token}\" download=\"PEDIDO_{$column->token}.pdf\"><img src=\"/assets/images/download.svg\" style=\"border-radius: 28px\" height=\"28px\"></a>";
                echo "</div>
                </div>
                </td>";
              echo "</tr>";
               }
            }
            
            ?>
        </tbody>
        </table>
    </div>
  </section>