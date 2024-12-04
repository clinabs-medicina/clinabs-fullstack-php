<?php
class Image
{
    public static function cropImage(
        $filepath,
        $cropX,
        $cropY,
        $cropWidth,
        $cropHeight,
        $outputPath
    ) {
        // Step 1: Load the image
      
        $imageType = mime_content_type($filepath);
      
        switch ($imageType) {
            case IMAGETYPE_GIF:
                $sourceImage = imagecreatefromgif($filepath);
                break;
            case IMAGETYPE_JPEG:
                $sourceImage = imagecreatefromjpeg($filepath);
                break;
            case IMAGETYPE_PNG:
                $sourceImage = imagecreatefrompng($filepath);
                break;
            default:
                echo "Unsupported image type.";
                return false;
        }

        // Step 2: Define the size and position of the crop
        $cropArray = [
            "x" => $cropX,
            "y" => $cropY,
            "width" => $cropWidth,
            "height" => $cropHeight,
        ];

        // Step 3: Use imagecrop() to crop the image
        $croppedImage = imagecrop($sourceImage, $cropArray);
        if ($croppedImage === false) {
            echo "Cropping failed.";
            return false;
        }

        // Step 4: Output or save the cropped image
        switch ($imageType) {
            case IMAGETYPE_GIF:
                imagegif($croppedImage, $outputPath);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($croppedImage, $outputPath);
                break;
            case IMAGETYPE_PNG:
                imagepng($croppedImage, $outputPath);
                break;
        }

        // Step 5: Destroy the image resources
        imagedestroy($sourceImage);
        imagedestroy($croppedImage);

        return true;
    }
  
    public static function reduce($max_width, $max_height, $source_file, $dst_dir, $quality = 100, $deleteSource = false){
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
    
        if(file_exists($source_file) && $deleteSource){
          unlink($source_file);
        }
    
        return file_exists($dst_dir)  &&  filesize($dst_dir) > 0;
    }

  public static function compress($source, $destination, $quality = 100) {
    $mime = mime_content_type($source);

      if ($mime == 'image/jpeg') 
          $image = imagecreatefromjpeg($source);

      elseif ($mime == 'image/gif') 
          $image = imagecreatefromgif($source);

      elseif ($mime == 'image/png') 
          $image = imagecreatefrompng($source);

      imagejpeg($image, $destination, $quality);

      return file_exists($destination);
  }

  public static function getThumbnail($source, $destination,$width = 1000,$height = 1000, $quality = 100){
    // Get new dimensions
    list($width_orig, $height_orig) = getimagesize($source);

    $ratio_orig = $width_orig/$height_orig;

    if ($width/$height > $ratio_orig) {
       $width = $height*$ratio_orig;
    } else {
       $height = $width/$ratio_orig;
    }

    // Resample
    $image_p = imagecreatetruecolor($width, $height);
    $image = imagecreatefromjpeg($source);
    imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);

    // Output
    imagejpeg($image_p, $destination, $quality);
  }
}
