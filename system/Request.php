<?php

class Conversion
{

    private $input;

    public function __construct(Request $_input)
    {
        $this->input = $_input;
    }

    /**
     * Analog isset
     * @return bool
     */
    public function is_set()
    {
        return $this->input->is_set();
    }

    /**
     * Return value "as is" from request
     * @return mixed
     */
    public function value()
    {
        return $this->input->value();
    }

    /**
     * Aliase for value()
     * @return mixed
     */
    public function __toString()
    {
        return $this->value();
    }

    /**
     * Convertion to int
     * @param int $def
     * @return int
     */
    public function int($def = 0)
    {
        if($this->is_set())
        {
            return intval($this->value());
        }
        else
        {
            return $def;
        }
    }

    /**
     * Append strtolower(#) to value. It using in getting "key" params from $_GET[]
     * @param string $def
     * @return string
     */
    public function low($def = '')
    {
        if($this->is_set())
        {
            return strtolower($this->value());
        }
        else
        {
            return $def;
        }
    }

    /**
     * Append htmlspecialchars(#) to value.
     * @param string $def
     * @return string
     */
    public function text($def = '')
    {
        if($this->is_set())
        {
            return htmlspecialchars($this->value());
        }
        else
        {
            return $def;
        }
    }

    /**
     * Return true if value equals to $true.
     * @param bool $def
     * @param string $true (defualt is '1')
     * @return bool
     */
    public function bool($def = false, $true = '1')
    {
        if($this->is_set())
        {
            if($this->value() == $true)
                return true;
            else
                return false;
        }
        else
            return $def;
    }

}

class Request
{
    public static $GET = array();
    public static $POST = array();
    public static $REQUEST = array();
    public static $COOKIE = array();
    private $key;
    private $value = '';
    private $is_set = false;

    public function __construct(& $_array, $_key)
    {
        $this->key = $_key;
        $this->is_set = array_key_exists($_key, $_array);
        if($this->is_set)
        {
            $this->value = $_array[$_key];
        }
    }

    public function is_set()
    {
        return $this->is_set;
    }

    public function value()
    {
        return $this->value;
    }

    public static function Get($key)
    {
        return new Request(self::$GET, $key);
    }

    public static function Post($key)
    {
        return new Request(self::$POST, $key);
    }

    public static function _Request($key)
    {
        return new Request(self::$REQUEST, $key);
    }

    public static function Cookie($key)
    {
        return new Request(self::$COOKIE, $key);
    }

    public static function Init()
    {
        if(get_magic_quotes_gpc())
        {
            function strip_array($var)
            {
                return is_array($var) ? array_map("strip_array", $var) : stripslashes($var);
            }

            $_GET = strip_array($_GET);
            $_POST = strip_array($_POST);
            $_REQUEST = strip_array($_REQUEST);
            $_COOKIE = strip_array($_COOKIE);          
        }
        self::$GET = $_GET;
        self::$POST = $_POST;
        self::$REQUEST = $_REQUEST;
        self::$COOKIE = $_COOKIE;
    }

}

function _GET($key)
{
    return new Conversion(Request::Get($key));
}

function _POST($key)
{
    return new Conversion(Request::Post($key));
}

function _REQUEST($key)
{
    return new Conversion(Request::_Request($key));
}

function _COOKIE($key)
{
    return new Conversion(Request::Cookie($key));
}

class RequestType
{
    const Html = 1;
    const Json = 2;

    private $type;

    public function __construct()
    {
        if($this->is_ajax_request())
        {
            $this->set_json();
        }
        else
        {
            $this->set_html();
        }
    }

    private function is_ajax_request()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) ? $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' : false;
    }

    public function get()
    {
        return $this->type;
    }

    public function set($type)
    {
        $this->type = $type;
    }

    public function set_html()
    {
        $this->Set(self::Html);
    }

    public function set_json()
    {
        $this->Set(self::Json);
    }

    public function equals($type)
    {
        return $this->type == $type;
    }

    public function is_html()
    {
        return $this->Equals(self::Html);
    }

    public function is_json()
    {
        return $this->Equals(self::Json);
    }

}

?>
