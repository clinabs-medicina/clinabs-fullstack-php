<section class="main">
    <section>
        <h1 class="titulo-h1">Fornecedores/Prestadores</h1>
    </section>
    <div class="flex-container produtos-flex">
        <table name="funcionario" id="tableUsuarios" class="display dataTable no-footer">
        <thead>
         <tr>
         <th>Nome</th>
          <th width="128px">Telefone</th>
          <th width="128px">Ações</th>
         </tr>
         </thead>

         <tbody>
          <?php
          foreach($pdo->query("SELECT
            `nome_completo`,
            `telefone`,
            `token`
            FROM
            `USUARIOS`
            ORDER BY
        `id` DESC")->fetchAll(PDO::FETCH_ASSOC) as $item) {
            echo '<tr>';
                echo '<td><img src="'.Modules::getUserImage($item['token']).'" width="48px" style="border-radius: 48px;display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;"><span class="paciente-nome">'.$item['nome_completo'].'</td>';
                echo '<td>'.$item['telefone'].'</td>';
                echo '<td><i class="fa fa-pencil table-action-btn" aria-hidden="true" data-token="'.$item['token'].'" onclick="doAction(this, \'editar-perfil\')" title="Editar Perfil"></td>';
            echo '</tr>';
          }
          ?>
</tbody>
        </table>
    </div>
  </section>

  