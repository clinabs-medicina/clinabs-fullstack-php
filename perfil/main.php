<section class="main" id="user-main">
    <div class="flex-container">
        <form id="formUpdateCadastro" action="/form/form.cadastro.update.<?=strtolower($_user->tipo)?>.php"
            method="POST" class="form-paciente">
            <h3 class="form-title titulo-h1">Meu Perfil</h3>

            <section id="tabControl1" class="tabControl locked">
                <div class="container-profile">
                    <section class="user-profile">
                        <div class="profile-image"
                            style='background-image: url("<?=Modules::getUserImage($_user->token) ?>")'> <label
                                class="profile-image-changer" for="image-file-input">Alterar</label> <input
                                autocomplete="off" disabled="true" type="file" accept="image/*" id="image-file-input"
                                style="display: none"> </div>
                        <div class="profile-info">
                            <h2><?=($_user->nome_completo)?></h2>
                            <p><?=($_user->esp)?></p>
                            <p><a href="#" class="btn-edit-form btn-edit">Editar Perfil</a></p>
                            <?php
                        function calculateAge($birthdate) {
                            $birthDate = new DateTime($birthdate);
                            $today = new DateTime('today');
                            $age = $birthDate->diff($today)->y;
                            return $age;
                        }

                        if($_user->objeto == 'PACIENTE' && calculateAge($birthdate) < 18 && $_user->responsavel_nome == '' || $_user->responsavel_cpf == '' || $_user->responsavel_email == '' || $_user->responsavel_celular == '') {
                            $warningMsg = "Atenção, é necessário preencher os campos do Responsável para poder agendar uma consulta.";
                        } else {
                            $warningMsg = false;
                        }
    
                        ?>
                        </div>
                    </section>
                    <div class="profile-info"> <img src=""> </div>
                </div>

                <?php
                $birthDate = new DateTime($_user->data_nascimento ?? $user->data_nascimento);
                $currentDate = new DateTime();
                $age = $currentDate->diff($birthDate);
            ?>
                <div class="tab-toolbar">
                    <span class="active" data-index="1" data-tab="tabControl1">Perfil</span>
                    <span data-index="2" data-tab="tabControl1">Endereços</span>
                    <?=($_user->objeto == 'PACIENTE' && ($age->y < 18) ? "<span data-index=\"8\" data-tab=\"tabControl1\" style=\"{$borderTab}\">Responsável</span>":'')?>
                    <span data-index="3" data-tab="tabControl1">Documentação</span>
                    <?=(($_user->objeto == 'MEDICO' && $_user->disponibilizar_agenda == 1) ? '<span data-index="4" data-tab="tabControl1">Calendário</span><span data-index="5" data-tab="tabControl1">Certificado</span>':'' )?>
                    <?=($user->tipo == 'FUNCIONARIO' && $user->perms->aba_api == 1  ? '<span data-index="6" data-tab="tabControl1">Servidor</span>':'' )?>
                    <?=($user->tipo == 'PACIENTE' && $user->perms->user_docs == 1  ? '<span data-index="9" data-tab="tabControl1">Anexos</span>':'' )?>
                </div>
                <div class="tab tab-disabled active" data-index="1" data-tab="tabControl1">
                    <section class="form-grid area3">
                        <section class="form-group"> <label for="nome_completo">Nome Completo</label> <input
                                autocomplete="off" data-required disabled="true" value="<?=($_user->nome_completo)?>"
                                type="text" id="nome_completo" class="form-control" name="nome_completo"
                                placeholder="Digite seu nome completo"> </section>
                        <section class="form-group"> <label for="nacionalidade">Nacionalidade</label> <select
                                data-search="true" name="nacionalidade" id="nacionalidade" disabled
                                style="background-image: url('https://static.significados.com.br/flags/<?=strtolower($_user->nacionalidade)?>.svg')">
                                <option disabled="" <?=$_user->nacionalidade == '' ? ' selected':''?>>Selecione uma
                                    Opção</option> <?php
                                        foreach(SQL::fetchTable(connector: $pdo, tableName: 'PAISES', Extraquery: " WHERE status = 'ATIVADO'") as $pais) {
                                            echo '<option id="'.$pais->sigla.'" value="'.$pais->sigla.'"'.($_user->nacionalidade == $pais->sigla ? ' selected="selected"':'').'>'.$pais->nome_pais.'</option>';
                                        }
                                        ?>
                            </select> </section>
                    </section>
                    <section class="form-grid area1">
                        <section class="form-group"> <label for="nome_preferencia">Nome de Preferência</label> <input
                                autocomplete="off" disabled="true" value="<?=($_user->nome_preferencia)?>" type="text"
                                id="nome_preferencia" class="form-control" name="nome_preferencia"
                                placeholder="Digite seu nome de Preferência."> </section>
                        <section class="form-group"> <label for="identidade_genero">Identidade de Gênero</label> <select
                                data-search="true" name="identidade_genero" class="form-select form-control"
                                id="identidade_genero" disabled>
                                <option value="" disabled
                                    <?=($_user->identidade_genero == '' ? ' selected="selected"':'')?>>Selecione uma
                                    Opção</option>
                                <option value="Masculino"
                                    <?=($_user->identidade_genero == 'Masculino' ? ' selected="selected"':'')?>>
                                    Masculino</option>
                                <option value="Feminino"
                                    <?=($_user->identidade_genero == 'Feminino' ? ' selected="selected"':'')?>>Feminino
                                </option>
                                <option value="Outros"
                                    <?=($_user->identidade_genero == 'Outros' ? ' selected="selected"':'')?>>Outros
                                </option>
                            </select> </section>
                        <section class="form-group"> <label for="data_nascimento">Data de Nascimento</label>
                            <input autocomplete="off" data-required disabled="true"
                                value="<?=date('d/m/Y', strtotime($_user->data_nascimento))?>" type="text"
                                data-mask="00/00/0000" name="data_nascimento" class="form-control" id="data_nascimento"
                                placeholder="__/__/____" maxlength="10">
                        </section>
                    </section>
                    <section class="form-grid area1">
                        <section class="form-group"> <label for="email">E-mail</label> <input autocomplete="off"
                                data-required disabled="true" type="email" value="<?=($_user->email)?>" name="email"
                                id="email" class="form-control" placeholder="Digite seu E-mail"> </section>
                        <section class="form-group"> <label for="telefone">Telefone</label> <input autocomplete="off"
                                disabled="true" value="<?=($_user->telefone)?>" type="text" id="telefone"
                                name="telefone" class="form-control" placeholder="(__) ____-____" maxlength="14">
                        </section>
                        <section class="form-group"> <label for="celular">Celular/WhatsApp</label>
                            <div class="input-group"> <input autocomplete="off" data-required disabled="true"
                                    value="<?=($_user->celular)?>" type="text" id="celular" name="celular"
                                    class="form-control" placeholder="(__) _ ____-____" maxlength="16"> <i
                                    class="fa fa-whatsapp" onclick="import_wa(this)"
                                    data-cell="<?=preg_replace("/[^A-Za-z0-9]/", "", $_user->celular)?>"
                                    title="Importar Foto do Perfil do WhatsApp"></i> </div>
                        </section>
                    </section>
                    <section class="form-grid area1">
                        <section class="form-group"> <label for="senha">Senha</label> <input autocomplete="off"
                                type="password" disabled="true" name="senha" id="senha" class="form-control pwd-alter"
                                placeholder="Digite sua nova Senha"> </section>
                        <section class="form-group"> <label for="confirm_senha">Confirmar Senha</label> <input
                                autocomplete="off" type="password" disabled="true" name="confirm_senha"
                                id="confirm_senha" class="form-control pwd-alter" placeholder="Confirme sua nova Senha">
                        </section>
                        <section class="form-group"> <label for="situacao">Situação Cadastral</label> <?php
                                     if($user->perms->perfil_situacao_cadastral == 1) {
                                         ?> <select class="form-control" name="situacao">
                                <option value="ATIVO" <?=($_user->status == 'ATIVO' ? ' selected':'')?>>REGULAR</option>
                                <option value="INATIVO" <?=($_user->status == 'INATIVO' ? ' selected':'')?>>INATIVO
                                </option>
                                <option value="BLOQUEADO" <?=($_user->status == 'BLOQUEADO' ? ' selected':'')?>>
                                    BLOQUEADO</option>
                                <option value="SUSPENSO" <?=($_user->status == 'SUSPENSO' ? ' selected':'')?>>SUSPENSO
                                </option>
                            </select> <?php
                                     }else {
                                         echo '<span class="form-control">'.($_user->status == 'ATIVO' ? 'REGULAR': $_user->status).'</span>';
                                         echo '<input autocomplete="off" type="hidden" name="situacao" value="'.$_user->status.'">';
                                     }
                                     ?> </section>
                    </section>
                    <?php
                  
                 	if($_user->objeto == 'USUARIO' && $user->perms->id == 2) {
                    	?>
                    <section class="form-grid area-full">
                        <section class="form-group">
                            <label for="select-marcas">Marcas</label>
                            <select data-search="true" multiple id="select-marcas" name="marcas" class="form-control">
                                <?php
                                            $stmtz = $pdo->query("SELECT * FROM `MARCAS`");
                      						$marcas = json_decode($_user->marcas, true);

                                            foreach($stmtz->fetchAll() as $marca) {
                                                if(in_array($marca['id'], $marca)) {
                                                  echo "<option selected value=\"{$marca['id']}\">{$marca['nome']}</option>";
                                                }else {
                                                  echo "<option value=\"{$marca['id']}\">{$marca['nome']}</option>";
                                                }
                                            } 
                                          ?>
                            </select>
                        </section>
                    </section>
                    <?php
                   		 }
                            if($_user->objeto == 'MEDICO') {
                                if($user->perms->id == 2 || $user->perms->id == 4 || $user->perms->id == 6) {
          
                                    ?>
                    <section class="form-grid area-pme" style="display: none !important">
                        <section class="form-group">
                            <label for="payment_type">Permitir Pagamento Externo</label>
                            <select autocomplete="off" data-required disabled="true" type="text"
                                value="<?=($_user->do_payment)?>" name="do_payment" id="do_payment"
                                class="form-control">
                                <option value="0" <?=($_user->do_payment == 0 ? ' selected':'')?>>Não</option>
                                <option value="1" <?=($_user->do_payment == 1 ? ' selected':'')?>>Sim</option>
                            </select>
                        </section>

                        <section class="form-group">
                            <label for="telefone">Nome da Clínica</label>
                            <input autocomplete="off" disabled="true" value="<?=($_user->nome_clinica)?>" type="text"
                                id="nome_clinica" name="nome_clinica" class="form-control uppercase"
                                placeholder="Nome de sua Clínica">
                        </section>

                        <section class="form-group">
                            <label for="telefone">Link Fixo de Pagamento</label>
                            <input autocomplete="off" disabled="true" value="<?=($_user->payment_link)?>" type="text"
                                id="payment_link" name="payment_link" class="form-control"
                                placeholder="Link para Pagamento Externo">
                        </section>
                        <section class="form-group">
                            <label for="celular">WhatsApp para Notificação de Pagamento</label>
                            <input autocomplete="off" data-required disabled="true"
                                value="<?=($_user->wa_notificacao)?>" type="text" id="wa_notificacao"
                                name="wa_notificacao" class="form-control" placeholder="(__) _ ____-____"
                                maxlength="16">
                        </section>
                    </section>

                    <section class="form-grid area1">

                        <section class="form-group">
                            <label for="grupo_especialidades">Grupo de Especialidades</label>
                            <?php
                            if($user->perms->grupo_especialidades == 1) {
                            ?>
                            <select disabled="true" value="<?=($_user->grupo_especialidades)?>"
                                id="grupo_especialidades" name="grupo_especialidades" class="form-control">
                                <option disabled>Selecione uma Opção</option>
                                <?php
                                                    $xstmt = $pdo->query('SELECT * FROM `GRUPO_ESPECIALIDADES`');
                                                    $grupos = $xstmt->fetchAll(PDO::FETCH_OBJ);

                                                    foreach($grupos as $grupo) {
                                                        if($_user->grupo_especialidades == $grupo->id) {
                                                            echo "<option selected value=\"{$grupo->id}\">{$grupo->nome}</option>";
                                                        } else {
                                                            echo "<option value=\"{$grupo->id}\">{$grupo->nome}</option>";
                                                        }
                                                    }
                                                ?>
                            </select>
                            <?php
                            }
                            else {
                                echo "<input class=\"form-control uppercase\" name=\"grupo_especialidades\" value=\"{$_user->grupo_especialidades}\">";
                            }
                            ?>
                        </section>


                        <section class="form-group">
                            <label for="tempo_limite_online">Tempo de Antecedencia do Agendamento ONLINE</label>
                            <select disabled="true" value="<?=($_user->tempo_limite_online)?>" id="tempo_limite_online"
                                name="tempo_limite_online" class="form-control">
                                <option value="10" <?=($_user->tempo_limite_online == 10 ? ' selected':'')?>>10 Minutos
                                </option>
                                <option value="15" <?=($_user->tempo_limite_online == 15 ? ' selected':'')?>>15 Minutos
                                </option>
                                <option value="20" <?=($_user->tempo_limite_online == 20 ? ' selected':'')?>>20 Minutos
                                </option>
                                <option value="30" <?=($_user->tempo_limite_online == 30 ? ' selected':'')?>>30 Minutos
                                </option>
                                <option value="45" <?=($_user->tempo_limite_online == 45 ? ' selected':'')?>>45 Minutos
                                </option>
                                <option value="60" <?=($_user->tempo_limite_online == 60 ? ' selected':'')?>>1 hora
                                </option>
                                <option value="120" <?=($_user->tempo_limite_online == 120 ? ' selected':'')?>>2 horas
                                </option>
                            </select>
                        </section>

                        <section class="form-group">
                            <label for="tempo_limite_presencial">Tempo de Antecedencia do Agendamento PRESENCIAL</label>
                            <select disabled="true" value="<?=($_user->tempo_limite_presencial)?>" id="tempo_limite_presencial"
                                name="tempo_limite_presencial" class="form-control">
                                <option value="10" <?=($_user->tempo_limite_presencial == 10 ? ' selected':'')?>>10
                                    Minutos</option>
                                <option value="15" <?=($_user->tempo_limite_presencial == 15 ? ' selected':'')?>>15
                                    Minutos</option>
                                <option value="20" <?=($_user->tempo_limite_presencial == 20 ? ' selected':'')?>>20
                                    Minutos</option>
                                <option value="30" <?=($_user->tempo_limite_presencial == 30 ? ' selected':'')?>>30
                                    Minutos</option>
                                <option value="45" <?=($_user->tempo_limite_presencial == 45 ? ' selected':'')?>>45
                                    Minutos</option>
                                <option value="60" <?=($_user->tempo_limite_presencial == 60 ? ' selected':'')?>>1 hora
                                </option>
                                <option value="120" <?=($_user->tempo_limite_presencial == 120 ? ' selected':'')?>>2
                                    horas</option>
                            </select>
                        </section>
                    </section>
                    <?php
                                }
                                ?>
                    <section class="form-grid area21">
                        <section class="form-group">
                            <label for="tipo_conselho">Tipo de conselho* </label> <select data-search="true" disabled
                                data-required class="form-select form-control" id="tipo_conselho" name="tipo_conselho"
                                value="<?=($_user->tipo_conselho)?>">
                                <option disabled selected>Selecione uma Opção</option>
                                <option value="CRM" <?=($_user->tipo_conselho == 'CRM' ? ' selected':'')?>>CRM</option>
                                <option value="CRO" <?=($_user->tipo_conselho == 'CRO' ? ' selected':'')?>>CRO</option>
                                <option value="CREFITO" <?=($_user->tipo_conselho == 'CREFITO' ? ' selected':'')?>>
                                    CREFITO</option>
                                <option value="CRBM" <?=($_user->tipo_conselho == 'CRBM' ? ' selected':'')?>>CRBM
                                </option>
                                <option value="CRMV" <?=($_user->tipo_conselho == 'CRMV' ? ' selected':'')?>>CRMV
                                </option>
                                <option value="COFITTO" <?=($_user->tipo_conselho == 'COFITTO' ? ' selected':'')?>>
                                    COFITTO</option>
                                <option value="CRN" <?=($_user->tipo_conselho == 'CRN' ? ' selected':'')?>>CRN</option>
                                <option value="CRP" <?=($_user->tipo_conselho == 'CRP' ? ' selected':'')?>>CRP</option>
                                <option value="OUTRO" <?=($_user->tipo_conselho == 'OUTRO' ? ' selected':'')?>>OUTRO
                                </option>
                            </select>
                        </section>
                        <section class="form-group"> <label for="uf_conselho">UF conselho*</label> <select
                                data-search="true" disabled data-required class="form-select form-control"
                                id="uf_conselho" name="uf_conselho" value="<?=($_user->uf_conselho)?>">
                                <option disabled selected>Selecione uma Opção</option>
                                <option value="AC" <?=($_user->uf_conselho == 'AC' ? ' selected':'')?>>Acre</option>
                                <option value="AL" <?=($_user->uf_conselho == 'AL' ? ' selected':'')?>>Alagoas</option>
                                <option value="AP" <?=($_user->uf_conselho == 'AP' ? ' selected':'')?>>Amapá</option>
                                <option value="AM" <?=($_user->uf_conselho == 'AM' ? ' selected':'')?>>Amazonas</option>
                                <option value="BA" <?=($_user->uf_conselho == 'BA' ? ' selected':'')?>>Bahia</option>
                                <option value="CE" <?=($_user->uf_conselho == 'CE' ? ' selected':'')?>>Cear</option>
                                <option value="DF" <?=($_user->uf_conselho == 'DF' ? ' selected':'')?>>Distrito Federal
                                </option>
                                <option value="ES" <?=($_user->uf_conselho == 'ES' ? ' selected':'')?>>Espírito Santo
                                </option>
                                <option value="GO" <?=($_user->uf_conselho == 'GO' ? ' selected':'')?>>Goiás</option>
                                <option value="MA" <?=($_user->uf_conselho == 'MA' ? ' selected':'')?>>Maranhão</option>
                                <option value="MT" <?=($_user->uf_conselho == 'MT' ? ' selected':'')?>>Mato Grosso
                                </option>
                                <option value="MS" <?=($_user->uf_conselho == 'MS' ? ' selected':'')?>>Mato Grosso do
                                    Sul</option>
                                <option value="MG" <?=($_user->uf_conselho == 'MG' ? ' selected':'')?>>Minas Gerais
                                </option>
                                <option value="PA" <?=($_user->uf_conselho == 'PA' ? ' selected':'')?>>Pará</option>
                                <option value="PB" <?=($_user->uf_conselho == 'PB' ? ' selected':'')?>>Paraíba</option>
                                <option value="PR" <?=($_user->uf_conselho == 'PR' ? ' selected':'')?>>Paraná</option>
                                <option value="PE" <?=($_user->uf_conselho == 'PE' ? ' selected':'')?>>Pernambuco
                                </option>
                                <option value="PI" <?=($_user->uf_conselho == 'PI' ? ' selected':'')?>>Piauí</option>
                                <option value="RJ" <?=($_user->uf_conselho == 'RJ' ? ' selected':'')?>>Rio de Janeiro
                                </option>
                                <option value="RN" <?=($_user->uf_conselho == 'RN' ? ' selected':'')?>>Rio Grande do
                                    Norte</option>
                                <option value="RS" <?=($_user->uf_conselho == 'RS' ? ' selected':'')?>>Rio Grande do Sul
                                </option>
                                <option value="RO" <?=($_user->uf_conselho == 'RO' ? ' selected':'')?>>Rondônia</option>
                                <option value="RR" <?=($_user->uf_conselho == 'RR' ? ' selected':'')?>>Roraima</option>
                                <option value="SC" <?=($_user->uf_conselho == 'SC' ? ' selected':'')?>>Santa Catarina
                                </option>
                                <option value="SP" <?=($_user->uf_conselho == 'SP' ? ' selected':'')?>>São Paulo
                                </option>
                                <option value="SE" <?=($_user->uf_conselho == 'SE' ? ' selected':'')?>>Sergipe</option>
                                <option value="TO" <?=($_user->uf_conselho == 'TO' ? ' selected':'')?>>Tocantins
                                </option>
                                <option value="EX" <?=($_user->uf_conselho == 'EX' ? ' selected':'')?>>Estrangeiro
                                </option>
                            </select> </section>
                        <section class="form-group"> <label for="num_conselho">Número conselho*</label> <input
                                autocomplete="off" disabled data-required id="num_conselho"
                                class="form-control uppercase" type="text" placeholder="Número do conselho."
                                value="<?=$_user->num_conselho?>" name="num_conselho" autofocus autocomplete="off" />
                        </section>
                        <section class="form-group"> <label for="especialidade">Especialidade Médica</label> <select
                                data-search="true" disabled id="especialidades" name="especialidades"
                                class="form-control">
                                <option disabled>Selecione uma Opção</option> <?php
                                                    $sql = "SELECT * FROM ESPECIALIDADES";

                                                    $stmtx = $pdo->prepare($sql);
                                                    $stmtx->execute();
                                                
                            
                                                    foreach($stmtx->fetchAll() as $item) {
                                                        if($item['id'] == $_user->especialidade){
                                                            echo '<option selected="selected" value="'.$item['id'].'">'.$item['nome'].'</option>';
                                                        }else {
                                                            echo '<option value="'.$item['id'].'">'.$item['nome'].'</option>';
                                                        }
                                                    }
                                                    ?>
                            </select> </section>
                    </section>

                    <section class="form-grid area21">
                        <section class="form-group">
                            <label for="inicio_ag">Intervalo de Início da Agenda</label>
                            <input autocomplete="off" required disabled data-required id="inicio_ag"
                                class="form-control uppercase" type="time"
                                value="<?=date('H:i', strtotime($_user->inicio_ag ?? '07:00'))?>" name="inicio_ag" />
                        </section>

                        <section class="form-group">
                            <label for="fim_ag">Intervalo de Fim da Agenda</label>
                            <input autocomplete="off" required disabled data-required id="fim_ag"
                                class="form-control uppercase" type="time"
                                value="<?=date('H:i', strtotime($_user->fim_ag ?? '23:00'))?>" name="fim_ag" />
                        </section>

                        <section class="form-group">
                            <label for="faixa_etaria">Faixa Etária de Atendimento</label>
                            <div class="container-track">
                                <input name="age_min" type="text" min="0" max="100"
                                    value="<?=($_user->age_min)?> Ano(s)" id="age-min" data-right="Ano(s)">
                                <input name="age_max" type="text" min="0" max="100"
                                    value="<?=($_user->age_max)?> Ano(s)" id="age-max" data-right="Ano(s)">
                            </div>
                        </section>

                        <section class="form-group">
                            <label for="google_agenda_link">Link da Agenda Google <sup><i class="fa fa-info fa-sm info-label" title="Na Sua Agenda do Google clique em Configuração e depois clique na sua Agenda na parte lateral esquerda e depois role a página até encontrar o campo 'Endereço secreto do formato iCal' no lado do campo clique em copiar e cole o link aqui."></i></sup></label>
                            <input placeholder="https://calendar.google.com/calendar/ical/*****/basic.ics"
                                autocomplete="off" disabled data-required id="google_agenda_link" class="form-control"
                                type="url" value="<?=$_user->google_agenda_link?>" name="google_agenda_link" />
                        </section>
                    </section>

                    <section class="form-grid area21">
                        <section class="form-group">
                            <label for="valor_consulta">Valor da Consulta ( Presencial )</label>
                            <input autocomplete="off" data-money="true" disabled data-required id="valor_consulta"
                                class="form-control uppercase" type="text" placeholder="Valor da Consulta Presencial."
                                value="<?=$_user->valor_consulta?>" name="valor_consulta" autofocus
                                autocomplete="off" />
                        </section>
                        <section class="form-group">
                            <label for="valor_consulta_online">Valor da Consulta ( Online )</label>
                            <input autocomplete="off" data-money="true" disabled data-required
                                id="valor_consulta_online" class="form-control uppercase" type="text"
                                placeholder="Valor da Consulta Online."
                                value="<?=($_user->valor_consulta_online * 100)?>" name="valor_consulta_online"
                                autofocus autocomplete="off" />
                        </section>

                        <section class="form-group">
                            <label for="duracao_atendimento">Duração do Atendimento</label>
                            <select data-search="true" disabled data-required id="duracao_atendimento"
                                class="form-control" value="<?=$_user->duracao_atendimento?>"
                                name="duracao_atendimento">
                                <option disabled selected>Selecione uma Duração</option>
                                <option value="20" <?=($_user->duracao_atendimento == 20 ? ' selected':'')?>>20 Minutos
                                </option>
                                <option value="30" <?=($_user->duracao_atendimento == 30 ? ' selected':'')?>>30 Minutos
                                </option>
                                <option value="40" <?=($_user->duracao_atendimento == 40 ? ' selected':'')?>>40 Minutos
                                </option>
                                <option value="50" <?=($_user->duracao_atendimento == 50 ? ' selected':'')?>>50 Minutos
                                </option>
                                <option value="60" <?=($_user->duracao_atendimento == 60 ? ' selected':'')?>>1 Hora
                                </option>
                            </select>
                        </section>

                        <section class="form-group">
                            <label for="prec_sr">Disponibilizar Agenda</label>
                            <select disabled data-required id="disponibilizar_agenda" class="form-control"
                                value="<?=$_user->disponibilizar_agenda?>" name="disponibilizar_agenda">
                                <option value="0" readonly disabled selected>Selecione uma Opção</option>
                                <option value="1" <?=($_user->disponibilizar_agenda == 1 ? ' selected':'')?>>Sim
                                </option>
                                <option value="0" <?=($_user->disponibilizar_agenda == 0 ? ' selected':'')?>>Não
                                </option>
                            </select>
                        </section>
                    </section>

                    <section class="form-grid area-full">
                        <section class="form-group"> <label for="anamnese">Queixa Principal</label>
                            <select data-search="true" multiple
                                data-selected="<?=implode(',', json_decode($_user->anamnese))?>" disabled id="anamnese"
                                name="anamnese[]" class="form-control select-tags" data-required>
                                <option disabled>Selecione uma Opção</option> <?php
                                                            $sql = "SELECT * FROM ANAMNESE";
                                                            $stmtx = $pdo->prepare($sql);
                                                            $stmtx->execute();
                                                            $anamneses = json_decode($_user->anamnese, true);
                                                            
                                                            foreach($stmtx->fetchAll(PDO::FETCH_ASSOC) as $queixa) {
                                                                echo '<option '.($queixa['nome'] == '').' value="'.trim($queixa['id']).'">'.$queixa['nome'].'</option>';
                                                            }
                                                        ?>
                            </select>
                        </section>
                    </section>
                    <section class="form-grid area-full">
                        <section class="form-group"> <label for="nacionalidade">Descrição</label> <textarea disabled
                                name="descricao" style="width: 100%;height: 128px"
                                maxlength="512"><?=$_user->descricao?></textarea> <small class="txt-counter"
                                data-area="descricao">512 caracteres restantes</small> </section>
                        <section class="form-group"> <label for="nacionalidade">Descrição Completa</label> <textarea
                                disabled name="descricao_html" style="width: 100%;height: 128px"
                                data-file="medicos/<?=$_user->token ?>"></textarea>


                            <textarea disabled id="descricao_completa" name="descricao_completa"
                                style="display: none;"><?=($_user->descricao_html)?></textarea>
                        </section>
                    </section> <?php
                            }
                            ?>
                </div>
                <div class="tab tab-disabled" data-index="2" data-tab="tabControl1">
                    <div class="street-editor">
                        <section class="form-grid area1">
                            <section class="form-group"> <label for="endereco_nome">Nome</label> <input
                                    autocomplete="off" disabled="true" value="" type="text" id="endereco_nome"
                                    name="nome" class="form-control" maxlength="100"> </section>
                            <section class="form-group"> <label for="endereco_nome">Tipo de Endereço</label> <select
                                    disabled="true" id="tipo_endereco" name="tipo_endereco" class="form-control">
                                    <option value="CASA">CASA</option>
                                    <option value="ATENDIMENTO">ATENDIMENTO</option>
                                    <option value="RESPONSAVEL">RESPONSÁVEL LEGAL</option>
                                </select> </section>
                        </section>
                        <section class="form-grid area13">
                            <section class="form-group"> <label for="cep">CEP</label> <input autocomplete="off"
                                    disabled="true" value="" type="text" id="cep" name="cep" class="form-control"
                                    placeholder="__.____-___" maxlength="10"> </section>
                            <section class="form-group"> <label for="endereco">Endereço</label> <input
                                    autocomplete="off" disabled="true" value="" type="text" id="endereco"
                                    name="logradouro" class="form-control" placeholder="Digite seu Endereço"> </section>
                            <section class="form-group"> <label for="numero">Número</label> <input autocomplete="off"
                                    disabled="true" value="" type="text" id="numero" name="numero" class="form-control"
                                    placeholder="N"> </section>
                            <section class="form-group"> <label for="complemento">Complemento</label> <input
                                    autocomplete="off" type="text" id="complemento" name="complemento"
                                    class="form-control" placeholder="Apto 13" value="" disabled="true"> </section>
                        </section>
                        <section class="form-grid area5">
                            <section class="form-group"> <label for="cidade">Cidade</label> <input autocomplete="off"
                                    disabled="true" value="<?=($_user->cidade)?>" type="text" id="cidade" name="cidade"
                                    class="form-control" placeholder="Digite sua Cidade"> </section>
                            <section class="form-group"> <label for="bairro">Bairro</label> <input autocomplete="off"
                                    disabled="true" value="<?=($_user->bairro)?>" type="text" id="bairro" name="bairro"
                                    class="form-control" placeholder="Digite seu Bairro"> </section>
                            <section class="form-group"> <label for="uf">UF</label> <select data-search="true"
                                    class="form-select form-control" id="uf" name="uf" disabled>
                                    <option value="AC" <?=($_user->uf == 'AC' ? ' selected':'')?>>Acre</option>
                                    <option value="AL" <?=($_user->uf == 'AL' ? ' selected':'')?>>Alagoas</option>
                                    <option value="AP" <?=($_user->uf == 'AP' ? ' selected':'')?>>Amap</option>
                                    <option value="AM" <?=($_user->uf == 'AM' ? ' selected':'')?>>Amazonas</option>
                                    <option value="BA" <?=($_user->uf == 'BA' ? ' selected':'')?>>Bahia</option>
                                    <option value="CE" <?=($_user->uf == 'CE' ? ' selected':'')?>>Ceará</option>
                                    <option value="DF" <?=($_user->uf == 'DF' ? ' selected':'')?>>Distrito Federal
                                    </option>
                                    <option value="ES" <?=($_user->uf == 'ES' ? ' selected':'')?>>Espírito Santo
                                    </option>
                                    <option value="GO" <?=($_user->uf == 'GO' ? ' selected':'')?>>Gois</option>
                                    <option value="MA" <?=($_user->uf == 'MA' ? ' selected':'')?>>Maranho</option>
                                    <option value="MT" <?=($_user->uf == 'MT' ? ' selected':'')?>>Mato Grosso</option>
                                    <option value="MS" <?=($_user->uf == 'MS' ? ' selected':'')?>>Mato Grosso do Sul
                                    </option>
                                    <option value="MG" <?=($_user->uf == 'MG' ? ' selected':'')?>>Minas Gerais</option>
                                    <option value="PA" <?=($_user->uf == 'PA' ? ' selected':'')?>>Pará</option>
                                    <option value="PB" <?=($_user->uf == 'PB' ? ' selected':'')?>>Paraba</option>
                                    <option value="PR" <?=($_user->uf == 'PR' ? ' selected':'')?>>Paraná</option>
                                    <option value="PE" <?=($_user->uf == 'PE' ? ' selected':'')?>>Pernambuco</option>
                                    <option value="PI" <?=($_user->uf == 'PI' ? ' selected':'')?>>Piau</option>
                                    <option value="RJ" <?=($_user->uf == 'RJ' ? ' selected':'')?>>Rio de Janeiro
                                    </option>
                                    <option value="RN" <?=($_user->uf == 'RN' ? ' selected':'')?>>Rio Grande do Norte
                                    </option>
                                    <option value="RS" <?=($_user->uf == 'RS' ? ' selected':'')?>>Rio Grande do Sul
                                    </option>
                                    <option value="RO" <?=($_user->uf == 'RO' ? ' selected':'')?>>Rondônia</option>
                                    <option value="RR" <?=($_user->uf == 'RR' ? ' selected':'')?>>Roraima</option>
                                    <option value="SC" <?=($_user->uf == 'SC' ? ' selected':'')?>>Santa Catarina
                                    </option>
                                    <option value="SP" <?=($_user->uf == 'SP' ? ' selected':'')?>>São Paulo</option>
                                    <option value="SE" <?=($_user->uf == 'SE' ? ' selected':'')?>>Sergipe</option>
                                    <option value="TO" <?=($_user->uf == 'TO' ? ' selected':'')?>>Tocantins</option>
                                </select> </section>
                        </section> <input autocomplete="off" type="hidden" name="isDefault" value="false"> <button
                            class="btn-button1" type="button" onclick="addAddress()">SALVAR ENDEREÇO</button>
                    </div>
                    <section class="street-container"> <?php
                            $street = null;
                            
                            $stmtx1 = $pdo->prepare('SELECT * FROM `ENDERECOS` WHERE user_token = :token');
                            $stmtx1->bindValue(':token', $_user->token);
                            $stmtx1->execute();

                            $street_token = null;
          
                            foreach ($stmtx1->fetchAll(PDO::FETCH_OBJ) as $item) {
                                
                                if($item->isDefault && $item->tipo_endereco == 'ATENDIMENTO') {
                                    $street = $item;
                                    $street_token = $item->token;
                                }
                                echo '<div class="street-item'.($item->isDefault ? ' selected':'').'" id="'.$item->token.'">
                                            <div class="street-info">
                                                <span class="street-label">
                                                    <strong>'.strtoupper($item->nome).'</strong> ('.$item->tipo_endereco.')
                                                </span>
                                                <span>'.$item->logradouro.', '.$item->numero.'</span>
                                                <span>'.$item->cidade.'-'.$item->bairro.'</span>
                                                <span>'.$item->cep.'</span>
                                            </div>
                                            <div class="street-btns">
                                                <div class="btns-info">
                                                <label class="default-street">'.($item->isDefault ? '(Padrão)':'').'</label>
                                                </div>

                                                <div class="btns-street">
                                                    <label data-action="editar" data-token="'.$item->token.'">Editar</label>
                                                    <label data-action="excluir" data-token="'.$item->token.'">Excluir</label>
                                                    <label data-action="def" data-token="'.$item->token.'" onclick="defEndPadrao(this)">Deixar Padrão</label>
                                                </div>
                                            </div>
                                            
                                            <input autocomplete="off" type="hidden" name="enderecos[]" data-token="'.$item->token.'" class="input-data" value="'.str_replace('"', "'", json_encode($item)).'">
                                        </div>';
                        
                            }
                            
                            $stmtx2 = $pdo->prepare("SELECT * FROM UNIDADES WHERE medicos LIKE '%\"{$_user->id}\"%'");
                            $stmtx2->execute();
                            foreach ($stmtx2->fetchAll(PDO::FETCH_OBJ) as $item) {
                                echo '<div class="street-item" id="'.$item->token.'">
                                            <div class="street-info">
                                                <span class="street-label">
                                                    <strong>'.strtoupper($item->nome).'</strong> (UNIDADE)
                                                </span>
                                                <span>'.$item->logradouro.', '.$item->numero.'</span>
                                                <span>'.$item->cidade.'-'.$item->bairro.'</span>
                                                <span>'.$item->cep.'</span>
                                            </div>
                                            <div class="street-btns">
                                                <div class="btns-info">
                                                <label class="default-street"></label>
                                                </div>

                                                <div class="btns-street">
                                                    
                                                </div>
                                            </div>
                                            
                                            <input autocomplete="off" type="hidden" name="enderecos[]" data-token="'.$item->token.'" class="input-data" value="'.str_replace('"', "'", json_encode($item)).'">
                                        </div>';
                        
                            }
                            
                            ?>

                        <p><button class="btn-button1" type="button" id="add-address" onclick="addStreet()">NOVO
                                ENDEREÇO</button></p>
                    </section>
                </div>
                <div class="tab tab-disabled" data-index="3" data-tab="tabControl1">
                    <section class="form-grid area6">
                        <section class="form-group"> <label for="cpf">CPF</label> <input autocomplete="off"
                                data-required disabled="true" value="<?=($_user->cpf)?>" type="text" id="cpf" name="cpf"
                                class="form-control" placeholder="___.___.___-__" maxlength="14"> </section>
                        <section class="form-group"> <label for="rg">RG</label> <input autocomplete="off" data-required
                                disabled="true" value="<?=($_user->rg)?>" type="text" id="rg" name="rg"
                                class="form-control" placeholder="__.__.__-__" maxlength="11"> </section>
                    </section> <?php
                        if($_user->objeto == 'PACIENTE') {
                            $med = '';
                            $crm = '';

                            foreach($medicos->getAll() as $medico) {

                                if($_user->medico_token == $medico->medico_token) {
                                    $med = $medico->nome_completo;
                                    $crm = $medico->num_conselho;
                                }
                            }
                            ?> <section class="form-grid area1">
                        <section class="form-group"> <label for="medico_token">Médico</label> <select data-search="true"
                                name="medico_token" id="medico_token" data-required="required">
                                <option value="" disabled selected>Selecione uma Opção</option> <?php
                                    $crm = '';

                                    foreach(SQL::fetchTable(connector: $pdo, tableName: 'MEDICOS') as $medico) {
                                        echo '<option data-crm="'.$medico->num_conselho.'" id="'.$medico->token.'" value="'.$medico->token.'"'.($_user->medico_token == $medico->token ? ' selected':'').'>'.$medico->nome_completo.'</option>';

                                        if($medico->token == $_user->medico_token || $medico->token == $user->medico_token) {
                                            $crm = $medico->num_conselho;
                                        }
                                    }
                                    ?>
                            </select> </section>
                        <section class="form-group"> <label for="crm">CRM</label> <input autocomplete="off" readonly
                                type="text" id="crm" name="crm" class="form-control" placeholder="" value="<?=$crm?>" />
                        </section>
                        <section class="form-group"> <label for="anamnese">Queixa Principal</label> <select disabled
                                id="anamnese" name="anamnese" class="form-control" height="256px">
                                <option disabled>Selecione uma Opção</option> <?php
                                        $sql = "SELECT * FROM ANAMNESE";

                                        $stmtx = $pdo->prepare($sql);
                                        $stmtx->execute();
                                    
                
                                        foreach($stmtx->fetchAll() as $anamnese) {
                                            if($anamnese['id'] == $_user->anamnese){
                                                echo '<option selected="selected" value="'.$anamnese['id'].'">'.$anamnese['nome'].'</option>';
                                            }else {
                                                echo '<option value="'.$anamnese['id'].'">'.$anamnese['nome'].'</option>';
                                            }
                                        }
                                        ?>
                            </select> </section>
                    </section>
                    <div class="container-flex1">
                        <div>
                            <h2 class="titulo-h1">Termos para Importação</h2>
                            <!-- <p>Faça o download dos documentos relativos ao termo de consentimento livre. Esclarecido da procuração de autorização para importação de produtos derivados de cannabis, conforme indicações de nossas equipes de atendimento ao cliente. Em caso de dúvidas, entre em contato pelo telefone (41) 3300-0790</p> -->

                            <p>Abaixo encontra-se opções para download dos termos de consentimento livre. Em caso de
                                dúvidas, entre em contato pelo telefone (41) 3300-0790</p>
                        </div>
                        <div> <a href="/docs/procuracao.pdf" target="_blank"><img src="/assets/images/btn-001.svg"
                                    title="Termo de Autorização de Importação"></a> </div>
                        <div> <a href="/docs/termo_consentimento.pdf" target="_blank"><img
                                    src="/assets/images/btn-002.svg" title="Termo"></a> </div>
                    </div>
                    <div class="container-flex1">
                        <div>
                            <h2 class="titulo-h1">Upload de Documentos</h2>
                            <p>Faça o upload dos seu documentos aqui!</p>
                        </div>
                    </div>
                    <div class="container-flex1">
                        <div class="d-flex"> <input autocomplete="off" type="checkbox" id="doc_validation"
                                name="doc_validation" checked="<?=$_user->doc_cnh == 'on' ? 'checked':''?>">
                            <strong>Minha CNH contém CPF e RG inclusos</strong>
                        </div>
                    </div>
                    <section class="form-grid area-doc">
                        <section class="form-group"> <label for="doc_rg_frente" class="anexo-item download-anexo-item"
                                data-file="<?=$_user->doc_rg_frente ?>"> <img data-title="Obrigatrio"
                                    src="<?=(Modules::getDoc($_user->doc_rg_frente))?>"> <input autocomplete="off"
                                    name="doc_rg_frente" type="hidden" id="file_doc_rg_frente"
                                    value="<?=($_user->doc_rg_frente)?>"> <input autocomplete="off" type="file"
                                    accept="image/jpeg,image/jpg, image/png, application/pdf" style="display: none"
                                    class="input-upload-doc" disabled id="doc_rg_frente">
                                <strong><?=$_user->doc_cnh == 'on' ? 'Anexo CNH (Frente)':'Anexo RG (Frente)'?></strong>
                            </label> </section>
                        <section class="form-group" <?=$_user->doc_cnh == 'on' ? ' style="display:none"':''?>> <label
                                for="doc_rg_verso" class="anexo-item download-anexo-item"
                                data-file="<?=$_user->doc_rg_verso ?>"> <img data-title="Obrigatrio"
                                    src="<?=(Modules::getDoc($_user->doc_rg_verso))?>"> <input autocomplete="off"
                                    name="doc_rg_verso" type="hidden" id="file_doc_rg_verso"
                                    value="<?=($_user->doc_rg_verso)?>"> <input autocomplete="off" type="file"
                                    accept="image/jpeg,image/jpg, image/png, application/pdf" style="display: none"
                                    class="input-upload-doc" disabled id="doc_rg_verso"> <strong>Anexar RG
                                    (Verso)</strong> </label> </section>
                        <section class="form-group" <?=$_user->doc_cnh == 'on' ? ' style="display:none"':''?>> <label
                                for="doc_cpf_frente" class="anexo-item download-anexo-item"
                                data-file="<?=$_user->doc_cpf_frente ?>"> <img data-title="Obrigatrio"
                                    src="<?=(Modules::getDoc($_user->doc_cpf_frente))?>"> <input autocomplete="off"
                                    name="doc_cpf_frente" type="hidden" id="file_doc_cpf_frente"
                                    value="<?=($_user->doc_cpf_frente)?>"> <input autocomplete="off" type="file"
                                    accept="image/jpeg,image/jpg, image/png, application/pdf" style="display: none"
                                    class="input-upload-doc" disabled id="doc_cpf_frente"> <strong>Anexar CPF
                                    (Frente)</strong> </label> </section>
                        <section class="form-group" <?=$_user->doc_cnh == 'on' ? ' style="display:none"':''?>> <label
                                for="doc_cpf_verso" class="anexo-item download-anexo-item"
                                data-file="<?=$_user->doc_cpf_verso ?>"> <img data-title="Obrigatório"
                                    src="<?=(Modules::getDoc($_user->doc_cpf_verso))?>"> <input autocomplete="off"
                                    type="file" accept="image/jpeg,image/jpg, image/png, application/pdf"
                                    style="display: none" class="input-upload-doc" disabled id="doc_cpf_verso"> <input
                                    autocomplete="off" name="doc_cpf_verso" type="hidden" id="file_doc_cpf_verso"
                                    value="<?=($_user->doc_cpf_frente)?>"> <strong>Anexar CPF (Verso)</strong> </label>
                        </section>
                        <section class="form-group"> <label for="doc_comp_residencia"
                                class="anexo-item download-anexo-item" data-file="<?=$_user->comp_residencia ?>"> <img
                                    data-title="Obrigatório" src="<?=(Modules::getDoc($_user->doc_comp_residencia))?>">
                                <input autocomplete="off" name="doc_comp_residencia" type="hidden"
                                    id="file_doc_comp_residencia" value="<?=($_user->doc_comp_residencia)?>"> <input
                                    autocomplete="off" type="file"
                                    accept="image/jpeg,image/jpg, image/png, application/pdf" style="display: none"
                                    class="input-upload-doc" disabled id="doc_comp_residencia"> <strong>Comprovante de
                                    Residência</strong> </label> </section>
                        <section class="form-group"> <label for="doc_procuracao" class="anexo-item download-anexo-item"
                                data-file="<?=$_user->doc_procuracao ?>"> <img data-title="Obrigatório"
                                    src="<?=(Modules::getDoc($_user->doc_procuracao))?>"> <input autocomplete="off"
                                    name="doc_procuracao" type="hidden" id="file_doc_procuracao"
                                    value="<?=($_user->doc_procuracao)?>"> <input autocomplete="off" type="file"
                                    accept="image/jpeg,image/jpg, image/png, application/pdf"
                                    value="<?=($_user->doc_procuracaoracao)?>" style="display: none"
                                    class="input-upload-doc" disabled id="doc_procuracao"> <strong>Procuração</strong>
                            </label> </section>
                        <section class="form-group"> <label for="doc_anvisa" class="anexo-item download-anexo-item"
                                data-file="<?=$_user->doc_anvisa ?>"> <img data-title="Obrigatório"
                                    src="<?=(Modules::getDoc($_user->doc_anvisa))?>"> <input autocomplete="off"
                                    name="doc_anvisa" type="hidden" id="file_doc_anvisa"
                                    value="<?=$_user->doc_anvisa?>"> <input autocomplete="off" type="file"
                                    accept="image/jpeg,image/jpg, image/png, application/pdf" class="input-upload-doc"
                                    style="display: none" disabled id="doc_anvisa"> <strong>Documento Anvisa</strong>
                            </label> </section>
                        <section class="form-group"> <label for="doc_termos" class="anexo-item download-anexo-item"
                                data-file="<?=($_user->doc_termos)?>"> <img
                                    src="<?=(Modules::getDoc($_user->doc_termos))?>"> <input autocomplete="off"
                                    name="doc_termos" type="hidden" id="file_doc_termos"
                                    value="<?=($_user->doc_termos)?>"> <input autocomplete="off" type="file"
                                    accept="image/jpeg,image/jpg, image/png, application/pdf" class="input-upload-doc"
                                    style="display: none" disabled id="doc_termos"> <strong>Termo de
                                    Consentimento</strong> </label> </section>
                    </section> <?php
                        }
                        ?>
                </div>


                <?php
                if($_user->objeto == 'MEDICO')
                {
                    echo '<div class="tab" data-index="4" data-tab="tabControl1">';
                    echo "<h2 class=\"titulo-h2\">Calendário de Agendamentos</h2>";
               
                    if($_user->tipo == 'MEDICO' || $user->tipo == 'FUNCIONARIO') {
                        echo '<input autocomplete="off" name="medico_id" type="hidden" id="medico_id" value="'.$_user->id.'">';
                        echo '<input autocomplete="off" name="medico_token" type="hidden" id="medico_token" value="'.$_user->token.'">';
                    }
                    
                    echo "<div class=\"calendar-box-container\">";
                    echo "<button class=\"btn-button1 ag-config\" type=\"button\" data-date=\"".date('Y-m-d')."\" data-timestart=\"".date('H:i', strtotime($_user->inicio_ag))."\" data-timeend=\"".date('H:i', strtotime($_user->fim_ag))."\" data-duration=\"".$_user->duracao_atendimento."\" data-week=\"".date('w')."\"><i class=\"fas fa-gear fa-lg\"></i> Configuar Agendamento</button>";
                    echo "<button class=\"btn-button1\" type=\"button\" onclick=\"clearAgenda()\"><i class=\"fas fa-broom fa-lg\"></i> Limpar Agenda</button>";
                    echo "<button class=\"btn-button1\" type=\"button\" onclick=\"syncAgenda()\"><i class=\"fas fa-calendar fa-lg\"></i> Sincronizar Agenda</button>";
                    echo "</div>";
                    
                    $token = $_user->token;
                    
                    $dados = $pdo->query("SELECT * FROM `AGENDA_MEDICA` WHERE medico_token = '$token'")->fetch(PDO::FETCH_OBJ);

                    //$ag_med = $pdo->query("SELECT data_agendamento as dt FROM `AGENDA_MED` WHERE medico_token = '$token'")->fetchAll(PDO::FETCH_ASSOC);

                    
                    $weekCalendar = new WeeklyCalendar($calendario);

                    if($user->tipo == 'MEDICO' || $user->tipo == 'FUNCIONARIO') {
                    ?> <section class="main">

                    <div class="flex-container">
                        <section class="calendar-container">
                            <div class="calendar-body">
                                <div class="calendar-schedules"
                                    data-street="<?=str_replace('"', "'", json_encode($street))?>"> <button
                                        type="button" class="prev-week">❮</button>
                                    <div class="wschedule-calendar"> <?php
                    
                                            $first = true;
                    
                                            $last = false;
                    
                                            $i = 0;
                    
                                            foreach($weekCalendar->array_week_month(15) as $weekDay)
                    
                                            {
                    
                                                echo '<div data-token="'.$_user->token.'" data-id="'.$_user->id.'" class="calendar-slide'.($first ? ' active':'').'" data-index="'.$i.'">';
                    
                                                foreach($weekDay as $item) {
                    
                                                    $_id = uniqid();
                    
                    
                    
                                                    echo '<div class="week-item">
                    
                                                                       <div class="week-head" data-date="'.$item['day'].'" data-week="'.date('w', strtotime($item['day'])).'" data-duration="'.$_user->duracao_atendimento.'" data-timestart="'.date('H:i', strtotime($_user->inicio_ag)).'" data-timeend="'.date('H:i', strtotime($_user->fim_ag)).'">
                    
                                                                         <img src="/assets/images/ico-calendar.svg" height="30px">
                    
                                                                         <div class="week-name" data-for="'.$_id.'">
                    
                                                                           <span>'.$item['name'].'</span>
                    
                                                                           <span>'.date('d', strtotime($item['day'])).' '.$item['month'].'</span>
                    
                                                                         </div>
                    
                                                                       </div>';

                                                    
                                                    if($_user->inicio_ag != "" || $_user->fim_ag != "") {
                                                        $horarios = $weekCalendar->calculateHoursInterval(date('H:i:s', strtotime($_user->inicio_ag)), date('H:i:s', strtotime($_user->fim_ag)), $_user->duracao_atendimento * 60);
                                                    } else {
                                                        $horarios = $weekCalendar->calculateHoursInterval('07:00:00', '23:00:00', $_user->duracao_atendimento * 60);
                                                    }
                                                    
                    
                    
                                                   
                                                    
                                                    
                                                    $calendar = json_decode($dados->calendario, true);
                                                    
                                                    foreach($horarios as $h) {
                    
                                                        $checked = is_array($calendar[date('Y-m-d', strtotime($item['day']))][$h]);
                                                        
                                                        if($checked) {
                                                            $_item = $calendar[date('Y-m-d', strtotime($item['day']))][$h];
                                                        }
                                                        
                                                        $specificDate = strtotime($item['day'].' '.$h);
                                                        $currentDate = time();

                                                        $uniqid = uniqid();
                                       
                                                        if ($currentDate < $specificDate) {
                        
                                                            echo '<label id="'.$uniqid.'" data-obj="'.date('d/m/Y', strtotime($item['day'])).' '.$h.'" data-selection="'.$_id.'" data-atendimento="'.($checked ? $_item['endereco'] : $street_token).'" data-online="'.($_item['online'] == 1 ? 'true' : 'false').'" data-presencial="'.($_item['presencial'] == 1 ? 'true' : 'false').'" data-date="'.$item['day'].'" data-time="'.$h.'" class="week-time week-schedule listmedic-box-dir-time'.($checked ? ' active':'').'" checked="false">
                                                                    '.$h.'H
                                                                    <i class="fa fa-home'.(!parse_bool($_item['presencial']) && $checked ? ' icon-disabled':'').'" name="presencial"></i>
                                                                    <i class="fa fa-globe'.(!parse_bool($_item['online']) && $checked ? ' icon-disabled':'').'" name="online"></i>
                                                                    <i class="fa fa-gear" name="conf" data-id="'.$uniqid.'"></i>
                                                                </label>';
                                                            
                                                        } else {
                                                            echo '<label id="'.$uniqid.'" data-obj="'.date('d/m/Y', strtotime($item['day'])).' '.$item['day'].' '.$h.'" data-selection="'.$_id.'"  data-atendimento="'.($checked ? $_item['endereco'] : $street_token).'" data-online="true" data-presencial="true" data-date="'.$item['day'].'" data-time="'.$h.'" class="week-time week-schedule week-schedule-btn-disabled listmedic-box-dir-time'.($checked ? ' active':'').'"  checked="false">
                                                                    '.$h.'H
                                                                    <i class="fa fa-home" name="presencial"></i>
                                                                    <i class="fa fa-globe" name="online"></i>
                                                                    <i class="fa fa-gear" name="conf" data-id="'.$uniqid.'"></i>
                                                                </label>';
                                                        }
                                                    }
                    
                    
                    
                                                    echo '</div>';
                    
                    
                    
                                                    $first = false;
                    
                                                }
                    
                                                echo '</div>';
                    
                    
                    
                                                $i++;
                    
                                            }
                    
                                            ?> </div> <button type="button" class="next-week">❯</button> <?php
                                        $stmtx2 = $pdo->prepare('SELECT * FROM `ENDERECOS` WHERE user_token = :token');
                                        $stmtx2->bindValue(':token', $_user->token);
                                        $stmtx2->execute();
                                            foreach($stmtx2->fetchAll(PDO::FETCH_OBJ) as $item) {
                                                if($item->tipo_endereco == 'ATENDIMENTO') {
                                                   echo '<input type="hidden" class="street-object" value="'.str_replace('"', "'", json_encode($item)).'">';
                                                }
                                            }
                                            
                                             $stmtx2 = $pdo->prepare("SELECT * FROM UNIDADES WHERE medicos LIKE '%\"{$_user->id}\"%'");
                                             $stmtx2->execute();
                                            foreach ($stmtx2->fetchAll(PDO::FETCH_OBJ) as $item) {
                                                $endereco = [
                                                        'nome' => $item->nome, 
                                                        'cep' => $item->cep, 
                                                        'logradouro' => $item->logradouro, 
                                                        'numero' => $item->numero, 
                                                        'complemento' => $item->complemento, 
                                                        'cidade' => $item->cidade, 
                                                        'bairro' => $item->bairro, 
                                                        'uf' => $item->uf
                                                    ];
                                                    
                                                echo '<input type="hidden" class="street-object" value="'.str_replace('"', "'", json_encode($endereco)).'">';
                                            }
                                        ?>
                                </div>
                            </div>
                        </section>
                    </div>
                </section> <?php
                    
                    } else {
                    
                        echo 'Acesso Negado.';
                    }

                    echo '</div>';
                    
                    ?>
                <div class="tab" data-index="5" data-tab="tabControl1">
                    <section class="form-grid area3">
                        <section class="form-group"> <label for="nome_completo">Nome Completo</label> <input
                                autocomplete="off" disabled="true" id="cert_common_name"
                                value="<?=($certificate->name ?? $certificate->name)?>" type="text" class="form-control"
                                placeholder="Digite seu nome completo"> </section>
                        <section class="form-group"> <label for="nacionalidade">Certificado</label> <input
                                autocomplete="off" name="cert_file" class="form-control" id="certificado" type="file"
                                accept=".pfx"> </section>
                    </section>
                    <section class="form-grid area1">
                        <section class="form-group"> <label for="nome_preferencia">Tipo de Padrão</label> <input
                                autocomplete="off" disabled="true" id="issuer_standard"
                                value="<?=($certificate->signer)?>" type="text" class="form-control"> </section>
                        <section class="form-group"> <label for="nome_preferencia">Unidade Certificadora</label> <input
                                autocomplete="off" disabled="true" id="issuer" value="<?=($certificate->organization)?>"
                                type="text" class="form-control"> </section>
                        <section class="form-group"> <label for="data_nascimento">Chave</label> <input
                                autocomplete="off" disabled="true" id="serial_number"
                                value="<?=($certificate->serial ?? '')?>" type="text" class="form-control"
                                maxlength="10"> </section> <input autocomplete="off" type="hidden" name="pfx_path"
                            id="pfx_path"> <input autocomplete="off" type="hidden" name="pfx_passwd" id="passwd">
                    </section> <input autocomplete="off" type="hidden" name="certificado"
                        value="<?=(base64_encode($_user->certificado))?>" id="certificate">
                </div>
                <?php
                   
                }

                if($user->tipo == 'FUNCIONARIO' && $user->perms->aba_api == 1){
                    
                        $curl = curl_init();

                        curl_setopt_array($curl, array(
                        CURLOPT_URL => 'https://app.hallo-api.com/v1/instance/GLCG-000629-i12u-aofB-X77N9XQ3GXZB/token/5RLU7PHL-zPBP-4AVw-BgNb-2B67W4L9VX85/instance',
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => '',
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 0,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => 'POST',
                        CURLOPT_POSTFIELDS => array('fLogin' => '9V60VP5I-SYAxyJ-AMbim7a2-ZHY47UPR3LCY','ACTION' => 'STATE'),
                        ));

                        $whatsapp = json_decode(curl_exec($curl));

                        curl_close($curl);

                        if($whatsapp->result->state == 'disconnected') {
                            /*
                            $curl = curl_init();

                            curl_setopt_array($curl, array(
                            CURLOPT_URL => 'https://app.hallo-api.com/v1/instance/GLCG-000629-i12u-aofB-X77N9XQ3GXZB/token/5RLU7PHL-zPBP-4AVw-BgNb-2B67W4L9VX85/instance',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS => array('fLogin' => '9V60VP5I-SYAxyJ-AMbim7a2-ZHY47UPR3LCY','ACTION' => 'CONNECT'),
                            ));

                            $whatsapp = json_decode(curl_exec($curl));

                            curl_close($curl);

                            */
                        }


                        //$ddi = substr($whatsapp->result->number, 0, 2);
                        //$ddd = substr($whatsapp->result->number, 2, 2);
                        //$cell_p1 = substr($whatsapp->result->number, 4, 4);
                        //$cell_p2 = substr($whatsapp->result->number, 8, 4);

                        
                        
                        if($user->perms->aba_api == 1) {
                    ?>
                <div class="tab tab-disabled" data-index="6" data-tab="tabControl1">

                    <section id="tabControl2" class="tabControl">
                        <input type="hidden" id="api_save" name="api_save" value="true">
                        <div class="tab-toolbar">
                            <span data-tab="tabControl2" class="active" data-index="1">Dashboard</span>
                            <span data-tab="tabControl2" data-index="2">Servidor SMTP</span>
                            <span data-tab="tabControl2" data-index="3">WhatsApp</span>
                            <span data-tab="tabControl2" data-index="4">Contas de E-mail</span>
                        </div>
                        <div class="tab active" data-tab="tabControl2" data-index="1">
                            <?php
                            $pedidos = $pdo->query('SELECT
                                                    	COUNT(*) AS pedidos,
                                                    	( SELECT COUNT(*) FROM PACIENTES ) AS pacientes,
                                                    	( SELECT COUNT(*) FROM MEDICOS ) AS medicos,
                                                    	( SELECT COUNT(*) FROM FUNCIONARIOS ) AS funcionarios,
                                                    	( SELECT COUNT(*) FROM VENDAS WHERE `status`=\'AGUARDANDO PAGAMENTO\') AS faturas,
                                                    	( SELECT COUNT(*) FROM AGENDA_MED WHERE `status` = \'AGENDADO\') AS consultas 
                                                    FROM
                                                    	FARMACIA');
                                                    
                                                    $pedidos = $pedidos->fetch(PDO::FETCH_OBJ);

                                                    $INICIO = date('Y-m-d H:i:s', strtotime('-15 second '.date('Y-m-d H:i:s')));
                                                    $FIM = date('Y-m-d H:i:s');

                                                    $stmt0 = $pdo->query("SELECT * FROM `SESSIONS` WHERE last_ping BETWEEN '$INICIO' AND '$FIM'");
                                                    $clients_online = 0;

                                                    try {
                                                      $stmt0->execute();
                                                      $clients_online = count($stmt0->fetchAll());
                                                    } catch(PDOException $ex) {
                                                      
                                                    }
        
                        ?>
                            <h3 class="titulo-h1 dashboard-h1">Dashboard</h3>
                            <small class="dashboard-time-updated">Atualizado às <?=date('H:i:s')?></small>
                            <section class="grid-container dashboard-cards" style="margin: 0 0 40px 0;">
                                <div class="grid-item">
                                    <span>PACIENTES</span>
                                    <h3 class="dashboard-conter-pacientes" data-value="<?=($pedidos->pacientes)?>">
                                        <?=($pedidos->pacientes)?></h3>
                                </div>
                                <div class="grid-item">
                                    <span>MÉDICOS</span>
                                    <h3 class="dashboard-conter-medicos" data-value="<?=($pedidos->medicos)?>">
                                        <?=($pedidos->medicos)?></h3>
                                </div>
                                <div class="grid-item">
                                    <span>CONSULTAS</span>
                                    <h3 class="dashboard-conter-consultas" data-value="<?=($pedidos->consultas)?>">
                                        <?=($pedidos->consultas)?></h3>
                                </div>
                                <div class="grid-item">
                                    <span>FATURAS</span>
                                    <h3 class="dashboard-conter-faturas" data-value="<?=($pedidos->faturas)?>">
                                        <?=($pedidos->faturas)?></h3>
                                </div>
                                <div class="grid-item">
                                    <span>PEDIDOS</span>
                                    <h3 class="dashboard-conter-pedidos" data-value="<?=($pedidos->pedidos)?>">
                                        <?=($pedidos->pedidos)?></h3>
                                </div>
                            </section>
                            <section class="grid-container dashboard-cards" style="margin: 0 0 40px 0;">
                                <div class="grid-item">
                                    <span>VISITAS HOJE</span>
                                    <h3 class="dashboard-conter-users-online" data-value="<?=($visitas_hoje)?>">
                                        <?=($visitas_hoje)?></h3>
                                </div>

                                <?php
                                $ram_total = intval(preg_replace('/[^0-9]+/', '', shell_exec("free | grep 'Mem:' | awk '{ print \$2}'")));
                                $ram_used = intval(preg_replace('/[^0-9]+/', '', shell_exec("free | grep 'Mem:' | awk '{ print \$3}'")))

                                ?>

                                <div class="grid-item">
                                    <span>CPU</span>
                                    <h3 class="dashboard-conter-cpu" data-value="0">
                                        0%
                                    </h3>
                                </div>

                                <div class="grid-item">
                                    <span>Memória</span>
                                    <h3 class="dashboard-conter-ram" data-value="0">
                                        <?=round(($ram_used * 100) / $ram_total)?>%
                                    </h3>
                                </div>

                                <div class="grid-item">
                                    <span>Armazenamento</span>
                                    <h3 class="dashboard-conter-storage" data-value="0">
                                        0% 
                                    </h3>
                                </div>
                            </section>

                            <table id="sessionTableX">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Nome Completo</th>
                                        <th>IP</th>
                                        <th>Início</th>
                                        <th>Tempo</th>
                                        <th style="max-width: 48px !important">Ações</th>
                                    </tr>
                                    </thread>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                        <div class="tab" data-tab="tabControl2" data-index="2">
                            <section class="form-grid area-x1-2">
                                <section class="form-group"> <label for="smtp_server">Servidor SMTP</label> <input
                                        autocomplete="off" disabled="true" value="<?=($conf->mail->host)?>" type="text"
                                        id="smtp_server" name="smtp_server" class="form-control"
                                        placeholder="smtp.mail.com"> </section>
                                <section class="form-group"> <label for="smtp_port">Porta</label>
                                    <select id="smtp_port" name="smtp_port" class="form-control"
                                        value="<?=($conf->mail->port)?>">
                                        <option value="25" <?=($conf->mail->port == 25 ? ' selected':'')?>>25 Sem
                                            Segurança</option>
                                        <option value="465" <?=($conf->mail->port == 465 ? ' selected':'')?>>465 SSL
                                        </option>
                                        <option value="587" <?=($conf->mail->port == 587 ? ' selected':'')?>>587 TLS
                                        </option>
                                    </select>
                                </section>
                            </section>
                            <section class="form-grid area-x2-1">
                                <section class="form-group"> <label for="smtp_user">Usuário</label> <input
                                        autocomplete="off" disabled="true" value="<?=($conf->mail->username)?>"
                                        type="text" id="smtp_user" name="smtp_user" class="form-control"
                                        placeholder="admin"> </section>
                                <section class="form-group"> <label for="smtp_port">Senha</label> <input
                                        autocomplete="off" disabled="true" value="<?=($conf->mail->password)?>"
                                        type="password" id="smtp_pwd" name="smtp_pwd" class="form-control"
                                        placeholder="***********"> </section>
                            </section>
                            <section class="form-grid area-x2-1">
                                <section class="form-group"> <label for="smtp_from_mail">E-mail do Remetente</label>
                                    <input autocomplete="off" disabled="true" value="<?=($conf->mail->from_mail)?>"
                                        type="text" id="smtp_from_mail" name="smtp_from_mail" class="form-control"
                                        placeholder="admin@clinabs.com">
                                </section>
                                <section class="form-group"> <label for="smtp_from_name">Nome do Remetente</label>
                                    <input autocomplete="off" disabled="true" value="<?=($conf->mail->from_name)?>"
                                        type="text" id="smtp_from_name" name="smtp_from_name" class="form-control"
                                        placeholder="Clinabs">
                                </section>
                            </section>
                        </div>
                        <div class="tab" data-tab="tabControl2" data-index="3">

                            <section class="carrinho-header user-profile" id="wa-status">
                                <?php
                         
                        if($whatsapp->result->state == 'disconnected'){
                            ?> <div class="profile-details"> <img
                                        src="data:image/png;base64,<?=($whatsapp->result->qrCode)?>" class="no-round"
                                        height="128px">
                                    <div class="details">
                                        <h2>Conecte seu WhatsApp</h2>
                                        <h4>ID da Instância: <?=($whatsapp->instance_id)?></h4> <small
                                            class="badge-danger">Desconectado</small>
                                    </div>
                                </div> <?php
                        }else {
                                $cell = "{$ddi}{$ddd}{$cell_p1}{$cell_p2}";
                            ?> <div class="profile-details wa-connect"> <img
                                        src="<?=('/data/wa-profiles/'.$cell.'.jpg')?>" height="128px">
                                    <div class="details">
                                        <h2><?=($whatsapp->result->name)?></h2>
                                        <h4><?=("+{$ddi} ({$ddd}) {$cell_p1}-{$cell_p2}")?></h4>
                                        <h5>Expira em: <?=(date('d/m/Y', strtotime($whatsapp->result->expiresAt)))?>
                                        </h5> <small
                                            class="badge-<?=($whatsapp->result->state == 'connected' ? 'success':'danger')?>"><?=($whatsapp->result->state == 'connected' ? 'Conectado':'Desconectado')?></small>
                                    </div>
                                </div> <?php
                        }
                        ?> </section>
                            <section class="form-grid area-x2-1">
                                <section class="form-group"> <label for="instanceKey">KEY da instância</label> <input
                                        autocomplete="off" disabled="true" value="<?=($conf->whatsapp->key)?>"
                                        type="text" id="instanceKey" class="form-control"
                                        placeholder=""> </section>
                                <section class="form-group"> <label for="instanceToken">Token da instância</label>
                                    <input autocomplete="off" disabled="true" value="<?=($conf->whatsapp->token)?>"
                                        type="text" id="instanceToken" class="form-control"
                                        placeholder="Clinabs" maxlength="4">
                                </section>
                            </section>
                            <section class="form-grid area-x2-1">
                                <section class="form-group"> <label for="instanceLogin">Login da instância</label>
                                    <input autocomplete="off" disabled="true" value="<?=($conf->whatsapp->login)?>"
                                        type="text" id="instanceLogin" class="form-control"
                                        placeholder="">
                                </section>
                                <section class="form-group"> <label for="instanceNumber">Conta Conectada</label> <input
                                        autocomplete="off" disabled="true" data-mask="+00 (00) 0000-0000"
                                        value="<?=("{$ddi}{$ddd}{$cell_p1}{$cell_p2}")?>" type="text"
                                        id="instanceNumber" class="form-control" placeholder=""
                                        maxlength="4"> </section>
                            </section>
                            <section class="form-grid area-full">
                                <section class="form-group"> <label for="webhook_url">Webhook url da instância</label>
                                    <input autocomplete="off" disabled="true"
                                        value="<?=($conf->whatsapp->webhook_url)?>" type="text" id="webhook_url"
                                         class="form-control" placeholder="">
                                </section>
                            </section>
                        </div>
                        <div class="tab" data-tab="tabControl2" data-index="4">

                            <table id="mailAccounts" width="100%" class="display">
                                <thead>
                                    <tr>
                                        <th>Nome do Usuário</th>
                                        <th width="256px">Conta de E-mail</th>
                                        <th width="180px">Ações</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                $mail_accounts = $pdo->query('SELECT * FROM CONTAS_EMAIL');
                                    foreach($mail_accounts->fetchAll(PDO::FETCH_OBJ) as $email){
                                        echo "<tr>";
                                            echo "<td>{$email->nome}</td>";
                                            echo "<td>{$email->email}</td>";
                                            echo "<td width=\"180px\"><div class=\"btns-act\" onclick=\"alterMailAccount('{$email->nome}', '{$email->email}', true)\"><img src=\"https://clinabs.com/assets/images/ico-edit.svg\" height=\"22px\"></div></td>";
                                        echo "</tr>";
                                    }
                                ?>
                                </tbody>
                            </table>
                            <?php
                           
                        ?>
                        </div>
                    </section>
                </div>
                <?php
               }
            }
            ?>
                <div class="tab" data-index="8" data-tab="tabControl1">
                    <section class="form-grid area-responsavel">
                        <section class="form-group">
                            <label for="cpf">CPF</label>
                            <input type="text" id="responsavel_cpf" class="form-control" name="responsavel_cpf"
                                data-mask="000.000.000-00" placeholder="Digite seu CPF"
                                value="<?=$_user->responsavel_cpf?>" />
                        </section>

                        <section class="form-group">
                            <label for="responsavel_nome_completo">Nome Completo</label>
                            <input type="text" id="responsavel_nome_completo" class="form-control capitalize"
                                name="responsavel_nome_completo" value="<?=$_user->responsavel_nome?>"
                                placeholder="Digite seu nome completo" />
                        </section>

                        <section class="form-group">
                            <label for="responsavel_rg">RG</label>
                            <input type="text" id="responsavel_rg" name="responsavel_rg" class="form-control"
                                placeholder="__.__.__-__" maxlength="11" value="<?=$_user->responsavel_rg?>" />
                        </section>

                        <section class="form-group">
                            <label for="responsavel_celular">Contato</label>
                            <input type="text" id="responsavel_celular" name="responsavel_celular"
                                data-mask="(00) 0 0000-0000" class="form-control" placeholder="(__) _ ____-____"
                                value="<?=$_user->responsavel_contato?>" />
                        </section>
                    </section>
                </div>

                <?php
                    if($user->tipo == 'PACIENTE' && $user->perms->user_docs == 1) {
                        echo '<div class="tab" data-index="9" data-tab="tabControl1">';

                       $user_docs = $pdo->query("SELECT * FROM `ANEXOS_PACIENTES` WHERE paciente_token = '{$user->token}'")->fetchAll(PDO::FETCH_OBJ);
                        ?>
                            <h1 class="titulo-h1">Documentos</h1>
                            <div class="toolbar">
                                <button type="button" class="btn-button1" onclick="addPrescFunc3()"><i class="fa fa-upload fa-2x"></i> Enviar Documento</button>
                            </div>
                            <div class="container-flex1">
                                <section class="form-grid area-doc">
                                    <?php
                                        foreach($user_docs as $doc) {
                                            $desc = base64_decode($doc->descricao);
                                            echo '<section class="form-group">
                                                    <label for="'.uniqid().'" class="anexo-item anexo-item-view" data-file="'.basename($doc->doc_path).'">
                                                        <img data-title="Obrigatório" src="/assets/images/ico-doc-pdf.svg">
                                                        <strong>'.$doc->doc_type.'</strong>
                                                        <small>'.$desc.'</small>
                                                    </label> 
                                                </section>';
                                        }
                                    ?>
                                </div>
                            </div>

                        <?php
                        echo '</div>';
                    }
                ?>

                </div>
            </section>
            <div class="form-footer">
                <input autocomplete="off" disabled="true" type="hidden" name="tabela"
                    value="<?=isset($_GET['token']) ? $_user->objeto : $user->tipo ?>S">
                <input autocomplete="off" disabled="true" id="user_token" type="hidden" name="token"
                    value="<?=$_user->token ?>"><button type="button" class="btn-button1 btn-edit-form">EDITAR
                    DADOS</button> <button type="submit" class="btn-button1 btn-save-form" disabled="true"
                    id="btn-save-profile">SALVAR DADOS</button>
            </div>
        </form>
        <input autocomplete="off" disabled="true" type="hidden" name="profileImage" id="profileImage"
            value="<?=Modules::getUserImage($_user->token)?>">
        <?php
            if($user->tipo == 'MEDICO' || $user->tipo == 'FUNCIONARIO') {
                echo '<input autocomplete="off" name="agenda_dados" type="hidden" id="agenda_dados" value="'.str_replace('"', "'", $dados->calendario).'">';
            }
        ?>
    </div>
</section>
<div class="modal" id="image-editor">
    <div class="modal-image-editor-overlay"></div>
    <div class="modal-image-editor">
        <div class="modal-image-container">
            <div class="modal-image-editor-image" id="image-cropper-profile"></div>
            <div class="modal-image-editor-btns"> <button class="modal-image-editor-cancel-btn">Cancelar</button>
                <button class="modal-image-editor-save-btn">Salvar</button>
            </div>
        </div>
    </div>
</div>