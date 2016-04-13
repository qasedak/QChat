<?php
error_reporting (E_ALL);

include('kcaptcha.php');

if(isset($_REQUEST['name']))
{
	session_name($_REQUEST['name']);
	session_start();
}


$captcha = new KCAPTCHA();

if(isset($_REQUEST['name']))
{
	$_SESSION['captcha_keystring'] = $captcha->getKeyString();
}

?>
