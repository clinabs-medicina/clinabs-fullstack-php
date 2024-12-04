<section class="main">
  <section>
      <h1 class="titulo-h1">Profissionais de Saúde</h1>
  </section>

  <section class="p-2 mb-3 container-medicos">
    <input type="search" class="form-control form-search" id="prontuario-search" data-search=".medico-item">
    <?php 
    

    echo '<div class="row mt-4 row-cols-sx-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-2 row-cols-xxl-3">';

    foreach ($medicos->getAll() as $medico) {
        $img = Modules::user_get_image($medico->token);
        $especialidade = $medico->esp;
        $sexo = $medico->identidade_genero == 'F' ? 'Dra. ' : 'Dr. ';
        $nome = $medico->nome_completo;
        $description = strlen($medico->descricao) > 300 ? substr($medico->descricao, 0, 300) . '...' : $medico->descricao;

        if ($medico->status == 'ATIVO') {
            $prefixo = strtoupper($medico->identidade_genero) == 'FEMININO' ? 'Dra.' : 'Dr.';
            $id = uniqid();


            $link = ($medico->especialidade == 68) ? 'https://api.whatsapp.com/send/?phone=554133000790&text=Olá Gostaria de Agendar uma Consulta com '.$prefixo.' '.$nome.'&type=phone_number&app_absent=0':'/agendamento/?filter_ag=medicos&select_filter='.$medico->id.'&filter_key=nome_completo&filter_value='.$medico->nome_completo;
                

            echo '
            <div class="col text-center medico-item p-0" data-aos="fade-up" data-aos-duration="1000" data-aos-once="true">
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
    
    echo '</div>';
    
    ?>
  </section>
  <br>
  <br>
</section>

  