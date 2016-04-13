<?php

require_once 'init.php';

class Archive extends BaseController
{

    public function __construct()
    {
        parent::__construct();
        $this->unauthorized_entry = Elf::Settings('archive_allow_guest');
    }

    /**
     * Display chat
     */
    public function action_index()
    {
        if(!Elf::Settings('archive_enable'))
        {
            $this->view = new View('archive_disable');
            $this->view->copyright = $this->copyright();
            $this->display();
            exit();
        }

        include_once 'models/Message.php';

        $donotShow = false;
        $password = '';

        $userId = 0;
        if ($this->auth && $this->auth->user)
            $userId = $this->auth->user->id;

        $default_room = Room::model()->find_one(array('default' => '1'));

        $roomId = $default_room->id;
        // Check room id from get params.
        if (_GET('room')->is_set()) {
            $roomId = _GET('room')->int();
            $room = Room::model()->load($roomId);
            if (empty($room)) {
                $roomId = $default_room->id;
            }
            else
            {
                if ($room->password != "") {
                    if ($room->password != _GET('password')->text('')) {
                        $donotShow = true;
                    }
                    else
                    {
                        $password = _GET('password')->text('');
                    }
                }
            }

        }

        $date = _GET('date')->text(date('d.m.Y'));
        $date = empty($date) ? date('d.m.Y') : $date;
        $show_array = explode('.', $date);
		@$show = mktime(0, 0, 0, $show_array[1], $show_array[0], $show_array[2]);
        $starttime = (int)$show;
        $endtime = $show + 86400;


        if (!$donotShow) {
            $sql = new Sql();
            $sql->select('*');
            $sql->from('?_messages', 'm');
            $sql->join('?_users u', 'm.user_id = u.id');
            $sql->order('m.id', 'DESC');
            $sql->from('?_messages', 'm');
            $sql->where(where("(m.type = 'msg')
                                AND (m.except = 0)
                                AND (m.room = ?room)
                                AND (m.personal = 0)
                                AND (m.delete = 0)
                                AND (m.time > ?starttime AND m.time < ?endtime)",
                              array(
                                   'room' => $roomId,
                                   'id' => $userId,
                                   'starttime' => $starttime,
                                   'endtime' => $endtime
                              )
                        ));


            $messagesFromDb = Elf::Db()->select($sql->limit(100));

            $messages = array();
            foreach ($messagesFromDb as $msg)
            {
                $data = json_decode($msg['data']);
                $messages[] = $data;
            }

            $messages = array_reverse($messages);
        }

        $this->view = new View('archive');
        $this->view->copyright = $this->copyright();

        if ($donotShow)
            $this->view->bad = true;
        else
            $this->view->messages = $messages;

        $this->view->password = $password;
        $this->view->roomId = $roomId;
        $this->view->date = $date;
        $this->view->starttime = $starttime;
        $this->view->rooms = Room::model()->find();
        $this->display();

    }
}

$archive = new Archive();
$archive->run();
?>
