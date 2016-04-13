<?php

class Skin
{

    protected $info = array();

    public function __construct($path = null)
    {
        $this->info['view_replace'] = false;

        if($path !== null)
        {
            $this->load($path);
        }
    }

    public function  __get($name)
    {
        return $this->info[$name];
    }

    public function load($path)
    {
        if (file_exists($path . '/info.php'))
        {
            include $path . '/info.php';

            if (isset($_INFO) && is_array($_INFO))
            {
                $this->info = array_merge($this->info, $_INFO);
                $this->info['view_path'] = ELFCHAT_ROOT . '/' . $_INFO['view_path'];

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

}

?>
