<?php
namespace wp_isms_contact\includes;

defined('ABSPATH') or die( 'Access Forbidden!' );

class iSMSContactCaptcha {
    function getCaptchaCode($length)
    {
        $random_alpha = md5(random_bytes(64));
        $captcha_code = substr($random_alpha, 0, $length);
        return $captcha_code;
    }
    
    function setSession($key, $value) {
        $_SESSION["$key"] = $value;
    }
    
    function getSession($key) {
        $value = "";
        if(!empty($key) && !empty($_SESSION["$key"]))
        {            
            $value = $_SESSION["$key"];
        }
        return $value;
    }

    function createCaptchaImage($captcha_code)
    {
        $target_layer = imagecreatetruecolor(72,28);
        $captcha_background = imagecolorallocate($target_layer, 204, 204, 204);
        imagefill($target_layer,0,0,$captcha_background);
        $captcha_text_color = imagecolorallocate($target_layer, 0, 0, 0);
        imagestring($target_layer, 5, 10, 5, $captcha_code, $captcha_text_color);
        
        return $target_layer;
    }
                                 
    function renderCaptchaImage($imageData)
    {
       // header("Content-type: image/jpeg");
        return imagejpeg($imageData);
    }
    
    function validateCaptcha($captcha) {
        $isValid = false;
        $capchaSessionData = $this-> getSession("captcha_code");
        
        if($capchaSessionData == $captcha) 
        {
            $isValid = true;
        }
        return $isValid;
    }
}
?>