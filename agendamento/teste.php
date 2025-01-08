<style>
.titulo{
  font-weight: bold;
  margin-left: 2%;
  margin-right: 2%;
}

.t_descricao_profissional{
font-weight: bold;
font-size: 18px;
text-decoration: underline;
margin-left: 2%;
margin-right: 2%;
}

.t_nome_doutor{
font-weight: bold;
font-size: 18px;
margin-right: 2%;
line-height: 0;
}

.texto_resumo_profissional{
  text-align: justify;
  margin-left: 2%;
  margin-right: 2%;
}

.t_crm{
font-weight: bold;
font-size: 15px;
margin-right: 2%;
line-height: 0;
}

.resumo_profissional{
  background: #f0f5d5;
  border-radius: 30px;
}

.foto_doutor{
  width: 150px;
    height: auto;
    border-radius: 25px;
    text-align: center;
}

.especialidade_medico{
  font-weight: bold;
  font-size: 15px;
    margin-right: 2%;
    line-height: 0;
}

.curriculo{
  width: 100% !important;
}

.sem_medico_selecionado{
  display: none;
}
</style> 


<section class="main">
    <section>
         <h1 class="titulo-h1">Agendar Consulta</h1>
    </section>
   <form method="GET" action="/agendamento/medico" id="form_agendamento2">
      <section class="agendamento-box">
          <div class="agendamento-filters">
              <h3>Inicie a busca direcionando por especialidades ou indicações sintomáticas</h3>
              <div class="filtros">
                  
                  <label>
                    <input <?=(isset($_GET['filter_ag']) && $_GET['filter_ag'] == 'medicos' ? ' checked':'')?> type="radio" value="medicos" id="filter_ag_medicos" name="filter_ag" data-key="nome_completo" data-title="Selecione um Médico"> Filtrar por nome do profissional
                  </label>

                  <label>
                    <input <?=(isset($_GET['filter_ag']) && $_GET['filter_ag'] == 'especialidades' ? ' checked':'')?> type="radio" value="especialidades" id="filter_ag_especialidades" name="filter_ag" data-key="nome" data-title="Selecione uma Especialidade"> Filtrar por especialidade
                  </label>
                  
                  <label>
                    <input <?=(isset($_GET['filter_ag']) && $_GET['filter_ag'] == 'queixas' ? ' checked':'')?> type="radio" value="anamnese" id="filter_ag_anamnese" name="filter_ag" data-key="nome" data-title="Selecione uma Queixa"> Filtrar por Queixa Principal
                  </label>
                  
                  <input type="hidden" name="data" id="dt_ag" value="">
              </div>

              <div id="filter-select">
              
                  <select disabled name="select_filter" id="filter_ag_select" no-trigger="true" <?=(isset($_GET['select_filter']) ? ' data-value="'.$_GET['select_filter'].'"':'')?>>
           <?php
                  /*
                      if(isset($_GET['select_filter']) && $_GET['filter_ag'] == 'medicos') {
                        $stmt = $pdo->prepare("SELECT * FROM MEDICOS WHERE id = :id");
                        $stmt->bindValue(':id', $_GET['select_filter']);

                        try {
                          $stmt->execute();
                          $row = $stmt->fetch(PDO::FETCH_OBJ);

                          echo "<option selected value=\"{$row->id}\">{$row->nome_completo}</option>";
                        } catch(Exception $error) {

                        }
                      } else if(isset($_GET['select_filter']) && $_GET['filter_ag'] == 'queixas') {
                        $stmt = $pdo->prepare("SELECT * FROM ANAMNESE WHERE id = :id");
                        $stmt->bindValue(':id', $_GET['select_filter']);

                        try {
                          $stmt->execute();
                          $row = $stmt->fetch(PDO::FETCH_OBJ);

                          echo "<option selected value=\"{$row->id}\">{$row->nome}</option>";
                        } catch(Exception $error) {

                        }
                      } else {
                        echo '<option selected disabled></option>';
                      }
                        */
                    ?>
                  </select>

                  <section class="container-medicos curriculo">

                  <?php
                  if(isset($_GET['select_filter']) && $_GET['filter_ag'] == 'medicos') {
                    $stmt = $pdo->prepare("SELECT *,(SELECT nome FROM ESPECIALIDADES WHERE id = especialidade) AS esp FROM MEDICOS WHERE id = :id");
                    $stmt->bindValue(':id', $_GET['select_filter']);

                    try {
                      $stmt->execute();
                      $medico = $stmt->fetch(PDO::FETCH_OBJ);
                    } catch(Exception $error) {

                    }
                  }
                  ?>
