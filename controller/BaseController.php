<?php

require_once 'controller/Controller.php';

abstract class BaseController extends Controller
{

    /**
     * If true, you can entry like guest. 
     *
     * @var bool
     */
    protected $unauthorized_entry = true;
    /**
     * Is user already logined
     *
     * @var bool
     */
    protected $authorized = false;
    /**
     *
     * @var Auth
     */
    protected $auth;

    public function __construct()
    {
        parent::__construct();

        $this->auth = new Auth();
    }

    public function before_run()
    {
        parent::run();
    }

    public function run()
    {
        if($this->auth->Check(_COOKIE('id')->int(), _COOKIE('hash')->value()))
        {
            $this->authorized = true;

            // Banned
            if($this->check_ban($this->auth->user->id, $this->auth->user->ip))
                $this->redirect('banned.php');

            // Group settings
            if(!$this->auth->user->group_settings->enter)
            {
                $this->auth->Logout(false);
                $this->error(tr('The administrator has forbidden login to the chat for you.'), '');
            }
        }

        if((!$this->unauthorized_entry && $this->authorized) || $this->unauthorized_entry)
        {
            $this->before_run();
        }
        else
        {
            $this->redirect('index.php');
        }
    }

    public function check_ban($user_id, $ip)
    {
        $ban = new Ban();
        $ban = $ban->find_one(where("(b.ban_id = 1 AND b.user_id = '?user_id') OR (b.ban_ip = 1 AND b.ip = '?ip')", array(
                            'user_id' => $user_id,
                            'ip' => $ip
                        )));
        if($ban)
            return $ban->is_banned();
        else
            return false;
    }

    public function error($error, $description)
    {
        $error_page = new View('error');
        $error_page->set_vars(array(
            'error' => $error,
            'description' => $description,
        ));
        $this->header_html();
        echo $error_page->render();
        exit();
    }

}

?>
