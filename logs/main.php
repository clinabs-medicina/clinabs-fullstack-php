<section class="main">
    <section>
        <h1 class="titulo-h1">Logs de Acessos</h1>
    </section>
    <div class="flex-container">
        <table name="funcionario" id="tableAcessLogs" class="display table">
        <thead>
         <tr>
            <th style="width: 256px !important">Data</th>
            <th>Nome</th>
            <th width="300px">Página</th>
            <th width="128px">IP</th>
            <th width="128px">Ações</th>
         </tr>
         </thead>

         <tbody>
          <?php
          foreach($pdo->query("SELECT * FROM `USER_LOGS` WHERE page NOT IN('/forms/schedule.calendar.php', '/form/form.update.image.php') AND nome_completo != '' AND LENGTH(data) > 10 ORDER BY `id` DESC")->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $uri = base64_decode($item['data']);
            $parsedUri = parse_url($uri);
            parse_str($parsedUri['path'], $parsedUri);

            if(!is_array($parsedUri)) {
              $parsedUri = json_decode($parsedUri, true);
            }
            

            $txtcontent = '';
            foreach($parsedUri as $key => $value) {
              if(is_array($value)) {
                foreach($value as $k => $v) {
                  $txtcontent .= $key.'['.$k.'] => '.$v.'<br>';
                }
              } else {
                $txtcontent .= $key.' => '.$value.'<br>';
              }
            }

            if($txtcontent != '') {
              echo '<tr>';
                echo '<td>'.date('d/m/Y H:i:s', strtotime($item['timestamp'])).'</td>';
                echo '<td>'.$item['nome_completo'].'</td>';
                echo '<td>'.$item['page'].'</td>';
                echo '<td>'.$item['ip'].'</td>'; 
                echo '<td><i data-content="'.base64_encode($txtcontent).'" class="fa fa-code" title="Ver Detalhes da Requisição Web" onclick="show_logs_details(this)"></i></td>';
            echo '</tr>';
            }
            
          }
          ?>
</tbody>
        </table>
    </div>
  </section>

  