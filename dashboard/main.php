<?php
if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    // if(isset($_SESSION['userObj'])) {
	//     $user = (object) $_SESSION['userObj'];
    // }
  global $user;    
?>
<div class="container-main">
    <h1>Dashboard</h1>
    <span class="dashboard_last_update">Atualizando...</span>
    <br>
    <div class="dashboard">
        <!-- Cards de quantidades -->
        <?php
            if((isset($user) && $user->tipo == 'FUNCIONARIO') && (isset($user->perms) && $user->perms->dashboard_view_medicos == 1)){
                echo '<div class="card">
                            <h2>Médicos</h2>
                            <p id="medicos_count" class="dashboard-conter">...</p>
                        </div>';
            }

            if((isset($user) && $user->tipo == 'FUNCIONARIO') && (isset($user->perms) && $user->perms->dashboard_view_pacientes == 1)){
                echo '<div class="card">
                            <h2>Pacientes</h2>
                            <p id="pacientes_count" class="dashboard-conter" class="dashboard-conter">...</p>
                        </div>';
            }

            if((isset($user) && $user->tipo == 'FUNCIONARIO') && (isset($user->perms) && $user->perms->dashboard_view_payments == 1)){
                echo '<div class="card">
                            <h2>Pagamentos em Aberto</h2>
                            <p id="pagamentos_count" class="dashboard-conter">...</p>
                        </div>';
            }


            if((isset($user) && $user->tipo == 'FUNCIONARIO') && (isset($user->perms) && $user->perms->dashboard_view_agendamentos == 1)){
                echo '<div class="card">
                    <h2>Agendamentos</h2>
                    <p id="agendamentos_count" class="dashboard-conter">...</p>
                </div>';
            }
         ?>
    </div>

    <div class="kanban">
        <?php
            if((isset($user) && $user->tipo == 'FUNCIONARIO') && (isset($user->perms) && $user->perms->dashboard_new_agendamentos == 1)) {
                echo '<div class="kanban-column" id="novosAgendamentos">
            <div class="column-header">
                <h2>Agendamentos</h2>
                <small class="column-count">0 registro(s)</small>
            </div>
        </div>';
            }

            if((isset($user) && $user->tipo == 'FUNCIONARIO') && (isset($user->perms) && $user->perms->dashboard_new_pacientes == 1)) {
                echo '<div class="kanban-column" id="novosPacientes">
            <div class="column-header">
                <h2>Novos Pacientes</h2>
                <small class="column-count">0 registro(s)</small>
            </div>
        </div>';
            }

            if((isset($user) && $user->tipo == 'FUNCIONARIO') && (isset($user->perms) && $user->perms->dashboard_new_medicos == 1)) {
                echo '<div class="kanban-column" id="novosMedicos">
            <div class="column-header">
                <h2>Novos Médicos</h2>
                <small class="column-count">0 registro(s)</small>
            </div>
        </div>';
            }

            if((isset($user) && $user->tipo == 'FUNCIONARIO') && (isset($user->perms) && $user->perms->dashboard_new_acompanhamentos == 1)) {
                echo '<div class="kanban-column" id="acompanhamentosMedicos">
            <div class="column-header">
                <h2>Próximos Acompanhamentos</h2>
                <small class="column-count">0 registro(s)</small>
            </div>
        </div>';
            }

            if((isset($user) && $user->tipo == 'FUNCIONARIO') && (isset($user->perms) && $user->perms->dashboard_new_pagamentos == 1)) {
                echo '<div class="kanban-column" id="contasReceber">
            <div class="column-header">
                <h2>Contas a Receber</h2>
                <small class="column-count">0 registro(s)</small>
            </div>
        </div>';
            }
        ?>
    </div>
</div>