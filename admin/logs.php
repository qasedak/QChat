<?php

require_once 'init.php';
require_once 'controller/CrudController.php';

class LogsControl extends AdminController
{

    public function __construct()
    {
        parent::__construct(array(
                    'select' => 'logs',
                    'title' => tr('Logs'),
                    'url' => 'logs.php?'
                ));

        $this->action_trigger = 'type';
        $this->action_default = 'moderator';
        $this->view = new View('logs/logs');
    }

    public function run()
    {
        parent::run();
        $this->view->type = $this->action_name;
        $this->display();
    }

    public function action_moderator()
    {
        $crud = new CrudController(ModeratorLogRecord::model(), array(
                    'pageable' => true,
                    'perpage' => 30,
                    'shift' => 3
                ));
        $crud->SetController($this);

        $crud->SetUrlParams(array('type' => $this->action_name));

        $crud->SetEditable(false);

        $columns = array(
            'time' => array('title' => tr('Time'), 'width' => '140px', 'view' => 'crud/time'),
            'name' => array('title' => tr('Moderator'), 'width' => '180px'),
            'doing' => array('title' => tr('Action'))
        );
        $crud->SetColumns($columns);
        $crud->run();
    }

    public function action_admin()
    {
        $crud = new CrudController(AdminLogRecord::model(), array(
                    'pageable' => true,
                    'perpage' => 30,
                    'shift' => 3
                ));
        $crud->SetController($this);

        $crud->SetUrlParams(array('type' => $this->action_name));

        $crud->SetEditable(false);

        $columns = array(
            'time' => array('title' => tr('Time'), 'width' => '140px', 'view' => 'crud/time'),
            'name' => array('title' => tr('Admin'), 'width' => '180px'),
            'doing' => array('title' => tr('Action'))
        );
        $crud->SetColumns($columns);

        $crud->run();
    }

}

$page = new LogsControl();
$page->Login();
?>
