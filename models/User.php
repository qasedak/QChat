<?php

/**
 * @property int $id
 * @property int $ownuser
 * @property string $outsider
 * @property int $outid
 * @property string $name
 * @property string $mask
 * @property string $avatar
 * @property int $guest
 * @property int $moder
 * @property array $settings
 * @property int $group
 * @property string $group_title
 * @property Group $group_settings
 * @property string $password
 * @property string $email
 * @property string $session
 * @property int $online
 * @property string $ip
 * @property int $room
 * @property string $status
 * @property int $time
 * @property boolean $connect
 * @property int $remember
 * @property int $silent_until
 */
class User extends ActiveRecord
{
    private $default_settings = array(
        'show_tooltip' => true,
        'show_images' => true,
        'play_immediately' => true
    );

    public function __construct()
    {
        parent::__construct();
        $this->primary_key = 'id';
        $this->sql->select('u.*, g.settings AS group_settings, g.title AS group_title');
        $this->others = array('group_settings', 'group_title');
        $this->sql->from('?_users', 'u');
        $this->sql->order('u.time', 'ASC');
        $this->sql->join('?_groups g', 'u.group = g.id');
        $this->data_handler['connect'] = 'boolean';
        $this->data_handler['online'] = 'boolean';
        $this->data_handler['remember'] = 'boolean';
    }

    public function to_array()
    {
        return array(
            'id' => $this->id,
            'room' => $this->room,
            'name' => $this->name,
            'status' => $this->status,
            'mask' => $this->mask,
            'avatar' => $this->avatar,
            'icon' => $this->group_settings->icon,
            'group_title' => $this->group_title,
            'group_settings' => array(
                'bbcode_status' => $this->group_settings->bbcode_status
            ),
        );
    }

    public function is_moder()
    {
        return $this->moder == true;
    }

    /**
     *
     * @param class $className
     * @return User
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function set_password($value)
    {
        if(trim($value) != '')
        {
            $value = sha1($value);
            $this->set('password', $value);
        }
    }

    public function set_name($name)
    {
        // Censure
        if(Elf::Settings('censure') && class_exists('Censure'))
        {
            $name = Censure::parse($name, '1', '', false, tr('[CENSURED]'));
        }
        $this->set('name', $name);
    }

    protected function before_db($data)
    {
        // If password is empty, leave old password
        if(array_key_exists('password', $data))
        {
            $pass = $data['password'];
            if($pass !== null && $pass == '')
                unset($data['password']);
        }

        // Serialize user settings
        if(array_key_exists('settings', $data))
            $data['settings'] = serialize($data['settings']);


        return $data;
    }

    protected function after_db($data)
    {
        $data['group_settings'] = (array)unserialize($data['group_settings']);
        $data['settings'] = array_merge($this->default_settings, (array)unserialize($data['settings']));
        return $data;
    }

    public function get_online_count()
    {
        $rows = $this->select(
            'count(u.id) as count',
            'u.online = 1 AND u.time > '.intval(time() - Elf::Settings('ajax_timeout'))
        );

        return $rows[0]['count'];
    }

}

?>
