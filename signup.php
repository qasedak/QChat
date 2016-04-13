<?php
require_once 'init.php';
require_once 'controller/FormController.php';

class SignUp extends BaseController
{

	public function __construct()
	{
		parent::__construct();
	}

	public function action_index()
	{
		$this->view = new View('signup');
		
		session_name('signup');
		session_start();		
		
		$form = new FormController();

                $name = new InputText('name', '');
                $name->AddValid(new Validation_NotEmpty(tr('Name must be filled.')));
                $name->AddValid(new Validation_MinLength(2));
                $name->AddValid(new Validation_MaxLength(Elf::Settings('strlen_name')));
                $name->AddValid(new Validation_Unique(User::model(), 'name', tr('This name is already taken by another user.')));
                $name->AddValid(new Validation_Callback(create_function('$name', 'return Auth::CheckName(trim($name));'), tr('This name is already taken by another user.')));
                $form->AddInput($name);

                $password = new InputPassword('password', '');
                $password->AddValid(new Validation_MinLength(4));
                $form->AddInput($password);

                $retype_password = new InputPassword('retype_password', '');
                $retype_password->AddValid(new Validation_Equals(_POST('password')->text(), tr('Passwords must match.')));
                $form->AddInput($retype_password);

                $email = new InputText('email', '');
                $email->AddValid(new Validation_Preg_Email(tr('Invalid email.')));
                $form->AddInput($email);

                $captcha = new InputText('captcha', '');
                if(isset($_SESSION['captcha_keystring']))
                    $captcha->AddValid(new Validation_Equals($_SESSION['captcha_keystring'], tr('Incorrect. Enter again.')));
                $form->AddInput($captcha);

                if($form->Check())
                {
                    $post = $form->GetArray();

                    $user = new User();

                    $user->name = $post['name'];
                    $user->password = $post['password'];
                    $user->email = $post['email'];
                    $user->group = Elf::Settings('group_of_ownusers');
                    $user->create();

                    $this->view->done = true;
                    session_destroy();
                    setcookie('signup', '0', time()-2592000);
                }
                else
                {
                    $this->view->inputs = $form->GetInputs();
                    $this->view->actionpost_hash = $form->actionpost_hash;
                }

                $this->view->copyright = $this->copyright();
		$this->display();
	}
}

$page = new SignUp();
$page->run();
?>