<?php if ($medico->nome_completo == '' ){
echo '<div class="resumo_profissional sem_medico_selecionado">';

echo '<div style="text-align: center;"><label class="t_descricao_profissional"> Descrição Profissional:  </label></div><br>';

echo '<div style="display: flex; width: 100%">';

echo '<div style="width: 15%; text-align: center;">';
echo '<img class="foto_doutor" src="'.Modules::getUserImage($medico->token).'" height="300px" width="300px">';     
echo '</div>';

echo '<div style="width: 85%;">';
echo '<label class="t_nome_doutor"> '.$medico->nome_completo.'  </label><br>';
echo '<label class="t_crm"> '.$medico->tipo_conselho.': '.$medico->num_conselho.' </label><br>';
echo '<label class="especialidade_medico">'.$medico->esp.' </label><br>';
echo '</div>';

echo '</div>';

echo '<label class="texto_resumo_profissional">'.$medico->descricao.'</label><br>';

echo '</div>';


}

else {

  
  echo '<div class="resumo_profissional">';

  echo '<div style="text-align: center;"><label class="t_descricao_profissional"> Descrição Profissional:  </label></div><br>';
  
 echo '<div style="display: flex; width: 100%">';
  
 echo '<div style="width: 15%; text-align: center;">';
 echo  '<img class="foto_doutor" src="'.Modules::getUserImage($medico->token).'" height="300px" width="300px">';     
 echo  '</div>';
  
  echo '<div style="width: 85%;">';
  echo '<label class="t_nome_doutor">'.$medico->nome_completo.'</label><br>';
  echo '<label class="t_crm">'.$medico->tipo_conselho.': '.$medico->num_conselho.' </label><br>';
  echo '<label class="especialidade_medico">'.$medico->esp.'</label><br>';
 echo  '</div>';
  
  echo '</div>';
  
  echo '<label class="texto_resumo_profissional">'.$medico->descricao.'</label><br>';
  
  echo '</div>';

}

?>

<!-- //******** CARROSSEL ESPECIALIDADES  ********/ -->

<div id="carouselExampleDark" class="carousel carousel-dark slide">
      <div class="carousel-indicators">
        <button
          type="button"
          data-bs-target="#carouselExampleDark"
          data-bs-slide-to="0"
          class="active"
          aria-current="true"
          aria-label="Diapositivo 1"
        ></button>
        <button
          type="button"
          data-bs-target="#carouselExampleDark"
          data-bs-slide-to="1"
          aria-label="Diapositivo 2"
        ></button>
        <button
          type="button"
          data-bs-target="#carouselExampleDark"
          data-bs-slide-to="2"
          aria-label="Diapositivo 3"
        ></button>
      </div>
      <div class="carousel-inner">
        <div class="carousel-item active" data-bs-interval="10000">
          <svg
            class="bd-placeholder-img bd-placeholder-img-lg d-block w-100"
            width="800"
            height="400"
            xmlns="http://www.w3.org/2000/svg"
            role="img"
            aria-label="Espaço reservado: Primeiro slide"
            preserveAspectRatio="xMidYMid slice"
            focusable="false"
          >
            <title>Placeholder</title>
            <rect width="100%" height="100%" fill="#f5f5f5"></rect>
            <text x="50%" y="50%" fill="#aaa" dy=".3em">First slide</text>
          </svg>
          <div class="carousel-caption d-none d-md-block">
            <h5>
              <font style="vertical-align: inherit; color: black"
                ><font style="vertical-align: inherit; color: black"
                  >Primeiro rótulo do slide</font
                ></font
              >
            </h5>
            <p>
              <font style="vertical-align: inherit"
                ><font style="vertical-align: inherit"
                  >Algum conteúdo representativo de espaço reservado para o
                  primeiro slide.</font
                ></font
              >
            </p>
          </div>
        </div>
        <div class="carousel-item" data-bs-interval="2000">
      
        <div class="col text-center medico-item p-0 aos-init aos-animate" data-aos="fade-up" data-aos-duration="1000" data-aos-once="true">
                    <div class="doctor-card rounded-pill ms-3 mt-5">
                        <div class="mt-5">
                            <img class="border border-4 border-primary rounded-circle" src="/data/images/profiles/603e0c5b9496888fac573b70f4a082ce.jpg" alt="Profile Picture" style="z-index: 1; margin-top: -45px; margin-left: -20px;">
                        </div>                        
                        <div class="doctor-info">
                            <div class="py-3 pe-5 text-start">
                                <h5 style="font-family: 'Poppins';">Dra. CAROL MOTINHO SILVA SÁ</h5>
                                <p>CLINICO GERAL</p>
                            </div>
                        </div>
                    </div>
                        <a class="btn btn-light ms-5" href="https://api.whatsapp.com/send/?phone=554133000790&amp;text=Olá Gostaria de Agendar uma Consulta com Dra. CAROL MOTINHO SILVA SÁ&amp;type=phone_number&amp;app_absent=0">AGENDAR CONSULTA</a>
                </div>

        </div>
        <div class="carousel-item">
          <svg
            class="bd-placeholder-img bd-placeholder-img-lg d-block w-100"
            width="800"
            height="400"
            xmlns="http://www.w3.org/2000/svg"
            role="img"
            aria-label="Espaço reservado: Terceiro slide"
            preserveAspectRatio="xMidYMid slice"
            focusable="false"
          >
            <title>Placeholder</title>
            <rect width="100%" height="100%" fill="#e5e5e5"></rect>
            <text x="50%" y="50%" fill="#999" dy=".3em">Third slide</text>
          </svg>
          <div class="carousel-caption d-none d-md-block">
            <h5>
              <font style="vertical-align: inherit"
                ><font style="vertical-align: inherit"
                  >Terceiro rótulo de slide</font
                ></font
              >
            </h5>
            <p>
              <font style="vertical-align: inherit"
                ><font style="vertical-align: inherit"
                  >Algum conteúdo representativo de espaço reservado para o
                  terceiro slide.</font
                ></font
              >
            </p>
          </div>
        </div>
      </div>
      <button
        class="carousel-control-prev"
        type="button"
        data-bs-target="#carouselExampleDark"
        data-bs-slide="prev"
      >
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden"
          ><font style="vertical-align: inherit"
            ><font style="vertical-align: inherit">Anterior</font></font
          ></span
        >
      </button>
      <button
        class="carousel-control-next"
        type="button"
        data-bs-target="#carouselExampleDark"
        data-bs-slide="next"
      >
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden"
          ><font style="vertical-align: inherit"
            ><font style="vertical-align: inherit">Próximo</font></font
          ></span
        >
      </button>
    </div>

