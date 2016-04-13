<?php

abstract class Input
{

    public $id;
    public $title;
    protected $value;
    protected $errors = array();
    protected $valid = array();

    public function __construct($_id, $_title, $_value = '')
    {
        $this->id = $_id;
        $this->value = $_value;
        $this->title = $_title;
    }

    public function SetValid($_valid)
    {
        if (is_array($_valid))
            $this->valid = $_valid;
        else
            $this->valid = array($_valid);
    }

    public function AddValid($_valid)
    {
        if (is_array($_valid))
            $this->valid = array_merge($this->valid, $_valid);
        else
            array_push($this->valid, $_valid);
    }

    public function Valid($new_value, $old_value)
    {
        $r = true;
        foreach ($this->valid as &$valid)
        {
            if (!$valid->Valid($new_value, $old_value))
                $r = false;
        }
        return $r;
    }

    public function collect_errors()
    {
        foreach ($this->valid as &$valid)
        {
            if ($valid->GetError() !== null)
                $this->errors[] = $valid->GetError();
        }
    }

    public function has_errors()
    {
        return!empty($this->errors);
    }

    public function get_errors()
    {
        return implode(' ', $this->errors);
    }

    public function get_first_error()
    {
        return $this->errors[0];
    }

    abstract public function render();

    public function Compile()
    {
        $new_value = _POST($this->id)->value();
        $old_value = $this->value;
        $this->SetValue($new_value);

        if ($this->Valid($new_value, $old_value))
        {
            return true;
        }
        else
        {
            $this->collect_errors();
            return false;
        }
    }

    public function GetValue()
    {
        return $this->value;
    }

    public function SetValue($v)
    {
        $this->value = $v;
    }

    public static function set_by_id(& $array, $id, $value)
    {
        $_id = explode('#', $id, 2);
        if (count($_id) == 1)
        {
            $array[$_id[0]] = $value;
        }
        else
        {
            $array[$_id[0]][$_id[1]] = $value;
        }
    }

    public static function get_by_id(& $array, $id)
    {
        $_id = explode('#', $id, 2);
        if (count($_id) == 1)
        {
            return $array->{$_id[0]};
        }
        else
        {
            return $array->{$_id[0]}->{$_id[1]};
        }
    }

}

class InputHidden extends Input
{

    public function __construct($_id, $_value = '')
    {
        parent::__construct($_id, '', $_value);
    }

    public function render()
    {
        return call('form/hidden', array('id' => $this->id, 'value' => $this->value));
    }

}

class InputText extends Input
{

    public function __construct($_id, $_title, $_value = '')
    {
        parent::__construct($_id, $_title, $_value);
    }

    public function render()
    {
        return call('form/text', array('id' => $this->id, 'value' => $this->value, 'error' => $this->get_errors()));
    }

}

class InputPassword extends Input
{

    public function __construct($_id, $_title, $_value = '')
    {
        parent::__construct($_id, $_title, $_value);
    }

    public function render()
    {
        return call('form/text', array('id' => $this->id, 'value' => '', 'error' => $this->get_errors()));
    }

}

class InputCheck extends Input
{

    public $checked;

    public function __construct($_id, $_title, $_checked = false)
    {
        parent::__construct($_id, $_title, 'on');
        $this->checked = $_checked;
    }

    public function render()
    {
        return call('form/checkbox', array('id' => $this->id, 'value' => $this->value, 'checked' => $this->checked));
    }

    public function Compile()
    {
        if (_POST($this->id)->is_set())
        {
            $this->checked = true;
        }
        else
        {
            $this->checked = false;
        }
        return true;
    }

    public function GetValue()
    {
        return $this->checked;
    }

    public function SetValue($v)
    {
        $this->checked = $v ? true : false;
    }

}

class InputSelect extends Input
{
    const Def = 1;
    const Bool = 2;
    protected $options;
    protected $type = 1;

    public function __construct($_id, $_title, $_value = '', $_options = array(), $type = 1)
    {
        parent::__construct($_id, $_title, $_value);
        $this->options = $_options;
        $this->type = $type;

        if ($this->type == self::Bool)
        {
            foreach ($this->options as &$opt)
            {
                $opt['value'] = $opt['value'] ? 'true' : 'false';
            }
        }
    }

    public function SetValue($v)
    {
        if ($this->type == self::Def)
        {
            parent::SetValue($v);
        }
        else if ($this->type == self::Bool)
        {
            if (is_bool($v))
            {
                $this->value = $v;
            }
            else
            {
                $this->value = ($v == 'true');
            }
        }
    }

    public function GetValue()
    {
        return $this->value;
    }

    public function render()
    {
        $value = $this->value;

        if ($this->type == self::Bool)
        {
            $value = $this->value ? 'true' : 'false';
        }

        return call('form/select', array('id' => $this->id, 'value' => $value, 'options' => $this->options));
    }

}


class InputRadio extends Input
{
    protected $options;

    public function __construct($_id, $_title, $_value = '', $_options = array())
    {
        parent::__construct($_id, $_title, $_value);
        $this->options = $_options;
    }

    public function render()
    {
        return call('form/radio', array('id' => $this->id, 'value' => $this->value, 'options' => $this->options));
    }

}

class InputMyView extends Input
{
    private $view;
    
    public function __construct($id, $title, $value, $view)
    {
        parent::__construct($id, $title, $value);
        $this->view = $view;
    }

    public function render()
    {
        $this->view->add_vars(array('id' => $this->id, 'value' => $this->value));
        return $this->view->render();
    }
}

?>
