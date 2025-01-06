<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if(isset($_SESSION['userObj'])) {
	    $user = (object) $_SESSION['userObj'];
    }
?>
<section class="main">
    <section>
        <h1 class="titulo-h1">Faturamento</h1>
    </section>
    <div class="flex-container produtos-flex">
        <section class="grid-container" style="margin: 0 0 40px 0;">
            <div class="grid-item">
                <span>Saldo atual</span>
                <h3>
                    <img src="">
                    </svg>
                    R$
                    <?=number_format($saldo, 2, '.', ',')?>
                </h3>
            </div>

            <div class="grid-item">
                <span>Previsto</span>
                <h3>
                    <img src="">
                    R$
                    <?=$previsto?>
                </h3>
            </div>

            <div class="grid-item">
                <span>Pendente</span>
                <h3>
                    <img src="">
                    R$
                    <?=$pendente?>
                </h3>
            </div>
        </section>

        <?php
        
        $badges = [
            'REFUNDED' => 'dark',
            'CONFIRMED' => 'info',
            'RECEIVED' => 'success',
            'PENDING' => 'secondary',
            'RECEIVED_IN_CASH' => 'success',
            'REFUND_REQUESTED' => 'warning',
            'OVERDUE' => 'danger',
            'PAYMENT_RECEIVED' => 'success',
            'PAYMENT_FEE' => 'danger',
            'TRANSFER' => 'dark'
        ];


        if(isset($_GET['extrato'])) {
            ?>
        <table class="display" id="faturamento-lf">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Saldo</th>
                </tr>
            </thead>

            <tbody>
                <?php
                    $extratos = $asaas->extrato();

                    $dados = [];
                    foreach($extratos->data as $d) {
                        $dados[$d->id] = $d;
                    }

                    ksort($dados);

                    foreach($dados as $id => $extrato) {
                        $date = date('d/m/Y', strtotime($extrato->date));
                        $tipo = $asaas->get_status($extrato->type);
                        $valor = number_format($extrato->value, 2 , ',', '.');
                        $saldo = number_format($extrato->balance, 2 , ',', '.');
                        $tpr = $badges[$extrato->type];

                        echo "<tr>
                            <td>{$extrato->id}</td>
                            <td>{$date}</td>
                            <td>{$extrato->description}</td>
                            <td><span class=\"badge badge-{$tpr}\">{$tipo}</span></td>
                            <td>R$ {$valor}</td>
                            <td>R$ {$saldo}</td>
                        </tr>";
                    }
                ?>
            </tbody>
        </table>

        <?php
        } else {
            ?>

        <table class="display" id="faturamento-lf">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Descrição</th>
                    <th>Paciente</th>
                    <th>Valor</th>
                    <th>Vencimento</th>
                    <th>Tipo</th>
                    <th>Status</th>
                    <th>Açoes</th>
                </tr>
            </thead>

            <tdoby>
                <?php
                $item = [];

                foreach($cobrancas as $cobranca) {
                    if(!$cobranca->deleted){
                        $pid = $evts[$cobranca->id];

                        $data = date('d/m/Y', strtotime($cobranca->dateCreated));
                        $valor = number_format($cobranca->value, 2, ',', '.');
                        $tipo = $asaas->get_status($cobranca->billingType);
                        $status = $asaas->get_status($cobranca->status);
                        $cliente = $asaas->getCliente($cobranca->customer);

                        $sts = strtr($cobranca->status, $badges);

                        if($status == 'RECEBIMENTO EXTERNO') {
                            $pay_btn = "data-id=\"{$pay_id}\" onclick=\"get_payment_info(this)\"";
                        } else {
                            $pay_id  = '';
                        }

                        $status = "<span class=\"badge badge-{$sts}\">{$status}</span>";

                        if($cobranca->transactionReceiptUrl != null) {
                            $uri = $cobranca->transactionReceiptUrl;
                        } else {
                            $uri = $cobranca->invoiceUrl;
                        }

                        $vencimento = date('d/m/Y', strtotime($cobranca->dueDate));

                    
                        echo "<tr>";
                            echo "<td><a href=\"/faturamento/view/{$cobranca->id}\">{$cobranca->id}</td>";
                            echo "<td>{$data}</td>";
                            echo "<td>{$cobranca->description}</td>";
                            echo "<td>{$cliente->name}</td>";
                            echo "<td>R$ {$valor}</td>";
                            echo "<td>{$vencimento}</td>";
                            echo "<td>{$tipo}</td>";
                            echo "<td {$pay_btn}>{$status}</td>";
                            echo "<td>";
                                echo "<div class=\"btn-actions\">";
                                echo "<a target=\"_blank\" href=\"{$uri}\"><img title=\"Ver Fatura\" src=\"/assets/images/ico-cart-btn.svg\" height=\"22px\"></a>";
                                    if($cobranca->status == 'CONFIRMED' || $cobranca->status == 'RECEIVED') {
                                        echo "<img onclick=\"wa_notify('{$cobranca->id}', '{$cliente->name}', 1)\" title=\"Enviar 2º via de Comprovante via WhatsApp\" src=\"/assets/images/wa.svg\" height=\"22px\">";
                                    }else {
                                        echo "<img onclick=\"wa_notify('{$cobranca->id}', '{$cliente->name}', 0)\" title=\"Enviar Lembrete de Cobrança via WhatsApp\" src=\"/assets/images/wa.svg\" height=\"22px\">";
                                        
                                        if($cobranca->status == 'RECEIVED_IN_CASH') {
                                            echo "<img onclick=\"payment_by_money('{$cobranca->id}', '{$cliente->name}', true)\" title=\"Desfazer Recebimento por Dinheiro\" src=\"/assets/images/ico-money.svg\" height=\"22px\">";
                                        } else {
                                            echo "<img onclick=\"caixa_recebimento_exec('{$cobranca->id}', ($cobranca->value * 100), '{$cliente->name}')\" title=\"Confirmar Recebimnento em Dinheiro\" src=\"/assets/images/ico-money.svg\" height=\"22px\">";
                                        }


                                        if($user->perms->deletar_item == 1 && ($cobranca->status == 'PENDING' || $cobranca->status == 'OVERDUE')) {
                                            echo "<img title=\"Deletar Pagamento\" class=\"btn-action\" onclick=\"action_btn_form_payment(this)\" data-token=\"{$cobranca->id}\" data-act=\"delete_payment\" src=\"/assets/images/ico-trash.svg\" height=\"22px\">";
                                        }
                                    }
                                echo "</div>";
                            echo "</td>";
                        echo "</tr>";
                    }
                }

                ?>
                </tbody>
        </table>
        <?php
        }
        ?>
    </div>
</section>