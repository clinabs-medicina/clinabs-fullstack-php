<?php
class Modules {

    public static function parseDate($date){
        $date = explode('/', $date);

        $y = $date[2];
        $m = $date[1];
        $d = $date[0];

        return str_replace('--', '', "{$y}-{$m}-{$d}");
    }

    public static function compressImage($source, $destination, $quality){

        $info = getimagesize($source);
    
        if ($info['mime'] == 'image/jpeg') 
            $image = imagecreatefromjpeg($source);
    
        elseif ($info['mime'] == 'image/gif') 
            $image = imagecreatefromgif($source);
    
        elseif ($info['mime'] == 'image/png') 
            $image = imagecreatefrompng($source);
    
        imagejpeg($image, $destination, $quality);
    
        return $destination;
    }

    public static function resize_crop_image($max_width, $max_height, $source_file, $dst_dir, $quality = 80){
        $imgsize = getimagesize($source_file);
        $width = $imgsize[0];
        $height = $imgsize[1];
        $mime = $imgsize['mime'];
     
        switch($mime){
            case 'image/gif':
                $image_create = "imagecreatefromgif";
                $image = "imagegif";
                break;
            case 'image/png':
                $image_create = "imagecreatefrompng";
                $image = "imagepng";
                break;
            case 'image/jpeg':
                $image_create = "imagecreatefromjpeg";
                $image = "imagejpeg";
                break;
            default:
                return false;
                break;
        }
         
        $dst_img = imagecreatetruecolor($max_width, $max_height);
        $src_img = $image_create($source_file);
         
        $width_new = $height * $max_width / $max_height;
        $height_new = $width * $max_height / $max_width;

        if($width_new > $width){
            $h_point = (($height - $height_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, 0, $h_point, $max_width, $max_height, $width, $height_new);
        }else{
            $w_point = (($width - $width_new) / 2);
            imagecopyresampled($dst_img, $src_img, 0, 0, $w_point, 0, $max_width, $max_height, $width_new, $height);
        }
         
        $image($dst_img, $dst_dir, $quality);
     
        if($dst_img)imagedestroy($dst_img);
        if($src_img)imagedestroy($src_img);


        return $dst_dir;
    }


    function cropImageAndPreserveAspectRatio($sourcePath, $destinationPath, $targetWidth, $targetHeight, $quality = 90) {
        // Load the image
        $image = imagecreatefromjpeg($sourcePath);
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);
    
        // Calculate aspect ratio
        $originalAspect = $originalWidth / $originalHeight;
        $targetAspect = $targetWidth / $targetHeight;
    
        // Determine cropping dimensions
        if ($originalAspect >= $targetAspect) {
            // If image is wider than the target aspect ratio
            $newHeight = $originalHeight;
            $newWidth = $originalHeight * $targetAspect;
            $cropX = ($originalWidth - $newWidth) / 2;
            $cropY = 0;
        } else {
            // If image is taller than the target aspect ratio
            $newWidth = $originalWidth;
            $newHeight = $originalWidth / $targetAspect;
            $cropX = 0;
            $cropY = ($originalHeight - $newHeight) / 2;
        }
    
        // Create a new blank image with the target dimensions
        $croppedImage = imagecreatetruecolor($targetWidth, $targetHeight);
    
        // Crop and resize the image
        imagecopyresampled(
            $croppedImage, $image, 
            0, 0, 
            $cropX, $cropY, 
            $targetWidth, $targetHeight, 
            $newWidth, $newHeight
        );
    
        // Save the cropped and compressed image
        imagejpeg($croppedImage, $destinationPath, $quality);
    
        // Free memory
        imagedestroy($image);
        imagedestroy($croppedImage);
    }
    

    public static function user_get_image($token){
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/data/images/profiles/'.$token.'.jpg')) {
            $img =  '/data/images/profiles/'.$token.'.jpg';
         }else{
            $img = '/assets/images/logo_clinabs.png';
         }

