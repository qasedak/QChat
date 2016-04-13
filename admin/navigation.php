<?php

$_NAVIGATION = array(
    'index' => array('link' => 'index.php', 'img' => 'skin/img/elfchat.png', 'text' => tr('Main'),),
    'settings' => array('link' => 'settings.php', 'img' => 'skin/img/Gear_Alt.png', 'text' => tr('Settings'),),
    'rooms' => array('link' => 'rooms.php', 'img' => 'skin/img/Discussion.png', 'text' => tr('Rooms'),),
    'users' => array('link' => 'users.php', 'img' => 'skin/img/users.png', 'text' => tr('Users'),),
    'groups' => array('link' => 'groups.php', 'img' => 'skin/img/groups.png', 'text' => tr('Groups'),),
    'logs' => array('link' => 'logs.php', 'img' => 'skin/img/Logs.png', 'text' => tr('Logs'),),    
);

if(TERMINAL)
    $_NAVIGATION['exe'] = array('link' => 'exe.php', 'img' => 'skin/img/Terminal.png', 'text' => tr('Terminal'),);
?>
