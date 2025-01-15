<style>
    .form-group label {
        width: fit-content !important;
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        align-content: center;
        justify-content: space-between;
        align-items: center;
        font-size: 14px;
    }

    .vermelho {
        color: red;
    }
    .titulo {
        font-size: 20px;
        font-weight: bold;
    }
</style>


<section class="main">
    <div class="flex-container">
        <form id="cadastroMedicoBasico" action="/form/cadastro.medico.php" autocomplete="on" method="POST"
            class="wizard-form validate-form">
            <h3 class="form-title titulo-h1">Cadastro de Médico</h3>
            <!-- Indicadores -->
            <div class="form-header">
                <span class="stepIndicator">(*) Campos obrigatórios</span>
            </div>
            <!-- efim de Indicadores -->

            <div class="step active">
                <p class="titulo">Preencha seus Dados para continuar</p>
                <div class="form-header">
                    <span>Campos Obrigatórios</span>
                    <span class="vermelho">*</span>
                    <!--<span class="stepIndicator">Endereço</span>-->
                </div>
                <section class="form-grid area25">
                    <section class="form-group">
                        <label for="cpf">CPF <small>*</small></label>
                        <input data-table="MEDICO" type="text" id="cpf" class="form-control" name="cpf" placeholder="Digite seu CPF" required value="<?= ($user->tipo == 'PACIENTE' ? $user->cpf : '') ?>" />
                    </section>

                    <section class="form-group">
                        <label for="rg">RG<small>*</small></label>
                        <input type="text" id="rg" class="form-control" name="rg" placeholder="Digite seu RG" required value="<?= ($user->tipo == 'PACIENTE' ? $user->rg : '') ?>" />
                    </section>

                    <section class="form-group">
                        <label for="nome_completo">Nome Completo <small>*</small></label>
                        <input type="text" id="nome_completo" class="form-control capitalize" name="nome_completo" placeholder="Digite seu nome completo" required value="<?= ($user->tipo == 'PACIENTE' ? $user->nome_completo : '') ?>" />
                    </section>
                </section>

                <section class="form-grid area1">
                    <section class="form-group">
                        <label for="nome_preferencia">Nome de Preferência <small>*</small></label>
                        <input type="text" id="nome_preferencia" class="form-control" name="nome_preferencia" placeholder="Digite seu nome de Preferência." required />
                    </section>

                    <section class="form-group">
                        <label for="identidade_genero">Sexo <small>*</small></label>
                        <select name="identidade_genero" class="form-select form-control" id="identidade_genero" required>
                            <
                            <option value="" disabled selected>Selecione uma Opção</option>
                            <option value="Masculino">Masculino</option>
                            <option value="Feminino">Feminino</option>
                            <option value="Outros">Outros</option>
                        </select>
                    </section>

                    <section class="form-group">
                        <label for="data_nascimento">Data de Nascimento <small>*</small></label>
                        <input type="text" data-mask="00/00/0000" name="data_nascimento" class="form-control" id="data_nascimento" required="" placeholder="__/__/____" maxlength="10" />
                    </section>
                </section>

                <section class="form-grid area1">
                    <section class="form-group">
                        <label for="tipo_conselho">Tipo de conselho <small>*</small></label>
                        <select required class="form-select form-control" id="tipo_conselho" name="tipo_conselho">
                            <option disabled selected>Selecione uma Opção</option>
                            <option value="crm">CRM</option>
                            <option value="CRO">CRO</option>
                            <option value="crm">CREFITO</option>
                            <option value="crm">CRBM</option>
                            <option value="crm">CRMV</option>
                            <option value="crm">COFITTO</option>
                            <option value="crm">CRN</option>
                            <option value="crm">CRP</option>
                            <option value="crm">OUTRO</option>
                        </select>
                    </section>

                    <section class="form-group">
                        <label for="uf_conselho">UF conselho <small>*</small></label>
                        <select required class="form-select form-control" id="uf_conselho" name="uf_conselho">
                            <option disabled selected>Selecione uma Opção</option>
                            <option value="AC">Acre</option>
                            <option value="AL">Alagoas</option>
                            <option value="AP">Amap</option>
                            <option value="AM">Amazonas</option>
                            <option value="BA">Bahia</option>
                            <option value="CE">Ceará</option>
                            <option value="DF">Distrito Federal</option>
                            <option value="ES">Espírito Santo</option>
                            <option value="GO">Goiás</option>
                            <option value="MA">Maranhão</option>
                            <option value="MT">Mato Grosso</option>
                            <option value="MS">Mato Grosso do Sul</option>
                            <option value="MG">Minas Gerais</option>
                            <option value="PA">Pará</option>
                            <option value="PB">Paraíba</option>
                            <option value="PR">Paraná</option>
                            <option value="PE">Pernambuco</option>
                            <option value="PI">Piauí</option>
                            <option value="RJ">Rio de Janeiro</option>
                            <option value="RN">Rio Grande do Norte</option>
                            <option value="RS">Rio Grande do Sul</option>
                            <option value="RO">Rondônia</option>
                            <option value="RR">Roraima</option>
                            <option value="SC">Santa Catarina</option>
                            <option value="SP">São Paulo</option>
                            <option value="SE">Sergipe</option>
                            <option value="TO">Tocantins</option>
                            <option value="EX">Estrangeiro</option>
                        </select>
                    </section>

                    <section class="form-group">
                        <label for="num_conselho">Número conselho<small>*</small></label>
                        <input required id="num_conselho" name="num_conselho" class="form-control uppercase" type="text" placeholder="Número do conselho." autofocus autocomplete="off" />
                    </section>
                </section>

                <section class="form-grid area1">
                    <section class="form-group">
                        <label for="email">E-mail <small>*</small></label>
                        <input data-table="MEDICO" type="email" name="email" id="email" autocomplete="on" class="form-control" placeholder="Digite seu E-mail" required />
                    </section>

                    <section class="form-group">
                        <label for="telefone">Telefone</label>
                        <input type="text" id="telefone" name="telefone" class="form-control" placeholder="(__) ____-____" />
                    </section>

                    <section class="form-group">
                        <label for="celular">WhatsApp <small>*</small></label>
                        <input data-table="MEDICO" data-mask="(00) 0 0000-0000" type="text" id="celular" name="celular" class="form-control" required placeholder="(__) _ ____-____" />
                    </section>
                </section>
            </div>


            <div class="form-footer">
                <button type="submit" class="btn-button1">FINALIZAR</button>
                <input type="hidden" name="xsrf_token" id="xsrf" value="<?= md5(uniqid()) ?>">
            </div>
        </form>
    </div>
</section>