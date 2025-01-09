<section class="main">
    <div class="flex-container">
        <form id="cadastroCompraMedicamento" action="/formularios/form.compra.medicamentos.php" method="GET" class="wizard-form">
            <h3 class="form-title titulo-h1">Compra de Medicamentos</h3>

            <div class="form-header">
                <span class="stepIndicator">Dados Pessoais</span>
                <span class="stepIndicator">Endereço</span>
                <span class="stepIndicator">Documentação</span>
                <span class="stepIndicator">Pagamento</span>
            </div>

            
            <?php
            error_reporting(1);
            ini_set('display_errors', 1);
            $frete = $_REQUEST['valor_frete'];
            $valorProdutos = $_REQUEST['valor_produtos'];
            $valorTotal = $_REQUEST['valor_total'];
            $valorTotalPix = $_REQUEST['valor_total'];
            $desc = round(($valorProdutos + $frete) - ($valorProdutos - ($valorProdutos / 100) * 5) + $frete);
            
            
            /*
            $stmtz = $pdo->prepare("SELECT
	*
FROM
	ENDERECOS
WHERE
	user_token = :token
ORDER BY
	isDefault DESC");
	
	$stmtz->bindValue(':token', $_user->token);
	$stmtz->execute();
	
            $endereco = $stmtz->fetch(PDO::FETCH_OBJ);
  
               $query = "SELECT
               *,
               (SELECT valor FROM PRODUTOS WHERE id = product_id) as valor_unitario
           FROM
               CARRINHO
            WHERE pid IN('".implode("', '", $_POST['produto'])."')";

             $stmtx = $pdo->query($query);

             $valorTotal = 0;
             $valorProdutos = 0;

             foreach($stmtx->fetchAll(PDO::FETCH_ASSOC) as $produto) {
                 $c = new CarrinhoCalc($pdo);
                 $prod = $c->getProdByPromo($produto['product_id'], $produto['qtde']);
    
    
                $qtde = round($produto['qtde']);
                $valorTotal += $prod['valor_total'];
                $valorProdutos += ($prod['valor'] * $produto['qtde']);
                
                $prod = http_build_query([
                    'id' => $produto['product_id'],
                    'qtde' => round($produto['qtde']),
                    'valor' => $produto['valor_unitario']
                ]);
                
                echo "<input type=\"hidden\" name=\"produto[]\" value=\"{$prod}\">";
             }
             */
            ?>
         
            <!-- passo1 -->
            <div class="step active">
                <section class="form-grid area19">
                        <section class="form-group">
                                    <label for="cpf">CPF</label>
                                    <input type="text" id="cpf" class="form-control" name="cpf" placeholder="Digite seu CPF" required="" value="<?=($User->cpf)?>">
                                </section>

                                <section class="form-group">
                                    <label for="nome_completo">Nome Completo</label>
                                    <input type="text" id="nome_completo" class="form-control capitalize" name="nome_completo" placeholder="Digite seu nome completo" value="<?=($User->nome_completo)?>">
                                </section>
                                
                                <section class="form-group">
                                    <label for="rg">RG</label>
                                    <input type="text" id="rg" name="rg" class="form-control" placeholder="__.__.__-__" maxlength="11" value="<?=($User->rg)?>">
                                </section>

                    <section class="form-group">
                        <label for="nacionalidade">Nacionalidade</label>
                        <select name="nacionalidade" id="nacionalidade">
                            <option value="" disabled selected>Selecione uma Opo</option>
                            <?php
                              foreach(SQL::fetchTable(connector: $pdo, tableName: 'PAISES', Extraquery: " WHERE status = 'ATIVADO'") as $pais) {
                                echo '<option id="'.$pais->sigla.'" value="'.$pais->sigla.'"'.($User->nacionalidade == $pais->sigla ? ' selected="selected"':'').'>'.$pais->nome_pais.'</option>';
                              }
                            ?>
                        </select>
                    </section>
                </section>

                <section class="form-grid area1">
                    <section class="form-group">
                        <label for="nome_preferencia">Nome de Preferência</label>
                        <input type="text" id="nome_preferencia" class="form-control capitalize" name="nome_preferencia" placeholder="Digite seu nome de Preferência." required="" value="<?=$User->nome_preferencia?>">
                    </section>

                    <section class="form-group">
                        <label for="identidade_genero">Sexo</label>
                        <select name="identidade_genero" class="form-select form-control" id="identidade_genero" required="" value="<?=($User->identidade_genero)?>">
                            <option value="" disabled selected>Selecione uma Opço</option>
                            <option value="Masculino"<?=($User->identidade_genero == 'Masculino' ? ' selected="selected"':'')?>>Masculino</option>
                            <option value="Feminino"<?=($User->identidade_genero == 'Feminino' ? ' selected="selected"':'')?>>Feminino</option>
                            <option value="Outros"<?=($User->identidade_genero == 'Outros' ? ' selected="selected"':'')?>>Outros</option>
                        </select>
                    </section>

                    <section class="form-group">
                        <label for="data_nascimento">Data de Nascimento</label>
                        <input type="text" name="data_nascimento" class="form-control" id="data_nascimento" data-mask="00/00/0000" required="" placeholder="__/__/____" maxlength="10" value="<?=date('d/m/Y', strtotime($User->data_nascimento));?>" />
                    </section>
                </section>

                <section class="form-grid area1">
                    <section class="form-group">
                        <label for="email">E-mail</label>
                        <input type="email" name="email" id="email" class="form-control lower-case" placeholder="Digite seu E-mail" required="" value="<?=($User->email)?>" />
                    </section>

                    <section class="form-group">
                        <label for="medico_token">Médico</label>
                        <select name="medico_token" id="medico_token" required="required">
                            <option value="" disabled selected>Selecione uma Opção</option>
                            <?php
                              foreach(SQL::fetchTable(connector: $pdo, tableName: 'MEDICOS') as $medico) {
                                echo '<option id="'.$medico->token.'" value="'.$medico->token.'"'.($User->medico_token == $medico->token ? ' selected="selected"':'').'>'.$medico->nome_completo.'</option>';
                              }
                            ?>
                        </select>
                    </section>

                    <section class="form-group">
                        <label for="celular">Celular/WhatsApp</label>
                        <input type="text" id="celular" name="celular" class="form-control" required placeholder="(__) _ ____-____" value="<?=($User->celular)?>" />
                    </section>
                </section>
            </div>

       
            <!-- passo 3 -->
            <div class="step">
                <div class="street-editor">
                    <section class="form-grid area1">
                            <section class="form-group">
                                <label for="endereco_nome">Nome</label>
                                <input value="" type="text"   id="endereco_nome" name="endereco_nome" class="form-control" maxlength="25">
                            </section>

                        <section class="form-group">
                            <label for="endereco_nome">Tipo de Endereço</label>
                            <select id="tipo_endereco" name="tipo_endereco" class="form-control">
                                <option value="CASA">CASA</option>
                                <option value="RESPONSAVEL">RESPONSAVEL LEGAL</option>
                            </select>
                        </section>
                    </section>
                        <section class="form-grid area13">
                                <section class="form-group">
                                    <label for="cep">CEP</label>
                                    <input value="" type="text"   id="cep" name="cep" class="form-control" placeholder="__.____-___">
                                </section>
                                <section class="form-group">
                                    <label for="endereco">Endereço</label>
                                    <input value="" type="text"   id="endereco" name="endereco" class="form-control" placeholder="Digite seu Endereço" >
                                </section>

                                <section class="form-group">
                                    <label for="numero">Nmero</label>
                                    <input value="" type="text"   id="numero" name="numero" class="form-control" placeholder="Nº" >
                                </section>

                                <section class="form-group">
                                    <label for="complemento">Complemento</label>
                                    <input type="text" id="complemento" name="complemento" class="form-control" placeholder="Apto 13" value="">
                                </section>
                        </section>

                        <section class="form-grid area5">
                                <section class="form-group">
                                    <label for="cidade">Cidade</label>
                                    <input value="<?=($_user->cidade)?>" type="text"   id="cidade" name="cidade" class="form-control" placeholder="Digite sua Cidade" >
                                </section>

                                <section class="form-group">
                                    <label for="bairro">Bairro</label>
                                    <input value="<?=($_user->bairro)?>" type="text"   id="bairro" name="bairro" class="form-control" placeholder="Digite seu Bairro" >
                                </section>

                                <section class="form-group">
                                    <label for="uf">UF</label>
                                    <select  class="form-select form-control" id="uf" name="uf">
                                        <option value="AC" <?=($_user->uf == 'AC' ? ' selected':'')?>>Acre</option>
                                        <option value="AL" <?=($_user->uf == 'AL' ? ' selected':'')?>>Alagoas</option>
                                        <option value="AP" <?=($_user->uf == 'AP' ? ' selected':'')?>>Amap</option>
                                        <option value="AM" <?=($_user->uf == 'AM' ? ' selected':'')?>>Amazonas</option>
                                        <option value="BA" <?=($_user->uf == 'BA' ? ' selected':'')?>>Bahia</option>
                                        <option value="CE" <?=($_user->uf == 'CE' ? ' selected':'')?>>Ceará</option>
                                        <option value="DF" <?=($_user->uf == 'DF' ? ' selected':'')?>>Distrito Federal</option>
                                        <option value="ES" <?=($_user->uf == 'ES' ? ' selected':'')?>>Espírito Santo</option>
                                        <option value="GO" <?=($_user->uf == 'GO' ? ' selected':'')?>>Goiás</option>
                                        <option value="MA" <?=($_user->uf == 'MA' ? ' selected':'')?>>Maranhão</option>
                                        <option value="MT" <?=($_user->uf == 'MT' ? ' selected':'')?>>Mato Grosso</option>
                                        <option value="MS" <?=($_user->uf == 'MS' ? ' selected':'')?>>Mato Grosso do Sul</option>
                                        <option value="MG" <?=($_user->uf == 'MG' ? ' selected':'')?>>Minas Gerais</option>
                                        <option value="PA" <?=($_user->uf == 'PA' ? ' selected':'')?>>Pará</option>
                                        <option value="PB" <?=($_user->uf == 'PB' ? ' selected':'')?>>Paraíba</option>
                                        <option value="PR" <?=($_user->uf == 'PR' ? ' selected':'')?>>Paraná</option>
                                        <option value="PE" <?=($_user->uf == 'PE' ? ' selected':'')?>>Pernambuco</option>
                                        <option value="PI" <?=($_user->uf == 'PI' ? ' selected':'')?>>Piauí</option>
                                        <option value="RJ" <?=($_user->uf == 'RJ' ? ' selected':'')?>>Rio de Janeiro</option>
                                        <option value="RN" <?=($_user->uf == 'RN' ? ' selected':'')?>>Rio Grande do Norte</option>
                                        <option value="RS" <?=($_user->uf == 'RS' ? ' selected':'')?>>Rio Grande do Sul</option>
                                        <option value="RO" <?=($_user->uf == 'RO' ? ' selected':'')?>>Rondônia</option>
                                        <option value="RR" <?=($_user->uf == 'RR' ? ' selected':'')?>>Roraima</option>
                                        <option value="SC" <?=($_user->uf == 'SC' ? ' selected':'')?>>Santa Catarina</option>
                                        <option value="SP" <?=($_user->uf == 'SP' ? ' selected':'')?>>São Paulo</option>
                                        <option value="SE" <?=($_user->uf == 'SE' ? ' selected':'')?>>Sergipe</option>
                                        <option value="TO" <?=($_user->uf == 'TO' ? ' selected':'')?>>Tocantins</option>
                                    </select>
                                </section>
                        </section>
                        <input type="hidden" name="isDefault" value="false">
                        <button class="btn-button1" type="button" onclick="addAddress2()">SALVAR ENDEREÇO</button>
                   </div>
                    <section class="street-container">
                            <?php 
                            $stmtx1 = $pdo->prepare('SELECT * FROM `ENDERECOS` WHERE user_token = :token');
                            $stmtx1->bindValue(':token', $User->token);
                            $stmtx1->execute();
                            
                            foreach ($stmtx1->fetchAll(PDO::FETCH_OBJ) as $item) {
                                echo '<div class="street-item" id="'.$item->token.'">
                                        <input type="radio" class="form-control" name="endereco" value="'.$item->token.'"'.($item->isDefault == 1 ? ' checked':'').'>
                                            <div class="street-info">
                                                <b>'.strtolower($item->nome).'</b>
                                                <span>'.$item->logradouro.', '.$item->numero.'</span>
                                                <span>'.$item->cidade.'-'.$item->bairro.'</span>
                                                <span>'.$item->cep.'</span>
                                            </div>
                                            <div class="street-btns">
                                                <div class="btns-info">
                                                <label for="" class="enderecos">'.($item->isDefault ? '(Padrão)':'').'</label>
                                                </div>
                                            </div>
                                            
                                            <input type="hidden" name="enderecos" data-token="'.$item->token.'" class="input-data" value="'.str_replace('"', "'", json_encode($item)).'">
                                        </div>';
                        
                            }
                            ?>

                            <p><button  class="btn-button1" type="button"  id="add-address" onclick="addStreet()">NOVO ENDEREÇO</button></p>
                        </section>
            </div>

            <!-- passo4 -->
            <div class="step">
            <div class="container-flex1">
                                    <div>
                                        <h2 class="titulo-h1">Documentos Necessários</h2>
                                        <p>Faça o download dos documentos relativos ao termo de consentimento livre e esclarecido e da procuraão para autorização de importação de produtos derivados de cannabis, conforme indicações de nossas equipes de atendimento ao cliente. Em caso de dúvidas, entre em contato pelo (41) 3341-0341</p>
                                    </div>
                                    <div>
                                        <a href="/docs/procuracao.pdf" target="_blank"><img src="/assets/images/btn-001.svg" title="Autorização de Importaço"></a>
                                    </div>
                                    <div>
                                        <a href="/docs/termo_consentimento.pdf" target="_blank"><img src="/assets/images/btn-002.svg" title="Termo"></a>
                                    </div>
                                </div>

                                <div class="container-flex1">
                                    <div>
                                        <h2 class="titulo-h1">Upload dos Arquivos</h2>
                                        <p>Faça o upload dos arquivos aqui!</p>
                                    </div>
                                </div>
                                
                                 <div class="container-flex1">
                                    <div class="d-flex">
                                        <input autocomplete="off" type="checkbox" id="doc_validation" name="doc_validation" checked="<?=$User->doc_cnh == 'on' ? 'checked':''?>"> <strong>Minha CNH contem CPF,RG inclusos</strong>
                                    </div>
                                </div>
                                
                                
                <section class="form-grid area7 grid-docs">

                    <section class="form-group">
                        <label for="doc_rg_frente" class="anexo-item">
                            <input name="file1" class="input-upload-doc" type="file" accept="image/png, image/jpeg, image/jpg, application/pdf" id="doc_rg_frente" class="form-control file-doc" placeholder="Anexo RG (Frente)" />
                            <img title="Obrigatório" src="<?=Modules::getDocIcon($User->doc_rg_frente)?>" />
                            <strong>Anexo RG (Frente)</strong>
                            <input type="hidden" name="doc_rg_frente" value="<?=($User->doc_rg_frente)?>">
                        </label>
                    </section>
                    <section class="form-group">
                        <label for="doc_rg_verso" class="anexo-item">
                            <input name="file2" class="input-upload-doc" type="file" accept="image/png, image/jpeg, image/jpg, application/pdf" id="doc_rg_verso" class="form-control file-doc" placeholder="Anexo RG (Verso)" />
                            <img title="Obrigatório" src="<?=Modules::getDocIcon($User->doc_rg_verso)?>" />
                            <strong>Anexo RG (Verso)</strong>
                            <input type="hidden" name="doc_rg_verso" value="<?=($User->doc_rg_verso)?>">
                        </label>
                    </section>
                    <section class="form-group">
                        <label for="doc_cpf_frente" class="anexo-item">
                            <input name="file3" class="input-upload-doc" type="file" accept="image/png, image/jpeg, image/jpg, application/pdf" id="doc_cpf_frente" class="form-control file-doc" placeholder="Anexo CPF (Frente)" />
                            <img title="Obrigatório" src="<?=Modules::getDocIcon($User->doc_cpf_frente)?>" />
                            <strong>Anexo CPF (Frente)</strong>
                            <input type="hidden" name="doc_cpf_frente" value="<?=($User->doc_cpf_frente)?>">
                        </label>
                    </section>
                    <section class="form-group">
                        <label for="doc_cpf_verso" class="anexo-item">
                            <input name="file3" class="input-upload-doc" type="file" accept="image/png, image/jpeg, image/jpg,  application/pdf" id="doc_cpf_verso" class="form-control file-doc" placeholder="Anexo CPF (Verso)" />
                            <img title="Obrigatório" src="<?=Modules::getDocIcon($User->doc_cpf_verso)?>" />
                            <strong>Anexo CPF (Verso)</strong>
                            <input type="hidden" name="doc_cpf_verso" value="<?=($User->doc_cpf_verso)?>">
                        </label>
                    </section>

                    <section class="form-group">
                        <label for="doc_comp_residencia" class="anexo-item">
                            <input name="file3" class="input-upload-doc" type="file" accept="image/png, image/jpeg, image/jpg,  application/pdf" id="doc_comp_residencia" class="form-control file-doc" placeholder="Comprovante de Residencia" />
                            <img title="Obrigatrio" src="<?=Modules::getDocIcon($User->doc_comp_residencia)?>" />
                            <strong>Comprovante de Residencia</strong>
                            <input type="hidden" name="doc_comp_residencia" value="<?=($User->doc_comp_residencia)?>">
                        </label>
                    </section>
                    <section class="form-group">
                        <label for="doc_procuracao" class="anexo-item">
                            <input name="file4" class="input-upload-doc" type="file" accept="image/png, image/jpeg, image/jpg,  application/pdf" id="doc_procuracao" class="form-control file-doc" placeholder="Procuraão" />
                            <img title="Obrigatório" src="<?=Modules::getDocIcon($User->doc_procuracao)?>" />
                            <strong>Procuração</strong>
                            <input type="hidden" name="doc_procuracao" value="<?=($User->doc_procuracao)?>">
                        </label>
                    </section>
                    <section class="form-group">
                        <label for="doc_anvisa" class="anexo-item">
                            <input name="file5" class="input-upload-doc" type="file" accept="image/png, image/jpeg, image/jpg,  application/pdf" id="doc_anvisa" class="form-control file-doc" placeholder="Documento Anvisa" />
                            <img title="Obrigatrio" src="<?=Modules::getDocIcon($User->doc_anvisa)?>" />
                            <strong>Documento Anvisa</strong>
                            <input type="hidden" name="doc_anvisa" value="<?=($User->doc_anvisa)?>">
                        </label>
                    </section>
                    <section class="form-group">
                        <label for="doc_termos" class="anexo-item">
                            <input  name="file6" class="input-upload-doc" type="file" accept="image/png, image/jpeg, image/jpg,  application/pdf" id="doc_termos" class="form-control file-doc" placeholder="Comprovante de Aceite de Termos" />
                            <img src="<?=Modules::getDocIcon($User->doc_termos)?>" />
                            <strong>Termo de Consentimento</strong>
                            <input type="hidden" name="doc_termos" data-table="FARMACIA" value="<?=($User->doc_termos)?>">
                        </label>
                    </section>

                </section>
            </div>
      
      <?php
        $token = $_REQUEST['medico_token'];
        $sql = "SELECT valor_consulta FROM MEDICOS WHERE token = '$token'";

       $stmt2 = $pdo->prepare($sql);
       $stmt2->execute();

       $med = $stmt2->fetch(PDO::FETCH_ASSOC);
       ?>
            <!-- passo 3 -->
            <div class="step">
                <section class="form-grid area-two">
                    <section class="form-group payment-method payment-product">
                        
                    <label for="payment_cc" class="payment-button credit-card btn-payment-active active">
                        <label for="payment_credit" class="payment-label">
                            <input type="radio" checked id="payment_cc" name="payment_mode" value="cartao_credito" required> PAGAR VIA CARTÃO DE CRÉDITO 
                      </label>
                            <span class="payment_valor_cc  payment-label">R$ <?=number_format(round($valorProdutos + $frete),2, ',','.')?></span>
                            <input type="hidden" data-frete="R$ <?=number_format($frete,2, ',','.')?>"  data-desc="R$ 0,00" data-value="R$ <?=(number_format(round($valorProdutos + $frete),2, ',','.'))?>"  data-total="R$ <?=number_format($valorProdutos + $frete,2, ',','.')?>" value="<?=($valorProdutos + $frete)?>" class="payment-value">
                    </label>
                    
                    <label for="payment_cc2" class="payment-button cc2">
                        <label for="payment_cc2" class="payment-label"><input type="radio" name="payment_mode" id="payment_cc2" value="cc" required> PAGAR VIA CARTÃO DE CRÉDITO ( A Vista )</label>
                        <span class="payment_valor_cc payment-label">R$ <?=number_format(round(($valorProdutos) - (($valorProdutos) / 100 * 5) + $frete),2, ',','.')?></span>
                        <input type="hidden" data-frete="R$ <?=number_format($frete,2, ',','.')?>" data-desc="R$ <?=number_format($desc,2, ',','.')?>" data-value="R$ <?=number_format(round(($valorProdutos) - (($valorProdutos) / 100 * 5) + $frete),2, ',','.')?>" data-total="R$ <?=number_format(round(($valorProdutos) - (($valorProdutos) / 100 * 5)) + $frete,2, ',','.')?>" value="<?=round(($valorProdutos) - (($valorProdutos) / 100 * 5) + $frete)?>" class="payment-value">
                    </label>
                    
                     <label for="payment_cd" class="payment-button cd">
                        <label for="payment_cd" class="payment-label"><input type="radio" name="payment_mode" id="payment_cd" value="cd" required> PAGAR VIA CARTÃO DE DÉBITO</label>
                        <span class="payment_valor_cd payment-label">R$ <?=number_format(round(($valorProdutos) - (($valorProdutos) / 100 * 5) + $frete),2, ',','.')?></span>
                        <input type="hidden" data-frete="R$ <?=number_format($frete,2, ',','.')?>" data-desc="R$ <?=number_format($desc,2, ',','.')?>" data-value="R$ <?=number_format(round(($valorProdutos) - (($valorProdutos) / 100 * 5) + $frete),2, ',','.')?>" data-total="R$ <?=number_format(round(($valorProdutos) - (($valorProdutos) / 100 * 5) + $frete),2, ',','.')?>" value="<?=round(($valorProdutos) - (($valorProdutos) / 100 * 5) + $frete)?>" class="payment-value">
                    </label>

                    <label for="payment_pix" class="payment-button pix">
                        <label for="payment_pix" class="payment-label"><input type="radio" name="payment_mode" id="payment_pix" value="pix" required> PAGAR VIA PIX</label>
                        <span class="payment_valor_pix payment-label">R$ <?=number_format(round(($valorProdutos) - (($valorProdutos) / 100 * 5) + $frete),2, ',','.')?></span>
                        <input type="hidden" data-frete="R$ <?=number_format($frete,2, ',','.')?>" data-desc="R$ <?=number_format($desc ,2, ',','.')?>" data-value="R$ <?=number_format(round(($valorProdutos) - (($valorProdutos) / 100 * 5) + $frete),2, ',','.')?>" data-total="R$ <?=number_format(round(($valorProdutos) - (($valorProdutos) / 100 * 5)) + $frete,2, ',','.')?>" value="<?=(($valorProdutos) - (($valorProdutos) / 100 * 5) + $frete)?>" class="payment-value">
                    </label>
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
                        <span><span>Produtos</span> <span class="subtotal">R$ <?=number_format($valorProdutos,2, ',','.')?></span></span>
                        <span><span>Frete</span> <span class="frete">R$ <?=number_format($frete,2, ',','.')?></span></span>
                        <span><span>Desconto</span> <span class="desconto">R$ 0,00</span></span>
                    </div>
                    <div class="payment-details-footer">
                        <b>Total</b>
                        <b class="valor-total">R$ <?=number_format($valorProdutos + $frete,2, ',','.')?></b>
                        </div>
                </section>

               </div>
                </section>
                <br>
                <section class="payment-details">
                    <div class="payment-details-header">Política de Cancelamento e Reembolso</div>
                    <div class="payment-details-info">
                        <ul style="text-align:left; font-weight:400; ">
                            <li style="padding:.3rem">- Se o cancelamento da consulta for solicitado com pelo menos 24 horas de antecedência, será reembolsado o valor total pago.</li>
                            <li style="padding:.3rem">- Em caso de cancelamento por parte do profissional, o paciente ter direito ao reembolso integral.</li>
                            <li style="padding:.3rem">- Se houver reagendamento por parte do profissional, o paciente poder optar entre solicitar o reembolso ou utilizar o valor pago para a nova consulta.</li>
                            <li style="padding:.3rem">- O no comparecimento do paciente à consulta não resultará em reembolso; o valor da consulta ser repassado ao profissional.</li>
                            <li style="padding:.3rem">- Atrasos superiores a 10 minutos resultarão no cancelamento da consulta, sem direito a reembolso.</li>
                            <li style="padding:.3rem">- Para detalhes mais específicos, recomendamos a leitura do nosso termo de uso.</li>          
                        <ul>
                    </div>
                </section>
            </div>

            <div class="form-footer">
                <input type="hidden" name="fid" value="<?=($user->tipo == 'FUNCIONARIO' ? $user->token : '')?>">
                <input type="hidden" name="paciente_token" value="<?=$User->token?>">
                <input type="hidden" name="tabela" value="FARMACIA">
                <input id="user_token" type="hidden" name="token" value="<?=$User->token?>">
                <input type="hidden" name="carrinho_pid" value="">
                <input type="hidden" name="valor_total" value="<?=$valorTotal ?>" id="valorTotal">
                <input type="hidden" name="valor_produtos" value="<?=$valorProdutos ?>" id="valorProdutos">
                <input type="hidden" name="payment_id" id="payment_id">
                <input type="hidden" name="valor_frete" id="<?=$frete?>">
                <input type="hidden" name="add_new" id="add_new">
                <button type="button" name="prev" class="btn-step-prev btn-button2 fwz-btn">VOLTAR</button>
                <button type="button" name="next" class="btn-step-next btn-button1 fwz-btn">PRÓXIMO</button>
                <button type="submit" class="btn-step-submit btn-button1">FINALIZAR COMPRA</button>
              <?php
                foreach($_POST['produtos'] as $produto) {
                    echo '<input type="hidden" name="produtos[]" value="'.$produto.'">';
                }
    ?>
            </div>
        </form>
    </div>
</section>