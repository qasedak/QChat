<?php

require_once 'init.php';
require_once 'models/Group.php';

class SettingsController extends AdminController
{

    public function __construct()
    {
        parent::__construct(array(
                    'select' => 'settings',
                    'title' => tr('Settings'),
                    'url' => 'settings.php?'
                ));
        $this->action_default = 'main';
    }

    public function run()
    {
        $this->view = new View('settings/settings');
        parent::run();
        $this->view->add_vars(array(
            'act' => $this->action_name,
            'message' => _GET('message')->text('')
        ));
        $this->display();
    }

    private function update_settings($array)
    {
        $settings = Elf::Get('settings');
        $settings->Set($array);
        $settings->Save();
        $this->Log(tr('Settings was updated.'));
    }

    private function settings($title, $inputs)
    {
        $form = new FormController();
        $form->SetTitle($title);
        $form->SetAction(url('act', $this->action_name));
        $form->SetSubmit(tr('Save'));

        foreach($inputs as $input)
        {
            $form->AddInput($input);
        }

        if($form->Check())
        {
            $this->update_settings($form->GetArray());
            $this->redirect(url(array('act' => $this->action_name, 'message' => tr('Settings saved successfully.'))));
        }
        else
        {
            $this->view->content = $form->render();
        }
    }

    public function action_main()
    {
        $title = tr('Main settings'); 

        $transmitters = array(
            array('value' => 'ajax', 'title' => 'Ajax Poll'),
        );

        $langs = Lang::langs();
        
        $inputs = array(
            new InputText('title', tr('Title#Chat title'), Elf::Settings('title')),
            new InputSelect('lang', tr('Language'), Elf::Settings('lang'), $langs),
            new InputText('chat_url', tr('Chat url'), Elf::Settings('chat_url')),
            new InputText('exit_url', tr('Exit url'), Elf::Settings('exit_url')),
            new InputText('cookie_domain', tr('Cookie domain'), Elf::Settings('cookie_domain')),
            new InputText('cookie_path', tr('Cookie path'), Elf::Settings('cookie_path')),
            new InputSelect('transmitter', tr('Transmitter <i>Way to communicate client to server.</i>'), Elf::Settings('transmitter'), $transmitters),
            new InputCheck('ownusers', tr('Use own users <i>Use own DB of users.</i>'), Elf::Settings('ownusers')),
            new InputCheck('guest_enable', tr('Allow guests to login'), Elf::Settings('guest_enable')),
            new InputCheck('login_messages', tr('Show login messages'), Elf::Settings('login_messages')),
            new InputText('default_status', tr('Default status'), Elf::Settings('default_status')),
            new InputText('status_list', tr('Default status list <i>Comma separated.</i>'), Elf::Settings('status_list')),
            new InputCheck('show_errors', tr('Debug mode'), Elf::Settings('show_errors')),
            new InputText('others_key', tr('Integration Key'), Elf::Settings('others_key'))
        );

        $this->settings($title, $inputs);
    }

    public function action_transmitter()
    {
        $title = tr('Transmitter settings');

        $inputs = array(
            new InputText('ajax_server', tr('Ajax Server'), Elf::Settings('ajax_server')),
            new InputText('ajax_delay', tr('Ajax Delay <i>Time of polling the server in milliseconds.</i>'), Elf::Settings('ajax_delay')),
            new InputText('ajax_timeout', tr('Ajax Timeout <i>Time of timeout in seconds.</i>'), Elf::Settings('ajax_timeout')),
        );

        $this->settings($title, $inputs);
    }

    public function action_archive()
    {
        $title = tr('Archive');

        $inputs = array(
            new InputCheck('archive_enable', tr('Allow access to the archive?'), Elf::Settings('archive_enable')),
            new InputCheck('archive_allow_guest', tr('Allow guests to log into the archive?'), Elf::Settings('archive_allow_guest')),
            new InputCheck('archive_autoclear', tr('Automatically clean archive?'), Elf::Settings('archive_autoclear')),
        );

        $this->settings($title, $inputs);
    }

    public function action_mail()
    {
        $title = tr('E-mail settings');

        $inputs = array(
            new InputText('mail_from', tr('From <i>In From will be this the address.</i>'), Elf::Settings('mail_from')),
            new InputText('mail_from_name', tr('Name <i>Send from this the name.</i>'), Elf::Settings('mail_from_name'))
        );

        $this->settings($title, $inputs);
    }

