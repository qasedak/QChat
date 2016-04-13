<?php

require_once 'init.php';
require_once 'controller/CrudController.php';
require_once 'models/Room.php';

class RoomsController extends AdminController
{
    public function __construct()
    {
        parent::__construct(array(
                    'select' => 'rooms',
                    'title' => tr('Rooms'),
                    'url' => 'rooms.php?'
                ));

        $this->view = new View('rooms');
    }

    public function action_order()
    {
        $this->request_type()->set_json();
        $rooms = Room::model();        
        $rooms->Reorder(_POST('orderline')->value());
        $this->json = array('success' => true);
        $this->display();
    }

    public function action_index()
    {
        $rooms = Room::model();

        $crud = new CrudController($rooms);
        $crud->SetController($this);


        $crud->SetLog($this->logs);
        $crud->SetLogsMessages(array('create' => tr('New room was created: %title%'), 'update' => tr('Room was edited: %title%'), 'delete' => tr('Room was deleted: %title%')));
        $crud->SetSubmits(array('create' => tr('Add new room'), 'update' => tr('Edit room'), 'delete' => tr('Delete room')));
        $crud->SetTitles(array('create' => tr('Create new room'), 'update' => tr('Edit room: %title%'), 'delete' => tr('Delete room: %title%')));
        $crud->SetMessages(array('create' => tr('New room was created.'), 'update' => tr('Room was edited: %title%'), 'delete' => tr('Room was deleted: %title%')));


        $act = $crud->GetAct();

        if ($act == CrudEnum::Read)
            $this->view->title = tr('Rooms');
        else
            $this->view->title = '';

        $columns = array(
            'id' => array('title' => tr('ID'), 'width' => '20px'),
            'title' => array('title' => tr('Room Title')),
            'password' => array('title' => tr('Room password'), 'width' => '200px'),
            'default' => array('title' => tr('Default'), 'width' => '20px', 'view' => 'crud/tick'),
            'order' => array('title' => tr('Order'), 'width' => '40px', 'view' => 'crud/order')
        );
        $crud->SetColumns($columns);

        $title = new InputText('title', tr('Name of the new room.'));
        $title->SetValid(array(new Validation_NotEmpty(), new Validation_MaxLength()));
        $crud->AddInput($title);
        $crud->AddInput(new InputText('password', tr('Password for room. <i>Leave this input to dont use password.</i>')));
        $crud->AddInput(new InputCheck('default', tr('Default room.')));

        $crud->run();
        $this->display();
    }
}

$page = new RoomsController();
$page->Login();
?>
