<section class="main">
    <div class="flex-container">
    <form id="cadastroProduto" action="/forms/form.cadastro.produtos.php" method="POST" class="wizard-form">
                <h3 class="form-title titulo-h1">Cadastro de Produtos</h3>
                <!-- Indicadores -->
                <div class="form-header">
                    <span class="stepIndicator active">Informaçes</span>
                    <span class="stepIndicator">Imagens</span>
                </div>
                <!-- efim de Indicadores -->

                <!-- Informações -->
                <div class="step active">
                    <section class="form-grid area12">
                        <section class="form-group">
                            <label for="codigo_produto">Código do Produto</label>
                            <input type="text" id="codigo_produto" class="form-control" name="codigo_produto" placeholder="" required value="CN-<?=uniqid()?>"/>
                        </section>

                        <section class="form-group">
                            <label for="nome_produto">Nome do Produto</label>
                            <input type="text" id="nome_produto" class="form-control upper-case" name="nome_produto" placeholder="" required />
                        </section>


                        <section class="form-group">
                            <label for="nacionalidade">Nacionalidade</label>
                            <select name="nacionalidade" id="nacionalidade">
                                <option value="" disabled selected>Selecione uma Opção</option>
                                <option value="nacional">Nacional</option>
                                <option value="importado">Importado</option>
                            </select>
                        </section>
                    </section>

                    <section class="form-grid area-0">
                        <section class="form-group">
                            <label for="nacionalidade">Descrição</label>
                            <textarea id="descricao" name="descricao" style="width: 100%; height: 107px;"></textarea>
                        </section>
                    </section>

                    <section class="form-grid area30">
              <section class="form-group">
                            <label for="moeda">Moeda</label>
                            <select name="moeda" class="form-select form-control" id="moeda">
                                <option value="" disabled selected>Selecione uma Opção</option>
                                <option value="BRL">Real (BRL)</option>
                                <option value="USD">Dolar (USD)</option>
                            </select>
                        </section>
                    <section class="form-group">
                            <label for="valor_compra">Valor de Compra</label>
                            <input type="text" id="valor_compra" data-money="true" class="form-control money" name="valor_compra" placeholder="R$ 0.00" required />
                        </section>

                        <section class="form-group">
                            <label for="valor_unitario">Valor de Venda</label>
                            <input type="text" id="valor_venda"  class="form-control money" name="valor_venda" placeholder="R$ 0.00" required />
                        </section>

                        <section class="form-group">
                            <label for="valor_frete_compra">Valor do Frete ( Compra ) </label>
                            <input type="text" id="valor_frete_compra" data-money="true"  class="form-control money" name="valor_frete_compra" placeholder="R$ 0.00" required />
                        </section>

                        <section class="form-group">
                            <label for="unidade_medida">Unidade de Medida</label>
                            <select name="unidade_medida" class="form-select form-control" id="unidade_medida" required>
                                <option value="" disabled selected>Selecione uma Opção</option>
                                <option value="ml">ML</option>
                                <option value="mg">MG</option>
                                <option value="g">G</option>
                            </select>
                        </section>

                        <section class="form-group">
                            <label for="capacidade">Capacidade</label>
                            <input type="text" title="Selecione uma Unidade de Medida" name="capacidade" class="form-control" id="capacidade" required="" placeholder="Selecione uma Unidade de Medida" maxlength="10" disabled />
                        </section>
                    </section>

                    <section class="form-grid area11">
                        <section class="form-group">
                            <label for="fornecedor">Fornecedor</label>
                            <input type="text" name="fornecedor" id="fornecedor" class="form-control" placeholder="Selecione um Fornecedor" required />
                        </section>

                            <section class="form-group">
                                <label for="nfe">NF/Ordem</label>
                                <input type="text" id="nfe" name="nfe" class="form-control" placeholder="NF-e ou Ordem" />
                            </section>

                        <section class="form-group">
                            <label for="lote">Lote</label>
                            <input type="text" id="lote" name="lote" class="form-control" placeholder="" />
                        </section>

                        <section class="form-group">
                            <label for="data_validade">Data de Validade</label>
                            <input type="date" id="data_validade" name="data_validade" class="form-control" placeholder="__/__/____" />
                        </section>
                    </section>

                    <section class="form-grid area11">
                        <section class="form-group">
                            <label for="fornecedor">Marca</label>
                            <input type="text" name="marca" id="marca" class="form-control upper-case" placeholder="" required />
                        </section>

                        <section class="form-group">
                            <label for="prazo_entrega">Prazo de Entrega</label>
                            <input type="text" name="prazo_entrega" id="prazo_entrega" class="form-control" placeholder="" required />
                        </section>

                        <section class="form-group">
                            <label for="valor_frete_venda">Valor do Frete ( Venda )</label>
                            <input type="text" id="valor_frete_venda" name="valor_frete_venda" data-money="true" class="form-control money" placeholder="" />
                        </section>

                        <section class="form-group">
                            <label for="numero_frascos">Frascos</label>
                            <input type="text" id="numero_frascos" name="numero_frascos" class="form-control" placeholder="" value="1" />
                        </section>
                    </section>
                </div>

                <!-- Imagens -->
                <div class="step">
                    <section class="form-grid grid-images">
                        <div class="form-input-upload">
                            <input type="file" id="product-image" accept="image/*">
                            <input type="hidden" name="product-image">
                        </div>
                    <div class="grid-image">
            
                    </div>
                    </section>
                </div>


                <div class="form-footer">
                    <input type="hidden" name="image-principal" id="image-principal" value="0">
                    <button type="button" name="prev" class="btn-step-prev btn-button2">VOLTAR</button>
                    <button type="button" name="next" class="btn-step-next btn-button1">PRÓXIMO</button>
                    <button type="submit" name="btn-finish" class="btn-step-submit btn-button1">FINALIZAR</button>
                </div>
    </form>
</div>
</section>