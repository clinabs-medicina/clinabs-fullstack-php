<section class="main">
    <section>
         <h1 class="titulo-h1">Agendar Consulta</h1>
    </section>
   <form method="GET" action="/agendamento/medico" id="form_agendamento2">
      <section class="agendamento-box">
          <div class="agendamento-filters">
              <!-- <h3>Inicie a busca direcionando por especialidades ou indicações sintomáticas</h3> -->
              <h3>Selecione abaixo a especialidade que você procura ou queixa principal:</h3>
              <div class="filtros">
              <div class="borda">
                  <label >
                    <input <?= isset($_GET['filter_ag']) &&
  $_GET['filter_ag'] == 'especialidades'
    ? ' checked'
    : '' ?> type="radio" value="especialidades" id="filter_ag_especialidades" name="filter_ag" data-key="nome" data-title="Selecione uma Especialidade"> Filtrar por especialidade
                  </label>
              </div>

              <div class="borda">
                  <label class="borda" >
                    <input <?= isset($_GET['filter_ag']) &&
  $_GET['filter_ag'] == 'queixas'
    ? ' checked'
    : '' ?> type="radio" value="anamnese" id="filter_ag_anamnese" name="filter_ag" data-key="nome" data-title="Selecione uma Queixa Principal"> Filtrar por Queixa palavra Principal
                  </label>
              </div>

              <div class="borda">
                  <label >
                    <input <?= isset($_GET['filter_ag']) &&
  $_GET['filter_ag'] == 'medicos'
    ? ' checked'
    : '' ?> type="radio" value="medicos" id="filter_ag_medicos" name="filter_ag" data-key="nome_completo" data-title="Selecione um Médico"> Filtrar por nome do profissional
                  </label>
              </div>
                  
                  
                  <input type="hidden" name="data" id="dt_ag" value="">
              </div>

              <br><br>
              <div id="filter-select" style="position: relative;">
              
                  <select disabled name="select_filter" id="filter_ag_select" no-trigger="true" <?= isset(
  $_GET['select_filter']
)
  ? ' data-value="' . $_GET['select_filter'] . '"'
  : '' ?>>
                
                  
                  <?php

                  /*
                   * if(isset($_GET['select_filter']) && $_GET['filter_ag'] == 'medicos') {
                   *   $stmt = $pdo->prepare("SELECT * FROM MEDICOS WHERE id = :id");
                   *   $stmt->bindValue(':id', $_GET['select_filter']);
                   *
                   *   try {
                   *     $stmt->execute();
                   *     $row = $stmt->fetch(PDO::FETCH_OBJ);
                   *
                   *     echo "<option selected value=\"{$row->id}\">{$row->nome_completo}</option>";
                   *   } catch(Exception $error) {
                   *
                   *   }
                   * } else if(isset($_GET['select_filter']) && $_GET['filter_ag'] == 'queixas') {
                   *   $stmt = $pdo->prepare("SELECT * FROM ANAMNESE WHERE id = :id");
                   *   $stmt->bindValue(':id', $_GET['select_filter']);
                   *
                   *   try {
                   *     $stmt->execute();
                   *     $row = $stmt->fetch(PDO::FETCH_OBJ);
                   *
                   *     echo "<option selected value=\"{$row->id}\">{$row->nome}</option>";
                   *   } catch(Exception $error) {
                   *
                   *   }
                   * } else {
                   *   echo '<option selected disabled></option>';
                   * }
                   */
                  ?>
                  </select>

                  <section class="container-medicos curriculo container">

                  <?php if (
                    isset($_GET['select_filter']) &&
                    $_GET['filter_ag'] == 'medicos'
                  ) {
                    $stmt = $pdo->prepare(
                      'SELECT *,(SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS esp FROM MEDICOS WHERE id = :id'
                    );
                    $stmt->bindValue(':id', $_GET['select_filter']);

                    try {
                      $stmt->execute();
                      $medico = $stmt->fetch(PDO::FETCH_OBJ);
                    } catch (Exception $error) {
                    }
                  } ?>


<?php

echo '<div class="resumo_profissional sem_medico_selecionado container">';
echo '<div style="text-align: center;"><legend class="t_descricao_profissional"> Descrição Profissional:  </legend></div><br>';
echo '<div class="container_foto_crm">';
echo '<div class="container_foto">';
echo '<img class="foto_doutor " src="'
  . Modules::getUserImage($medico->token)
  . '" height="300px" width="300px">';
echo '</div>';
echo '<div style="width: 100%;" class="medico-perfil">';
echo '<legend class="t_nome_doutor"> '
  . $medico->nome_completo
  . '  </legend><br>';
echo '<legend class="t_crm"> '
  . $medico->tipo_conselho
  . ': '
  . $medico->num_conselho
  . '/'
  . $medico->uf_conselho
  . '</legend><br>';
echo '<legend class="especialidade_medico">'
  . $medico->esp
  . ' </legend><br>';

echo '<legend class="valores_consultas">' . number_format($medico->valor_consulta_online, 2, ',', '.') . '(online), ' . number_format($medico->valor_consulta, 2, ',', '.') . ' (presencial)</legend><br>';

if ($medico->age_min > 0 && $medico->age_max > 0) {
  echo '<legend class="faixa_etaria"><b>Faixa Etária: </b>'
    . $medico->age_min
    . " Anos a '.$medico->age_max.' Ano(s)</legend><br>";
} else {
  echo '<legend class="faixa_etaria"><b>Faixa Etária: </b>'
    . $medico->age_min . ' Ano(s)</legend><br>';
}

echo '<legend class="texto_resumo_profissional">' . (isset($medico->descricao) ? $medico->descricao : '') . '</legend><br><legend class="texto_resumo_profissional">' . nl2br((isset($medico->descricao) ? $medico->descricao : '')) . '</legend><br>';
echo '</div>';
echo '</div>';

echo '<div class="mini-curriculo">
            <button type="button">ver mais ▼</button>
            <div class="mini-curriculo-area">
             <div class="doctor-tags"></div>
              <div class="curriculo-area"></div>
            </div>
          </div>';
echo '</div>';

?>
    </section>
            </div>
              
          </div>

        </section>

        <div class="calendar-container">
          <div class="calendar-legend">
            <div class="legend"><button type="button" style="background-color: #05ad94;color: #fff"></button> Horários Disponiveis</div>
          </div>
          <div class="calendar-header">
            <button type="button" class="calendar-prev-btn">◀</button>
            <h2 id="calendar-info"></h2>
            <button type="button" class="calendar-next-btn">▶</button>
          </div>
        
        <div id="calendar" class="calendar"></div>
    </div>
        
        <?php if (isset($_REQUEST['medico_token'])) {
          echo '<input type="hidden" name="medico_token" value="'
            . $_REQUEST['medico_token']
            . '">';
        } ?>
  </form>