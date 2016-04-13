<?php
require_once 'init.php';

class Exiter extends BaseController
{

	public function __construct()
	{
		parent::__construct();
		$this->unauthorized_entry = false;
	}

	public function action_index()
	{
		$this->auth->Logout();		
		$this->view = new View('exit');
		$this->display();
	}
}

$page = new Exiter();
$page->run();
?>
