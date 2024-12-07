<?php
class Crypto {

  public static function encryptFile($inputFile, $outputFile, $key) {
      // Read the file content
      $data = file_get_contents($inputFile);

      // Generate an IV
      $ivLength = openssl_cipher_iv_length('aes-256-cbc');
      $iv = openssl_random_pseudo_bytes($ivLength);

      // Encrypt the data
      $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

      // Combine IV and encrypted data
      $result = $iv . $encryptedData;

      // Save the encrypted content to the output file
      file_put_contents($outputFile, $result);
  }

  // Function to decrypt a file
  public static function decryptFile($inputFile, $outputFile, $key) {
      // Read the encrypted file content
      $data = file_get_contents($inputFile);

      // Extract the IV
      $ivLength = openssl_cipher_iv_length('aes-256-cbc');
      $iv = substr($data, 0, $ivLength);

      // Extract the encrypted data
      $encryptedData = substr($data, $ivLength);

      // Decrypt the data
      $decryptedData = openssl_decrypt($encryptedData, 'aes-256-cbc', $key, OPENSSL_RAW_DATA, $iv);

      // Save the decrypted content to the output file
      file_put_contents($outputFile, $decryptedData);
  }

}