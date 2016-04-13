<?php

require_once 'controller/Controller.php';

class CrudEnum
{
    const Create = 0;
    const Read = 1;
    const Update = 2;
    const Delete = 3;
    const Create_Done = 4;
    const Update_Done = 5;
    const Delete_Done = 6;
}

class CrudController extends Controller
{

    /**
     * This using to access data base.
     * @var ActiveRecord
     */
    private $model;
    /**
     * Controler where crud is.
     * @var Controller
     */
    private $controller;
    /**
     * Current action: create, read, edit, delete
     * @var string
     */
    private $act;
    /**
     * Default url params.
     * @var array
     */
    private $url_params = array();
    /**
     * Filtr to show by CrudModel::Read()
     * @var array or null
     */
    private $filtr = null;
    /**
     * Last updated or deleted row here.
     * @var array
     */
    private $row = array();
    /**
     * Using to add log
     * @var Logs
     */
    private $logs = null;
    /**
     * Columns to show in table.
     * @var array
     */
    private $columns = array();
    /**
     * Inputs using in create and update forms.
     * @var array of Input
     */
    private $inputs = array();
    /**
     * Is it editable table?
     * @var boolean
     */
    private $editable = true;
    /**
     * Texts for buttons.
     * @var array
     */
    private $submits = array('create' => 'Add new', 'update' => 'Edit', 'delete' => 'Delete');
    /**
     * Titles.
     * @var array
     */
    private $titles = array('create' => 'Add new', 'update' => 'Edit', 'delete' => 'Delete');
    /**
     * User message to show after any action.
     * @var array
     */
    private $messages = array('create' => 'Add new', 'update' => 'Edit', 'delete' => 'Delete');
    /**
     * Messages to log
     * @var array
     */
    private $logsMessages = array('create' => null, 'update' => null, 'delete' => null);

    public function __construct(ActiveRecord & $_model, $options = array())
    {
        $this->SetSubmits(array('create' => tr('Add new'), 'update' => tr('Edit'), 'delete' => tr('Delete')));
        $this->SetTitles(array('create' => tr('Create'), 'update' => tr('Edit: %title%'), 'delete' => tr('Delete: %title%')));
        $this->SetMessages(array('create' => tr('New was created.'), 'update' => tr('Edited: %title%.'), 'delete' => tr('Deleted: %title%.')));

        $this->options['tableid'] = 'crudtable';
        $this->options['pageable'] = false;
        $this->options['perpage'] = 30;
        $this->options['shift'] = 3;
        $this->options['action_trigger'] = 'crud_act';
        parent::__construct($options);

        $this->model = $_model;
        $this->action_default = 'read';
        $this->action_trigger = $this->options['action_trigger'];

        switch (_GET($this->action_trigger)->low())
        {
            case 'create':
                $this->act = CrudEnum::Create;
                break;
            case 'update':
                $this->act = CrudEnum::Update;
                break;
            case 'delete':
                $this->act = CrudEnum::Delete;
                break;
            default:
                $this->act = CrudEnum::Read;
                break;
        }
    }

    public function SetController(Controller $controller)
    {
        $this->controller = $controller;
    }

    public function GetAct()
    {
        return $this->act;
    }

    public function GetRow()
    {
        return $this->row;
    }

    public function SetUrlParams($p)
    {
        $this->url_params = $p;
    }

    public function SetFilter($find = null)
    {
        $this->filtr = $find;
    }

    public function SetLog(Logs & $log)
    {
        $this->logs = &$log;
    }

    private function Log($doing)
    {
        if ($doing !== null)
            $this->logs->Log($doing);
    }

    public function SetColumns($cols)
    {
        foreach ($cols as $id => $params)
        {
            $def = array('title' => '##', 'width' => '', 'view' => 'crud/item');
            $cols[$id] = array_merge($def, $params);
        }
        $this->columns += $cols;
    }

    public function AddInput(Input $input)
    {
        $this->inputs[$input->id] = $input;
    }

    public function SetEditable($_editable)
    {
        $this->editable = $_editable;
    }

    public function SetSubmits($_submits)
    {
        $this->submits = array_merge($this->submits, $_submits);
    }

    public function SetTitles($_titles)
    {
        $this->titles = array_merge($this->titles, $_titles);
    }

    public function SetMessages($_messages)
    {
        $this->messages = array_merge($this->messages, $_messages);
    }

    public function SetLogsMessages($_messages)
    {
        $this->logsMessages = array_merge($this->logsMessages, $_messages);
    }

