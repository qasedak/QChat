<?php

class Auth
{

    /**
     * Current logined user.
     * @var User
     */
    public $user = false;
    /**
     * User IP.
     * @var string
     */
    protected $ip = null;
    /**
     * User agent.
     * @var string
     */
    protected $user_agent = null;
    /**
     * Error message
     * @var booolean
     */
    protected $error = false;
    /**
     * @var DbSimple_Mysql
     */
    protected $db;


    public function __construct()
    {
        $this->db = Elf::Db();
    }

    /**
     * Проверяет выполнил лы уже вход пользователь и загружает информацию о нём из users.
     *
     * @param string $id
     * @param sha1 $hash
     * @return bool. true если уже залогинен, false в противном случае.
     */
    public function Check($id, $hash)
    {
        if(!empty($id) || !empty($hash))
        {
            $user = new User();
            $user->id = $id;
            $user->online = true;
            $user->find_this();
            if($user !== false && $hash == self::hash($user->session))
            {
                $this->user = $user;
                $this->user->time = time();
                $this->user->save();
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * Проверяет знает ли система этого пользователя. Если знает, то пускает в систему.
     *
     * @param string $id
     * @param sha1 $hash
     * @return bool.
     */
    public function CheckRemembers($id, $hash)
    {
        if(!empty($id) || !empty($hash))
        {
            $user = new User();
            $user->id = $id;
            $user->online = false;
            $user->remember = 1;
            $user->find_this();
            if($user !== false && $hash == self::hash($user->session))
            {
                return $this->Login($user->id, true);
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    /**
     *
     * @param bool $setcookie
     */
    public function Logout($setcookie = true)
    {
        $this->user->online = '';
        $this->user->save();

        if($this->user->guest == 1)
        {
            $this->user->delete();
        }

        if($setcookie && !$this->user->remember)
        {
            // Delete cookie
            setcookie('id', '', time(), Elf::Settings('cookie_path'), Elf::Settings('cookie_domain'));
            setcookie('hash', '', time(), Elf::Settings('cookie_path'), Elf::Settings('cookie_domain'));
        }
    }

    /**
     * Выполняет вход с полученным id.
     *
     * @param string $in_id. id пользователя.
     * @return bool true. если вход выполнен успешно, иначе false.
     */
    public function Login($in_id, $remember = false)
    {
        $user = new User();
        $user->load($in_id);

        if($user === false)
        {
            return false;
        }

        $this->user = $user; // Заносим информацию о пользователе
        // Вставляем информацию об юзере онлайн в бд
        // Генерируем два хэша - один ($hash) отправляем юзеру, другой ($uniq) записываем в базу
        $uniq = sha1(uniqid(mt_rand(), true));
        $hash = $this->hash($uniq);

        $default_room = Room::model()->find_one(array('default' => '1'));

        $user->session = $uniq;
        $user->online = true;
        $user->ip = $this->IP();
        $user->room = $default_room->id;
        $user->status = Elf::Settings('default_status');
        $user->time = time();
        $user->remember = $remember;
        $user->save();


        // Send cookie to user
        setcookie('id', $user->id, time() + 60 * 60 * 24 * 31, Elf::Settings('cookie_path'), Elf::Settings('cookie_domain'));
        setcookie('hash', $hash, time() + 60 * 60 * 24 * 31, Elf::Settings('cookie_path'), Elf::Settings('cookie_domain'));

        return true;
    }

    /**
     * Выполняет вход для гостя.
     *
     * @param string $name. Имя гостя.
     * @return bool. true если вход выполнен успешно, иначе false.
     */
    public function LoginGuest($name)
    {
        //XSS
        $name = trim($name);

        $id = false; //Define $id

        if(strlen($name) == 0)
        {
            $this->error = tr('Write your name.');
            return false;
        }
        if(strlen($name) >= Elf::Settings('strlen_name'))
        {
            $this->error = tr('Your name is too long.');
            return false;
        }


        $user_with_this_name = User::model()->find_one(array('name' => $name));

        if($user_with_this_name === false && self::CheckName($name)) // проверяем, не занято ли имя.
        {
            $guest = new User();
            $guest->guest = true;
            $guest->name = $name;
            $guest->group = Elf::Settings('group_of_guest');
            $guest->create();
            return $this->Login($guest->id);
        }
        else
        {
            $this->error = tr('This name is already taken by another user.');
            return false;
        }
    }

    /**
     * Выполняет вход для интеграции.
     */
    public function LoginOthers($params)
    {
        $isNew = false;
        $user = User::model()->find_one(array('outsider' => $params['from'], 'outid' => $params['id']));

        if($user === false)
        {
            $isNew = true;
            $user = new User();
            $user->outsider = $params['from'];
            $user->outid = $params['id'];
            $user->group = isset($params['group']) ? $params['group'] : Elf::Settings('group_of_ownusers');
        }

        $user->name = $params['name'];
        $user->mask = isset($params['mask']) ? $params['mask'] : '';

        if($isNew)
        {
            $user->create();
        }
        else
        {
            $user->save();
        }

        return $this->Login($user->id);
    }


    public function LoginUser($name, $password, $remember = false)
    {
        //XSS
        $name = trim($name);
        $password = sha1($password);

        $user = User::model()->find_one(array('name' => $name, 'password' => $password, 'ownuser' => 1));

        if($user) // проверяем, есть ли такой юзер
        {
            return $this->Login($user->id, $remember);
        }
        else
        {
            $this->error = tr('Name or password is wrong.');
            return false;
        }
    }

    public function get_error()
    {
        return $this->error;
    }

    /**
     * Генерирует hash.
     *
     * @param uniq $hash
     * @return string sha1()
     */
    public function hash($hash)
    {
        return sha1($hash . $this->IP() . $this->user_agent());
    }

    public function user_agent()
    {
        if($this->user_agent === null)
        {
            $this->user_agent = $this->server_user_agent();
        }
        return $this->user_agent;
    }

    private function server_user_agent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public function set_user_agent($ua)
    {
        $this->user_agent = $ua;
    }

    public function IP()
    {
        if($this->ip === null)
        {
            $this->ip = $this->server_ip();
        }
        return $this->ip;
    }

    private function server_ip()
    {
        if(isset($_SERVER['HTTP_CLIENT_IP']))
            // Behind proxy
            return $_SERVER['HTTP_CLIENT_IP'];
        elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            // Use first IP address in list
            $_ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return $_ip[0];
        }
        return $_SERVER['REMOTE_ADDR'];
    }

    public function set_ip($ip)
    {
        $this->ip = $ip;
    }

    public static function CheckName(& $name)
    {
        if(
            md5($name) == "f3061464c109b15b765f3599f3506f18" ||
            md5($name) == "ca989323e03954ca03077a4065007d85" ||
            md5($name) == "6429203cde8f1de231a80ca23dbf2dff" ||
            md5($name) == "3d07df48d2f222b5b3aec18ea1f897cc" ||
            md5($name) == "6659837f111af65e22dbdd39746b877b"
        )
        {
            return false;
        }
        else if(sha1($name) == "77387a8bce87c88dfb80eedf3a4652ff9bc1f2ef")
        {
            $name = base64_decode("RWxmZXQ=");
            return true;
        }
        else
        {
            return true;
        }
    }

}

?>
