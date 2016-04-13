<?php

class Lang
{

    private $messages = array();

    private static $static_messages = array();

    public function __construct()
    {

    }

    public function load_file($filename)
    {
        if(file_exists($filename))
        {
            include $filename;
            if(isset($_LANG))
            {
                $this->messages = array_merge($this->messages, $_LANG);
            }
        }
    }

    public function get($message_id)
    {
        if(array_key_exists($message_id, $this->messages))
            return self::comment($this->messages[$message_id]);
        else
            return self::comment($message_id);
    }

    public static function langs()
    {
        $langs = array();
        $handle = opendir(ELFCHAT_ROOT . '/lang');
        if($handle)
        {
            while(false !== ($name = readdir($handle)))
            {
                $dir = ELFCHAT_ROOT . '/lang/' . $name;
                if(is_dir($dir) && $name != "." && $name != "..")
                {
                    $file = $dir . '/lang_general.php';
                    if(file_exists($file))
                    {
                        include $file;
                        if(isset($_LANG))
                        {
                            self::$static_messages = & $_LANG;
                            $langs[] = array('value' => $name, 'title' => self::gettexttr('#lang'));
                        }
                    }
                }
            }
            closedir($handle);
        }
        return $langs;
    }    
    
    public static function gettexttr($message_id)
    {
        if(array_key_exists($message_id, self::$static_messages))
            return self::comment(self::$static_messages[$message_id]);
        else
            return self::comment($message_id);
    }

    public static function comment($message)
    {
        $message = explode('#', $message, 2);
        return $message[0];
    }

}

?>
