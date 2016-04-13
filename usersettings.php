<?php

require_once 'init.php';
require_once 'controller/FormController.php';

$skin = new Skin(ELFCHAT_ROOT . '/skin/special/default');
View::set_skin($skin);

class UserSettings extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->unauthorized_entry = false;
    }

    public function action_index()
    {
        $this->view = new View('user/usersettings');
        $form = new FormController();
        $form->AddInput(new InputCheck('remember', tr('Log in automatically?'), $this->auth->user->remember));
        $form->AddInput(new InputCheck('settings#show_tooltip', tr('Show tooltip?'), $this->auth->user->settings->show_tooltip));
        $form->AddInput(new InputCheck('settings#show_images', tr('Show images in chat?'), $this->auth->user->settings->show_images));
        $form->AddInput(new InputCheck('settings#play_immediately', tr('Play sounds immediately in chat?'), $this->auth->user->settings->play_immediately));
        $form->SetSubmit(tr('Save'));

        if($form->Check())
        {
            $array = $form->GetArray();
            $this->auth->user->remember = $array['remember'];
            $this->auth->user->settings = $array['settings'];
            $this->auth->user->save();

            $this->view->settings = json_encode($array['settings']);
        }

        $this->view->content = $form->render();
        $this->display();
    }

}

$userSettings = new UserSettings();
$userSettings->run();
?>
