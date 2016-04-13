<?php

require_once 'init.php';
require_once 'controller/CrudController.php';
require_once 'models/Admin.php';

class AdminControllerPage extends AdminController
{

    public function __construct()
    {
        parent::__construct(array(
                    'select' => 'users',
                    'title' => tr('Admins'),
                    'url' => 'admins.php?'
                ));
        $this->view = new View('users/users');
    }

    public function action_index()
    {
        $this->view->set_vars(array('filtr' => 'admins'));

        $admins = Admin::model();

        $crud = new CrudController($admins);
        $crud->SetController($this);
        $act = $crud->GetAct();

        $page_title = '';
        if ($act == CrudEnum::Read)
            $page_title = tr('Admins');

        if ($act == CrudEnum::Read)
        {
            $columns = array(
                'id' => array('title' => tr('ID'), 'width' => '20px'),
                'name' => array('title' => tr('Name')),
                'time' => array('title' => tr('Last login'), 'width' => '180px', 'view' => 'crud/time'),
                'ip' => array('title' => tr('IP'), 'width' => '120px',),
            );
            $crud->SetColumns($columns);
        }

        $title = new InputText('name', tr('Name'));
        $title->SetValid(array(new Validation_NotEmpty(), new Validation_MaxLength(), new Validation_Unique($admins, 'name', tr('This name alredy using by another admin.'))));
        $crud->AddInput($title);

        if ($act == CrudEnum::Create)
        {
            $password = new InputPassword('password', tr('Password'));
            $password->SetValid(new Validation_NotEmpty());
            $crud->AddInput($password);
        }
        else if ($act == CrudEnum::Update)
        {
            $password = new InputPassword('password', tr('Password. <i>Leave blank if you do not want to change password.</i>'));
            $crud->AddInput($password);
        }

        $crud->SetLog($this->logs);
        $crud->SetLogsMessages(array('create' => tr('New admin was added: %name%'), 'update' => tr('Admin was edited: %name%'), 'delete' => tr('Admin(%id%) was deleted: %name%')));
        $crud->SetSubmits(array('create' => tr('Add new admin'), 'update' => tr('Edit admin'), 'delete' => tr('Delete admin')));
        $crud->SetTitles(array('create' => tr('Add new admin'), 'update' => tr('Edit admin: %name%'), 'delete' => tr('Delete admin: %name%')));
        $crud->SetMessages(array('create' => tr('New admin was added.'), 'update' => tr('Admin was edited: %name%'), 'delete' => tr('Admin was deleted: %name%')));

        $crud->run();       
        $this->display();
    }

}

$page = new AdminControllerPage();
$page->Login();
?>
