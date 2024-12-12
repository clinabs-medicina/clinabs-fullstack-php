<?php
$valor_consulta = strtolower($_GET['atendimento']) == 'online' ? $_GET['valor_consulta_online'] : $_GET['valor_consulta_presencial'];
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(isset($_SESSION['user'])) {
    try {
       $user = (object) $_SESSION['user'];
   } catch (PDOException $e) {
 
   }
}
?>

<section class="main">
    <div class="flex-container">
        <form id="cadastroAgendamento" action="/forms/form.consulta.agendamento.php" method="GET" class="wizard-form">
            <h3 class="form-title titulo-h1">Agendamento de Consulta</h3>
            <div class="form-header">
                <span class="stepIndicator">Dados Pessoais</span>
                <span class="stepIndicator">Pagamento</span>
            </div>

          <div class="step">
                <section class="form-grid area19">
                    <section class="form-group">
                        <label for="cpf">CPF</label>
                        <input type="text"<?=($user->tipo == 'PACIENTE' ? ' data-value="'.$user->cpf.'"':'')?> class="form-control fill-form" id="cpf" name="cpf" placeholder="Digite seu CPF" required value="<?=($_user->cpf)?>" />
                    </section>
                    <section class="form-group">
                        <label for="nome_completo">Nome Completo</label>
                        <input type="text" id="nome_completo" class="form-control capitalize" id="nome_completo" name="nome_completo" placeholder="Digite seu nome completo" required value="<?=($_user->nome_completo)?>" />
                    </section>
                                
                    <section class="form-group">
                        <label for="rg">RG</label>
                        <input type="text" id="rg" name="rg" class="form-control" placeholder="__.__.__-__" maxlength="11" value="<?=($_user->rg)?>">
                    </section>

                    <section class="form-group">
                        <label for="nacionalidade">Nacionalidade</label>
                        <select name="nacionalidade" id="nacionalidade">
                            <option value="" disabled>Selecione uma Opção</option>
                            <?php
                              foreach(SQL::fetchTable(connector: $pdo, tableName: 'PAISES', Extraquery: " WHERE status = 'ATIVADO'") as $pais) {
                                echo '<option id="'.$pais->sigla.'" value="'.$pais->sigla.'">'.$pais->nome_pais.'</option>';
                              }
                            ?>
                        </select>
                    </section>
                </section>

                <section class="form-grid area1">
                    <section class="form-group">
                        <label for="nome_preferencia">Nome de Preferência</label>
                        <input type="text" id="nome_preferencia" class="form-control capitalize" name="nome_preferencia" placeholder="Digite seu nome de Preferência." required value="<?=($_user->nome_preferencia)?>" />
                    </section>

                    <section class="form-group">
                        <label for="identidade_genero">Sexo</label>
                        <select name="identidade_genero" class="form-select form-control" id="identidade_genero" required value="<?=($_user->identidade_genero)?>")?>">
                            <option value="" disabled>Selecione uma Opção</option>
                            <option value="Masculino"<?=($_user->identidade_genero == 'Masculino' ? ' selected' :'')?>>Masculino</option>
                            <option value="Feminino"<?=($_user->identidade_genero == 'Feminino' ? ' selected' :'')?>>Feminino</option>
                        </select>
                    </section>

                    <section class="form-group">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input type="text" data-mask="00/00/0000" name="data_nascimento" class="form-control validate-age" id="data_nascimento" required="" placeholder="__/__/____" maxlength="10" value="<?=(isset($_user->data_nascimento) ? date('d/m/Y', strtotime($_user->data_nascimento)) : '')?>" />
                    </section>
                </section>

                <section class="form-grid area1">
                    <section class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" class="form-control lower-case" placeholder="Digite seu E-mail" required value="<?=($_user->email)?>" />
                    </section>

                    <section class="form-group">
                        <label for="anamnese">Queixa Principal</label>
                        <select id="anamnese" data-selected="1" name="anamnese" class="form-control" required data-value="<?=($_user->queixa_principal)?>">
                            <option disabled>Selecione uma Opção</option>
                               <?php
                                    $sql = "SELECT * FROM ANAMNESE";

                                    $stmtx = $pdo->prepare($sql);
                                    $stmtx->execute();
                                    
                                    foreach($stmtx->fetchAll() as $anamnese) {
                                        echo '<option value="'.$anamnese['id'].'" '.($user->tipo == 'PACIENTE' &&  $_user->anamnese == $anamnese ? ' selected' :'').'>'.$anamnese['nome'].'</option>';
                                    }
                                ?>
                                    </select>
                    </section>

                    <section class="form-group">
                        <label for="celular">Celular/WhatsApp</label>
                        <input type="text" data-mask="+55 (00) 0 0000-0000" id="celular" name="celular" class="form-control" required placeholder="(__) _ ____-____" value="<?=($_user->celular)?>" />
                    </section>
                </section>

                <fieldset class="row" id="responsavel-legal">
                    <legend>Dados do Responsável</legend>
                    <section class="form-grid area-responsavel">
                            <section class="form-group">
                                <label for="cpf">CPF</label>
                                <input type="text" id="responsavel_cpf" data-mask="000.000.000-00" class="form-control" name="responsavel_cpf" placeholder="Digite seu CPF"/>
                            </section>

                            <section class="form-group">
                                <label for="responsavel_nome_completo">Nome Completo</label>
                                <input type="text" id="responsavel_nome_completo" class="form-control capitalize" name="responsavel_nome_completo" placeholder="Digite seu nome completo" />
                            </section>
                                    
                            <section class="form-group">
                                <label for="responsavel_rg">RG</label>
                                <input type="text" id="responsavel_rg" name="responsavel_rg" class="form-control" placeholder="__.__.__-__" maxlength="11" />
                            </section>

                            <section class="form-group">
                                <label for="responsavel_celular">Contato</label>
                                <input type="text" data-mask="(00) 0 0000-0000" id="responsavel_celular" name="responsavel_celular" class="form-control" placeholder="(__) _ ____-____" />
                            </section>
                        </section>
                </fieldset>
            </div>
       <?php
        $token = $_GET['medico_token'];
        $sql = "SELECT * FROM MEDICOS WHERE token = '$token'";

       $stmt2 = $pdo->prepare($sql);
       $stmt2->execute();

       $med = $stmt2->fetch(PDO::FETCH_ASSOC);
       ?>
            <!-- passo 3 -->
            <div class="step">
                <section class="form-grid area-two">
                    <section class="form-group payment-method">
                        <!--
                        <div class="payment-button credit-card" style="filter: grayscale(1); point-events: none">
                                    <label for="payment_credit">
                                        <input id="payment_credit" type="radio" id="payment_pix" name="payment_mode" value="PIX" required> PIX
                                    </label>
                                    <span class="payment_pix">R$ <?=number_format($valor_consulta,2, ',','.')?></span>
                                </div>
                                 -->

                                <div class="payment-button credit-card">
                                    <label for="payment_credit">
                                        <input id="payment_credit" type="radio" id="payment_pix" name="payment_mode" value="CREDIT_CARD" required> CARTÃO DE CRÉDITO ou DÉBITO
                                    </label>
                                    <span class="payment_pix">R$ <?=number_format($valor_consulta,2, ',','.')?></span>
                                </div>
                               
                        <?php
                        
                        if(/*(in_array($_SERVER['REMOTE_ADDR'], $ALLOWED_IP) || $_SERVER['REMOTE_ADDR'] == '127.0.0.1') && */$user->tipo == 'FUNCIONARIO') 
                        {
                            
                           echo "<input type=\"hidden\" name=\"external_payment\" value=\"{$_REQUEST['medico_token']}\">";
                                echo '<div class="payment-button credit-card">
                                            <input type="hidden" name="payment_token" value="">
                                            <label for="external_payment"><input id="external_payment" type="radio" id="external_payment_mode" name="payment_mode" value="PIX" required> Pagamento Presencial</label>
                                            <span class="payment_valor_cc">R$ '.number_format($valor_consulta,2, ',','.').'</span>
                                            <input type="hidden" class="payment-value" value="'.($valor_consulta).'">
                                        </div>';
                            
                        } 
                        ?>
                    </section>
                
               <div class="payment-box">
                   <section class="payment-details">
                       <div class="payment-details-header">CUPOM DE DESCONTO</div>
                       <div class="payment-details-info">
                           <span>Código</span>
                           <span class="cupom">
                               <input name="cupom" id="cupomDesconto" value="">
                               <button type="button" class="btn-cupom btn-button1">APLICAR</button>
                           </span>
                       </div>
                   </section>
                   
                    <section class="payment-details">
                    <div class="payment-details-header">Detalhes da Cobrança</div>
                    <div class="payment-details-info">
                        <span><span>Subtotal</span> <span class="subtotal">R$ <?=number_format($valor_consulta,2, ',','.')?></span></span>
                        <span><span>Desconto</span> <span class="desconto">R$ 0,00</span></span>
                    </div>
                    <div class="payment-details-footer">
                        <b>Total</b>
                        <b class="valor-total">R$ <?=number_format($valor_consulta,2, ',','.')?></b>
                        </div>
                </section>
                
                <section class="payment-details">
                    <div class="payment-details-header">Política de Cancelamento e Reembolso</div>
                    <div class="payment-details-info">
                        <ul style="text-align:left; font-weight:400; ">
                            <li style="padding:.3rem">- Se o cancelamento da consulta for solicitado com pelo menos 24 horas de antecedência, ser reembolsado o valor total pago.</li>
                            <li style="padding:.3rem">- Em caso de cancelamento por parte do profissional, o paciente terá direito ao reembolso integral.</li>
                            <li style="padding:.3rem">- Se houver reagendamento por parte do profissional, o paciente poderá optar entre solicitar o reembolso ou utilizar o valor pago para a nova consulta.</li>
                            <li style="padding:.3rem">- O não comparecimento do paciente à consulta não resultará em reembolso; o valor da consulta será repassado ao profissional.</li>
                            <li style="padding:.3rem">- Atrasos superiores a 10 minutos resultarão no cancelamento da consulta, sem direito a reembolso.</li>
                            <li style="padding:.3rem">- Para detalhes mais específicos, recomendamos a leitura do nosso termo de uso.</li>          
                        <ul>
                    </div>
                </section>

               </div>
                </section>
            </div>

            <div class="form-footer">
                <input type="hidden" name="fid" value="<?=($user->tipo == 'FUNCIONARIO' ? $user->token : '')?>">
                <input type="hidden" name="pid" id="pid" value="">
                <input type="hidden" name="atendimento" value="<?=($_GET['atendimento'])?>">
                <input type="hidden" name="data_agendamento" value="<?=date('Y-m-d H:i:s', strtotime(trim(preg_replace('!\s+!', ' ',(isset($_GET['data_agendamento']) ? $_GET['data_agendamento'] : $_GET['dt'])))))?>">
                <input type="hidden" name="medico_token" value="<?=($_GET['medico_token'])?>">
                <input type="hidden" name="paciente_token" id="paciente_token" value="<?=($_user->tipo == 'PACIENTE' ? $user->token:$_user->token)?>">
                <input type="hidden" name="valor_consulta" value="<?=trim($valor_consulta) ?>" id="valorTotal">
                <input type="hidden" name="valor_pagar" value="<?=trim($valor_consulta) ?>">
                <input type="hidden" name="payment_id" id="payment_id">
                <input type="hidden" name="add_new" id="add_new">
                <button type="button" name="prev" class="btn-step-prev btn-button2">VOLTAR</button>
                <button type="button" name="next" class="btn-step-next btn-button1">PRÓXIMO</button>
                <button type="submit" class="btn-step-submit btn-button1">FINALIZAR COMPRA</button>
            </div>
        </form>
    </div>
</section>
