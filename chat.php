<?php

require_once 'init.php';

class Chat extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->unauthorized_entry = false;
    }

    /**
     * Display chat
     */
    public function action_index()
    {
        // Check room id from get params.
        if(_GET('room')->is_set())
        {
            $this->auth->user->room = _GET('room')->int();
            $this->auth->user->save();
        }

        $this->view = new View('chat');
        $this->view->copyright = $this->copyright();
        $this->view->user = $this->auth->user;
        $this->display();
    }

    /**
     * Get user ip and user_agent in json format.
     */
    public function action_remote()
    {
        $this->json = array(
            'ip' => $this->auth->IP(),
            'user_agent' => $this->auth->user_agent()
        );
        $this->display();
    }

    /**
     * Load users online list.
     */
    public function action_list()
    {
        include_once 'models/Room.php';

        $rooms = Room::model()->find();
        foreach($rooms as $room)
        {
            $this->json['rooms'][$room->id] = array(
                'id' => $room->id,
                'title' => $room->title,
                'current' => $this->auth->user->room == $room->id,
                'lock' => $room->password != ''
            );
        }

        $current_time = time();
        $users = User::model()->find("u.online = 1");
        foreach($users as $user)
        {
            if($user->connect || $user->id == $this->auth->user->id)
            {
                $this->json['users'][] = $user->to_array();
            }
            else
            {
                if($current_time - $user->time > 30)
                {
                    // If after 30 seconds still dont conneced,
                    // delete online
                    $user->online = false;
                    $user->save();

                    if($user->guest == 1)
                    {
                        $user->delete();
                    }
                }
            }
        }

        $this->display();
    }

    /**
     * Load last messages in room and users infos.
     */
    public function action_last_messages()
    {
        include_once 'models/Message.php';

        $this->json['messages'] = array();
        $this->json['users'] = array();
        
        $room = _GET('room')->int($this->auth->user->room);        
        
        $messages = Message::model()->find(
                        where("     (m.type = 'msg' OR m.type = '')
                                AND (m.except <> ?id OR m.except = 0)
                                AND (m.room = ?room OR m.room = 0)
                                AND (m.personal = 0 OR m.personal = ?id OR m.user_id = ?id)
                                AND (m.delete = 0)",
                                array(
                                    'room' => $room,
                                    'id' => $this->auth->user->id
                                )
                        ),
                        Elf::Settings('last_messages')
        );
        $messages = array_reverse($messages);
        $users = array();

        foreach($messages as $msg)
        {
            $data = json_decode($msg->data);
            $data->msg_id = $msg->id;

            // Remember all users from this messages.
            $users[$msg->user_id] = true;
            if($msg->personal != 0)
                    $users[$msg->personal] = true;

            $this->json['messages'][] = $data;
        }

        foreach($users as $id => $true)
        {
            $user = User::model()->load($id);
            if($user)
                $this->json['users'][] = $user->to_array();
        }

        $this->display();
    }

}

$chat = new Chat();
$chat->run();
?>
