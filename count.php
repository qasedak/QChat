<?php

require_once 'init.php';

Header("Content-Type: text/javascript");
$user = User::model();
$count = $user->get_online_count();
if($count != 0)
{
    echo "document.write('($count)');";
}
