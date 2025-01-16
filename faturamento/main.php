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
                    <?= number_format($balance, 2, ',', '.') ?>
                </h3>
            </div>

            <div class="grid-item">
                <span>Pendente</span>
                <h3>
                    <img src="">
                    R$
                    <?= number_format($paymentsBalance->value, 2, ',', '.') ?>
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

        <section id="tabControl1" class="tabControl fw" data-lock="false">
            <div class="tab-toolbar">
                <span class="active" data-index="1" data-tab="tabControl1">Consultas</span>
                <span data-index="2" data-tab="tabControl1">Medicamentos</span>
            </div>

            <div class="tab active" data-index="1" data-tab="tabControl1">
                <section class="form-grid area-full">
                    <section class="form-group">
                    <table class="display table" id="faturamento-tb-consultas">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Data de Agendamento</th>
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
                            try {
                                $payloads = $pdo->query("SELECT     `id`, (SELECT data_agendamento FROM AGENDA_MED WHERE token = VENDAS.code LIMIT 1) AS data_agendamento,    (         SELECT nome_completo         FROM PACIENTES         WHERE             token = (                 SELECT paciente_token                 FROM AGENDA_MED                 WHERE                     token = VENDAS.code                 LIMIT 1             )         LIMIT 1     ) AS paciente,     (         SELECT nome_completo         FROM PACIENTES         WHERE             payment_id = VENDAS.customer         LIMIT 1     ) AS paciente_nome,     (         SELECT data_agendamento         FROM AGENDA_MED         WHERE             AGENDA_MED.token = VENDAS.reference         LIMIT 1     ) AS data_agendamento,     `nome`,     `dueTime`,     `code`,     `payment_id`,     `amount`,     `customer`,     `status`,     `created_at`,     `updated_at`,     `payment_method`,     `asaas_payload` FROM `VENDAS` WHERE     module = 'AGENDA_MED' ORDER BY `created_at` DESC;");
                                $payments = $payloads->fetchAll(PDO::FETCH_OBJ);

                                foreach ($payments as $payment) {
                                    $data_agendamento = date('d/m/Y H:i', strtotime($payment->data_agendamento));

                                    if ($payment->customer != null && $payment->customer != '' && $payment->asaas_payload != '[]') {
                                        $payload = json_decode($payment->asaas_payload);
                                        $sts = $asaas->get_status($payload->status);
                                        $psts = $badges[$payload->status];

                                        $ptype = $asaas->get_status($payload->billingType);

                                        if ($payload->status === 'CONFIRMED' || $payload->status == 'RECEIVED' || $payload->status == 'RECEIVED_IN_CASH') {
                                            $link = "<a href=\"{$payload->invoiceUrl}\" target=\"asaas_{$payload->invoiceNumber}\">{$payload->invoiceNumber}</a>";
                                        } else {
                                            $link = "<a href=\"{$payload->transactionReceiptUrl}\" target=\"asaas_{$payload->invoiceNumber}\">{$payload->invoiceNumber}</a>";
                                        }

                                        if (strlen($link)) {
                                            $link = $payment->id;
                                        }

                                        $isConfirm = $payment->status == 'AGUARDANDO CONFIRMAÇÃO';

                                        $sts = $isConfirm ? 'AGUARDANDO CONFIRMAÇÃO' : $sts;

                                        echo '<tr data-id="' . $payload->id . '">';
                                        echo "<td>{$link}</td>";
                                        echo '<td>' . date('d/m/Y', strtotime($payload->dateCreated ?? $payment->created_at)) . '</td>';
                                        echo "<td>{$payload->description}</td>";
                                        echo "<td>{$data_agendamento}</td>";
                                        echo "<td>{$payment->paciente_nome}</td>";
                                        echo '<td>R$ ' . number_format($payload->value, 2, ',', '.') . '</td>';
                                        echo '<td>' . date('d/m/Y', strtotime($payload->dueDate)) . '</td>';
                                        echo "<td>{$ptype}</td>";
                                        echo "<td><span class=\"badge badge-{$psts}\">{$sts}</span></td>";
                                        echo '<td>';
                                        echo '<div  class="td-dflex">';

                                        if ($payload->status == 'RECEIVED_IN_CASH' && !$isConfirm) {
                                            echo '<img onclick="payment_by_money(\'' . $payload->id . "', '" . $payment->customer . '\', true)" title="Desfazer Recebimnento em Dinheiro" src="/assets/images/ico-money-cancel.svg" height="22px">';
                                        } else if ($payload->status == 'PENDING' && !$isConfirm) {
                                            echo '<img onclick="caixa_recebimento_exec(\'' . $payload->id . "', (" . $payload->value . " * 100), '" . $payment->customer . '\')" title="Confirmar Recebimnento em Dinheiro" src="/assets/images/ico-money.svg" height="22px">';
                                        }

                                        if ($payload->status === 'CONFIRMED' || $payload->status == 'RECEIVED') {
                                            echo '<img onclick="wa_notify(\'' . $payload->id . "', '" . $payment->customer . '\', 1)" title="Enviar Comprovante via WhatsApp" src="/assets/images/wa.svg" height="22px">';

                                            echo '<img title="Estornar Pagamento" class="btn-action" onclick="refund_payment(\'' . $payload->id . "','" . $payload->value . '\')" src="/assets/images/ico-trash.svg" height="22px">';
                                        } else if ($payload->status === 'PENDING' && !$isConfirm) {
                                            echo '<img onclick="wa_notify(\'' . $payload->id . "', '" . $payment->customer . '\', 0)" title="Enviar Lembrete de Cobrança via WhatsApp" src="/assets/images/wa.svg" height="22px">';
                                            echo '<img title="Cancelar Pagamento" class="btn-action" onclick="action_btn_form_payment(this)" data-token="' . $payload->id . '" data-act="delete_payment" src="/assets/images/ico-trash.svg" height="22px">';
                                        }

                                        if ($isConfirm) {
                                            echo '<img title="Confirmar Solicitação" class="btn-action" onclick="auth_payment(this)" data-token="' . $payload->id . '" data-act="auth_payment" src="/assets/images/ico-success.svg" height="32px">';

                                            echo '<img title="Cancelar Pagamento" class="btn-action" onclick="action_btn_form_payment(this)" data-token="' . $payload->id . '" data-act="delete_payment" src="/assets/images/ico-trash.svg" height="22px">';
                                        }

                                        echo '</div>';
                                        echo '</td>';
                                        echo '</tr>';
                                    } else {
                                        echo '<tr data-id="' . $payment->id . '">';
                                        echo "<td>{$payment->id}</td>";
                                        echo '<td>' . date('d/m/Y', strtotime($payment->created_at)) . '</td>';
                                        echo "<td>{$payment->nome}</td>";
                                        echo "<td>{$data_agendamento}</td>";
                                        echo "<td>{$payment->paciente}</td>";
                                        echo '<td>R$ ' . number_format($payment->amount, 2, ',', '.') . '</td>';
                                        echo '<td></td>';
                                        echo '<td>' . $asaas->get_status($payment->payment_method) . '</td>';
                                        echo "<td><span>{$payment->status}</span></td>";
                                        echo '<td>';

                                        if ($payment->status === 'AGUARDANDO CONFIRMAÇÃO') {
                                            echo "<img data-token=\"{$payment->id}\" title=\"Confirmar Solicitação\" class=\"btn-action\" onclick=\"auth_payment(this, true)\" data-token=\"{$payment->id}\" data-action=\"auth_payment\" src=\"/assets/images/ico-success.svg\" height=\"32px\">";
                                        } else if ($payment->status === 'AGENDADO') {
                                            echo "<img title=\"Cancelar Pagamento\"  onclick=\"change_payment(this)\" data-token=\"{$payment->id}\" data-action=\"delete_payment\" src=\"/assets/images/ico-trash.svg\" height=\"22px\">";
                                        } else if ($payment->status === 'AGUARDANDO PAGAMENTO') {
                                            echo "<img title=\"Confirmar Pagamento\" onclick=\"change_payment(this)\" data-token=\"{$payment->id}\" data-action=\"confirm_payment\" src=\"/assets/images/ico-money.svg\" height=\"32px\">";
                                        }

                                        echo '</td>';
                                        echo '</tr>';
                                    }
                                }
                            } catch (Exception $e) {
                                echo $e->getMessage();
                            }
                            ?>
                        </tbody>
                    </table>
                            
                    </section>
                </section>
            </div>

            <div class="tab" data-index="2" data-tab="tabControl1">
                <section class="form-grid area-full">
                    <section class="form-group">
                    <table class="display table" id="faturamento-tb-medicamentos">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Paciente</th>
                                <th>Valor</th>
                                <th>Status</th>
                                <th width="150px">Açoes</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $payloads = $pdo->query("SELECT `id`, (SELECT nome_completo FROM PACIENTES WHERE token = VENDAS.customer) AS paciente_nome, (SELECT data_agendamento FROM AGENDA_MED WHERE AGENDA_MED.token = VENDAS.reference) AS data_agendamento, `nome`, `dueTime`, `code`, `payment_id`, `amount`, `customer`, `status`, `created_at`, `updated_at`, `payment_method`, `asaas_payload` FROM `VENDAS` WHERE module = 'FARMACIA' ORDER BY `created_at` DESC;");
                            $payments = $payloads->fetchAll(PDO::FETCH_OBJ);

                            foreach ($payments as $payment) {
                                echo '<tr>';
                                echo "<td>{$payment->id}</td>";
                                echo '<td>' . date('d/m/Y', strtotime($payment->created_at)) . '</td>';
                                echo "<td>{$payment->nome}</td>";
                                echo "<td>{$payment->paciente_nome}</td>";
                                echo '<td>R$ ' . number_format($payment->amount, 2, ',', '.') . '</td>';
                                echo "<td><span>{$payment->status}</span></td>";
                                echo '<td>';
                                echo '<div  class="td-dflex">';

                                if ($payment->status == 'AGUARDANDO PAGAMENTO') {
                                    echo '<img title="Confirmar Pagamento" class="btn-action" data-action="confirm" onclick="manual_payment(this)" data-id="' . $payment->id . '" src="/assets/images/ico-money.svg" height="22px">';
                                    echo '<img title="Cancelar Pagamento" class="btn-action" data-action="delete" onclick="manual_payment(this)" data-id="' . $payment->id . '" src="/assets/images/ico-trash.svg" height="22px">';
                                } else if ($payment->status == 'PAGO') {
                                    echo '<img title="Cancelar Pagamento" class="btn-action" data-action="delete" onclick="manual_payment(this)" data-id="' . $payment->id . '" src="/assets/images/ico-trash.svg" height="22px">';
                                } else {
                                    if ($payment->status == 'CANCELADO') {
                                        echo '<img title="Confirmar Pagamento" class="btn-action" data-action="confirm" onclick="manual_payment(this)" data-id="' . $payment->id . '" src="/assets/images/ico-money.svg" height="22px">';
                                    }
                                    echo '<img title="Deletar Pagamento" class="btn-action" data-action="delete" onclick="manual_payment(this)" data-id="' . $payment->id . '" src="/assets/images/ico-trash.svg" height="22px">';
                                }
                                echo '</div>';
                                echo '</td>';
                                echo '</tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                            
                    </section>
                </section>
            </div>
        </div>

    </div>
</section>