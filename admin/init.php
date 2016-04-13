<?php
// Define all before require main init.php
define('ELFCHAT_ROOT', dirname(__FILE__) . '/..');
define('SKIN_PATH', ELFCHAT_ROOT . '/admin/skin');
define('NO_CONTROLER', true); // We dont need BaseController in admin cp.
define('SECURE_LINKS', true);

// Only After all defines require init.php
require_once ELFCHAT_ROOT . '/init.php';

define('ADMIN_URL_SALT', 'ADMIN_URL_SALT');
define('TERMINAL', true);

Elf::Set(ADMIN_URL_SALT, '123');
Elf::Set(ACTION_POST_HASH, '123');

// Afler require init.php we can require anything, becouse init include path.
require_once 'controller/AdminController.php';
require_once 'controller/FormController.php';
require_once 'system/Url.php';
require_once 'models/Logs.php';
?>
