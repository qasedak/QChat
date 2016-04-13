<?php

require_once 'controller/AdminAuth.php';

abstract class AdminController extends AdminAuth
{

    public $logs;

    /**
     *
     * @param array $opts ( 'select' - выбранная вкладка панели управления , 'title' - заголовок окна )
     */
    public function __construct($options = array())
    {
        $options = array_merge(array('select' => 'index', 'title' => '', 'url' => 'index.php?'), $options);        
        parent::__construct($options);

        $this->set_base_url($this->options['url']);
    }

    public function SecureLinks()
    {
        try
        {
            if (SECURE_LINKS)
            {
                $signer = new HTTP_ParamSigner(Elf::Get(ADMIN_URL_SALT));
                Request::$GET = $signer->parseParam($_SERVER['QUERY_STRING']);
                Request::$REQUEST = array();
                Request::$REQUEST = Request::$GET + Request::$POST + Request::$COOKIE;
            }
        }
        catch (ElfException $e)
        {
            $this->Error($e->getMessage());
        }
    }

    public function Log($doing)
    {
        $this->logs->Log($doing);
    }

    public function Error($msg)
    {
        $this->view = new View('error');
        $this->view->error = $msg;
        $this->display();
        exit();
    }

    public function Content()
    {
        $this->logs = new AdminLog();
        $this->logs->SetId($this->admin['id']);
        $this->SecureLinks();
        $this->Main();
    }

    public function display_html()
    {
        include_once 'admin/navigation.php';
        $vars = array(
            'navigation' => $_NAVIGATION,
            'select' => $this->options['select'],
            'name' => $this->admin['name'],
            'copyright' => $this->Copyright()
        );
        $this->view->layout('panel')->set_vars($vars);
        $this->view->set_for('wrap')->title = $this->options['title'];
        parent::display_html();
    }

    public function LoginForm()
    {
        $login = new View('login', array('wrong_password' => $this->wrong_password, 'copyright' => $this->Copyright()));
        parent::header_html();
        echo $login->render();
    }

    public function ExitForm()
    {
        $exit = new View('exit', array('copyright' => $this->Copyright()));
        parent::header_html();
        echo $exit->render();
    }

    public function Main()
    {
        $this->run();
    }

}

?>
