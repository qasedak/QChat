<?php

require_once 'models/Message.php';
require_once 'system/Signal.php';
require_once 'system/MessageProcessing.php';

class Protocol extends Signal
{

    public function action_set_room(User $user, $receive)
    {
        $room = new Room();
        $room->id = $receive['room_id'];
        if($room->find_this())
        {
            if($receive['password'] == $room->password)
            {
                $user->room = $room->id;
                $user->save();
                $data = array(
                    'type' => 'change_room',
                    'user_id' => $user->id,
                    'room_id' => $room->id
                );
                $this->save($data);
                $this->emit('send_all', $data);
            }
            else
            {
                $data = array(
                    'type' => 'alert',
                    'personal' => $user->id,
                    'msg' => tr('You have entered an incorrect password.')
                );
                $this->save($data);
                $this->emit('send_one', $user->id, $data);
            }
        }
    }


    public function action_msg(User $user, $receive)
    {
        // Is is silence?
        if($user->silent_until >= time())
        {
            $message = format(tr('You can not send chat messages until %time%.'), array('time' => date('H:i:s', $user->silent_until)));
            $this->send_user_message($user, $message);
            return;
        }

        // Antispam
        if(Elf::Settings('antispam') && $user->group_settings->enable_antispam)
        {
            $rows = Message::model()->select(
                'COUNT(m.id) as count',
                where('m.user_id = \'?user_id\' AND m.time >= ?time',
                      array(
                           'user_id' => $user->id,
                           'time' => time() - Elf::Settings('antispam_time')
                      )
                )
            );

            if($rows[0]['count'] > Elf::Settings('antispam_count'))
            {
                $user->silent_until = time() + Elf::Settings('antispam_silence_time');
                $user->save();
                $this->send_user_message($user, tr('Too many messages from you.'));
                return;
            }
        }

        // Init of vars.
        $msg = new MessageProcessing($receive['msg']);
        $text = $msg->process()->message();
        $data = array(
            'type' => 'msg',
            'room' => $user->room,
            'time' => date(ELf::Settings('date_format')),
            'user_id' => $user->id,
            'msg' => $text
        );

        // Process.

        if(isset($receive['color']))
        {
            if(preg_match('/^#[a-f0-9]{6}$/i', $receive['color']))
                $data['color'] = $receive['color'];
        }

        if(isset($receive['personal']))
        {
            $data['personal'] = intval($receive['personal']);
        }

        // Send     

        if(isset($data['personal']))
        {
            // Send personal message
            $personal_name = '';
            $personal_user = User::model()->load($data['personal']);
            if($personal_user)
                $personal_name = $personal_user->name;
            $data_to_save = array(
                'name' => $user->name,
                'personal_name' => $personal_name
            );

            $this->save($data, $data_to_save);

            if($data['personal'] != $user->id)
            {
                // Message to another user               
                $this->emit('send_one', $data['personal'], $data);
                $this->emit('send_one', $user->id, $data);
            }
            else
            {
                // Message to myself
                $this->emit('send_one', $user->id, $data);
            }
        }
        else
        {
            // Send message to room

            $data_to_save = array(
                'name' => $user->name
            );
            $this->save($data, $data_to_save);
            $this->emit('send_room', $user->room, $data);
        }
    }

    private function send_user_message(User $user, $message)
    {
        // Message to myself
        $data = array(
            'type' => 'msg',
            'time' => date(ELf::Settings('date_format')),
            'room' => $user->room,
            'personal' => $user->id,
            'msg_type' => 'warning',
            'msg' => $message
        );
        $this->save($data);
        $this->emit('send_one', $user->id, $data);
    }

    private function send_all_message($message)
    {
        $data = array(
            'type' => 'msg',
            'time' => date(ELf::Settings('date_format')),
            'msg_type' => 'warning',
            'msg' => $message,
        );
        $this->save($data);
        $this->emit('send_all', $data);
    }

    public function action_status(User $user, $receive)
    {
        $mp = new MessageProcessing($receive['status']);
        $status = $mp->process()->message();
        $status = mb_substr($status, 0, Elf::Settings('status_length'), 'UTF-8');

        $user->status = $status;
        $user->save();

        $data = array(
            'type' => 'status',
            'user_id' => $user->id,
            'status' => $status
        );

        $this->save($data);
        $this->emit('send_all', $data);
    }

    public function action_avatar(User $user, $receive)
    {
        // If some user attr have been changed from the outside,
        // you need to reload the user, otherwise in websocket it will not work.
        $user->load($user->id);

        $data = array(
            'type' => 'avatar',
            'user_id' => $user->id,
            'avatar' => $user->avatar
        );

        $this->save($data);
        $this->emit('send_all', $data);
    }

