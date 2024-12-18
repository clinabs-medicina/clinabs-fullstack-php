<?php
class IMG {
  public static function compressImage($source, $destination, $quality = 50) {
      // Get image info
      $imgInfo = getimagesize($source);
      $mime = $imgInfo['mime'];

      // Create a new image from file
      switch($mime){
          case 'image/jpeg':
              $image = imagecreatefromjpeg($source);
              break;
          case 'image/png':
              $image = imagecreatefrompng($source);
              break;
          case 'image/gif':
              $image = imagecreatefromgif($source);
              break;
          default:
              $image = imagecreatefromjpeg($source);
      }

      // Save image
      imagejpeg($image, $destination, $quality);

      // Free up memory
      imagedestroy($image);
  }

  public static function resizeImage($file, $outputFile, $width, $height, $quality) {
        list($originalWidth, $originalHeight, $imageType) = getimagesize($file);

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($file);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($file);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($file);
                break;
            default:
                throw new Exception('Unsupported image type');
        }

        $aspectRatio = $originalWidth / $originalHeight;
        if ($width / $height > $aspectRatio) {
            $width = $height * $aspectRatio;
        } else {
            $height = $width / $aspectRatio;
        }

        $newImage = imagecreatetruecolor($width, $height);

        // Preserve transparency for PNG and GIF images
        if ($imageType == IMAGETYPE_PNG || $imageType == IMAGETYPE_GIF) {
            imagecolortransparent($newImage, imagecolorallocatealpha($newImage, 0, 0, 0, 127));
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);

        switch ($imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($newImage, $outputFile, $quality);
                break;
            case IMAGETYPE_PNG:
                imagepng($newImage, $outputFile, $quality);
                break;
            case IMAGETYPE_GIF:
                imagegif($newImage, $outputFile);
                break;
        }

        imagedestroy($image);
        imagedestroy($newImage);
    }
}