<?php
class Str {
    public static function strToHex($string){
        $hex='';
        for ($i=0; $i < strlen($string); $i++){
            $hex .= dechex(ord($string[$i]));
        }
        return $hex;
    }
    
    
    public static function hexToStr($hex){
        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2){
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }

    public static function MaxVal(string $string,int $maximum, string $where) {
        return (strlen($string) > $maximum ? trim(substr($string, 0 ,$maximum - 3)).'...' : $string);
    }

    public static function formatPhoneNumber($phone) {
        // Use a regular expression to capture the parts of the phone number
        $formatted = preg_replace('/^(\d{2})(\d{2})(\d{1})(\d{4})(\d{4})$/', '+$1 ($2) $3 $4-$5', $phone);

        return $formatted;
    }
       
}