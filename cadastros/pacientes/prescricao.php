<?php
 $stmt2 = $pdo->prepare("SELECT * FROM PACIENTES WHERE token = :token");
 $stmt2->bindValue(':token', $_GET['token']);

 $stmt2->execute();
 $_user = $stmt2->fetch(PDO::FETCH_OBJ);
 
 $presc = [];

 
?>
<section class="main" id="user-main">
  <div class="flex-container">
    <form id="formPrescricao" action="/formularios/prescricao.add.php" method="POST" class="form-prescricao" data-id="">
            <h3 class="form-title titulo-h1">Prontuário</h3>
            <section id="tabControl1" class="tabControl" data-lock="false">
                <!-- passo1 -->
                <div class="tab active" data-index="1" data-table="tabControl1">
                    <section class="form-grid area-full">
                        <section class="form-group">
                            <label for="nacionalidade">Prescrição</label>
                                <table id="tablePrescricao" class="presc-obs">
                                    <thead>
                                        <th width="32px">ID</th>
                                        <th>Data/Hora</th>
                                        <th>Prescrição</th>
                                        <th width="200px">Medicamento</th>
                                        <th width="64px">Frascos</th>
                                        <th width="80px">Ações</th>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $stmt = $pdo->prepare('SELECT 
                                            *,
                                            (SELECT nome FROM PRODUTOS WHERE id = produto_id) AS produto_nome
                                            FROM PRESCRICOES 
                                            WHERE paciente_token = :paciente_token;');
                                            $stmt->bindValue(':paciente_token', $_GET['token']);
                                            $stmt->execute();
    
                                            $i = 1;
    
                                            foreach($stmt->fetchAll(PDO::FETCH_OBJ) as $item) {
                                                $prescricao = base64_decode($item->prescricao);
                                                $presc[] = strip_tags($prescricao);
                                                $ts = date('d/m/Y H:m', strtotime($item->timestamp));
                                                echo "<tr data-id=\"".$item->id."\" data-id=\"".$item->medico_token."\" data-product=\"".$item->produto_id."\" data-frascos=\"".$item->frascos."\" data-prescricao=\"".$item->prescricao."\">";
                                                    echo "<td data-set=\"id\">{$i}</td>";
                                                    echo "<td data-set=\"timestamp\">{$ts}</td>";
                                                    echo "<td data-set=\"prescricao\">{$prescricao}</td>";
                                                    echo "<td data-set=\"produto_nome\">{$item->produto_nome}</td>";
                                                    echo "<td data-set=\"frascos\">{$item->frascos}</td>";
                                                    echo '<td class="td-act">
                                                    <div class="btns-act">
                                                        <div class="btns-table">';
                                                        echo "<button type=\"button\" title=\"Cancelar Prescrição\" class=\"btn-action\" onclick=\"action_btn_presc(this)\" data-action=\"presc-delete\"><img src=\"/assets/images/ico-delete.svg\" height=\"28px\"></button>";
                                                        echo "<button type=\"button\" title=\"Editar Prescrição\" class=\"btn-action\" onclick=\"action_btn_presc(this)\" data-action=\"presc-edit\"><img src=\"/assets/images/ico-edit.svg\" height=\"28px\"></button>";
                                                echo '</div>
                                                    </div>
                                                    </td>';
                                
                                                echo "</tr>";
    
                                                $i++;
                                            }
                
                                        ?>
                                    </tbody>
                                </table>
                            </section>
                        </section>
                </div>
            </section>
        </form>
    </div>
</section>