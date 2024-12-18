<?php
error_reporting(1);
ini_set('display_errors', 1);
// Importar as classes 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require_once $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
try
            {
                $mail = new PHPMailer();
                $mail->SMTPDebug = true;
                $mail->SMTPAuth = true;

                $mail->Username   = 'desenvolvedor@clinabs.com';
                $mail->Password   = 'gfjnAVJ5NcOP8tkp';
                $mail->SMTPSecure = 'tls';
                $mail->Host = 'smtp-relay.brevo.com';
                $mail->Port = 587;
                $mail->setFrom('naoresponder@clinabs.com', 'CLINABS');
                

                $mail->addAddress('adrianonecosilva290@gmail.com', 'Adriano Neco');
                
                $mail->isHTML(false);
                $mail->Subject = utf8_decode('Informações de Pedido');
                $mail->Body = utf8_decode('Teste de Envio');

                $mail->send();
            }
            catch (Exception $e)
            {
                echo '<pre>';
                    print_r($e, true);
                echo '</pre>';
            }