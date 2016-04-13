<?php

require_once 'init.php';

if (!TERMINAL)
    die('Turn on terminal in init.php');

require_once 'models/Admin.php';
require_once 'models/Group.php';
require_once 'models/Logs.php';
require_once 'models/Room.php';
require_once 'models/User.php';

class Terminal extends AdminController
{

    public function __construct()
    {
        parent::__construct(array(
                    'select' => 'exe',
                    'title' => tr('Terminal'),
                    'url' => 'exe.php?'
                ));
    }

    public function action_index()
    {
        if($this->admin['id'] != Elf::Settings('super_admin'))
        {
            $this->Error(tr('Only the super admin is allowed here.'));
            exit();
        }
        $echo = '';
        $code = '';
        $success = false;
        $load_code = false;
        if (_POST('eval')->is_set())
        {
            $code = _POST('eval')->value('');
        }
        else
        {
            $load_code = true;
        }

        if ($code != '')
        {
            ob_start();
            eval($code);
            $echo = ob_get_clean();
            if ($echo == '')
                $success = true;
            file_put_contents('cache/terminal_exe.txt', $code, FILE_USE_INCLUDE_PATH);
        }

        if($load_code)
        {
            $code = file_get_contents('cache/terminal_exe.txt', FILE_USE_INCLUDE_PATH);
        }

        $this->view = new View('rooms');
        $this->view->title = '';
        $this->view->content = "
            <div style='padding:30px;'>
            " . ($success ? "<div class='info'>Success!</div>" : '') . "
            <pre>$echo</pre>
            <br>
            <form action='exe.php' method='POST'>
            <textarea name='eval' spellcheck='false' style='width:100%;height:200px; background:black; color:white;'>" . escape($code) . "</textarea>
            <br><br>
            <div style='text-align: center;'><input type='submit' value='Evaluate'></div>
            </form>
            </div>";
        $this->display();
    }

}

$page = new Terminal();
$page->Login();
?>
