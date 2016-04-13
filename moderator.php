<?php

require_once 'init.php';
require_once 'controller/FormController.php';
require_once 'models/Logs.php';

$skin = new Skin(ELFCHAT_ROOT . '/skin/special/default');
View::set_skin($skin);

class Moderator extends BaseController
{

    /**
     * @var ModeratorLog
     */
    private $logs = null;

    public function __construct()
    {
        parent::__construct();
        $this->unauthorized_entry = false;        
    }

    public function before_run()
    {
        if($this->auth->user->is_moder())
        {
            parent::before_run();
        }
        else
        {
            $this->redirect('index.php');
        }
    }

    public function log($msg)
    {
        if($this->logs === null)
        {
            $this->logs = new ModeratorLog();
            $this->logs->SetId($this->auth->user->id);
        }

        $this->logs->Log($msg);
    }

    public function action_index()
    {
        $this->view = new View('moderator/table');
        $this->view->bans = Ban::model()->find();
        $this->display();
    }

    public function action_ban()
    {
        $this->view = new View('moderator/ban');

        $user_id = _GET('id')->int();
        $user = new User();

        if(!$user->load($user_id))
        {
            $user = new User();
        }

        if($user)
        {

            $form = new FormController();
            $form->SetAction('moderator.php?act=ban');

            $form->AddInput(new InputText('name', tr('User name'), $user->name));
            $form->AddInput(new InputCheck('ban_id', tr('Ban by ID?'), true));
            $form->AddInput(new InputText('user_id', tr('User id'), $user->id));
            $form->AddInput(new InputCheck('ban_ip', tr('Ban by IP?'), false));
            $form->AddInput(new InputText('ip', tr('User IP'), $user->ip));
            $form->AddInput(new InputSelect('for_time', tr('Ban for time'), 60 * 60, Ban::model()->time_array));
            $form->AddInput(new InputText('reason', tr('Reason <i>Describe the reason for the ban.</i>')));

            $form->SetSubmit(tr('Add ban'));

            if($form->Check())
            {
                $array = $form->GetArray();
                $ban = new Ban();
                $ban->setData($array);
                $ban->start_time = time();
                $ban->create();

                $msg = format(tr('Ban user: %%.'), $ban->name);
                $this->log($msg);
                $this->view->info = $msg;
                $this->view->content = '';
                $this->view->ban = $ban; // Send to chat message
            }
            else
            {
                $this->view->content = $form->render();
            }
        }
        else
        {
            $this->view->info = tr('No user with this id.');
            $this->view->content = '';
        }

        $this->display();
    }

    public function action_delete()
    {
        $index = _GET('index')->int();
        $ban = new Ban();
        if($ban->load($index))
        {
            $this->log(format(tr('Ban deleted for %%.'), $ban->name));
            $ban->delete();
        }

        $this->redirect('moderator.php');
    }

}

$moderator = new Moderator();
$moderator->run();
?>
