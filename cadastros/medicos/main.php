<section class="main">

    <section>

        <h1 class="titulo-h1">Médicos</h1>

    </section>

    <div class="flex-container produtos-flex">

        <table name="medico" id="tableMedicos" class="display dataTable no-footer">

        <thead>

         <tr>

         <th width="220px">Nome</th>
          <th width="180px">Especialidade</th>
          <th width="128px">Conselho</th>
          <th width="100px">Nº Conselho</th>
          <th width="128px">Situação Cadastral</th>
          <th width="80px">Ações</th>

         </tr>

         </thead>



         <tbody>

          <?php
          
          if (session_status() === PHP_SESSION_NONE) {
            session_start();
          }
          if(isset($_SESSION['userObj'])) {
            try {
                $user = (object) $_SESSION['userObj'];
            } catch (PDOException $e) {
        
            }
          }
        

          foreach($pdo->query("SELECT
            `nome_completo`,
            (SELECT nome FROM ESPECIALIDADES WHERE ESPECIALIDADES.id = MEDICOS.especialidade) AS esp,
            `tipo_conselho`,
            `uf_conselho`,
            `num_conselho`,
            `status`,
            `token`,
            `identidade_genero`,
            `session_online`
            FROM
            `MEDICOS`
            ORDER BY
        `id` DESC")->fetchAll(PDO::FETCH_ASSOC) as $item) {
            $prefixo = strtoupper($item['identidade_genero']) == 'FEMININO' ? 'Dra.':'Dr.';
            echo '<tr id="'.$item['token'].'">';

                echo '<td><img src="'.Modules::getUserImage($item['token']).'" width="48px" style="border-radius: 48px;display:inline-flex; vertical-align:middle; margin: -5px 5px 0 5px;"><span class="user-item" id="USER_'.$item['token'].'" data-online="'.$item['session_online'].'">'.$prefixo.' '.$item['nome_completo'].'</span></td>';

                echo '<td>'.ucwords(strtolower($item['esp'])).'</td>';
                echo '<td>'.$item['tipo_conselho'].'-'.$item['uf_conselho'].'</td>';
                echo '<td>'.$item['num_conselho'].'</td>';
                echo '<td>'.($item['status'] == 'ATIVO' ? 'REGULAR':$item['status']).'</td>';
                echo '<td>
                <button class="btn-action" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="editar-perfil"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M0 24L10.3735 20.5735C10.0771 19.9797 3.85153 13.8611 3.56456 13.7061L0 24ZM23.5432 4.96696L18.992 0.417349C18.0576 -0.231234 16.5777 -0.169237 15.9872 0.925246L23.0522 8.07635C24.174 7.47545 24.2499 5.92792 23.5432 4.96696ZM13.2835 3.56011L20.4363 10.8185L22.1178 9.10168C21.7739 8.4531 15.4962 2.58485 15.0527 1.86235L13.2835 3.56011ZM4.38989 12.5854L6.07612 14.2283L13.9476 6.34043L12.2329 4.6379C11.3957 5.42002 4.60571 12.1991 4.38989 12.5854ZM9.84938 18.0197L11.5024 19.696C12.0669 19.3908 14.7421 16.5127 15.4488 15.8093C16.7532 14.4978 18.1785 13.1672 19.3881 11.8343L17.6805 10.1103L9.84938 18.0197ZM7.11252 15.1607C7.31411 15.5804 8.50466 16.775 8.92207 16.98L16.6868 9.13983L14.9531 7.36815C14.4954 7.65906 13.4732 8.79408 13.0108 9.26144L7.11252 15.1607Z" fill="black"/>
                </svg> </button>
                <button class="btn-action" onclick="actionBtn(this)" data-token="'.$item['token'].'" data-action="delete-medico"><svg width="24" height="24" viewBox="0 0 38 38" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M18.9801 1C29.0101 1 37.1201 9.14001 37.1201 19.14C37.1201 29.17 29.0101 37.31 18.9801 37.31C8.95006 37.31 0.810059 29.17 0.810059 19.14C0.810059 9.13001 8.94006 1 18.9801 1Z" fill="#FC5C5C" stroke="#FC5C5C" stroke-width="1.0205" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"></path>
                <path fill-rule="evenodd" clip-rule="evenodd" d="M11 13.7381L13.7285 11L19 16.234L24.2529 11L27 13.7381L21.7285 18.9721L27 24.2433L24.2529 27L19 21.7101L13.7285 27L11 24.2433L16.2529 18.9721L11 13.7381Z" fill="white"></path>
                </svg></button>
                </td>';

            echo '</tr>';

          }

          ?>

</tbody>

        </table>

    </div>

  </section>