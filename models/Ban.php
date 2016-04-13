<?php

/**
 * @property int $index
 * @property int $user_id
 * @property string $name
 * @property boolean $ban_id
 * @property boolean $ban_ip
 * @property string $ip
 * @property int $for_time
 * @property int $start_time
 * @property string $reason
 */
class Ban extends ActiveRecord
{

    public $time_array;

    public function __construct()
    {
        parent::__construct();
        $this->primary_key = 'index';
        $this->sql->select('b.*');
        $this->sql->from('?_ban', 'b');
        $this->sql->order('b.index', 'DESC');
        $this->data_handler['ban_id'] = 'boolean';
        $this->data_handler['ban_ip'] = 'boolean';

        // Times
        $this->time_array = array(
            array('value' => 60, 'title' => tr('One minute')),
            array('value' => 60 * 5, 'title' => tr('Five minutes')),
            array('value' => 60 * 10, 'title' => tr('Ten minutes')),
            array('value' => 60 * 60, 'title' => tr('One hour')),
            array('value' => 60 * 60 * 2, 'title' => tr('Two hours')),
            array('value' => 60 * 60 * 12, 'title' => tr('Twelve hours')),
            array('value' => 60 * 60 * 24, 'title' => tr('One day')),
            array('value' => 60 * 60 * 24 * 2, 'title' => tr('Two days')),
            array('value' => 60 * 60 * 24 * 7, 'title' => tr('One week')),
            array('value' => 60 * 60 * 24 * 31, 'title' => tr('One month')),
            array('value' => 60 * 60 * 24 * 365, 'title' => tr('Year')),
            array('value' => 60 * 60 * 24 * 365 * 100, 'title' => tr('Forever'))
        );
    }

    /**
     *
     * @param class $className
     * @return Ban
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function time_title($time = null)
    {
        if($time === null)
        {
            $time = $this->for_time;
        }

        foreach ($this->time_array as $bt)
        {
            if($bt['value'] == $time)
                return $bt['title'];
        }
        return format(tr('%% hours'), round(($time / (60*60)), 1) );
    }

    public function ban_ends()
    {
        $time = $this->start_time + $this->for_time;
        if($time >= time())
            return date('d.m.y H:i:s', $time);
        else
            return tr('Ban is over');
    }

    public function is_banned()
    {
        $time = $this->start_time + $this->for_time;
        if($time >= time())
            return true;
        else
            return false;
    }

}

?>
