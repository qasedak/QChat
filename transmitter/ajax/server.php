<?php

require_once '../../init.php';
require_once 'models/Message.php';
require_once 'system/Protocol.php';

class Server extends BaseController
{

    /**
     * @var Protocol
     */
    protected $protocol;

    public function __construct()
    {
        parent::__construct();
        $this->unauthorized_entry = false;
        $this->protocol = new Protocol();
    }

    public function action_load()
    {
        // Say online in not connected
        if(!$this->auth->user->connect)
        {
            $this->protocol->action_connect($this->auth->user);
        }

        // Timeout
        $this->action_timeout();

        // Last id detect.
        $last = _GET('last')->int();
        if($last == 0)
        {
            $last_message = Message::model()->find_one();
            $last = $last_message->id;
            $this->json['last'] = $last;
        }


        $messages = Message::model()->find(
                        where("     (m.id > ?last)
                                AND (m.except <> ?id OR m.except = 0)
                                AND (m.room = ?room OR m.room = 0)
                                AND (m.personal = 0 OR m.personal = ?id OR m.user_id = ?id)
                                AND (m.delete = 0)",
                                array(
                                    'last' => $last,
                                    'room' => $this->auth->user->room,
                                    'id' => $this->auth->user->id
                                )
                        ),
                        30
        );
        if(count($messages))
        {
            $this->json['last'] = $messages[0]->id;
            $messages = array_reverse($messages);
            $this->json['msg'] = array();
            foreach($messages as $msg)
            {
                $data = (array)json_decode($msg->data);
                $data['msg_id'] = $msg->id;
                unset($data['name']);
                $this->json['msg'][] = $data;
            }
        }
        $this->header_json();
        $this->display_json();
    }

    public function action_send()
    {
        $name = 'action_' . _POST('type')->low();
        if(method_exists($this->protocol, $name))
        {
            $this->protocol->{$name}($this->auth->user, (array) $_POST);
        }
    }

    public function action_timeout()
    {
        $current_time = time();
        $users = User::model()->find("u.connect = 1");
        foreach($users as $user)
        {
            if($current_time - $user->time > Elf::Settings('ajax_timeout'))
            {
                $user->online = false;
                $user->save();

                if($user->guest == 1)
                {
                    $user->delete();
                }

                $this->protocol->action_disconnected($user);
            }
        }
    }

    public function action_logout()
    {
        $this->protocol->action_disconnected($this->auth->user);
    }

}

$server = new Server();
$server->run();
?>