<!-- **************************************************** -->




        <!-- <input type="search" class="form-control form-search" id="prontuario-search" data-search=".medico-item"> -->
        
       <?php 
        //Modules::getUserImage($medico->token)
        echo '<div class="row mt-4" id="medicos-list-group">';

     
        // foreach (array_slice(shuffle($medicos->getAll()), 0, 3) as $medico) {

        /*

          foreach ($medicos->getAll() as $medico) {
           
            $img = Modules::user_get_image($medico->token);
            $especialidade = $medico->esp;
            $sexo = $medico->identidade_genero == 'F' ? 'Dra. ' : 'Dr. ';
            $nome = $medico->nome_completo;
            $description = strlen($medico->descricao) > 300 ? substr($medico->descricao, 0, 300) . '...' : $medico->descricao;

            if ($medico->status == 'ATIVO') {
                $prefixo = strtoupper($medico->identidade_genero) == 'FEMININO' ? 'Dra.' : 'Dr.';
                $id = uniqid();
                
                $link = ($medico->disponibilizar_agenda == 0) ? 'https://api.whatsapp.com/send/?phone=554133000790&text=Olá Gostaria de Agendar uma Consulta com '.$prefixo.' '.$nome.'&type=phone_number&app_absent=0':'/agendamento/?filter_ag=medicos&select_filter='.$medico->id.'&filter_key=nome_completo&filter_value='.$medico->nome_completo;
                
                echo '
                <div class="col-6 text-center medico-item p-0" data-aos="fade-up" data-aos-duration="1000" data-aos-once="true">
                    <div class="doctor-card rounded-pill ms-3 mt-5">
                        <div class="mt-5">
                            <img class="border border-4 border-primary rounded-circle" src="'.$img.'" alt="Profile Picture" class="img-fluid rounded-circle" style="z-index: 1; margin-top: -45px; margin-left: -20px;">
                        </div>                        
                        <div class="doctor-info">
                            <div class="py-3 pe-5 text-start">
                                <h5 style="font-family: \'Poppins\';">'.$prefixo. ' '. $nome.'</h5>
                                <p>'.$especialidade.'</p>
                            </div>
                        </div>
                    </div>
                        <a class="btn btn-light ms-5" href="'. $link .'">AGENDAR CONSULTA</a>
                </div>
                ';
            }
        } 
        
        */
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
        
        <?php
        if(isset($_REQUEST['medico_token'])) {
            echo '<input type="hidden" name="medico_token" value="'.$_REQUEST['medico_token'].'">';
        }
        ?>
  </form>

