    <section class="page">
        <div class="page-flex">
            <div class=" titulo-h1">
                <h1>Você é paciente ou profissional de saúde?</h1>
            </div>
            
            

            <?php
            if($user->tipo == 'FUNCIONARIO' && $user->perms->add_paciente == 1){
                echo '<div class="page-box" data-link="/cadastro/cadastro-paciente">
                <div class="page-boxlement">
                    <div class="page-boxlement-ico">
                        <img src="/assets/images/ico-paciente.svg" alt="paciente">
                    </div>
                    <div>
                        <h2 class="titulo-h2">Paciente</h2>
                        <p>Tenho o interesse em descobrir os produtos CBD de alta qualidade disponíveis no mercado, quero consultar com médicos de alto nível e experiência.</p>
                    </div>
                </div>
            </div>';
            }
            
            
            if($user->tipo == 'FUNCIONARIO' && $user->perms->add_medico == 1){
                echo '<div class="page-box" data-link="/cadastro/medico">
                    <div class="page-boxlement">
                        <div class="page-boxlement-ico">
                            <img src="/assets/images/ico-medico.svg"  alt="Médico">
                        </div>
                        <div>
                            <h2 class="titulo-h2">Médico</h2>
                            <p>Sou profissional de saúde, minha intenção é utilizar este site como plataforma para prescrever produtos contendo cannabidiol (CBD).</p>
                        </div>
                    </div>
                </div>';
            }
            
            if($user->tipo == 'FUNCIONARIO' && $user->perms->add_funcionario == 1) {
                    echo '<div class="page-box" data-link="/cadastro/funcionario">
                            <div class="page-boxlement">
                                <div class="page-boxlement-ico">
                                    <img src="/assets/images/user1.jpg" height="95px"  alt="Médico">
                                </div>
                                <div>
                                    <h2 class="titulo-h2">Funcionário</h2>
                                    <p>Cadastro de Funcionários para acesso as Funcionalidades médicas do sistema.</p>
                                </div>
                            </div>
                        </div>';
              
               echo '<div class="page-box" data-link="/cadastro/usuario">
                            <div class="page-boxlement">
                                <div class="page-boxlement-ico">
                                    <img src="/assets/images/user1.jpg" height="95px"  alt="Usuário">
                                </div>
                                <div>
                                    <h2 class="titulo-h2">Usuário</h2>
                                    <p>Cadastro de Usuário Externo.</p>
                                </div>
                            </div>
                        </div>';
        } else {
            echo '<div class="page-box" data-link="/cadastro/cadastro-paciente">
                <div class="page-boxlement">
                    <div class="page-boxlement-ico">
                        <img src="/assets/images/ico-paciente.svg" alt="paciente">
                    </div>
                    <div>
                        <h2 class="titulo-h2">Paciente</h2>
                        <p>Tenho o interesse em descobrir os produtos CBD de alta qualidade disponíveis no mercado, quero consultar com médicos de alto nível e experiência.</p>
                    </div>
                </div>
            </div>';
            
            echo '<div class="page-box" data-link="/cadastro/medico">
                    <div class="page-boxlement">
                        <div class="page-boxlement-ico">
                            <img src="/assets/images/ico-medico.svg"  alt="Médico">
                        </div>
                        <div>
                            <h2 class="titulo-h2">Médico</h2>
                            <p>Sou profissional de saúde, minha intenção é utilizar este site como plataforma para prescrever produtos contendo cannabidiol (CBD).</p>
                        </div>
                    </div>
                </div>';
        }
    ?>

        </div>
    </section> 