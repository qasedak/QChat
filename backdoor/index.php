<?php
define('ELFCHAT_ROOT', dirname(__FILE__) . '/..');
require_once '../init.php';

class Backdoor extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function action_index()
    {
        if(Elf::Settings('others_enable'))
        {
            $get = $_GET;
            $hash = $get['hash'];
            unset($get['hash']);

            if(sha1(sha1(http_build_query($get)) . sha1(Elf::Settings('others_key'))) == $hash)
            {
                if($this->auth->LoginOthers($_GET))
                {
                     $this->redirect(Elf::Settings('chat_url') . '/chat.php');
                }
            }
        }

        $this->redirect(Elf::Settings('chat_url') . '/index.php');
    }
}

$page = new Backdoor();
$page->run();
?>
