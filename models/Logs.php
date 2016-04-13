<?php

abstract class Logs
{

    protected $id;

    public function SetId($_id)
    {
        $this->id = $_id;
    }

    abstract public function Log($doing);
}

class AdminLog extends Logs
{

    public function Log($doing)
    {
        $log = array('id' => $this->id, 'doing' => $doing, 'time' => time());
        Elf::Db()->query('INSERT INTO ?_adminslogs SET ?a', $log);
    }

    public function Clear()
    {
        Elf::Db()->query('DELETE FROM ?_adminslogs');
    }

}

class AdminLogRecord extends ActiveRecord
{

    public function __construct()
    {
        parent::__construct();
        $this->primary_key = 'key';
        $this->sql->select('log.*, admin.name');
        $this->others = array('name');
        $this->sql->from('?_adminslogs', 'log');
        $this->sql->join('?_admins admin', 'admin.id = log.id');
        $this->sql->order('log.time', 'DESC');
    }

    /**
     *
     * @param class $className
     * @return AdminLogRecord
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

}

class ModeratorLog extends Logs
{

    public function Log($doing)
    {
        $log = array('id' => $this->id, 'doing' => $doing, 'time' => time());
        Elf::Db()->query('INSERT INTO ?_moderslogs SET ?a', $log);
    }

    public function Clear()
    {
        Elf::Db()->query('DELETE FROM ?_moderslogs');
    }

    public static function Add($id, $doing)
    {
        $l = new self();
        $l->SetId($id);
        $l->Log($doing);
    }

}

class ModeratorLogRecord extends ActiveRecord
{

    public function __construct()
    {
        parent::__construct();
        $this->primary_key = 'key';
        $this->sql->select('l.*, u.name');
        $this->others = array('name');
        $this->sql->from('?_moderslogs', 'l');
        $this->sql->join('?_users u', 'u.id = l.id');
        $this->sql->order('l.time', 'DESC');
    }

    /**
     *
     * @param class $className
     * @return ModeratorLogRecord
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

}

?>
