<?php
global $user, $favoritos, $carrinho;
session_start();

if (isset($_COOKIE['sessid_clinabs_uid'])) {
    $sql = "SELECT
      objeto AS tipo
   FROM
      MEDICOS AS M 
      WHERE 
      M.token = :token
      UNION ALL
      (
      SELECT
         objeto AS tipo
      FROM
         PACIENTES AS P
         WHERE 
         P.token = :token
      ) UNION ALL
      (
      SELECT
         objeto AS tipo
      FROM
      FUNCIONARIOS AS F
      WHERE 
      F.token = :token
      )";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':token', isset($_COOKIE['sessid_clinabs_uid']) ? $_COOKIE['sessid_clinabs_uid'] : $user->token);
    $stmt->execute();
    $obj = $stmt->fetch(PDO::FETCH_OBJ);

    $tableName = $obj->tipo . 'S';

    if ($tableName !== 'S') {
        $stmt2 = $pdo->prepare("SELECT * FROM $tableName WHERE token = :token");
        $stmt2->bindValue(':token', isset($_COOKIE['sessid_clinabs_uid']) ? $_COOKIE['sessid_clinabs_uid'] : $user->token);

        $stmt2->execute();
        $_user = $stmt2->fetch(PDO::FETCH_OBJ);
    } else {
        $_user = false;
    }
} else {
    
}
?>
<!-- BREADCRUMBS -->
<?php if (count(explode('/', $_SERVER['REQUEST_URI'])) >= 3 && $page->bc != false) { ?>

<section class="breadcrumbs">
    <div class="breadcrumbs-flex">
        <ul class="m-0 p-0">
            <?= generateBreadcrumb($_SERVER['REQUEST_URI'])?>
        </ul>
    </div>
</section>
<?php } ?>
<!-- FIM BREADCRUMBS -->
<header class="header">
    <div class="flex-container">
        <a href="/"><img src="/assets/images/logo.svg" alt="logo"></a>
        <div class="link-consult-mobile"
            style="text-align: center; text-decoration: none; color: #2c8a7a;  border: 1px solid #ffb60dc2;  padding: 0.25rem; border-radius: 8px; font-size: 11px;">
            <a href="/agendamento" alt="Agendar Consulta" title=""
                style="text-align: center; font-weight: 600; text-decoration: none; color:#05ad94;font-size: 12pxs">AGENDAR CONSULTA</a>
        </div>
        <nav class="menu-header">
            <ul class="m-0">
                <li class="<?= $page->name == 'link_home' ? 'active' : ''?>" data-ref="/"><a href="/" alt=""
                        title="">INÍCIO</a></li>
                <?php if (isset($_COOKIE['sessid_clinabs']) && $_COOKIE['sessid_clinabs'] != null) { ?>
                <?= !$is_nabscare && $user->perms->link_agendar_consulta ? '<li class="consult-li-link data-ref="consulta"><a href="/agendamento" alt="Agendar Consulta" title="">AGENDAR CONSULTA</a></li>' : '' ?>
                <?= $user->perms->link_medicos && !$is_nabscare ? '<li data-ref="medicos"><a href="/medicos" alt="Nossos Médicos" title="">MÉDICOS</a></li>' : '' ?>
                <?= $user->perms->link_produtos == 1 ? '<li data-ref="produtos"><a href="/produtos" alt="Nossos produtos" title="">PRODUTOS</a></li>' : '' ?>
                <?= $user->perms->link_unidades && !$is_nabscare ? '<li data-ref="Unidades"><a href="/unidades" alt="" title="">UNIDADES</a></li>' : '' ?>
                <?= $user->perms->link_cadastro && !$is_nabscare ? '<li data-ref="cadastro"><a href="/cadastro" alt="" title="">CADASTRE-SE</a></li>' : '' ?>
                <?= $user->perms->link_academy && !$is_nabscare ? '<li data-ref="/academy"><a href="/academy" alt="" title="">ACADEMY</a></li>' : '' ?>
                <?= $user->perms->link_blog_medico && !$is_nabscare ? '<li data-ref="blog"><a href="/blog/medico" alt="" title="">BLOG</a></li>' : '' ?>
                <?php } else { ?>
                <li class="consult-li-link <?= $page->name == 'link_agendar_consulta' ? 'active' : ''?>"
                    data-ref="consulta"><a href="/agendamento" alt="Agendar Consulta" title="">AGENDAR CONSULTA</a></li>
                <li class="<?= $page->name == 'link_medicos' ? 'active' : ''?>" data-ref="medicos"><a href="/medicos"
                        alt="Nossos Médicos" title="">MÉDICOS</a></li>
                <li class="<?= $page->name == 'link_unidades' ? 'active' : ''?>" data-ref="Unidades"><a href="/unidades"
                        alt="" title="">UNIDADES</a></li>
                <li class="<?= $page->name == 'link_cadastro' ? 'active' : ''?>" data-ref="cadastro"><a href="/cadastro"
                        alt="" title="">CADASTRE-SE</a></li>
                <li class="<?= $page->name == 'link_academy' ? 'active' : ''?>" data-ref="/academy"><a href="/academy"
                        alt="" title="">ACADEMY</a></li>
                <li class="<?= $page->name == 'link_blog_medico' ? 'active' : ''?>" data-ref="blog"><a
                        href="/blog/medico" alt="" title="">BLOG</a></li>
                <?php } ?>

            </ul>
        </nav>
        <nav class="menu-ico">
            <div class="m-0">
                <?= $user->perms->link_cart ? '<li data-badge="' . count($carrinho->getAll($user->cpf)) . '" data-source="cart-items-count"><a href="/carrinho"><img class="ico-hover" src="/assets/images/ico-cart.svg" alt=""></a></li>' : '' ?>

                <?= $user->perms->link_notificacao && !$is_nabscare ? '<li data-badge="0"><a href="#"><img class="ico-hover" src="/assets/images/ico-notifica.svg" alt=""></a></li>' : '' ?>

                <?php if (isset($user->nome_completo)) { ?>
                <div class="user-default" id="user-link-menu">
                    <img class="ico-hover user" src="<?= Modules::getUserImage($_user->token ?? $user->token) ?>"
                        alt="Usuário">
                    <div class="user-link">
                        <p class="m-0">Olá, <a
                                href="/perfil"><?=  trim($_SESSION['apelido'] ?? trim(explode(' ', $_SESSION['usuario'])[0])) ?></a>
                        </p>
                        <p class="m-0"><a href="/perfil">MINHA CONTA</a> | <a
                                href="/logout<?= isset($_COOKIE['sessid_clinabs_uid']) || isset($_COOKIE['sessid_clinabs_uid']) ? '?session=user' : '' ?>">SAIR</a>
                        </p>
                    </div>

                    <ul class="user-menu m-1 p-0">
                        <span class="usermenu-arrow"></span>
                        <div class="menu-user-flex">
                            <div class="menu-user-title p-0">
                                <div><img src="<?= Modules::getUserImage($user->token) ?>" alt="Usuário"></div>
                                <div>
                                    <p class="menu-user-title-name m-0"><?= $user->nome_completo ?></p>
                                    <p class="menu-user-title-crm m-0"><?= $user->tipo ?></p>
                                    <p class="menu-user-title-crm">
                                        <small><?= isset($user->setor) ? $user->setor : '' ?></small>
                                    </p>
                                </div>

                            </div>
                            <div class="menu-user-links">
                                <ul class="m-0">
                                    <?= $user->perms->link_perfil == 1 ? '<li><img src="/assets/images/ico-menuuser-password.svg" alt=""><a href="/perfil">MEU PERFIL</a></li>' : '' ?>
                                    <?= $user->perms->dashboard_link == 1 && $user->tipo == 'FUNCIONARIO' ? '<li><img src="/assets/images/ico-menuuser-password.svg" alt=""><a href="/dashboard">DASHBOARD</a></li>':'' ?>
                                    <?= $user->perms->link_funcionarios == 1 && !$is_nabscare ? '<li><img src="/assets/images/ico-menu-funcionario.svg" alt=""><a href="/cadastros/funcionarios">FUNCIONÁRIOS</a></li>' : '' ?>
                                    <?= $user->perms->link_paciente == 1 && !$is_nabscare ? '<li><img src="/assets/images/ico-menu-phone.svg" alt=""><a href="/cadastros/pacientes">PACIENTES</a></li>' : '' ?>
                                    <?= $user->perms->link_menu_medicos == 1 && !$is_nabscare ? '<li><img src="/assets/images/ico-menu-medico.svg" alt=""><a href="/cadastros/medicos">MÉDICOS</a></li>' : '' ?>
                                    <?= $user->perms->link_usuarios == 1 && !$is_nabscare ? '<li><img src="/assets/images/ico-menu-phone.svg" alt=""><a href="/cadastros/usuarios">Fornecedores/Prestadores</a></li>' : '' ?>
                                    <?= $user->perms->link_agenda == 1 && !$is_nabscare  && $user->tipo == 'MEDICO' || $user->tipo == 'FUNCIONARIO' ? '<li><img src="/assets/images/ico-menu-clock.svg" alt=""><a href="/agenda">AGENDA</a></li>' : '<li><img src="/assets/images/ico-menu-clock.svg" alt=""><a href="/agenda">MEUS AGENDAMENTOS</a></li>' ?>
                                    <!--<?= $user->perms->link_financeiro == 1 && !$is_nabscare ? '<li><img src="/assets/images/ico-menu-money.svg" alt=""><a href="/financeiro">FINANCEIRO</a></li>' : '' ?>-->
                                    <?= $user->perms->link_financeiro == 1 && !$is_nabscare ? '<li><img src="/assets/images/ico-menu-money.svg" alt=""><a href="/faturamento">FATURAMENTO</a></li>' : '' ?>
                                    <?= $user->perms->link_pedidos_paciente == 1 ? '<li><img src="/assets/images/ico-menu-cart.svg" alt=""><a href="/pedidos">' . ($user->tipo == 'FUNCIONÁRIO' ? 'Pedidos' : 'Meus Pedidos') . '</a></li>' : '' ?>
                                    <?= $user->perms->table_logs == 1 ? '<li><img src="/assets/images/ico-menu-funcionario.svg" alt=""><a href="/logs">Registros de Logs</a></li>' : '' ?>
                                
                                </ul>
                            </div>
                            <div class="menu-user-logout">
                                <hr>
                                <p><a href="/logout">Sair</a></p>
                            </div>

                        </div>
                </div>
                </div>

                <!--<li  id="user-link-menu"><a><img class="ico-hover user" src="<?= $user !== null && $user->profileImage !== null ? $user->profileImage : '/assets/images/user2.png' ?>" alt="" width="29px"></a>-->

                <?php } else { ?>
                <div class="user-default">
                    <img class="ico-hover user" src="/assets/images/user-deafualt.svg" alt="Usuário">
                    <div class="user-link">
                        <p class="m-0">Fazer <a href="/login">LOGIN</a> ou</p>
                        <p class="m-0">crie sua <a href="/cadastro">CONTA</a></p>
                    </div>
                </div>
                <?php } ?>

            </ul>
        </nav>
        <div class="mobile-menu" style="background-color: #05ad94; height: 90px; padding: 28px 10px; width: auto;">
            <a href="#" id="mobile-toggle-menu"
                style="color: white; text-decoration: none; font-size: 30px; float:left;">
                <h6 class="menu-mobile-title" style="text-align: revert-layer;font-size: 0.90rem; float: left; padding: 6px;">MENU</h6>
                <img class="ico-hover" src="/assets/images/ico-menu-burger.svg" alt="Menu"
                    style="filter: brightness(0) invert(1); display: none;">
            </a>
        </div>

        <?php
            if ($_SERVER['HTTP_HOST'] == 'homolog.clinabs.com' || $_SERVER['HTTP_HOST'] == 'homolog.clinabs.com.br') {
                echo '<div class="app-mode">HOMOLOG</div>';
            } else if($_SERVER['HTTP_HOST'] == 'dev.clinabs.com') {
                echo '<div class="app-mode">DEV</div>';
            }
            else if($_SERVER['HTTP_HOST'] == 'deploy.clinabs.com' || $_SERVER['HTTP_HOST'] == 'deploy.clinabs.com.br') {
                echo '<div class="app-mode">DEPLOY</div>';
            }
        ?>
</header>