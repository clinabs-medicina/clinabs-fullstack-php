<section class="footer-1">
    <div class="footer-column">
        <div class="footer-flex">
            <div class="footer-box flex-column" style="width: 23%;">
                <h4 class="h4 ico-set-1">LINKS</h4>
                <ul class="">
                    <li><a href="/" class="link-1">home</a></li>
                    <li><a href="/agendamento" class="link-1">agendar consulta</a></li>
                    <li><a href="/medicos" class="link-1">médicos</a></li>
                    <li><a href="/unidades" class="link-1">unidades</a></li>
                </ul>
            </div>
            <div class="footer-box flex-column" style="width: 23%;">
                <h4 class="h4 ico-set-1">Clinabs Medicina Integrativa</h4>
                <ul class="">
                    <li><a href="/docs/politica_privacidade.pdf" class="link-1" target="_blank">Políticas de privacidade</a></li>
                    <li><a href="/docs/termo_uso.pdf" class="link-1" target="_blank">Termo de Uso</a></li>
                    <li><a href="/docs/politica_agendamento.pdf" class="link-1" target="_blank">Políticas de agendamento</a></li>
                </ul>
            </div>
            <div class="footer-box flex-column" style="width: 25%;">
                <h4 class="h4 ico-set-1">ATENDIMENTO</h4>
                <p class="font-1" style="display: flex; align-items: center; gap: 0.8rem; flex-wrap: wrap;"><img src="/assets/images/ico-fone1.svg" alt="telefone" /><b><a href="tel:4133000790">(41) 3300-0790</a></b></p>
                <p class="p1">
                    <strong>Endereço: </strong>
                    <a href="https://maps.app.goo.gl/hNyNwg1NBjFa8DU88" class="street-link">
                        Rua Bruno Filgueira, 369<br />
                        cj.1604 | Edifício 109<br />
                        AGUA VERDE | CURITIBA- PR<br />
                        CEP: 80240-220
                    </a>
                </p>
                <p class="boxespaco-1">
                PLACE CENTRO DE MEDICINA INTEGRADA LTDA<br />
                    CNPJ: 55.087.626/0001-79
                </p>

                <div class="footer-box flex-column boxespaco-1">
                    <h4 class="boxespaco-1">SOCIAL MEDIA</h4>
                    <div class="gap">
                        <a href="<?=$FACEBOOK_LINK?>" target="_facebook"><img src="/assets/images/ico-footer-facebook.svg" target="_facebook" class="ico-socialfooter" alt="facebook" /></a>
                        <a href="<?=$INSTAGEM_LINK?>" target="_instagram"><img src="/assets/images/ico-instagram-medico.svg" class="ico-socialfooter" alt="instagram" /></a>
                        <a href="<?=$YOUTUBE_LINK?>" target="_youtube"><img src="/assets/images/ico-youtube-medico.svg" target="_youtube" class="ico-socialfooter" alt="youtube" /></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<section class="footer-2">
    
    <div class="footer-flex">
        <img src="/assets/images/selos-seguranca.png" title="Forma de Pagamento" alt="Forma de Pagamento" height="35px" />
    </div>
</section>
<div class="whatsapp-button">
    <a href="https://wa.me/554133000790?text=Seja+bem+vindo%21" target="_blank”"><img src="/assets/images/icons/whatsapp-icon.png" alt="Fale com Atendente" /></a>
</div>
    <?php 
        if($page->name == 'link_home'){
    ?>

    <div class="whatsapp-widget" data-aos="slide-left" data-aos-duration="800" style="
        border-top: solid 1px rgb(255 255 255 / 50%);
        background-color: rgb(0 0 0 / 20%);
        backdrop-filter: blur(2px);
        box-shadow: 0 2px 2px 0 rgb(0 0 0 / 14%), 0 3px 1px -2px rgb(0 0 0 / 12%), 0 1px 5px 0 rgb(0 0 0 / 20%);
        border-radius: 10px;
        overflow: hidden;
        ">
        <div class="whatsapp-header">
            <h5 class="m-0">
                <i class="fab fa-whatsapp"></i> WhatsApp
            </h5>
            <div class="rounded-circle btn btn-light">
                <i class="fas fa-times"></i>
            </div>       
        </div>
        <div class="whatsapp-body" style="background-image: url(/assets/images/whatsapp-bg.jpg); background-position: center;">
            <div class="border rounded-3 p-2 border-3 whatsapp-msg bg-white" style="box-shadow: 0 2px 2px 0 rgb(0 0 0 / 14%), 0 3px 1px -2px rgb(0 0 0 / 12%), 0 1px 5px 0 rgb(0 0 0 / 88%);">
                <i class="fas fa-user"></i> Atendimento:<br>
                Seja bem vindo. Clique no botão para falar com um de nossos atendentes.
            </div>
        </div>
        <div class="whatsapp-footer">
            <a  style="text-decoration: none;" href="https://wa.me/554133000790?text=<?= urlencode('Seja bem vindo. Clique no botão para falar com um de nossos atendentes.') ?>" target="_blank">
                <button>
                    Enviar mensagem agora <i class="fas fa-paper-plane"></i>
                </button>
            </a>
        </div>

    </div>
    <script>
        const element = document.querySelector('.whatsapp-header .btn');
        if (element) {
            element.addEventListener('click', function() {
            const widget = document.querySelector('.whatsapp-widget');
            if (widget) {
                // Remove a classe de entrada e adiciona a classe de saída
                widget.classList.remove('slide-in-left');
                widget.classList.add('slide-out-right');
                // Esperar a animação completar antes de remover o componente
                setTimeout(function() {
                    widget.remove();
                }, 800); // 800ms corresponde à duração da animação
            }
            });
        } else {
            console.error('Element not found!');
        }
    </script>
    <?php  } ?>