    public function action_create()
    {
        $form = new FormController();

        $form->SetAction(url($this->url_params + array($this->action_trigger => 'create')));

        foreach ($this->inputs as & $input)
        {
            $form->AddInput($input);
        }

        $form->SetTitle($this->titles['create']);
        $form->SetSubmit($this->submits['create']);

        if ($form->Check())
        {
            // Every thing is good. Lets Create!
            $this->row = $form->GetArray();
            $this->model->setData($this->row);
            $this->model->create();
            $this->act = CrudEnum::Create_Done;
            $this->Log(format($this->logsMessages['create'], $this->row));
            // And show new added items.
            $this->redirect(url($this->url_params + array('message' => format($this->messages['create'], $this->row))));
        }
        else
        {
            // Get html of form
            $this->controller->view->content = $form->render();
        }
    }

    public function action_read()
    {
        $message = _GET('message')->value('');

        $vars = array(
            'url_params' => $this->url_params,
            'columns' => $this->columns,
            'editable' => $this->editable,
            'pageable' => $this->options['pageable'],
            'tableid' => $this->options['tableid'],
            'action_trigger' => $this->action_trigger,
            'message' => $message
        );

        if ($this->options['pageable'])
        {
            $vars['pageable'] = true;

            $page = _GET('page')->int(1);
            $start = $this->options['perpage'] * ($page - 1);
            if ($start < 0)
                $start = 0;
            $per = $this->options['perpage'];

            $count = 0;
            $vars['rows'] = $this->model->page($count, $start, $per, $this->filtr);

            $vars['count'] = $count;
            $vars['page'] = $page;
            $vars['perpage'] = $this->options['perpage'];
            $vars['shift'] = $this->options['shift'];
        }
        else
        {
            $vars['page'] = 1;
            $vars['rows'] = $this->model->find($this->filtr);
        }


        $this->controller->view->content = View::Call('crud/table', $vars);
    }

    public function action_update()
    {
        $id = _GET('id')->int(0);
        $page = _GET('page')->int();

        $record = $this->model->load($id);
        
        if ($record)
            $this->row = $record->getData();

        $editing = $id && $record ? true : false;

        $form = new FormController();
        $form->SetAction(url($this->url_params + array($this->action_trigger => 'update', 'id' => $id, 'page' => $page)));

        foreach ($this->inputs as & $input)
        {
            if ($editing)
                $input->SetValue(Input::get_by_id($record, $input->id));

            $form->AddInput($input);
        }

        $form->SetTitle(format($this->titles['update'], $this->row));
        $form->SetSubmit(format($this->submits['update'], $this->row));

        if ($form->Check())
        {
            // Every thing is good. Lets Update!
            $id = _GET('id')->int();
            $this->row = $form->GetArray();

            $this->model->setData($this->row);
            $this->model->setPrimary($id);
            $this->model->save();

            $this->act = CrudEnum::Update_Done;
            $this->Log(format($this->logsMessages['update'], $this->row));

            $this->redirect(url($this->url_params + array('message' => format($this->messages['update'], $this->row), 'page' => $page)));
        }
        else
        {
            // Get html of form
            $this->controller->view->content = $form->render();
        }
    }

    public function action_delete()
    {
        $id = _GET('id')->int(0);
        $record = $this->model->load($id);

        if ($record)
            $this->row = $record->getData();

        if (_GET('type')->low() == 'ajax')
        {
            $this->request_type()->set_json();

            if ($record)
            {
                $this->model->delete($id);
                $this->act = CrudEnum::Delete_Done;
                $this->Log(format($this->logsMessages['delete'], $this->row));
                $this->controller->json = array('success' => true, 'message' => format($this->messages['delete'], $this->row));
            }
            else
            {
                $this->controller->json = array('success' => false, 'message' => tr('Can not delete this.'));
            }
        }
        else
        {
            $form = new FormController();
            $form->SetAction(url(array($this->action_trigger => 'delete')));

            $form->AddHidden(new InputHidden('id', $id));

            $form->SetTitle(format($this->titles['delete'], $this->row));
            $form->SetSubmit(format($this->submits['delete'], $this->row));

            if ($form->Check())
            {
                $id = _POST('id')->int();
                $record = $this->model->load($id);

                if ($record)
                {
                    $this->model->delete($id);
                    $this->act = CrudEnum::Delete_Done;

                    $this->row = $record->getData();
                    $this->Log(format($this->logsMessages['delete'], $this->row));
                    $this->Redirect(url(array('message' => format($this->messages['delete'], $this->row))));
                }
            }
            else
            {
                // Get html of form
                $this->controller->view->content = $form->render();
            }
        }
    }

}

?>
