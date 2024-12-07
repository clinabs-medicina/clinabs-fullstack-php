<?php
require_once '../config.inc.php';


        $buffer = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/data/images/docs/'.$_REQUEST['filename']);
        $fname = $_REQUEST['filename'];
        $mime = mime_content_type($_SERVER['DOCUMENT_ROOT'].'/data/images/docs/'.$_REQUEST['filename']);
     
          header('Content-Type: '.$mime);
          header('Content-Disposition: attachment; filename='.$fname);
          header('Content-Transfer-Encoding: binary');
          header('Expires: 0');
          header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
          header('Pragma: public');
          echo $buffer;
