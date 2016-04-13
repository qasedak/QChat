<?php

require_once 'init.php';
require_once 'controller/CrudController.php';
require_once 'models/Group.php';

class GroupsController extends AdminController
{
    public function __construct()
    {
        parent::__construct(array(
                    'select' => 'groups',
                    'title' => tr('Groups'),
                    'url' => 'groups.php?'
                ));

        $this->view = new View('groups');
    }

    public function action_index()
    {
        $groups = Group::model();

        $crud = new CrudController($groups);
        $crud->SetController($this);

        $crud->SetLog($this->logs);
        $crud->SetLogsMessages(array('create' => tr('New group was created: %title%'), 'update' => tr('Group was edited: %title%'), 'delete' => tr('Group was deleted: %title%')));
        $crud->SetSubmits(array('create' => tr('Add new group'), 'update' => tr('Edit group'), 'delete' => tr('Delete group')));
        $crud->SetTitles(array('create' => tr('Create new group'), 'update' => tr('Edit group: %title%'), 'delete' => tr('Delete group: %title%')));
        $crud->SetMessages(array('create' => tr('New group was created.'), 'update' => tr('Group was edited: %title%'), 'delete' => tr('Group was deleted: %title%')));


        $act = $crud->GetAct();

        $page_title = '';
        if ($act == CrudEnum::Read)
            $page_title = tr('Groups');
        $this->view->title = $page_title;

        $columns = array(
            'id' => array('title' => tr('ID'), 'width' => '20px'),
            'title' => array('title' => tr('Title'))
        );
        $crud->SetColumns($columns);

        $title = new InputText('title', tr('Title of new group.'));
        $title->SetValid(array( new Validation_NotEmpty(), new Validation_MaxLength() ));
        $title->AddValid (new Validation_Unique($groups, 'title', tr('Title of group have to be unique.')));
        $crud->AddInput($title);

        $crud->AddInput(new InputCheck('settings#enter', tr('Can enter to chat?'), true));
        $crud->AddInput(new InputText('settings#icon', tr('Icon of group'), ''));
        $crud->AddInput(new InputCheck('settings#bbcode_status', tr('Can use bbcode in status?'), false));
        $crud->AddInput(new InputCheck('settings#enable_antispam', tr('Turn on antispam?'), true));

        $crud->run();      
        $this->display();
    }

    public function action_reset()
    {
        $group_settings = array(
            'enter' => true,
            'icon' => '',
            'bbcode_status' => false,
            'enable_antispam' => true
        );
        Group::model()->updateAll('', array(
            'group_setting' => serialize($group_settings)
        ));
        $this->logs->Log(tr('All groups was reseted.'));
        $this->redirect(url( array('message' => tr('All groups reseted.')) ));
    }
}

$page = new GroupsController();
$page->Login();
?>
