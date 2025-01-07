<section class="main">

    <section>

        <h1 class="titulo-h1">Pacientes</h1>

    </section>

    <div class="flex-container produtos-flex">

        <table name="paciente" id="tablePacientes" class="display dataTable">

            <thead>

                <tr>

                    <th>Nome</th>
                    <th>Idade</th>
                    <th>Nome do Médico</th>
                    <th width="128px">Celular</th>
                    <th width="128px">Situação</th>
                    <th width="160px">Ações</th>

                </tr>

            </thead>
            <tbody>

        <?php
          $wr = '';
          
          if($user->tipo == 'MEDICO') {
              $wr = ' WHERE medico_token="'.$user->token.'" ';
          }
          
          function valida_email($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            }
          
          function validaCPF($cpf) {
 
                // Extrai somente os números
                $cpf = preg_replace( '/[^0-9]/is', '', $cpf );
                 
                // Verifica se foi informado todos os digitos corretamente
                if (strlen($cpf) != 11) {
                    return false;
                }
            
                // Verifica se foi informada uma sequência de digitos repetidos. Ex: 111.111.111-11
                if (preg_match('/(\d)\1{10}/', $cpf)) {
                    return false;
                }
            
                // Faz o calculo para validar o CPF
                for ($t = 9; $t < 11; $t++) {
                    for ($d = 0, $c = 0; $c < $t; $c++) {
                        $d += $cpf[$c] * (($t + 1) - $c);
                    }
                    $d = ((10 * $d) % 11) % 10;
                    if ($cpf[$c] != $d) {
                        return false;
                    }
                }
                return true;
            
            }
            
          function validateDate($date, $format = 'Y-m-d H:i:s') {
                try {
                    $d = DateTime::createFromFormat($format, $date);
                    return $d && $d->format($format) == $date;
                } catch(Exception $ex) {
                    return false;
                }
            }
            
            function validateCell($cell) {
               return (strlen(preg_replace( '/[^0-9]/is', '', $cell)) >= 11);
            }

          
          function check_user($item) {
              $isValid = true;
              
                $hints = [];
                  
                  if(!validaCPF($item['cpf'])) {
                     $hints[] = 'CPF Inválido';
                     
                     if($isValid) {
                         $isValid = false;
                     }
                  }
                  
                  if(!valida_email($item['email'])) {
                      $hints[] = 'E-mail Inválido';
                      
                      if($isValid) {
                         $isValid = false;
                     }
                  }
                  
                  if(!validateCell($item['celular'])) {
                      $hints[] = 'Celular Inválido';
                      
                      if($isValid) {
                         $isValid = false;
                     }
                  }
                  
              return  ['isValid' => $isValid, 'hints' => $hints];
          }
         
          foreach($pdo->query("SELECT
            `nome_completo`,
            `cpf`,
            `telefone`,
            `data_nascimento`,
            `identidade_genero`,
            `rg`,
            `email`,
            `telefone`,
            `celular`,
            `token`,
            `objeto`,
            TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()) AS age,
            (SELECT MEDICOS.nome_completo FROM MEDICOS WHERE MEDICOS.token = medico_token) AS medico_nome,
            `session_online`
            FROM
            `PACIENTES`
            ORDER BY `id` DESC")->fetchAll(PDO::FETCH_ASSOC) as $item) {

            if($item['age'] < 18 && ($item['responsavel_nome'] == '' || $item['responsavel_cpf'] == '' || $item['responsavel_email'] == '' || $item['responsavel_celular'] == ''))
            {
                $trColor = '#ff000045';
            } else {
                $trColor = '';
            }

            echo '<tr style="background-color: '.$trColor.'">';
                echo '<td data-label="Paciente: "><img src="'.Modules::getUserImage($item['token']).'" width="64px" style="border-radius: 64px;display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;"><span class="user-item '.$item['token'].'">'.$prefixo.' '.$item['nome_completo'].'</span></td>';
                echo "<td data-label=\"Idade: \">{$item['age']} ano(s)</td>";
                echo '<td data-label="Médico: "><img src="/assets/images/ico-medicoblack.svg" width="20px" style="display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;"><span class="paciente-nome">'.($item['medico_nome'] ?? 'Nenhum Profissional Selecionado.').'</span></td>';
                echo '<td data-label="Celular: ">'.$item['celular'].'</td>';
                $validation = check_user($item);
                echo '<td data-label="Cadastro: ">'.($validation['isValid'] ? '<b style="color: green;">Atualizado</a>':'<b style="color: red;" title="'.implode("\n", $validation['hints']).'">Desatualizado</b>').'</td>';
                echo '<td class="td-act">';

                if($item['age'] < 18 && ($item['responsavel_nome'] == '' || $item['responsavel_cpf'] == '' || $item['responsavel_email'] == '' || $item['responsavel_celular'] == '')) {
                    if($user->perms->comprar_medicamento == 1 ) {
                        echo '<button disabled class="btn-action" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="cart-products" ><img src="/assets/images/ico-cart-btn.svg" height="32px" title="Comprar Medicamentos  ( [ Atenção ] Cadastro deste Paciente Precisa de Preencher os dados do Responsável )"></button>';
                    }
                    echo '<button disabled class="btn-action" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="agendar-consulta"><img src="/assets/images/ico-doctor-btn.svg" height="32px" title="Agendar Consulta  ( [ Atenção ] Cadastro deste Paciente Precisa de Preencher os dados do Responsável )"></button>';
                } else {
                    if($user->perms->comprar_medicamento == 1 ) {
                        echo '<button class="btn-action" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="cart-products" ><img src="/assets/images/ico-cart-btn.svg" height="32px" title="Comprar Medicamentos"></button>';
                    }

                    echo '<button class="btn-action" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="agendar-consulta"><img src="/assets/images/ico-doctor-btn.svg" height="32px" title="Agendar Consulta"></button>';
                }

                echo '<button class="btn-action" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="editar-perfil"><img src="/assets/images/ico-edit.svg" height="32px title="Editar Perfil"></button>';
                
                echo"<button class=\"btn-action\" onclick=\"wa_send_message(this)\" data-wa=\"".preg_replace("/[^A-Za-z0-9]/", "", $item['celular'])."\"><img src=\"/assets/images/ico-wa-btn.svg\" height=\"36px\" title=\"Enviar Notificação/Mensagem\"></button>";
                if($user->tipo == 'MEDICO') {
                    if($user->prescricao_sem_receita == 1) {
                        echo '<a class="btn-action" href="/cadastros/pacientes/prescricao/'.$item['token'].'" data-token="'.$item['token'].'"><img src="/assets/images/ico-presc.svg" height="32px" title="Prescrição Médica"></button>';
                    }
                    
                    if($user->perms->link_paciente_acompanhamento == 1) {
                        echo '<a class="btn-action" href="/cadastros/pacientes/acompanhamento/'.$item['token'].'" data-token="'.$item['token'].'"><img src="/assets/images/ico-presc.svg" height="32px" title="Acompanhamento de Paciente"></button>';
                    }
                } else if($user->tipo == 'FUNCIONARIO' && $user->perms->link_paciente_acompanhamento == 1) {
                    echo '<a class="btn-action" href="/cadastros/pacientes/acompanhamento/'.$item['token'].'" data-token="'.$item['token'].'"><img src="/assets/images/ico-presc.svg" height="32px" title="Acompanhamento de Paciente"></button>';
                }
                
                echo '</td>';
            echo '</tr>';

          }

          ?>

            </tbody>

        </table>

    </div>

</section>
