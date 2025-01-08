<section class="main">
    <section>
        <h1 class="titulo-h1">Produtos</h1>
    </section>
    <div class="flex-container produtos-flex">
        <table name="paciente" id="tablePacientes" class="display dataTable">
        <thead>
             <tr>
             <th width="128px">Código</th>
              <th>Nome</th>
              <th width="128px">Valor</th>
              <th width="64px">Ações</th>
             </tr>
        </thead>

            <tbody>
              <?php
                  $stmt = $pdo->prepare("SELECT * FROM `PRODUTOS` ORDER BY `id` DESC");
                  $stmt->execute();
                  $produtos = $stmt->fetchAll(PDO::FETCH_OBJ);
                  
                  
                  foreach($produtos as $produto) {
                    echo '<tr>';
                        echo "<td></td>";
                    echo '</tr>';
                  }
              ?>
            </tbody>
        </table>
    </div>
  </section>

  