    public function action_groups()
    {
        $title = tr('Group settings');

        $groups = Group::model()->select('g.id AS value, g.title AS title');

        $inputs = array(
            new InputSelect('group_of_guest', tr('Group of guests'), Elf::Settings('group_of_guest'), $groups),
            new InputSelect('group_of_ownusers', tr('Group of users'), Elf::Settings('group_of_ownusers'), $groups)
        );

        $this->settings($title, $inputs);
    }

    public function action_filters()
    {
        $title = tr('Filters');

        $transmitters = array(
            array('value' => 'ajax', 'title' => 'Ajax Poll'),
        );


        $inputs = array(
            new InputText('strlen_name', tr('Max name length'), Elf::Settings('strlen_name')),
            new InputText('status_length', tr('Max status length'), Elf::Settings('status_length')),
            new InputText('last_messages', tr('Number of recent messages to display'), Elf::Settings('last_messages')),
            new InputText('max_message_length', tr('Max message length'), Elf::Settings('max_message_length')),
            new InputCheck('censure', tr('Censure'), Elf::Settings('censure')),
            new InputCheck('antispam', tr('Antispan'), Elf::Settings('antispam')),
            new InputText('antispam_time', tr('Antispam time <i>Time is sec.</i>'), Elf::Settings('antispam_time')),
            new InputText('antispam_count', tr('Antispam count <i>Maximum number of messages allowed in a period of antispam time.</i>'), Elf::Settings('antispam_time')),
            new InputText('antispam_silence_time', tr('Antispam silence time <i>Fine silent. Time is sec.</i>'), Elf::Settings('antispam_silence_time')),
        );

        $this->settings($title, $inputs);
    }

    public function action_skin()
    {
        $title = tr('Skin');
        $inputs = array();

        $skins = array();
        $handle = opendir(ELFCHAT_ROOT . '/skin');
        if($handle)
        {
            while(false !== ($name = readdir($handle)))
            {
                $dir = ELFCHAT_ROOT . '/skin/' . $name;
                if(is_dir($dir) && $name != "." && $name != "..")
                {
                    $skin = new Skin();                    
                    if($skin->load($dir))
                    {
                        $skins[$name] = $skin;
                    }
                }
            }
            closedir($handle);
        }

        $skins_view = new View('settings/skins');
        $skins_view->skins = $skins; 
        $inputs[] = new InputMyView('skin', tr('Skin'), Elf::Settings('skin'), $skins_view);

        $inputs[] = new InputCheck('use_avatars', tr('Use avatars in chat'), Elf::Settings('use_avatars'));

        $side_options = array(
            array('value' => true, 'title' => tr('On left side')),
            array('value' => false, 'title' => tr('On right side'))
        );
        $inputs[] = new InputSelect('online_on_left', tr('Users list'), Elf::Settings('online_on_left'), $side_options, InputSelect::Bool);
        $inputs[] = new InputText('avatar_size_width', tr('Avatar width'), Elf::Settings('avatar_size_width'));
        $inputs[] = new InputText('avatar_size_height', tr('Avatar height'), Elf::Settings('avatar_size_height'));

        $this->settings($title, $inputs);
    }

    public function action_date()
    {
        $title = tr('Date & Time');
        $inputs = array();

        $timezone_options = array();
        include('admin/timezonedb.php');
        foreach($_TIMEZONEDB as $tz)
        {
            $timezone_options[] = array('value' => $tz, 'title' => $tz);
        }

        $inputs[] = new InputSelect('timezone', tr('Timezone'), Elf::Settings('timezone'), $timezone_options);
        $inputs[] = new InputText('date_format', tr('Date format <i>As in PHP.</i>'), Elf::Settings('date_format'));

        $this->settings($title, $inputs);
    }

    public function action_reset()
    {
        $form = new FormController();
        $form->SetTitle(tr('Reset all settings?'));
        $form->SetAction(url('act', $this->action_name));
        $form->SetSubmit(tr('Reset all'));
        $form->SetReset(false);


        if($form->Check())
        {
            $settings = Elf::Get('settings');
            $settings->Reset();
            $this->Log(tr('Settings was reset.'));
            $this->redirect(url(array('act' => $this->action_default, 'message' => tr('All settings reset.'))));
        }
        else
        {
            $this->view->content = $form->render();
        }
    }

}

$page = new SettingsController();
$page->Login();
?>
