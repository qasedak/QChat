<?php

class Settings
{

    private $file = null;
    private $settings = array();
    private $default_settings = array();

    public function __construct()
    {

    }

    public function LoadDefaultSettings($file = "")
    {
        if (file_exists($file))
        {
            @include_once $file;
            if (isset($_SETTINGS))
                $this->default_settings = $_SETTINGS;
        }
    }

    public function LoadSettings($file = "")
    {
        if (file_exists($file))
        {
            $this->file = $file;
            @include_once $file;
            if (isset($_SETTINGS))
                $this->settings = $_SETTINGS;
        }
    }

    public function Get($key = array())
    {
        if (is_array($key))
        {
            return array_merge($this->default_settings, $this->settings);
        }
        else
        {
            if (is_string($key))
            {
                if (isset($this->settings[$key]))
                {
                    return $this->settings[$key];
                }
                else
                if (isset($this->default_settings[$key]))
                {
                    return $this->default_settings[$key];
                }
                else
                    return false;
            }
            else
            {
                return false;
            }
        }
    }

    public function Set($in_settings = array(), $val = '')
    {
        if (is_array($in_settings))
        {
            $this->settings = array_merge($this->settings, $in_settings);
        }
        else if (is_string($in_settings))
        {
            if (isset($this->settings[$in_settings]))
                $this->settings[$in_settings] = $val;
        }
    }

    public function Save()
    {
        $settings = array_diff_assoc($this->settings, $this->default_settings);
        $var = var_export($settings, true);
        $php = "<?php " . '$_SETTINGS = ' . $var . "; ?>";
        file_put_contents($this->file, $php);
    }

    public function Reset()
    {
        $php = "<?php " . '$_SETTINGS = ' ."array(); ?>";
        file_put_contents($this->file, $php);
    }

}

?>
