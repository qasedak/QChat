<?php

require_once 'init.php';
require_once 'controller/FormController.php';

class LostPassword extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function action_index()
    {
        $this->view = new View('lostpassword');

        session_name('signup');
        session_start();

        $form = new FormController();

        $email = new InputText('email', '');
        $email->AddValid(new Validation_Preg_Email(tr('Invalid email.')));
        $form->AddInput($email);

        $captcha = new InputText('captcha', '');
        if (isset($_SESSION['captcha_keystring']))
            $captcha->AddValid(new Validation_Equals($_SESSION['captcha_keystring'], tr('Incorrect. Enter again.')));
        $form->AddInput($captcha);

        if ($form->Check())
        {
            $post = $form->GetArray();

            $users = User::model()->find(array('email' => $post['email'], 'ownuser' => 1));
            if (!empty($users))
            {
                require_once 'lib/phpmailer/class.phpmailer.php';

                $mail = new PHPMailer(); // defaults to using php "mail()"

                $mail->CharSet = "utf-8";
                $mail->From = Elf::Settings('mail_from');
                $mail->FromName = Elf::Settings('mail_from_name');
                $mail->Subject = tr('Password Recovery');
                $mail->AddAddress($post['email']);

                $new_passwords = array();

                foreach ($users as $user)
                {
                    $password = $this->generate_password();

                    $new_passwords[] = array(
                        'name' => $user->name,
                        'password' => $password
                    );

                    $user->password = $password; // It will pass to sha1() in overloaded set_password() function of User class
                    $user->save();
                }

                $body = new View('mail/passwords');
                $body->set_render_engine(new PregRender()); // Here we will use one of eval renders.
                $body->passwords = $new_passwords;

                $mail->MsgHTML($body->render());

                $mail->Send();
            }

            $this->view->done = true;
            session_destroy();
            setcookie('signup', '0', time() - 2592000);
        }
        else
        {
            $this->view->inputs = $form->GetInputs();
            $this->view->actionpost_hash = $form->actionpost_hash;
        }

        $this->view->copyright = $this->copyright();
        $this->display();
    }

    private function generate_password($length = 8)
    {
        $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++)
        {
            $string .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        return $string;
    }

}

$page = new LostPassword();
$page->run();
?>
