<?php
require_once '../config.inc.php';

$curl = curl_init();

curl_setopt_array($curl, array(
CURLOPT_URL => 'https://app.hallo-api.com/v1/instance/GLCG-000629-i12u-aofB-X77N9XQ3GXZB/token/5RLU7PHL-zPBP-4AVw-BgNb-2B67W4L9VX85/instance',
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => '',
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 0,
CURLOPT_FOLLOWLOCATION => true,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => 'POST',
CURLOPT_POSTFIELDS => array('fLogin' => '9V60VP5I-SYAxyJ-AMbim7a2-ZHY47UPR3LCY','ACTION' => 'STATE'),
));

$whatsapp = json_decode(curl_exec($curl));

$ddi = substr($whatsapp->result->number, 0, 2);
$ddd = substr($whatsapp->result->number, 2, 2);
$cell_p1 = substr($whatsapp->result->number, 4, 4);
$cell_p2 = substr($whatsapp->result->number, 8, 4);

if($whatsapp->result->state != 'disconnected') {
    file_put_contents('../wa-config.json', json_encode($whatsapp->result, JSON_PRETTY_PRINT));
}

curl_close($curl);

if($whatsapp->result->state == 'disconnected') {
    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://app.hallo-api.com/v1/instance/GLCG-000629-i12u-aofB-X77N9XQ3GXZB/token/5RLU7PHL-zPBP-4AVw-BgNb-2B67W4L9VX85/instance',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('fLogin' => '9V60VP5I-SYAxyJ-AMbim7a2-ZHY47UPR3LCY','ACTION' => 'CONNECT'),
    ));

    $whatsapp = json_decode(curl_exec($curl));

    curl_close($curl);
}

                        if($whatsapp->result->state == 'disconnected'){
                            ?>
                            <div class="profile-details">
                                <img src="data:image/png;base64,<?=($whatsapp->result->qrCode != "" ? $whatsapp->result->qrCode : '/assets/images/loading.gif')?>" class="no-round" height="128px">
                                <div class="details">
                                <h2>Conecte seu WhatsApp</h2>
                                <h4>ID da Instância: <?=($whatsapp->instance_id)?></h4>
                                <small class="badge-danger">Desconectado</small>
                                </div>
                            </div>
     
                        <?php
                        }else {
                            
                            $img = file_exists('../data/wa-profiles/'.$whatsapp->result->number.'.jpg') ? '/data/wa-profiles/'.$whatsapp->result->number.'.jpg':'/assets/images/icon-512x512.png';
                            ?>
                            <div class="profile-details wa-connect">
                                <img src="<?=($img)?>" height="128px">
                                <div class="details">
                                <h2><?=($whatsapp->result->name)?></h2>
                                <h4><?=("+{$ddi} ({$ddd}) {$cell_p1}-{$cell_p2}")?></h4>
                                <h5>Expira em: <?=(date('d/m/Y', strtotime($whatsapp->result->expiresAt)))?></h5>
                                <small class="badge-<?=($whatsapp->result->state == 'connected' ? 'success':'danger')?>"><?=($whatsapp->result->state == 'connected' ? 'Conectado':'Desconectado')?></small>
                                <p><label class="link-primary" onclick="disconnect_wa(this)">Desconectar</label></p>
                                </div>
                            </div>
                   
                    <?php
                        }
                        ?>