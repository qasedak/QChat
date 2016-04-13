<?php

require_once 'init.php';

class Banned extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->unauthorized_entry = false;
    }

    public function run()
    {
        $error = tr('You are banned.');
        $description = $error;
        $ban = Ban::model()->find_one(where('b.user_id = \'?user_id\' OR b.ip = \'?ip\' ', array('user_id' => _COOKIE('id')->int(), 'ip' => $this->auth->IP() )) );
        if($ban)
        {
            if($ban->is_banned())
                $description = format(tr('You will be able to enter the chat after %time%.'), array('time' => $ban->ban_ends()));
            else
                $this->redirect('index.php');
        }

        $this->error($error, $description);
    }

}

$page = new Banned();
$page->run();
?>
