<?php

class Message extends ActiveRecord
{
    public function __construct()
    {
        parent::__construct();
        $this->primary_key = 'id';
        $this->sql->select('m.*');
        $this->sql->from('?_messages', 'm');
        $this->sql->order('id', 'DESC');
    }

    /**
     *
     * @param class $className
     * @return Message
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function clearAll()
    {
        $this->db->query($this->sql->delete()->where('time < '.(time()-86400))->generate());
    }
}

?>
