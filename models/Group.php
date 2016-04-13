<?php

class Group extends ActiveRecord
{
    private static $default = array(
        'enter' => true,
        'icon' => '',
        'bbcode_status' => true,
        'enable_antispam' => true,
    );

    public function __construct()
    {
        parent::__construct();
        $this->primary_key = 'id';
        $this->sql->select('g.*');
        $this->sql->from('?_groups', 'g');
        $this->sql->order('g.id', 'ASC');
    }

    public static function default_settings()
    {
        return serialize(self::$default);
    }

    /**
     *
     * @param class $className
     * @return Group
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function after_db($data)
    {
        if (array_key_exists('settings', $data))
            $data['settings'] = array_merge(self::$default, (array)unserialize($data['settings']));
        return $data;
    }

    public function before_db($data)
    {
        if (array_key_exists('settings', $data))
            $data['settings'] = serialize($data['settings']);
        return $data;
    }

}

?>
