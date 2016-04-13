<?php
session_name('upgrade');
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

$skin = new Skin(ELFCHAT_ROOT . '/upgrade/skin');
View::set_skin($skin);

class Upgrade extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    protected $pages = array('index');

    public function run()
    {
        parent::run();
        $this->view->set_for('wrap')->title = tr('Upgrade');
        $this->view->set_for('wrap')->copyright = $this->copyright();
        $this->view->set_for('wrap')->act = $this->action_name;
        $this->display();
    }


    public function action_index()
    {
        $this->view = new View('index');

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

$page = new Upgrade();
$page->run();
?>
