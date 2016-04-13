<?php
class FormController
{
    private $inputs = array();
    private $hiddens = array();
    private $submit = 'Submit';
    private $title = '';
    private $action = '';
    private $reset = true;
    public $actionpost_hash;

    public function  __construct()
    {
        $this->actionpost_hash = md5(Elf::Get(ACTION_POST_HASH));
    }

    public function SetAction($_action)
    {
        $this->action = $_action;
    }

    public function AddInput(Input $input)
    {
        $this->inputs[$input->id] = $input;
    }

    public function AddHidden(Input $hidden)
    {
        $this->hiddens[$hidden->id] = $hidden;
    }

    public function GetInputs()
    {
        return $this->inputs;
    }

    public function SetReset($reset)
    {
        $this->reset = $reset;
    }

    public function SetSubmit($_submit)
    {
        $this->submit = $_submit;
    }

    public function SetTitle($_title)
    {
        $this->title = $_title;
    }

    public function Check()
    {
        if(_POST('_actionpost')->low() == $this->actionpost_hash)
        {
            $allCompile = true;
            foreach ($this->inputs as &$input)
            {
                if( ! $input->Compile() )
                    $allCompile = false;
            }

            //Save value to all inputs as hidden
            foreach ($this->hiddens as &$hidden)
            {
                $hidden->Compile(); // Valid takes no effect for hidden now
            }

            return $allCompile;
        }
        else
        {
            return false;
        }
    }

    public function GetArray()
    {
        $r = array();

        foreach ($this->inputs as &$input)
        {
            Input::set_by_id($r, $input->id, $input->GetValue());
        }

        return $r;
    }

    public function render()
    {
        $formview = new View('form/form');

        $formview->set_vars(array(
                '_actionpost_hash' => $this->actionpost_hash,
                'action' => $this->action,
                'inputs' => $this->inputs,
                'hiddens' => $this->hiddens,
                'title' => $this->title,
                'submit' => $this->submit,
                'reset' => $this->reset
        ));

        return $formview->render();
    }
}
?>
