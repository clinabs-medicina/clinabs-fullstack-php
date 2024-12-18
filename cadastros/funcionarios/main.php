<section class="main">
    <section>
        <h1 class="titulo-h1">Funcionarios</h1>
    </section>
    <div class="flex-container produtos-flex">
        <table name="funcionario" id="tableFuncionarios" class="display dataTable no-footer">
        <thead>
         <tr>
         <th>Nome</th>
          <th width="128px">Telefone</th>
          <th width="128px">Setor</th>
          <th width="150px">Tempo de Sessão</th>
          <th width="128px">Ações</th>
         </tr>
         </thead>

         <tbody>
          <?php
          foreach($pdo->query("SELECT
            `nome_completo`,
            `telefone`,
            `setor`,
            `token`,
            `session_online`,
            `last_ping`,
            `first_ping`
            FROM
            `FUNCIONARIOS`
            ORDER BY
        `id` DESC")->fetchAll(PDO::FETCH_ASSOC) as $item) {

            $start_time = new DateTime($item['first_ping'] );
            $end_time = new DateTime($item['last_ping'] );
            $diff = $end_time->diff($start_time);

            $hh =  $diff->format('%H');
            $mm = $diff->format('%I');
            $ss = $diff->format('%S');


            if($item['session_online'] == 0) {
                $item['duration'] = "Offline";
            } else {
                if($hh > 0) {
                    if($mm > 0) {
                        $item['duration'] = "{$hh} hora(s), {$mm} minuto(s)";
                    }
                } else if($hh == 0 && $mm == 0) {
                    $item['duration'] = "{$ss} segundo(s)";
                }
                else {
                    $item['duration'] = "{$mm} minuto(s)";
                }
            }

            echo '<tr>';
                echo '<td><img src="'.Modules::getUserImage($item['token']).'" width="48px" style="border-radius: 48px;display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;"><span class="user-item '.$item['token'].'" data-online="'.$item['session_online'].'">'.$item['nome_completo'].'</td>';
                echo '<td>'.$item['telefone'].'</td>';
                echo '<td>'.$item['setor'].'</td>'; 
                echo '<td id="session_'.$item['token'].'">'.$item['duration'] .'</td>';
                echo '<td><i class="fa fa-pencil table-action-btn" aria-hidden="true" data-token="'.$item['token'].'" onclick="doAction(this, \'editar-perfil\')" title="Editar Perfil"></td>';
            echo '</tr>';
          }
          ?>
</tbody>
        </table>
    </div>
  </section>

  