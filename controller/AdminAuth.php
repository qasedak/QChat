<?php
require_once 'controller/Controller.php';
abstract class AdminAuth extends Controller
{
    private $session_name = '';

    public $wrong_password = false;

    public $admin;

    public function __construct($options = array())
    {
        parent::__construct($options);
        $this->session_name = 'elfchat_admin';
    }

    public function GetHash($hash, $time, $ip, $user_agent)
    {
        return sha1($hash . sha1($time) . sha1($ip) . sha1($user_agent));
    }

    public function Check()
    {
        if( isset($_SESSION['admin']) && isset($_SESSION['hash']) )
        {
            $admin = Elf::Db()->selectRow('SELECT * FROM ?_admins WHERE id=?', $_SESSION['admin']);

            if( $admin['logined'] == 1 && $this->GetHash($admin['hash'] , date('Ymd') , $this->IPDetect() , $_SERVER['HTTP_USER_AGENT']) == $_SESSION['hash'] )
            {
                $this->admin = $admin;
                Elf::Set(ADMIN_URL_SALT, $this->admin['admin_url_hash']); //For secure links salt
                return true;
            }
            else
            {
                return false;
            }
        }
    }

    public function TryLogin()
    {
        if( isset($_POST['admin']) && isset($_POST['password']) )
        {
            $admin = Elf::Db()->selectRow('SELECT * FROM ?_admins WHERE name=?', $_POST['admin']);

            if( $admin && sha1($_POST['password']) == $admin['password'] )
            {
                $_SESSION['admin'] = $admin['id'];

                $ip = $this->IPDetect();

                $newhash = sha1(uniqid(mt_rand(), true));

                $_SESSION['hash'] = $this->GetHash($newhash , date('Ymd') , $ip, $_SERVER['HTTP_USER_AGENT']);

                $admin_url_hash = sha1(uniqid(mt_rand(), true));

                $row = array( 'logined' => 1, 'time' => time(), 'ip' => $ip, 'hash' => $newhash, 'admin_url_hash' => $admin_url_hash );

                Elf::Db()->query('UPDATE ?_admins SET ?a WHERE id=?', $row, $admin['id']);
            }
            else
            {
                $this->wrong_password = true;
            }
        }
    }

    public function TryExit()
    {
        if( isset($_POST['exit']) || isset($_GET['exit']) )
        {
            Elf::Db()->query('UPDATE ?_admins SET logined=0 WHERE id=?', $_SESSION['admin']);

            $_SESSION['admin'] = '';
            $_SESSION['hash'] = '';
            session_destroy();
            return true;
        }
        else
        {
            return false;
        }
    }

    public function Login()
    {
        session_name($this->session_name);
        session_start();

        $this->TryLogin();
        if( $this->Check() )
        {
            if( $this->TryExit() )
            {
                $this->ExitForm();
            }
            else
            {
                $this->Content();
            }
        }
        else
        {
            $this->LoginForm();
        }

    }

    abstract function Content();

    abstract function LoginForm();

    abstract function ExitForm();

    public function IPDetect()
    {
        return $_SERVER['REMOTE_ADDR'];
    }
}
?>
