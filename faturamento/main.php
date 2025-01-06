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
                    <?=number_format($balance, 2, '.', ',')?>
                </h3>
            </div>

            <div class="grid-item">
                <span>Pendente</span>
                <h3>
                    <img src="">
                    R$
                    <?=number_format($paymentsBalance->value, 2, '.', ',')?>
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
            'TRANSFER' => 'dark',
        ];

        ?>
        <table class="display table" id="faturamento-tb">
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
                    <th width="150px">Açoes</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $payloads = $pdo->query("SELECT reference,(SELECT nome_completo FROM PACIENTES WHERE payment_id = customer) AS customer,asaas_payload FROM VENDAS WHERE payment_id LIKE 'pay_%';");
                    $payments = $payloads->fetchAll(PDO::FETCH_OBJ);

                    foreach($payments as $payment) {
                        $payload = json_decode(base64_decode($payment->asaas_payload));
                        $sts = $asaas->get_status($payload->status);
                        $psts = $badges[$payload->status];

                        $ptype = $asaas->get_status($payload->billingType);

                        if($payload->status === 'CONFIRMED' || $payload->status == 'RECEIVED' || $payload->status == 'RECEIVED_IN_CASH') {
                            $link = "<a href=\"{$payload->invoiceUrl}\" target=\"asaas_{$payload->invoiceNumber}\">{$payload->invoiceNumber}</a>";
                        } else {
                            $link = "<a href=\"{$payload->transactionReceiptUrl}\" target=\"asaas_{$payload->invoiceNumber}\">{$payload->invoiceNumber}</a>";
                        }

                        echo "<tr>";
                        echo "<td>{$link}</td>";
                        echo "<td>".date('d/m/Y', strtotime($payload->dateCreated))."</td>";
                        echo "<td>{$payload->description}</td>";
                        echo "<td>{$payment->customer}</td>";
                        echo "<td>R\$ ".number_format($payload->value, 2, ',', '.')."</td>";
                        echo "<td>".date('d/m/Y', strtotime($payload->dueDate))."</td>";
                        echo "<td>{$ptype}</td>";
                        echo "<td><span class=\"badge badge-{$psts}\">{$sts}</span></td>";
                        echo "<td>";
                        echo "<div  class=\"td-dflex\">";

                        if($payload->status == 'RECEIVED_IN_CASH') {
                            echo '<img onclick="payment_by_money(\''.$payload->id.'\', \''.$payment->customer.'\', true)" title="Desfazer Recebimnento em Dinheiro" src="/assets/images/ico-money-cancel.svg" height="22px">';
                        } else if($payload->status == 'PENDING') {
                            echo '<img onclick="caixa_recebimento_exec(\''.$payload->id.'\', ('.$payload->value.' * 100), \''.$payment->customer.'\')" title="Confirmar Recebimnento em Dinheiro" src="/assets/images/ico-money.svg" height="22px">';
                        }
                        
                        
                        if($payload->status === 'CONFIRMED' || $payload->status == 'RECEIVED') {
                            echo '<img onclick="wa_notify(\''.$payload->id.'\', \''.$payment->customer.'\', 1)" title="Enviar Comprovante via WhatsApp" src="/assets/images/wa.svg" height="22px">';

                            echo '<img title="Estornar Pagamento" class="btn-action" onclick="refund_payment(\''.$payload->id.'\',\''.$payload->value.'\')" src="/assets/images/ico-trash.svg" height="22px">';
                            
                        } else if($payload->status === 'PENDING') {
                            echo '<img onclick="wa_notify(\''.$payload->id.'\', \''.$payment->customer.'\', 0)" title="Enviar Lembrete de Cobrança via WhatsApp" src="/assets/images/wa.svg" height="22px">';
                            echo '<img title="Cancelar Pagamento" class="btn-action" onclick="action_btn_form_payment(this)" data-token="'.$payload->id.'" data-act="delete_payment" src="/assets/images/ico-trash.svg" height="22px">';
                        }

                       
                        echo "</div>";
                         echo "</td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
</section>