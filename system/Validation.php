<?php
abstract class Validation
{
    private $error = null;

    public function Valid($new_value, $old_value)
    {
        if($this->Check($new_value, $old_value))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    abstract protected function Check($new_value, $old_value);

    protected function SetError($e)
    {
        $this->error = $e;
    }

    public function GetError()
    {
        return $this->error;
    }
}

class Validation_NotEmpty extends Validation
{
    private $error_message;
    public function  __construct($error_message = null)
    {
        if($error_message === null)
            $this->error_message = tr('This must be not empty.');
        else
            $this->error_message = $error_message;
    }

    protected function Check($new_value, $old_value)
    {
        if(trim($new_value) != '')
            return true;
        else
        {
            $this->SetError($this->error_message);
            return false;
        }
    }
}

class Validation_MaxLength extends Validation
{
    protected $max;
    public function  __construct($max = 255)
    {
        $this->max = $max;
    }
    protected function Check($new_value, $old_value)
    {
        if(strlen(trim($new_value)) <= $this->max)
            return true;
        else
        {
            $this->SetError(format(tr('Too long. This field must be < %max%.'), array('max' => $this->max)));
            return false;
        }
    }
}

class Validation_MinLength extends Validation
{
    protected $min;
    public function  __construct($min = 4)
    {
        $this->min = $min;
    }
    protected function Check($new_value, $old_value)
    {
        if(strlen(trim($new_value)) >= $this->min)
            return true;
        else
        {
            $this->SetError(format(tr('Too short. This field must be > %min%.'), array('min' => $this->min)));
            return false;
        }
    }
}

class Validation_Unique extends Validation
{
    private $model;
    private $error_message;
    private $columnName;
    public function  __construct(ActiveRecord &$model, $columnName = 'name', $error_message = null)
    {
        $this->model = $model;
        $this->columnName = $columnName;
        if($error_message === null)
            $this->error_message = tr('This field must be unique.');
        else
            $this->error_message = $error_message;
    }
    protected function Check($new_value, $old_value)
    {
        $this->model->clear();
        $this->model->{$this->columnName} = trim($new_value);
        $this->model->find_fetch(1);
        if( $new_value == $old_value || !$this->model->fetch() )
            return true;
        else
        {
            $this->SetError($this->error_message);
            return false;
        }
    }
}

class Validation_Callback extends Validation
{
    private $error_message;
    private $callback;
    public function  __construct($callback, $error_message = null)
    {
        $this->callback = $callback;
        if($error_message === null)
            $this->error_message = tr('Hmmmmm, this field is not good.');
        else
            $this->error_message = $error_message;
    }

    protected function Check($new_value, $old_value)
    {
        $func = $this->callback;
        if($func($new_value, $old_value) != '')
            return true;
        else
        {
            $this->SetError($this->error_message);
            return false;
        }
    }
}


class Validation_Equals extends Validation
{
    private $error_message;
    private $equals;
    public function  __construct($equals, $error_message = null)
    {
        $this->equals = $equals;
        if($error_message === null)
            $this->error_message = tr('Must be in equals with something.');
        else
            $this->error_message = $error_message;
    }

    protected function Check($new_value, $old_value)
    {
        if($new_value == $this->equals)
            return true;
        else
        {
            $this->SetError($this->error_message);
            return false;
        }
    }
}

class Validation_Preg extends Validation
{
    private $error_message;
    private $preg;
    public function  __construct($preg, $error_message = null)
    {
        $this->preg = $preg;
        if($error_message === null)
            $this->error_message = tr('Hmmmmm, this field is not good.');
        else
            $this->error_message = $error_message;
    }

    protected function Check($new_value, $old_value)
    {
        if(preg_match($this->preg, $new_value))
            return true;
        else
        {
            $this->SetError($this->error_message);
            return false;
        }
    }
}

class Validation_Preg_Email extends Validation_Preg
{
    public function  __construct($error_message = null)
    {
        parent::__construct('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', $error_message);
    }
}
?>
