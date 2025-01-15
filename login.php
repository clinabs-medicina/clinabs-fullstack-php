<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <title>Clinabs | Sistema de consultas online para pacientes que dependem de medicamentos com canabidiol.</title>
      <link rel="icon" type="image/x-icon" href="/assets/images/favicon.ico"/>
      <meta charset="utf-8" />
      <meta name="X-App-Version" content="1.0.2"/>
      <meta name="viewport" content="width=device-width, initial-scale=1"/>
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.9.0/dist/sweetalert2.min.css">
      <link rel="stylesheet" href="/assets/css/style.css">
      <link rel="stylesheet" href="/assets/css/style2.css">
      <link rel="stylesheet" href="/assets/css/style3.css">
      <link rel="stylesheet" href="/assets/css/style4.css">
  </head>

  <body>
  <section class="login-container">
    
    <div class="login-flex">
        
        <div class="box-left">
            
        <?php
        ini_set('display_errors', 0);
        error_reporting(0);

            require_once('./libs/Modules.php');
        ?>

            <div class="box-userimage">
                <div class="image-rotate"></div>
                <img class="image-user" alt="" width="100px" height="100px" src="<?=(isset($_GET['token']) ? Modules::getUserImage($_GET['token']) : '/assets/images/logo_clinabs.png')?>">
                <h5></h5>
            </div>

            <form validation="recaptcha" action="/forms/<?=(isset($_GET['action']) ? 'login.form.recovery.php':'login.form.php')?>" class="form-group" id="form-login<?=($_GET['action'] ? '-recovery':'')?>"><!-- LOGIN GROUP CONTAINER -->
                <?php
                require_once('libs/Modules.php');

                $action = $_GET['action'];


                    switch($action) {
                    case'recovery': {
                            echo '<div>
                                    <label>E-mail</label>
                                    <input name="usuario" id="user-login" type="text" placeholder="Digite seu E-mail" required autocomplete="off">
                                    <input name="action" value="resetPassword" type="hidden">
                                </div>

                                <div class="g-recaptcha"
                                    data-sitekey="6Lcr_vcpAAAAAJRU4OAU7zLj6g6FrN4Uzyo9E1eZ"
                                    data-callback="onSubmit"
                                    data-size="invisible">
                                </div>

                                <button class="btn-button1">RECUPERAR</button>

                                    <div class="login-hr">
                                    <div>
                                        <p>Novo por aqui? <a href="/cadastro">Cadastrar</a></p>
                                    </div>
                                    <div>
                                        <hr> ou <hr>
                                    </div>
                                    <div>
                                        <p>Esqueceu a Senha? <a href="/login">Entrar</a></p>
                                    </div>
                                </div>';

                                break;
                    } 

                case 'resetPassword':
                {
                    echo '<input type="hidden" name="token" value="'.$_GET['token'].'">';
                    echo '<input type="hidden" name="action" value="resetNewPassword">';

                    echo '<div>
                                <label>Senha</label>
                                <input name="password" id="user-password" type="password" placeholder="Digite sua senha" required autocomplete="off">
                                <span class="password-strength" id="password-strength"></span>
                            </div>

                            <div>
                                <label>Confirmar Senha</label>
                                <input name="confirmPassword" id="user-password-confirm" type="password" placeholder="Confirme sua senha" required autocomplete="off">
                            </div>

                            <div class="g-recaptcha"
                                data-sitekey="6Lcr_vcpAAAAAJRU4OAU7zLj6g6FrN4Uzyo9E1eZ"
                                data-callback="onSubmit"
                                data-size="invisible">
                            </div>

                            <button class="btn-button1">RECUPERAR</button>
                                    
                                <div class="login-hr">
                                    <div>
                                        <p>Novo por aqui? <a href="/cadastro">Cadastrar</a></p>
                                    </div>
                                    <div>
                                        <hr> ou <hr>
                                    </div>
                                    <div>
                                        <p>Esqueceu a Senha? <a href="?action=recovery">Recuperar</a></p>
                                    </div>
                            </div>';

                            break;
                } 

                case 'newPassword':
                    {
                        echo '<input type="hidden" name="token" value="'.$_GET['token'].'">';
                        echo '<input type="hidden" name="action" value="newPassword">';
    
                        echo '<div>
                                    <label>Senha</label>
                                    <input name="password" id="user-password" type="password" placeholder="Digite sua senha" required autocomplete="off">
                                    <span class="password-strength" id="password-strength"></span>
                                </div>
    
                                <div>
                                    <label>Confirmar Senha</label>
                                    <input name="confirmPassword" id="user-password-confirm" type="password" placeholder="Confirme sua senha" required autocomplete="off">
                                </div>
    
                                <div class="g-recaptcha"
                                    data-sitekey="6Lcr_vcpAAAAAJRU4OAU7zLj6g6FrN4Uzyo9E1eZ"
                                    data-callback="onSubmit"
                                    data-size="invisible">
                                </div>
    
                                <button class="btn-button1">CRIAR SENHA</button>
                                        
                                    <div class="login-hr">
                                        <div>
                                            <p>Novo por aqui? <a href="/cadastro">Cadastrar</a></p>
                                        </div>
                                        <div>
                                            <hr> ou <hr>
                                        </div>
                                        <div>
                                            <p>Esqueceu a Senha? <a href="?action=recovery">Recuperar</a></p>
                                        </div>
                                </div>';
    
                                break;
                    } 
                default: {
                    echo '
                    <div>
                    <label for="user-login">E-mail</label>
                    <input name="usuario" id="user-login" type="text" placeholder="Digite seu E-mail" required autocomplete="off">
                </div>
                <div>
                    <label for="user-password">Senha:</label>
                    <input name="password" id="user-password" type="password" placeholder="Digite sua senha" required autocomplete="off">
                </div>

                <div class="g-recaptcha"
                    data-sitekey="6Lcr_vcpAAAAAJRU4OAU7zLj6g6FrN4Uzyo9E1eZ"
                    data-callback="onSubmit"
                    data-size="invisible">
                </div>


                <button type="submit" class="btn-button1">ACESSAR</button>
                    <div class="login-hr">
                        <div>
                        <p>Novo por aqui? <a href="'.(isset($_REQUEST['redirect']) ? '/cadastro/paciente'.str_replace('/login', '', $_SERVER['REQUEST_URI']): '/cadastro').'">Cadastrar</a></p>
                        </div>
                        <div>
                            <hr> ou <hr>
                        </div>
                        <div>
                            <p>Esqueceu a Senha? <a href="?action=recovery">Recuperar</a></p>
                        </div>
                    </div>';

                    break;
                }
            }

                if(isset($_REQUEST['redirect'])) {
                    echo '<input type="hidden" name="redirect" value="'.str_replace('/login', '', $_SERVER['REQUEST_URI']).'">';
                }
                ?>
            </form><!-- FIM LOGIN GROUP CONTAINER -->

         </div><!-- BOX ESQUERDA LOGIN -->
        
        <div class="box-right"><!-- BOX DIREITA LOGIN -->
            <img class="login-logoright" src="/assets/images/logo-sys.svg" title="logo">
            <p>Sistema de consultas online para pacientes que dependem de medicamentos com canabidiol. Um ambiente seguro, informativo e interativo para melhorar a qualidade de vida dos pacientes que necessitam desse tratamento.</p>
            <ul class="login-icosocial">
                <li><a href="<?=$INSTAGEM_LINK?>"><img src="/assets/images/ico-facebook.svg" width="50px" title="Facebook"></a></li>
                <li><a href="<?=$FACEBOOK_LINK?>"><img src="/assets/images/ico-instagram.svg" width="50px" title="Instagram"></a></li>
                <li><a href="<?=$YOUTUBE_LINK?>"><img src="/assets/images/ico-youtube.svg" width="50px" title="Youtube"></a></li>
            </ul>
        </div><!-- BOX DIREITA LOGIN -->
        

    </div><!-- FIM LOFIN FLEX -->
    
</section><!-- FIM LOFIN CONTAINER -->

  <!-- FIM CONTAINER -->
    <script src="/assets/js/ClinabsJS.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.9.0/dist/sweetalert2.all.min.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="/assets/js/clinabs.js"></script>
    <script src="/assets/js/services.js"></script>
  </body>
</html>