<?php

require_once(dirname(__FILE__) . '/includes/iSMSContactCaptcha.php');

$captcha = new \wp_isms_contact\includes\iSMSContactCaptcha();    

$captcha_code = $captcha->getCaptchaCode(6);

$captcha->setSession('captcha_code', $captcha_code);

$imageData = $captcha->createCaptchaImage($captcha_code);
echo $captcha->renderCaptchaImage($imageData);

?>