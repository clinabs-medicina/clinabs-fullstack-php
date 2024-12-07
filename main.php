<style>
.quem_somos {
    width: 90%;
    background: lightgrey;
    height: 500px;
    border-radius: 20px;
}
</style>

<section class="main">

    <section class="custom-container my-5 lh-base">
        <h2 class="text-center fs-1 fw-bold mb-5 text-dark"
            style="font-family: 'Poppins';font-size: 1.50rem !important;">Por que <spam class="text-primary"
                style="font-family: 'Poppins';">a Clinabs?</spam>
        </h2>
        <div class="row text-center">
            <div class="col-6 col-lg-3 col-md-6 col-sm-6" data-aos="fade-right" data-aos-duration="2000"
                data-aos-once="true">
                <img src="/assets/images/icons/icon-can.png" alt="Especialistas em CBD" class="custom-icon">
                <div class="rounded-5 ml-1 custom-box square-card">
                    <h3 class="fs-4 fw-semibold" style="font-family: 'Poppins';"><br>Especialistas em CBD</h3>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6 col-sm-6" data-aos="fade-right" data-aos-duration="1500"
                data-aos-once="true">
                <img src="/assets/images/icons/icon-call.png" alt="Atendimento rápido e fácil" class="custom-icon">
                <div class="rounded-5 ml-1 custom-box square-card rounded-4">
                    <h3 class="fs-4 fw-semibold" style="font-family: 'Poppins';"><br>Atendimento rápido e fácil</h3>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6 col-sm-6" data-aos="fade-left" data-aos-duration="1500"
                data-aos-once="true">
                <img src="/assets/images/icons/icon-cruz.png" alt="Diversas especialidades" class="custom-icon">
                <div class="rounded-5 ml-1 custom-box square-card rounded-4">
                    <h3 class="fs-4 fw-semibold" style="font-family: 'Poppins';"><br>Diversas especialidades</h3>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6 col-sm-6" data-aos="fade-left" data-aos-duration="2000"
                data-aos-once="true">
                <img src="/assets/images/icons/icon-hand.png" alt="Valores acessíveis" class="custom-icon">
                <div class="rounded-5 ml-1 custom-box square-card rounded-4">
                    <h3 class="fs-4 fw-semibold" style="font-family: 'Poppins';"><br>Valores acessíveis</h3>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6 col-sm-6" data-aos="fade-left" data-aos-duration="2000"
                data-aos-once="true">
                <img src="/assets/images/img1.png" alt="Valores acessíveis" class="custom-icon">
                <div class="rounded-5 ml-1 custom-box square-card rounded-4">
                    <h3 class="fs-4 fw-semibold" style="font-family: 'Poppins';"><br>Acolhimento</h3>
                </div>
            </div>

            <div class="col-6 col-lg-3 col-md-6 col-sm-6" data-aos="fade-left" data-aos-duration="2000"
                data-aos-once="true">
                <img src="/assets/images/img2.png" alt="Valores acessíveis" class="custom-icon">
                <div class="rounded-5 ml-1 custom-box square-card rounded-4">
                    <h3 class="fs-4 fw-semibold" style="font-family: 'Poppins';"><br>Atendimento Humanizado</h3>
                </div>
            </div>
        </div>
    </section>

    <section class="main">
        <!--
    <h2 class="text-center fs-1 fw-bold mb-5 text-dark" style="font-family: 'Poppins';font-size: 1.50rem !important;">Quem <spam class="text-primary" style="font-family: 'Poppins';">somos?</spam></h2>
    <section class="custom-container my-5 lh-base quem_somos">
    </section>
    -->

        <section class="container-fluid">
            <h1 class="titulo-h1">Profissionais de Saúde</h1>

            <section class="container-medicos">
                <input type="search" class="form-control form-search" id="prontuario-search" data-search=".medico-item">
                <?php 

        echo '<div class="row">';

        $grupos = $medicos->getGroups();
        foreach($medicos->getAllWithGroup() as $group => $medicos) {
            echo "<p><h2 class=\"titulo-h2\">{$grupos[$group]}</h2></p>";
            echo "<div class=\"row mt-4 row-cols-sx-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-2 row-cols-xxl-3\">";
            $mds = [];

            foreach($medicos as $medico) {
                $mds[$medico->nome_completo] = $medico;
            }

            ksort($mds);

            foreach($mds as $m => $medico) {
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
            echo "</div>";
         } 
        
        echo '</div>';
        
        ?>
            </section>

            <section>
                <div class="help-flex">
                    <p><b>DÚVIDAS?</b></p>
                    <h5>FALE AGORA COM UM ESPECIALISTA</h5>
                    <p>Estamos online!</p>
                    <a href="https://wa.me/554133000790?text=Seja+bem+vindo%21" target="_blank"><button>CONTATE-NOS
                            AGORA!</button></a>
                </div>
            </section>
            <?php
if(in_array($_SERVER['REMOTE_ADDR'], $ALLOWED_IP)) {
?>
            <div class="row-columns">
                <section>
                    <h1 class="titulo-h1">Selecione a sua Patologia:</h1>
                </section>
                <div class="box-container">
                    <?php
                $stmtx = $pdo->query("SELECT token,id,anamnese,nome_completo FROM MEDICOS WHERE status = 'ATIVO' ORDER BY nome_completo ASC");
                $rows = $stmtx->fetchAll(PDO::FETCH_OBJ);

                $especialidades = [];

                foreach ($rows as $row) {
                    $stmty = $pdo->query("SELECT calendario,medico_token,(SELECT anamnese FROM MEDICOS WHERE token = medico_token) AS esp FROM AGENDA_MEDICA WHERE medico_token = '{$row->token}' GROUP BY medico_token");
                    $calendario = $stmty->fetch(PDO::FETCH_OBJ);

                    $result_medicos = [];

                    try {
                        $calendar = json_decode($calendario->calendario, true);

                        $datas = [];

                        foreach ($calendar as $data) {
                            foreach ($data as $d) {
                                if (strtotime("{$d['date']} {$d['time']}") > strtotime(date('Y-m-d H:i'))) {
                                    $datas[] = "{$d['date']} {$d['time']}";
                                }
                            }
                        }
                    } catch (Exception $ex) {
                    }

                    if (count($datas) > 0) {
                        $anamneses = json_decode($calendario->esp);

                        foreach ($anamneses as $anamnese) {
                            $stmtz = $pdo->query("SELECT nome FROM ANAMNESE WHERE id = '{$anamnese}'");
                            $esp = $stmtz->fetch(PDO::FETCH_OBJ);

                            $results['results'][] = ['id' => $anamnese, 'text' => $esp->nome];
                        }

                        $x = [];

                        foreach ($results['results'] as $i) {
                            $x[$i['id']] = $i;
                        }

                        $results['results'] = [];

                        foreach ($x as $y) {
                            $results['results'][] = $y;
                        }
                    }
                }

                foreach ($results['results'] as $queixa) {
                    echo "<a class=\"item-box\" href=\"/agendamento/?filter_ag=anamnese&select_filter={$queixa['id']}&filter_key=nome&filter_value={$queixa['text']}\">
                            <div class=\"box-item\">
                                <img src=\"/assets/images/ico-paciente.svg\" height=\"64px\"> 
                            </div>
                            <small>{$queixa['text']}</small>
                        </a>";
                }
             ?>
                </div>


                <div class="container-box">
                    <div class="row-col1">
                        <div class="column1">
                            <div class="columns2">
                                <h2 class="titulo-h2">O que é medicina canábica?</h2>
                            </div>

                            <div class="row">
                                A cannabis medicinal é uma opção terapêutica que auxilia no tratamento de diversas
                                patologias. Seu principal componente é o canabidiol (CBD), substância responsável por
                                ativar e regular o sistema nervoso e imunológico, além do THC (Tetrahidrocanabinol).
                            </div>
                            <div class="row-uls">
                                <ul class="ul-column">
                                    <li>Alcoolismo</li>
                                    <li>Alzheimer</li>
                                    <li>Anorexia</li>
                                    <li>Autismo</li>
                                    <li>Ansiedade</li>
                                    <li>Bipolaridade</li>
                                    <li>Doença de Crohn</li>
                                    <li>Depressão</li>
                                    <li>Diabetes</li>
                                    <li>Dores Crônicas</li>
                                </ul>

                                <ul class="ul-column">
                                    <li>Esclerose Múltipla</li>
                                    <li>Epilepsia</li>
                                    <li>Enxaqueca</li>
                                    <li>Fibromialgia</li>
                                    <li>Glaucoma</li>
                                    <li>Intestino Irritável</li>
                                    <li>Insnia</li>
                                    <li>Náuseas</li>
                                    <li>Parkinson</li>
                                    <li>TDAH</li>
                                </ul>
                            </div>
                        </div>

                        <div class="row-col2"><img alt="Img" src=""></div>
                    </div>
                </div>

                <div class="columns2">
                    <h2 class="titulo-h2">Como funciona o CLINABS?</h2>
                </div>
                <div class="container-box-columns">
                    <div class="column">
                        <h4>Agendamento</h4>
                        <p>Após o pagamento você receberá um link de nossa agenda via whatsapp onde poderá escolher o
                            médico e horário que melhor se adapte a sua agenda.</p>
                    </div>
                    <div class="column">
                        <h4>Pagamento</h4>
                        <p>Assim que clcar em agendar consulta você será direcionado para o pagamento.
                            Pague de forma Segura através de <b>cartão de crédito</b> ou <b>Pix</b>,
                            o valor da consulta é apartir de R$ *.***
                        </p>
                    </div>

                    <div class="column">
                        <h4>Consulta</h4>
                        <p><b>15 minutos</b> antes da consulta você receberá um link para atendimento via telemedicina
                            via whatsapp e e-mail.</p>
                    </div>

                    <div class="column">
                        <h4>Pós-Consulta</h4>
                        <p>Caso um de nossos médicos te prescreva um medicamento, lhe daremos total suporte para
                            obtenção de medicamentos, incluindo auxílio na autorização da <b>ANVISA</b> e orientação
                            legal.</p>
                    </div>
                </div>

                <?php
}
?>
        </section>