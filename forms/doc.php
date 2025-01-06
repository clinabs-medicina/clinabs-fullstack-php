<?php
require_once '../config.inc.php';


    $doc = $_SERVER['DOCUMENT_ROOT'].$_REQUEST['doc'];


   if(isset($_REQUEST['dl'])) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename='.basename($doc));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    ob_clean();
    flush();
    readfile($doc);
    exit;
   }else{
    header('Content-Type: '.mime_content_type($doc));
    echo file_get_contents($doc);
   }