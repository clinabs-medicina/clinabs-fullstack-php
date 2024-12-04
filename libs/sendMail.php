<?php
function sendMail($mailer, $to = array(), $subject = '', $body = '') {
    // Email
    try
    {
        $mailer->addAddress($to['email'], $to['name']);

        $mailer->isHTML(true);
        $mailer->Subject = $subject;
        $mailer->Body    =  utf8_decode($body);

        return $mailer->send();
    } 
    catch (Exception $e) {
        return $mailer->ErrorInfo;
    }
}