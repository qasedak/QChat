<?php

class Admin extends ActiveRecord
{

    public function __construct()
    {
        parent::__construct();
        $this->primary_key = 'id';
        $this->sql->select('admin.*');
        $this->sql->from('?_admins', 'admin');
        $this->sql->order('admin.id', 'ASC');
    }

    /**
     *
     * @param class $className
     * @return User
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function set_password($value)
    {
        if (trim($value) != '')
        {
            $value = sha1($value);
            $this->set('password', $value);
        }
    }

    public function before_db($data)
    {
        // If password is empty, leave old password
        if (array_key_exists('password', $data))
        {
            $pass = $data['password'];
            if ($pass !== null && $pass == '')
                unset($data['password']);
        }
        return $data;
    }

}
// Q ASEDAK G - Mohamad Anbarestany - Mahro Bagheri
?>
