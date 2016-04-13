<?php
session_name('install');
session_start();

if(!isset($_SESSION['lang']))
{
    if(strpos($_SERVER["HTTP_ACCEPT_LANGUAGE"], 'fa') === false)
        $_SESSION['lang'] = 'fa';
    else
        $_SESSION['lang'] = 'fa';
}

if(isset($_SESSION['lang']) && is_scalar($_SESSION['lang']))
    define('LANG', $_SESSION['lang']);
else
    define('LANG', 'fa');
    
define('NO_DB', true);
define('NO_SETTINGS', true);
require_once '../init.php';

$skin = new Skin(ELFCHAT_ROOT . '/install/skin');
View::set_skin($skin);

class Install extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    protected $pages = array('index', 'config', 'db', 'settings');

    public function run()
    {
        parent::run();
        $this->view->set_for('wrap')->title = tr('Install ElfChat');
        $this->view->set_for('wrap')->copyright = $this->copyright();
        $this->view->set_for('wrap')->act = $this->action_name;
        $this->display();
    }

    public function action_index()
    {
        $this->view = new View('index');

        foreach($this->pages as $page)
            unset($_SESSION['success_' . $page]);

        $dirs = array();

        if(!is_writable(ELFCHAT_ROOT . '/cache'))
            $dirs[] = '/cache';

        if(!is_writable(ELFCHAT_ROOT . '/upload'))
            $dirs[] = '/upload';

        if(empty($dirs))
        {
            $this->view->ok = true;
            $_SESSION['success_index'] = true;
        }
        else
            $this->view->dirs = $dirs;
    }

    public function action_config()
    {
        $this->view = new View('config');

        if(_POST('save')->is_set())
        {
            $this->view->saved = true;

            // Config

            $config = array(
                'hostname' => _POST('hostname')->value(),
                'username' => _POST('username')->value(),
                'password' => _POST('password')->value(),
                'dbname' => _POST('dbname')->value(),
                'prefix' => _POST('prefix')->value()
            );

            $var = var_export($config, true);
            $php = "<?php " . '$_CONFIG = ' . $var . "; ?>";
            file_put_contents(ELFCHAT_ROOT . '/cache/config.php', $php);

            // Settings

            $chat_url = _POST('chat_url')->text();

            //clearing url:
            $chat_url = trim($chat_url);
            $chat_url = 'http://' . str_replace('http://', '', $chat_url);
            if(substr($chat_url, -1) == '/')
                $chat_url = substr($chat_url, 0, strlen($chat_url) - 1);

            $url_data = parse_url($chat_url);
            $chat_host = $url_data['host'];
            $chat_path = array_key_exists('path', $url_data) ? $url_data['path'] : '';


            $settings = array(
                'title' => _POST('title')->text(),
                'chat_url' => $chat_url,
                'cookie_domain' => '.' . $chat_host,
                'websocket_server' => $chat_host,
                'websocket_path' => $chat_path . '/transmitter/websocket/server.php',
                'ajax_server' => $chat_url . '/transmitter/ajax/server.php'
            );

            $var = var_export($settings, true);
            $php = "<?php " . '$_SETTINGS = ' . $var . "; ?>";
            file_put_contents(ELFCHAT_ROOT . '/cache/settings.php', $php);

            $_SESSION['success_config'] = true;
        }
        else
        {

            // MySQl config
            $this->view->config = array(
                'hostname' => 'localhost',
                'username' => 'root',
                'password' => '',
                'dbname' => '',
                'prefix' => 'elfchat_'
            );
            $filename = 'cache/config.php';
            if(file_exists(ELFCHAT_ROOT . '/' . $filename))
            {
                include($filename);
                if(isset($_CONFIG))
                    $this->view->config = array_merge($this->view->config, $_CONFIG);
            }

            // Chat settings

            $this->view->settings = array(
                'title' => 'QChat',
                'chat_url' => 'http://mydomain.com/chat'
            );
            $filename = 'cache/settings.php';
            if(file_exists(ELFCHAT_ROOT . '/' . $filename))
            {
                include($filename);
                if(isset($_SETTINGS))
                    $this->view->settings = array_merge($this->view->settings, $_SETTINGS);
            }
        }
    }

    public function action_db()
    {
        $this->view = new View('db');

        $filename = 'cache/config.php';
        if(file_exists(ELFCHAT_ROOT . '/' . $filename))
        {
            include($filename);

            if(@mysql_connect($_CONFIG['hostname'], $_CONFIG['username'], $_CONFIG['password']) && @mysql_select_db($_CONFIG['dbname']))
            {
                $this->view->connected = true;

                if(_GET('create')->is_set())
                {
                    $this->view->created = true;

                    $sql_array = file_get_contents(ELFCHAT_ROOT . '/install/database.sql');

                    $sql_array = explode(';', $sql_array);

                    include_once 'models/Group.php';
                    $default_group_settings = Group::default_settings();

                    foreach($sql_array as &$sql)
                    {
                        $sql = str_replace('%default_group_settings%', mysql_real_escape_string($default_group_settings), $sql);
                    }

                    $this->querys($sql_array, $_CONFIG);

                    if(count($this->view->errors) == 0)
                        $_SESSION['success_db'] = true;
                }
            }
        }
    }

    public function action_settings()
    {
        $this->view = new View('settings');
        $filename = 'cache/config.php';
        if(file_exists(ELFCHAT_ROOT . '/' . $filename))
        {
            include($filename);

            if(@mysql_connect($_CONFIG['hostname'], $_CONFIG['username'], $_CONFIG['password']) && @mysql_select_db($_CONFIG['dbname']))
            {
                $this->view->connected = true;

                if(_POST('save')->is_set())
                {
                    $this->view->saved = true;
                    $sql_array = array();

                    $sql_array[] = "SET NAMES utf8";
                    $sql_array[] = "DELETE FROM `elfchat_admins` WHERE `elfchat_admins`.`id` = 1 LIMIT 1";
                    $sql_array[] = "INSERT INTO `elfchat_admins` (`id`, `name`, `password`) VALUES ('1', '"
                            . mysql_real_escape_string($_POST['name']) . "', '"
                            . sha1(mysql_real_escape_string($_POST['password'])) . "')";

                    $sql_array[] = "DELETE FROM `elfchat_rooms` WHERE `elfchat_rooms`.`id` = 1 LIMIT 1";
                    $sql_array[] = "INSERT INTO `elfchat_rooms` (`id`, `title`, `order`, `password`, `default`) VALUES ('1', '"
                            . mysql_real_escape_string(_POST('room_title')->text()) . "', '0', '', '1')";

                    $this->querys($sql_array, $_CONFIG);

                    if(count($this->view->errors) == 0)
                        $_SESSION['success_settings'] = true;
                }
            }
        }
    }

    private function querys($sql_array, $_CONFIG)
    {
        $this->view->errors = array();
        $query_number = 0;
        foreach($sql_array as $sql)
        {
            $query_number += 1;
            $sql = str_replace(array("\n", "\t", "\r"), ' ', $sql);
            $sql = trim($sql);

            // Prefix:
            $sql = str_replace('elfchat_', $_CONFIG['prefix'], $sql);

            if($sql == '')
                continue;

            $result = mysql_query($sql);
            if(!$result)
            {
                $this->view->errors[] = mysql_error() . "(Query number: $query_number)";
            }
        }
    }

    public function action_goodbye()
    {
        $this->view = new View('goodbye');

        $success = true;
        foreach($this->pages as $page)
            if(!isset($_SESSION['success_' . $page]))
                $success = false;

        $this->view->success = $success;

        $filename = 'cache/settings.php';
        if(file_exists(ELFCHAT_ROOT . '/' . $filename))
        {
            include($filename);
            $this->view->chat_url = $_SETTINGS['chat_url'];
        }

        $filename = 'settings.default.php';
        if(file_exists(ELFCHAT_ROOT . '/' . $filename))
        {
            include($filename);
            $this->view->serial = $_SETTINGS['serial_number'];
        }
    }

}

$page = new Install();
$page->run();
?>
