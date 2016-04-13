<?php

require_once 'init.php';

class Index extends AdminController
{

    public function __construct()
    {
        parent::__construct(array(
                    'select' => 'index',
                    'title' => tr('Main'),
                    'url' => 'index.php?'
                ));
    }

    public function action_index()
    {
        $this->view = new View('index');

        $admins_logs = AdminLogRecord::model()->find(null, 7);
        $moders_logs = ModeratorLogRecord::model()->find(null, 7);

        $skin = new Skin(ELFCHAT_ROOT . '/skin/' . Elf::Settings('skin'));

        $this->view->set_vars(array(
            'admins_logs' => $admins_logs,
            'moders_logs' => $moders_logs,
            'version' => ELFCHAT_VERSION,
            'lang' => Elf::Settings('lang'),
            'skin' => $skin->title
        ));

        $this->display();
    }

}

$page = new Index();
$page->Login();
?>
