<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.com/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <title>Bootstrap Example</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </head>
  <body class="p-3 m-0 border-0 bd-example m-0 border-0">

    <!-- Example Code -->
    
        
    <div id="carouselExampleCaptions" class="carousel slide">
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="" aria-label="Diapositivo 1"></button>
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Diapositivo 2" class="active" aria-current="true"></button>
        <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Diapositivo 3" class=""></button>
      </div>
      <div class="carousel-inner">
        <div class="carousel-item">
          <svg class="bd-placeholder-img bd-placeholder-img-lg d-block w-100" width="800" height="400" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Espaço reservado: Primeiro slide" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#777"></rect><text x="50%" y="50%" fill="#555" dy=".3em"></text></svg>
          <div class="carousel-caption d-none d-md-block">
          
            <?php 
            include '../libs/Modules.php';

            $medico = new stdClass(); 
           
            $medico->nome_completo = 'José';
            $medico->token = 'das9ewfjsa9da'; ?> 
           
           <?php
           echo '<img class="foto_doutor" src="'.Modules::getUserImage($medico->token).'" height="50px" width="50px">';   
           ?>

            <?=$medico->nome_completo?>

          </div>
        </div>

        <div class="carousel-item active">
          <svg class="bd-placeholder-img bd-placeholder-img-lg d-block w-100" width="800" height="400" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Espaço reservado: Segundo slide" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#666"></rect><text x="50%" y="50%" fill="#444" dy=".3em">Second slide</text></svg>
          <div class="carousel-caption d-none d-md-block">
            
          <div class="col-6 text-center medico-item p-0 aos-init aos-animate" data-aos="fade-up" data-aos-duration="1000" data-aos-once="true">
                    <div class="doctor-card rounded-pill ms-3 mt-5">
                        <div class="mt-5">
                            <img class="border border-4 border-primary rounded-circle" src="/data/images/profiles/f250c1d25446b77a566f875bb9672c61.jpg" alt="Profile Picture" style="z-index: 1; margin-top: -45px; margin-left: -20px;">
                        </div>                        
                        <div class="doctor-info">
                            <div class="py-3 pe-5 text-start">
                                <h5 style="font-family: 'Poppins';">Dr. ANDRE LUIZ BASSO</h5>
                                <p>PSIQUIATRIA</p>
                            </div>
                        </div>
                    </div>
                        <a class="btn btn-light ms-5" href="https://api.whatsapp.com/send/?phone=554133000790&amp;text=Olá Gostaria de Agendar uma Consulta com Dr. ANDRE LUIZ BASSO&amp;type=phone_number&amp;app_absent=0">AGENDAR CONSULTA</a>
            </div>
          </div>
        </div>


        <div class="carousel-item">
          <svg class="bd-placeholder-img bd-placeholder-img-lg d-block w-100" width="800" height="400" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="Espaço reservado: Terceiro slide" preserveAspectRatio="xMidYMid slice" focusable="false"><title>Placeholder</title><rect width="100%" height="100%" fill="#555"></rect><text x="50%" y="50%" fill="#333" dy=".3em">Third slide</text></svg>
          <div class="carousel-caption d-none d-md-block">
            <h5><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Terceiro rótulo de slide</font></font></h5>
            <p><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Algum conteúdo representativo de espaço reservado para o terceiro slide.</font></font></p>
          </div>
        </div>
      </div>
      <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Anterior</font></font></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Próximo</font></font></span>
      </button>
    </div>
    
      
    <!-- End Example Code -->
  </body>
</html>