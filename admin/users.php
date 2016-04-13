<?php

require_once 'init.php';
require_once 'controller/CrudController.php';
require_once 'models/User.php';
require_once 'models/Group.php';

class UsersControl extends AdminController
{

    public function __construct()
    {
        parent::__construct(array(
                    'select' => 'users',
                    'title' => tr('Users'),
                    'url' => 'users.php?'
                ));

        $this->view = new View('users/users');
    }

    public function action_index()
    {
        $filtr = _GET('filtr')->low('allusers');
        $this->view->filtr = $filtr;

        $users = User::model();
        $crud = new CrudController($users, array(
                    'pageable' => true,
                    'perpage' => 20,
                    'shift' => 3
                ));
        $crud->SetController($this);
        $act = $crud->GetAct();

        $url_params = array('filtr' => $filtr);
        if($filtr == 'moders')
        {
            $crud->SetFilter('u.moder = 1');
        }
        else if($filtr == 'groups')
        {
            $group_filtr = _GET('group_filtr')->int(-1);
            $crud->SetFilter(array('group' => $group_filtr));
            $url_params['group_filtr'] = $group_filtr;
            $this->view->group_filtr = $group_filtr;
        }
        else if($filtr == 'find')
        {
            $find_name = _GET('find_name')->text();
            $find_email = _GET('find_email')->text();

            if($act == CrudEnum::Read)
            {
                $form = new FormController();
                $form->SetTitle(tr('Find user'));
                $form->SetAction(url('filtr', 'find'));
                $form->SetSubmit(tr('Find'));
                $form->SetReset(false);
                $form->AddInput(new InputText('find_name', tr('Name'), $find_name));
                $form->AddInput(new InputText('find_email', tr('Email'), $find_email));

                if($form->Check())
                {
                    $post = $form->GetArray();
                    $find_name = $post['find_name'];
                    $find_email = $post['find_email'];
                }
                $this->view->find = $form->render();
            }

            // TODO: Rewrite this code. XSS.
            $find = array();
            if($find_name != '')
                $find[] = "u.name LIKE '" . mysql_real_escape_string($find_name) . "'";
            if($find_email != '')
                $find[] = "u.email LIKE '" . mysql_real_escape_string($find_email) . "'";
            $crud->SetFilter(implode(' AND ', $find));

            $url_params['find_name'] = $find_name;
            $url_params['find_email'] = $find_email;
        }
        $crud->SetUrlParams($url_params);


        if($act == CrudEnum::Read)
        {
            $columns = array(
                'id' => array('title' => tr('ID'), 'width' => '20px'),
                'group_title' => array('title' => tr('Group'), 'width' => '180px'),
                'name' => array('title' => tr('Name')),
                'email' => array('title' => tr('Email'), 'width' => '180px')
            );
            $crud->SetColumns($columns);
        }
        else
        {

            $title = new InputText('name', tr('Name'));
            $title->SetValid(array(new Validation_NotEmpty(), new Validation_MaxLength(), new Validation_Unique($users, 'name', tr('This name alredy using by another user.'))));
            $crud->AddInput($title);

            if($act == CrudEnum::Create)
            {
                $password = new InputPassword('password', tr('Password'));
                $password->SetValid(new Validation_NotEmpty());
                $crud->AddInput($password);
            }
            else if($act == CrudEnum::Update)
            {
                $password = new InputPassword('password', tr('Password <i>Leave blank if you do not want to change password.</i>'));
                $crud->AddInput($password);
            }

            $crud->AddInput(new InputText('mask', tr('Mask <i>Use "*" as user name.</i>')));

            $crud->AddInput(new InputText('avatar', tr('Avatar')));

            $crud->AddInput(new InputText('email', tr('Email')));

            $groups = Group::model()->select('g.id AS value, g.title AS title');
            $crud->AddInput(new InputSelect('group', tr('Group'), 1, $groups));

            $crud->AddInput(new InputCheck('moder', tr('Moderator <i>If you want to give moderator permissions to this user.</i>')));
        }

        $crud->SetLog($this->logs);
        $crud->SetLogsMessages(array('create' => tr('New user was added: %name%'), 'update' => tr('User was edited: %name%'), 'delete' => tr('User(%id%) was deleted: %name%')));
        $crud->SetSubmits(array('create' => tr('Add new user'), 'update' => tr('Edit user'), 'delete' => tr('Delete user')));
        $crud->SetTitles(array('create' => tr('Add new user'), 'update' => tr('Edit user: %name%'), 'delete' => tr('Delete user: %name%')));
        $crud->SetMessages(array('create' => tr('New user was added.'), 'update' => tr('User was edited: %name%'), 'delete' => tr('User was deleted: %name%')));

        $crud->run();
        $this->display();
    }

}

$page = new UsersControl();
$page->Login();
?>