    public function action_connect(User $user)
    {
        $user->connect = true;
        $user->save();
        $data = array(
            'type' => 'connect',
            'user' => $user->to_array(),
            'except' => $user->id
        );
        $this->save($data);
        $this->emit('send_except', $user->id, $data);

        if(Elf::Settings('login_messages'))
        {
            // Say to all
            $data = array(
                'type' => 'msg',
                'msg_type' => 'connect',
                'room' => 0,
                'time' => date(ELf::Settings('date_format')),
                'user_id' => $user->id,
                'name' => $user->name,
                'msg' => ''
            );

            $this->save($data);
            $this->emit('send_all', $data);
        }
    }

    public function action_disconnected(User $user)
    {
        if(Elf::Settings('login_messages'))
        {
            // Say to all
            $data = array(
                'type' => 'msg',
                'msg_type' => 'disconnect',
                'room' => 0,
                'time' => date(ELf::Settings('date_format')),
                'user_id' => $user->id,
                'name' => $user->name,
                'msg' => ''
            );

            $this->save($data);
            $this->emit('send_all', $data);
        }

        // Disconnect
        $user->connect = false;
        $user->time = time(); // Refresh time, to dont kick user from online by timeout.
        $user->save();
        $data = array(
            'type' => 'disconnect',
            'id' => $user->id,
            'except' => $user->id
        );
        $this->save($data);
        $this->emit('send_except', $user->id, $data);
    }

    /**
     *
     * Moderator actions
     *
     */
    public function action_silence(User $user, $receive)
    {
        include_once 'models/Logs.php';
        if($user->is_moder())
        {
            $user_id = $receive['user_id'];
            $silence = new User();
            if($silence->load($user_id))
            {
                ModeratorLog::Add($user->id, format(tr('Silenced user: %%'), $silence->name));

                $min = 60 * 5; // 5 minutes

                $silence->silent_until = time() + $min;
                $silence->save();

                $this->send_all_message(format(tr('%name% in silence for %min% minutes.'), array('name' => $silence->name, 'min' => $min / 60)));
            }
        }
    }

    public function action_kill(User $user, $receive)
    {
        include_once 'models/Logs.php';
        if($user->is_moder())
        {
            $user_id = $receive['user_id'];
            $kill_user = new User();
            if($kill_user->load($user_id))
            {
                ModeratorLog::Add($user->id, format(tr('Kill user: %%'), $kill_user->name));
                $kill_user->online = false;
                $kill_user->remember = false;
                $this->action_disconnected($kill_user);
                $this->send_all_message(format(tr('%name% was thrown out of the chat.'), array('name' => $kill_user->name)));
            }
        }
    }
// QASEDAKGROUP - 2013-2014 - Mohammad Anbarestany - Mahroo Baghery
    public function action_ban(User $user, $receive)
    {
        include_once 'models/Logs.php';
        if($user->is_moder())
        {
            $user_id = $receive['user_id'];
            $ban = new User();
            if($ban->load($user_id))
            {
                $ban->online = false;
                $ban->remember = true;
                $this->action_disconnected($ban);
                $message = format(tr('%name% was banned.'), array('name' => $ban->name));

                if(!empty($receive['reason']))
                    $message .= "\n" . format(tr('Reason: %reason%'), array('reason' => $receive['reason']));

                if(!empty($receive['for_time']))
                    $message .= "\n" . format(tr('For time: %time%'), array('time' => $receive['for_time']));

                $this->send_all_message($message);
            }
        }
    }

    public function action_delete_message(User $user, $receive)
    {
        include_once 'models/Logs.php';
        if($user->is_moder())
        {
            $msg_id = $receive['msg_id'];
            $message = new Message();
            if($message->load($msg_id))
            {
                ModeratorLog::Add($user->id, format(tr('Delete message (id=%%)'), $message->id));

                $message->delete = true;
                $message->save();

                $data = array(
                    'type' => 'delete_message',
                    'delete_id' => $message->id
                );
                $this->save($data);
                $this->emit('send_all', $data);
            }
        }
    }

    /**
     * Save data to DB, and put in $data msg_id key.
     * @param array $data
     * @param array $data_to_save
     * @return int
     */
    protected function save(& $data, & $data_to_save = array())
    {
        $msg = new Message();
        $msg->time = time();

        if(isset($data['type']))
            $msg->type = $data['type'];

        if(isset($data['user_id']))
            $msg->user_id = $data['user_id'];

        if(isset($data['personal']))
            $msg->personal = $data['personal'];

        if(isset($data['except']))
            $msg->except = $data['except'];

        if(isset($data['room']))
            $msg->room = $data['room'];

        $msg->data = json_encode(array_merge($data, $data_to_save));
        $insert_id = $msg->create();

        $data['msg_id'] = $insert_id;

        return $insert_id;
    }

}

?>
