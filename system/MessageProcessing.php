<?php

class MessageProcessing
{

    private $msg = '';

    /**
     * Put your message in constructor.
     * @param string $msg Message to be processing.
     */
    public function __construct($msg)
    {
        $this->msg = $msg;
    }

    /**
     * Do some staff with message.
     * @return MessageProcessing
     */
    public function process()
    {
        // XSS:
        $this->msg = htmlspecialchars($this->msg);

        // Max length:
        $this->msg = mb_substr($this->msg, 0, Elf::Settings('max_message_length'), 'UTF-8');

        // Censure
        if(Elf::Settings('censure') && class_exists('Censure'))
        {
            $this->msg = Censure::parse($this->msg, '1', '', false, tr('[CENSURED]'));
        }

        return $this;
    }

    /**
     * Return message.
     * @return string
     */
    public function message()
    {
        return $this->msg;
    }

}
?>
