<?php

if(!file_exists('cache/config.php'))
{
    header('Location: install/');
    exit();
}

require_once 'init.php';

class Index extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function action_index()
    {
        if (!Elf::Settings('ownusers') && !Elf::Settings('guest_enable'))
        {
            $this->error(tr('An error'), tr('To access the chat you must login.'));
        }

        $this->view = new View('login');

        $error_guest = '';
        if (_POST('guest_name')->is_set())
        {
            if ($this->auth->LoginGuest(_POST('guest_name')->text()))
            {
                $this->redirect_chat();
            }
            else
            {
                $error_guest = $this->auth->get_error();
            }
        }

        if (_GET('gn')->is_set())
        {
            if ($this->auth->LoginGuest(_GET('gn')->text()))
            {
                $this->redirect_chat();
            }
            else
            {
                $error_guest = $this->auth->get_error();
            }
        }

        $error_ownuser = '';
        if (_POST('name')->is_set())
        {
            if ($this->auth->LoginUser(_POST('name')->value(), _POST('password')->value(), _POST('remember')->bool()))
            {
                $this->redirect_chat();
            }
            else
            {
                $error_ownuser = $this->auth->get_error();
            }
        }

        if (_COOKIE('id')->is_set() && _COOKIE('hash')->is_set())
        {
            if ($this->auth->CheckRemembers(_COOKIE('id')->value(), _COOKIE('hash')->value()))
            {
                $this->redirect_chat();
            }
        }

        if ($this->authorized)
        {
            if(Elf::Settings('archive_autoclear'))
            {
                include_once 'models/Message.php';
                Message::model()->clearAll();
            }
            $this->redirect_chat();
        }

        $this->view->set_vars(array('error_guest' => $error_guest, 'error_ownuser' => $error_ownuser, 'copyright' => $this->copyright()));
     
        $this->display();
    }

    public function redirect_chat()
    {
        // Check room id from get params.
        if (_GET('room')->is_set())
        {
            $this->auth->user->room = _GET('room')->int();
            $this->auth->user->save();
        }
        $this->redirect('chat.php');
    }

}

if($_SERVER['QUERY_STRING'] == 'serial')
{
    die('Serial number: 9b299c5d6a0b180e4610de7eb92ee05f60063ac1');
}

$page = new Index();
$page->run();
?>
