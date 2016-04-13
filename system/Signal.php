<?php

class Signal
{

    private $slots = array();

    protected function emit($signal)
    {
        $args = func_get_args();
        array_shift($args);
        if(array_key_exists($signal, $this->slots))
            foreach($this->slots[$signal] as $slot)
            {
                call_user_func_array($slot['callback'], $args);
            }
    }

    public function connect($signal, $slot)
    {
        $this->slots[$signal][] = array(
            'callback' => $slot
        );
    }

}

?>
