<?php

class Room extends ActiveRecord
{

    public function __construct()
    {
        parent::__construct();
        $this->primary_key = 'id';
        $this->sql->select('room.*');
        $this->sql->from('?_rooms', 'room');
        $this->sql->order('room.order', 'ASC');
    }

    /**
     *
     * @param class $className
     * @return Room
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function before_db($data)
    {
        if (array_key_exists('default', $data))
        {
            $def = $data['default'];
            if ($def !== null && $def == 1)
                $this->updateAll(array('default' => 1), array('default' => 0));
        }
        return $data;
    }

    public function Reorder($orderline)
    {
        $pre_orderpair = explode(';', $orderline);
        $orderpair = array();
        foreach ($pre_orderpair as $pairline)
        {
            $pair = explode('=', $pairline);
            $orderpair[trim($pair[0])] = trim($pair[1]);
        }

        foreach ($orderpair as $id => $order)
        {
            $this->db->query('UPDATE ?_rooms SET ?a WHERE id=? LIMIT 1', array('order' => $order), $id);
        }
    }

}

?>