         return $img;
    }

    public static function getUserImage($token, $encode = true){
        
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/data/images/profiles/'.$token.'.jpg')) {
           if($encode) {
               $img =  'data:image/jpeg;base64,'.base64_encode(
                       file_get_contents($_SERVER['DOCUMENT_ROOT'].'/data/images/profiles/'.$token.'.jpg'));
           }else {
               $img =  '/data/images/profiles/'.$token.'.jpg';
           }
        }else{
            if($encode) {
                $img = 'data:image/png;base64,'.base64_encode(
                        file_get_contents($_SERVER['DOCUMENT_ROOT'].'/assets/images/logo_clinabs.png')
                    );
            }else {
                $img = '/assets/images/logo_clinabs.png';
            }
        }

        return $img == 'data:image/jpeg;base64,' ? 'data:image/png;base64,'.base64_encode(
            file_get_contents($_SERVER['DOCUMENT_ROOT'].'/assets/images/logo_clinabs.png')
        ) : $img;
    }

    public static function getDoc($docName){
        if($docName != '') {
            if(file_exists($_SERVER['DOCUMENT_ROOT'].'/data/images/docs/'.$docName)) {
                $array = explode('.', $_SERVER['DOCUMENT_ROOT'] . '/data/images/docs/' . $docName);
                if(end($array) == 'jpg' || end($array) == 'jpeg' || end($array) == 'png'){
                    return "/assets/images/ico-doc-img.svg";
                }else {
                    return "/assets/images/ico-doc-pdf.svg";
                }
            }else{
                return "/assets/images/ico-doc-large.svg";
            }
        }else{
            return "/assets/images/ico-doc-large.svg";
        }
    }

    public static function getDocIcon($docName){
        if($docName != '') {
            if(file_exists($_SERVER['DOCUMENT_ROOT'].'/data/images/docs/'.$docName)) {
                $array = explode('.', $_SERVER['DOCUMENT_ROOT'] . '/data/images/docs/' . $docName);
                if(end($array) == 'jpg' || end($array) == 'png' || end($array) == 'jpeg'){
                    return "/assets/images/ico-doc-img.svg";
                }else {
                    return "/assets/images/ico-doc-pdf.svg";
                }
            }else{
                return "/assets/images/ico-doc-large.svg";
            }
        }else{
            return "/assets/images/ico-doc-large.svg";
        }
    }

    public static function getProductImage($docName) {
        if(file_exists($_SERVER['DOCUMENT_ROOT'].'/data/images/produtos/'.$docName)) {
            return sprintf("data:image/jpeg;base64,%s", base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/data/images/produtos/'.$docName)));
        }else{
            return sprintf("data:image/jpeg;base64,%s", base64_encode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/assets/images/user1.jpg')));
        }
    }

    public static function calcularIdade($dataNascimento){
        $date = new DateTime($dataNascimento );
        $interval = $date->diff( new DateTime( date('Y-m-d') ) );
        return $interval->format( '%Y anos' );
    }

    public static function fetchJSON($url){
        $curl = curl_init();
        curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $url
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode(json_encode($response));
    }
    
    public static function formatPhone($phone){
        // Remove all non-numeric characters from the phone number
        $phone = preg_replace('/\D/', '', $phone);
        // Check if the phone number is valid
        if (strlen($phone) == 11) {
            return preg_replace('/^(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        } else {
           return  preg_replace('/^(\d{2})(\d{2})(\d{5})(\d{4})/', '+$1 ($2) $3-$4', $phone);
        }
    }
  
  public static function getDocByBlob($buffer) {
    $finfo = finfo_open();
    $mime = finfo_buffer($finfo, $buffer, FILEINFO_MIME_TYPE);
    finfo_close($finfo);
    
    
    if($mime == 'application/pdf') {
      return "/assets/images/ico-doc-pdf.svg";
    } else if($mime == 'image/png' || $mime == 'image/jpg' || $mime == 'image/jpeg') {
      return "/assets/images/ico-doc-img.svg";
    } else {
      return "/assets/images/ico-doc-large.svg";
    }
    
  }
}