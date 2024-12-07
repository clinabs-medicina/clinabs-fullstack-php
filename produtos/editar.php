<pre>
 <?php
  $stmt = $pdo->query('SELECT * FROM PRODUTOS WHERE token = "'.$_GET['product'].'";');
  $product = $stmt->fetch(PDO::FETCH_OBJ);
 ?>
</pre>
<section class="main">
    <div class="flex-container">
        <form id="updateProduto" action="/forms/form.update.produtos.php" method="POST" class="wizard-form">
            <h3 class="form-title titulo-h1">Editar Produto</h3>
            <!-- Indicadores -->
            <div class="form-header">
                <span class="stepIndicator active">Informações</span>
                <span class="stepIndicator">Anexos</span>
            </div>
            <!-- efim de Indicadores -->

            <!-- Informações -->
            <div class="step active">
                <section class="form-grid area11">
                    <section class="form-group">
                        <label for="codigo_produto">Código do Produto</label>
                        <input type="text" id="codigo_produto" class="form-control" name="codigo_produto" placeholder=""
                            value="<?=$product->codigo?>" />
                    </section>

                    <section class="form-group">
                        <label for="nome_produto">Nome do Produto</label>
                        <input type="text" id="nome_produto" class="form-control upper-case" name="nome_produto"
                            placeholder="" value="<?=$product->nome?>" />
                    </section>


                    <section class="form-group">
                        <label for="nacionalidade">Nacionalidade</label>
                        <select name="nacionalidade" id="nacionalidade">
                            <option value="" disabled<?=$product->nacionalidade == '' ? ' selected':''?>>Selecione uma
                                Opção</option>
                            <option value="nacional" <?=$product->nacionalidade == 'nacional' ? ' selected':''?>>
                                Nacional</option>
                            <option value="importado" <?=$product->nacionalidade == 'importado' ? ' selected':''?>>
                                Importado</option>
                        </select>
                    </section>

                    <section class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status">
                            <option value="" disabled selected>Selecione uma Opção</option>
                            <option value="ATIVO" <?=$product->status == 'ATIVO' ? ' selected':''?>>ATIVO</option>
                            <option value="INATIVO" <?=$product->status == 'INATIVO' ? ' selected':''?>>INATIVO</option>
                        </select>
                    </section>
                </section>

                <section class="form-grid area-0">
                    <section class="form-group">
                        <label for="nacionalidade">Descrição</label>
                        <textarea id="descricao" name="descricao"
                            style="width: 100%; height: 107px;"><?=$product->descricao?></textarea>
                    </section>
                </section>

                <section class="form-grid area30">
                    <section class="form-group">
                        <label for="moeda">Moeda</label>
                        <select name="moeda" class="form-select form-control" id="moeda" value="<?=$product->moeda?>">
                            <option value="" disabled<?=($product->moeda == '' ? ' selected':'')?>>Selecione uma Opção
                            </option>
                            <option value="BRL" <?=($product->moeda == 'BRL' ? ' selected':'')?>>Real (BRL)</option>
                            <option value="USD" <?=($product->moeda == 'USD' ? ' selected':'')?>>Dolar (USD)</option>
                        </select>
                    </section>
                    <section class="form-group">
                        <label for="valor_compra">Valor de Compra</label>
                        <input type="text" mask-money="true" id="valor_compra" class="form-control" name="valor_compra"
                            placeholder="R$ 0.00" value="<?=(intval($product->valor_compra))?>" />
                    </section>

                    <section class="form-group">
                        <label for="valor_venda">Valor de Venda</label>
                        <input type="text" mask-money="true" id="valor_venda" class="form-control" name="valor_venda"
                            placeholder="R$ 0.00" value="<?=(intval($product->valor_venda) * 100)?>" />
                    </section>

                    <section class="form-group">
                        <label for="valor_frete_compra1" mask-money="true">Valor do Frete ( Compra ) </label>
                        <input type="text" id="valor_frete_compra1" data-money="true" class="form-control money"
                            name="valor_frete_compra" placeholder="R$ 0.00"
                            value="<?=(intval($product->valor_frete_compra)) ?>" />
                    </section>

                    <section class="form-group">
                        <label for="unidade_medida">Unidade de Medida</label>
                        <select name="unidade_medida" class="form-select form-control" id="unidade_medida">
                            <option value="" disabled selected>Selecione uma Opço</option>
                            <option value="ml" <?=(strtoupper($product->unidade_medida) == 'ML' ? ' selected':'')?>>ML
                            </option>
                            <option value="mg" <?=(strtoupper($product->unidade_medida) == 'MG' ? ' selected':'')?>>MG
                            </option>
                            <option value="g" <?=(strtoupper($product->unidade_medida) == 'G' ? ' selected':'')?>>G
                            </option>
                            <option value="ct" <?=(strtoupper($product->unidade_medida) == 'ct' ? ' selected':'')?>>CT
                            </option>
                        </select>
                    </section>

                    <section class="form-group">
                        <label for="capacidade">Capacidade</label>
                        <input type="text" title="Selecione uma Unidade de Medida" name="capacidade"
                            class="form-control" id="capacidade" placeholder="Selecione uma Unidade de Medida"
                            maxlength="10" disabled value="<?=$product->capacidade?>" />
                    </section>
                </section>

                <section class="form-grid area11">
                    <section class="form-group">
                        <label for="fornecedor">Fornecedor</label>
                        <select name="fornecedor" id="fornecedor" class="form-control">
                            <option disabled <?=($product->fornecedor !== null ? 'selected':'')?>>Selecione um
                                Fornecedor</option>
                            <?php
  								$stmtx = $pdo->query('SELECT * FROM `FORNECEDORES` WHERE status = "ATIVO"');
                                $fornecedores = $stmtx->fetchAll(PDO::FETCH_ASSOC);
                                   
                                   foreach($fornecedores as $fornecedor) {
                                     	if($product->fornecedor == $fornecedor['id']) {
                                          echo "<option selected value=\"{$fornecedor['id']}\">{$fornecedor['razao_social']}</option>";
                                        } else {
                                          echo "<option value=\"{$fornecedor['id']}\">{$fornecedor['razao_social']}</option>";
                                        }
                                   }
  							?>
                        </select>
                    </section>

                    <section class="form-group">
                        <label for="nfe">NF/Ordem</label>
                        <input type="text" id="nfe" name="nfe" class="form-control" placeholder="NF-e ou Ordem"
                            value="<?=$product->nfe_ordem?>" />
                    </section>

                    <section class="form-group">
                        <label for="lote">Lote</label>
                        <input type="text" id="lote" name="lote" class="form-control" placeholder=""
                            value="<?=$product->lote?>" />
                    </section>

                    <section class="form-group">
                        <label for="data_validade">Data de Validade</label>
                        <input type="month" id="data_validade" name="data_validade" class="form-control"
                            placeholder="__/____" value="<?=$product->data_validade?>" />
                    </section>
                </section>

                <section class="form-grid area11">
                    <section class="form-group">
                        <label for="fornecedor">Marca</label>
                        <select name="marca" id="marca" class="form-control upper-case">
                            <?php
  								$stmty = $pdo->query('SELECT * FROM `MARCAS` WHERE status = "ATIVO"');
                                $marcas = $stmty->fetchAll(PDO::FETCH_ASSOC);
                                   
                                   foreach($marcas as $marca) {
                                     	if($product->marca == $marca['id']) {
                                          echo "<option selected value=\"{$marca['id']}\">{$marca['nome']}</option>";
                                        } else {
                                          echo "<option value=\"{$marca['id']}\">{$marca['nome']}</option>";
                                        }
                                   }
  							?>
                            ?>
                        </select>
                    </section>

                    <section class="form-group">
                        <label for="prazo_entrega">Prazo de Entrega</label>
                        <input type="text" name="prazo_entrega" id="prazo_entrega" class="form-control" placeholder=""
                            value="<?=$product->prazo_entrega?>" />
                    </section>

                    <section class="form-group">
                        <label for="valor_frete_venda">Valor do Frete ( Venda )</label>
                        <input type="text" id="valor_frete_venda" name="valor_frete_venda" data-money="true"
                            class="form-control money" placeholder=""
                            value="<?=($product->valor_frete_venda)?>" />
                    </section>

                    <section class="form-group">
                        <label for="numero_frascos">Frascos</label>
                        <input type="text" id="numero_frascos" name="numero_frascos" class="form-control" placeholder=""
                            value="<?=$product->numero_frascos?>" />
                    </section>
                </section>
            </div>

            <!-- Imagens -->
            <div class="step">
                <section class="form-grid grid-images">
                    <div class="form-input-upload">
                        <section class="form-group">
                            <label for="product-image">Imagem</label>
                            <input type="file" id="product-image" accept="image/*">
                            <input type="hidden" id="product_image_name" name="product-image"
                                value="<?=$product->image?>">
                        </section>

                        <section class="form-group">
                            <label for="product-catalog">Catalogo</label>
                            <input type="file" id="product-catalog" accept="application/pdf">
                            <input type="hidden" id="product_catalog_name" name="product-catalog"
                                value="<?=$product->catalog_file?>">
                        </section>
                    </div>
                    <div class="grid-image"
                        style="background-image: url('/data/images/produtos/<?=($product->image)?>');"
                        id="product-image-view">
                    </div>
                </section>
            </div>

            <div class="form-footer">
                <input type="hidden" name="token" id="token" value="<?=$product->token?>">
                <input type="hidden" name="image-principal" id="image-principal" value="0">
                <label class="input-label"><input name="exclur" type="checkbox" id="exclur" value="1"> Excluir
                    Produto</label>
                <button type="button" name="prev" class="btn-step-prev btn-button2">VOLTAR</button>
                <button type="button" name="next" class="btn-step-next btn-button1">PRÓXIMO</button>
                <button type="submit" name="btn-finish" class="btn-step-submit btn-button1">FINALIZAR</button>
            </div>
        </form>
    </div>
</section>