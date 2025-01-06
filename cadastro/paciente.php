<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(isset($_SESSION['userObj'])) {
	    $user = (object) $_SESSION['userObj'];
    }
?>
<section class="main">
    <div class="flex-container">
        <form id="cadastroPacienteBasico" action="/form/cadastro.paciente.php" method="POST"
            class="wizard-form validate-form">
            <h3 class="form-title titulo-h1">Cadastro de Paciente</h3>
            <!-- Indicadores -->
            <div class="form-header">
                <span class="stepIndicator">(*) Campos obrigatórios</span>
            </div>
            <!-- efim de Indicadores -->

            <!-- passo1 -->
            <div class="step active">
                <section class="form-grid area1">
                    <section class="form-group">
                        <label for="cpf">CPF</label>
                        <input data-table="PACIENTE" type="text" id="cpf" class="validade form-control" name="cpf"
                            placeholder="Digite seu CPF" required
                            value="<?=($user->tipo == 'PACIENTE' ? $user->cpf :'')?>" />
                    </section>

                    <section class="form-group">
                        <label for="nome_completo">Nome Completo</label>
                        <input type="text" id="nome_completo" class="form-control capitalize" name="nome_completo"
                            placeholder="Digite seu nome completo" required
                            value="<?=($user->tipo == 'PACIENTE' ? $user->nome_completo :'')?>" />
                    </section>

                    <section class="form-group">
                        <label for="nacionalidade">Nacionalidade</label>
                        <select name="nacionalidade" id="nacionalidade">
                            <option value="" disabled selected>Selecione uma Opção</option>
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
                        <input type="text" id="nome_preferencia" class="form-control capitalize" name="nome_preferencia"
                            placeholder="Digite seu nome de Preferência." required
                            value="<?=($user->tipo == 'PACIENTE' ? $user->nome_preferencia :'')?>" />
                    </section>

                    <section class="form-group">
                        <label for="identidade_genero">Sexo</label>
                        <select name="identidade_genero" class="form-select form-control" id="identidade_genero"
                            required value="<?=($user->tipo == 'PACIENTE' ? $user->identidade_genero :'')?>">
                            <option value="" disabled selected>Selecione uma Opção</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Feminino">Feminino</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </section>

                    <section class="form-group">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input type="text" data-mask="00/00/0000" name="data_nascimento" class="form-control"
                            id="data_nascimento" required="" placeholder="__/__/____" maxlength="10"
                            value="<?=($user->tipo == 'PACIENTE' ? $user->data_nascimento :'')?>" />
                    </section>
                </section>

                <section class="form-grid area1">
                    <section class="form-group">
                        <label for="email">E-mail</label>
                        <input data-table="PACIENTE" type="email" name="email" id="email"
                            class="form-control lower-case" placeholder="Digite seu E-mail" required
                            value="<?=($user->tipo == 'PACIENTE' ? $user->email :'')?>" />
                    </section>

                    <section class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="text" id="telefone" name="telefone" class="form-control"
                            placeholder="(__) ____-____"
                            value="<?=($user->tipo == 'PACIENTE' ? $user->telefone :'')?>" />
                    </section>

                    <section class="form-group">
                        <label for="celular">Celular/WhatsApp</label>
                        <input data-table="PACIENTE" type="text" id="celular" name="celular" class="form-control"
                            required placeholder="(__) _ ____-____"
                            value="<?=($user->tipo == 'PACIENTE' ? $user->celular :'')?>" />
                    </section>

                </section>

                <fieldset class="row" id="responsavel-legal">
                    <legend>Dados do Responsável</legend>
                    <section class="form-grid area-responsavel">
                        <section class="form-group">
                            <label for="cpf">CPF</label>
                            <input type="text" id="responsavel_cpf" data-mask="000.000.000-00" class="form-control"
                                name="responsavel_cpf" placeholder="Digite seu CPF" />
                        </section>

                        <section class="form-group">
                            <label for="responsavel_nome_completo">Nome Completo</label>
                            <input type="text" id="responsavel_nome_completo" class="form-control capitalize"
                                name="responsavel_nome_completo" placeholder="Digite seu nome completo" />
                        </section>

                        <section class="form-group">
                            <label for="responsavel_rg">RG</label>
                            <input type="text" id="responsavel_rg" name="responsavel_rg" class="form-control"
                                placeholder="__.__.__-__" maxlength="11" />
                        </section>

                        <section class="form-group">
                            <label for="responsavel_celular">Contato</label>
                            <input type="text" data-mask="(00) 0 0000-0000" id="responsavel_celular"
                                name="responsavel_celular" class="form-control" placeholder="(__) _ ____-____" />
                        </section>
                    </section>
                </fieldset>

                <section class="form-grid area-full">
                    <div class="checkbox-field">
                        <input type="checkbox" id="newsletter" name="newsletter" />
                        <label for="newsletter">Quero receber novidades por e-mail, SMS, WhatsApp ou mensagens nos App's
                            Clinabs!</a></label>
                    </div>
                    <div class="checkbox-field">
                        <input type="checkbox" id="termos" name="termos" required />
                        <label for="termos">Li e estou de acordo com as políticas da empresa e <a href="#">políticas de
                                privacidade.*</a></label>
                    </div>
                </section>

            </div>


            <div class="form-footer">
                <input type="hidden" name="fid" value="<?=($user->tipo == 'FUNCIONARIO' ? $user->token : '')?>">
                <input type="hidden" name="pid" id="pid" value="">
                <input type="hidden" name="token" value="" id="token">
                <?php
                    if(isset($_REQUEST['redirect'])) {
                        echo '<input type="hidden" name="redirect" value="'.str_replace('/cadastro/paciente?redirect=', '', $_SERVER['REQUEST_URI']).'">';
                    }
                ?>
                <button type="button" name="prev" class="btn-step-prev btn-button2">VOLTAR</button>
                <button type="button" name="next" class="btn-step-next btn-button1">PRÓXIMO</button>
                <button type="submit" class="btn-step-submit btn-button1">FINALIZAR</button>
                <input type="hidden" name="xsrf_token" id="xsrf" value="<?=md5(uniqid())?>">
            </div>
        </form>
    </div>
</section>