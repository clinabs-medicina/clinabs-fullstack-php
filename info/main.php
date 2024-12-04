<?php
    $token = $_GET['token'];
    $ag = $pdo->query("SELECT *,(SELECT nome_completo FROM MEDICOS WHERE token = medico_token) AS medico_nome FROM `AGENDA_MED` WHERE token = '{$token}';");
    $agx = $ag->fetch(PDO::FETCH_OBJ);
    $meet = json_decode($agx->meet);

    echo '<pre>';
        //print_r($meet);
    echo '</pre>';
?>

<div class="container-wb" id="wb-container">
    <div class="wb-frame">
        <whereby-embed room="<?=($meet->hostRoomUrl)?>&fieldName=<?=($agx->medico_nome)?>" />
    </div>
    <div class="column-2">
        <div class="flex-col" id="x-form-presc">
            <div class="swal2-checks">
                <label for="prescricao_mod"><input class="presc_mod" type="radio" name="presc_mod" id="prescricao_mod" checked value="prescricao"> Prescrição de Médicamentos</label>
                <label for="acompanhamento_mod"><input type="radio" class="presc_mod" name="presc_mod" id="acompanhamento_mod" value="acompanhamento"> Acompanhamento Médico</label>
            </div>

            <div class="row-presc">
                <div class="form-group">
                    <fieldset><legend>Selecionar um Produto</legend>
                    <select id="produto_sa_x"></select></fieldset>
                </div>

                <div class="form-group presc-num">
                    <fieldset><legend>Frascos</legend>
                    <input id="produto_frascos_x" type="number" min="1" max="100" value="1" data-arrows="false" placeholder="1">
                </div>
            </div>
            <textarea class="tiny-mce" style="width: 100%;" rows="4"></textarea>

            <div class="container-btn">
                <button type="button" onclick="presc_form()">SALVAR</button>
            </div>
        </div>

        <section id="tabControl1" class="tabControl tab-presc1">
            <div class="container-profile">
                <div class="tab-toolbar">
                    <span class="active" data-index="1" data-tab="tabControl1">Prescrição</span>
                    <span data-index="2" data-tab="tabControl1">Acompanhamento</span>
                </div>
            </div>

            <div class="tab active" data-index="1" data-tab="tabControl1">
                <table id="presc-wb" class="table" data-user="<?=($user->token)?>" data-token="<?=($_REQUEST['token'])?>" data-paciente="<?=($agx->paciente_token)?>"  data-medico="<?=($agx->medico_token)?>">
                    <thead>
                        <tr>
                            <th width="48px">#</th>
                            <th>Descrição</th>
                            <th>Medicamento</th>
                            <th>Frascos</th>
                            <th>Ações</th>
                        </tr>
                    </thead>

                    <tdody>
                        <?php
                            $token = $_GET['token'];
       
                            $db = $pdo->prepare('SELECT *,(SELECT nome FROM PRODUTOS WHERE id = produto_id) AS produto_nome FROM `PRESCRICOES` WHERE agenda_token = :agenda_token');
                            $db->bindValue(':agenda_token', $token);
                            $db->execute();
                            $rows = $db->fetchAll(PDO::FETCH_ASSOC);

                            $i = 1;

                            foreach($rows as $row) {
                                $item['index'] = $i;
                                $desc = base64_decode($row['prescricao']);
                                $remedio = strtoupper(trim($row['produto_nome']));
                                $frascos = $row['frascos'];

                                echo "<tr>";
                                    echo "<td>{$i}</td>";
                                    echo "<td>{$desc}</td>";
                                    echo "<td>{$remedio}</td>";
                                    echo "<td>{$frascos}</td>";
                                    echo "<td></td>";
                                echo "</tr>";
                                $i++;
                            }
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="tab" data-index="2" data-tab="tabControl1">
                <div class="acompanhamentos-list"></div>
            </div>
        </div>
    </div>
</div>
