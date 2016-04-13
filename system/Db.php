<?php

require_once 'lib/DbSimple/Generic.php';

class Db
{
    /**
     *
     * @param array $config
     * @return DbSimple_Mysql
     */
    public static function Connect($config)
    {
        $mysql = DbSimple_Generic::connect('mysql://' . $config['username'] . ':' . $config['password'] . '@' . $config['hostname'] . '/' . $config['dbname'] . '');
        //new Db(Elfchat::Config("hostname"), , Elfchat::Config("password"), Elfchat::Config("dbname"));
        $mysql->query('SET NAMES \'utf8\'');
        $mysql->setIdentPrefix($config['prefix']);
        // Устанавливаем обработчик ошибок.
        $mysql->setErrorHandler('databaseErrorHandler');

        return $mysql;
    }

}

// Код обработчика ошибок SQL.
function databaseErrorHandler($message, $info)
{
    // Если использовалась @, ничего не делать.
    if(!error_reporting())
        return;
    // Выводим подробную информацию об ошибке.
    echo "SQL Error: $message<br><pre>";
    print_r($info);
    echo "</pre>";
    exit();
}

?>
