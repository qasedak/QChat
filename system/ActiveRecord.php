<?php

abstract class ActiveRecord
{

    /**
     * @var DbSimple_Mysql
     */
    protected $db;
    /**
     * The primary key of the current table.
     * @var string
     */
    protected $primary_key = 'id';
    /**
     * Sql generator. To change default, call only like this: $this->sql->select('*');
     * @var Sql
     */
    protected $sql;
    /**
     * All cells from data base row in this array.
     * @var array
     */
    private $data = array();
    /**
     * Array of key what is dirty (changed) in data array;
     * @var array
     */
    private $dirty = array();
    /**
     * Default data setter and getter.
     * @var array
     */
    protected $data_handler = array();
    /**
     * Others elements in select, but not from main table.
     * This must be init in children constructor if you use joins.
     * @var array
     */
    protected $others = array();
    /**
     * Do or not unset of primary data before any DB actions (insert, update)
     * @var boolean
     */
    protected $unset_primary = true;
    /**
     * If Only find/select function allowed.
     * @var boolean
     */
    protected $read_only = false;
    /**
     * Cache for overloaded models. This array fill method model().
     * @var array
     */
    private static $_models = array();
    /**
     * Array for store search result from find_fetch func.
     * @var array
     */
    protected $_fetch = array();

    /**
     * In children call parents constructor and declare $sql & $primary_key.
     */
    public function __construct()
    {
        $this->db = Elf::Db();
        $this->sql = new Sql();
        $this->sql->select('*');
    }

    /**
     * If no call to overloaded functions this will called.
     * If no default handler exist, will calls standart get and set functions.
     */
    public function __call($query, $arguments)
    {
        @list($func, $key) = explode('_', $query, 2);

        if ($func == 'get')
        {
            if (isset($this->data_handler[$key]))
            {
                $handler = 'get_' . $this->data_handler[$key];
                return $this->{$handler}($key);
            }
            else
            {
                return $this->get($key);
            }
        }
        else if ($func == 'set')
        {
            if (isset($this->data_handler[$key]))
            {
                $handler = 'set_' . $this->data_handler[$key];
                $this->{$handler}($key, $arguments[0]);
            }
            else
            {
                $this->set($key, $arguments[0]);
            }
        }
        else if ($func == 'before' || $func == 'after')
        {
            // If (before|after)_db func does not exist
            return $arguments[0];
        }
        else
        {
            trigger_error('Undefined method called: ' . $query, E_USER_NOTICE);
            return null;
        }
    }

    /**
     * Call overloaded functions.
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->call_func('set', $key, $value);
    }

    /**
     * Call overloaded functions.
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->call_func('get', $key);
    }

    /**
     * Isset for data.
     * @param string $key
     * @return boolean
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Aliase for call_user_func.
     * @param string $func  Func name(get,set)
     * @param string $key  Key for func $func_$key
     * @param mixed $value Optional
     * @return mixed
     */
    final public function call_func($func, $key, $value = null)
    {
        return call_user_func(array($this, $func . '_' . $key), $value);
    }

    /**
     * Set data without any treatment.
     * @param string or array $key
     * @param mixed $value
     */
    protected function set($key, $value = null)
    {
        $this->data[$key] = $value;
        $this->dirty[$key] = true;
    }

    /**
     * Set data by passing overloaded users function.
     * All old data in $data will be cleared.
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = array();
        foreach ($data as $key => $value)
        {
            $this->call_func('set', $key, $value);
        }
    }

    /**
     * Set primary value.
     * @param int $value
     */
    public function setPrimary($value)
    {
        $this->data[$this->primary_key] = $value;
        $this->dirty[$this->primary_key] = true;
    }

    /**
     * Add elements to others array.
     * @param array in args by func_get_args
     */
    protected function add_others()
    {
        $args = func_get_args();
        $this->others = array_merge($this->others, $args);
    }

    /**
     * Get data without any treatment.
     * @param string $key
     * @return mixed
     */
    protected function get($key)
    {
        if (array_key_exists($key, $this->data))
        {
            // For access array: $obj->same_array['field'] or $obj->same_array->field
            if (is_array($this->data[$key]))
            {
                return new ActiveArray(& $this->data[$key]);
            }
            else
            {
                return $this->data[$key];
            }
        }
        else
        {
            return null;
        }
    }

    /**
     * Get data by passing overloaded users function.
     * @return array
     */
    public function getData()
    {
        $data = array();
        foreach ($this->data as $key => $value)
        {
            $data[$key] = $this->call_func('get', $key);
        }
        return $data;
    }

    /**
     * Return primary value from data array.
     * @return int
     */
    public function getPrimaryValue()
    {
        return $this->data[$this->primary_key];
    }

    /**
     *
     *
     * Handlers: setters and getters.
     *
     *
     */
    public function get_boolean($key)
    {
        return $this->get($key) == '1' ? true : false;
    }

    public function set_boolean($key, $value)
    {
        $this->set($key, $value ? '1' : '0');
    }

    /**
     *
     *
     * Data Base functions.
     *
     *
     */

    /**
     * All children have to overload this function and call parent::model(__CLASS__)
     * @param string $className
     * @return object
     */
    public static function model($className)
    {
        if (isset(self::$_models[$className]))
            return self::$_models[$className];
        else
        {
            $model = self::$_models[$className] = new $className();
            return $model;
        }
    }

    /*
     * Clear $data.
     */

    public function clear()
    {
        $this->data = array();
        $this->dirty = array();
    }

    /**
     * Insert new row to current table.
     * @return int Insert id
     */
    public function create()
    {
        $data = $this->data;
        $data = $this->before_db($data);
        $data = $this->clear_others($data);
        if ($this->unset_primary)
            unset($data[$this->primary_key]);

        $insert_id = $this->db->query($this->sql->insert('?a')->generate(), $data);
        $this->data[$this->primary_key] = $insert_id;
        return $insert_id;
    }

    /**
     * Save only dirty changes.
     */
    public function save()
    {
        $where = array($this->primary_key => $this->data[$this->primary_key]);

        $data = array();
        foreach ($this->dirty as $key => $true)
        {
            $data[$key] = $this->data[$key];
        }
        $data = $this->before_db($data);
        $data = $this->clear_others($data);
        if ($this->unset_primary)
            unset($data[$this->primary_key]);

        $this->db->query($this->sql->update('?a')->where($where)->limit(1)->generate(), $data);
    }

    public function updateAll($find, $update)
    {
        $find = $this->_find($find);
        return $this->db->query($this->sql->update('?a')->where($find)->no_limit()->generate(), $update);
    }

    /**
     * Delete row by primary key.
     * @param int $primary  Primary id.
     */
    public function delete($primary = null)
    {
        if ($primary !== null)
        {
            $this->setPrimary($primary);
        }

        $where = array($this->primary_key => $this->data[$this->primary_key]);
        $this->db->query($this->sql->delete()->where($where)->limit(1)->generate());
    }

    /**
     * Find and return ActiveRecord array
     * @param string $find
     * @return array
     */
    public function find($find = null, $limit_a = null)
    {
        $find = $this->_find($find);
        $rows = $this->db->select($this->sql->select()->where($find)->limit($limit_a)->generate());

        $className = get_class($this);
        $ars = array();
        foreach ($rows as $data)
        {
            $obj = new $className();
            $obj->data = $obj->after_db($data);
            $ars[] = $obj;
        }
        return $ars;
    }

    /**
     * Select needly rows from db without any treatment.
     * @param string $select
     * @param mixed $find
     * @param int $limit_a
     * @return array
     */
    public function select($select, $find = null, $limit_a = null)
    {
        $find = $this->_find($find);
        $rows = $this->db->select($this->sql->copy()->select($select)->where($find)->limit($limit_a)->generate());
        return $rows;
    }

    /**
     * Return ActiveRecord if it exist and if not - false.
     * @param string $find
     * @return ActiveRecord
     */
    public function find_one($find = null)
    {
        $find = $this->_find($find);

        $data = $this->db->selectRow($this->sql->select()->where($find)->limit(1)->generate());
        if ($data)
        {
            $className = get_class($this);
            $obj = new $className();
            $obj->data = $obj->after_db($data);
            return $obj;
        }
        else
        {
            return false;
        }
    }

    /**
     * Performs a search on this $data.
     * Others don't clearing in this function.
     * Results in fetch array. 
     * @param int $limit_a
     * @return int Num of finded rows
     */
    public function find_fetch($limit_a = null)
    {
        $where_data = $this->before_db($this->data);
        $rows = $this->db->select($this->sql->select()->where($where_data)->limit($limit_a)->generate());

        $className = get_class($this);
        $this->_fetch = array();
        $num = 0;
        foreach ($rows as $data)
        {
            $obj = new $className();
            $obj->data = $obj->after_db($data);
            $this->_fetch[] = $obj;
            $num++;
        }
        return $num;
    }

    /**
     * Fetch object finded by find_fetch func.
     * @return ActiveRecord
     */
    public function fetch()
    {
        return array_shift($this->_fetch);
    }

    /**
     * Performs a search on this $data.
     * Others don't clearing in this function.
     * Results in this current object.
     * @param int $limit_a
     * @return boolean true if founded, else false.
     */
    public function find_this($limit_a = null)
    {
        $where_data = $this->before_db($this->data);
        $data = $this->db->selectRow($this->sql->select()->where($where_data)->limit($limit_a)->generate());
        if ($data)
        {
            $this->data = $this->after_db($data);
            $this->dirty = array();
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Returns rows limit $start, $per and return all in $count
     * @param int $count
     * @param int $start
     * @param int $per
     * @param string $find
     * @return array
     */
    public function page(&$count, $start = 0, $per = 30, $find = null)
    {
        $find = $this->_find($find);

        $rows = $this->db->selectPage($count, $this->sql->select()->where($find)->limit($start, $per)->generate());

        $className = get_class($this);

        foreach ($rows as & $data)
        {
            $obj = new $className();
            $obj->data = $obj->after_db($data);
            $data = $obj;
        }
        return $rows;
    }

    /**
     * Loading data from database found by primary key.
     * Current data will replace with new, also new data will be return.
     * @param int $primary_value
     * @return ActiveRecord
     */
    public function load($primary_value)
    {
        $obj = $this->find_one(array($this->primary_key => $primary_value));
        if ($obj)
        {
            $this->data = $obj->data; // We already call after_db func in $this->find_one, so we do not need to call it again
            $this->dirty = array(); // Clear dirty array, we all loaded from db.
        }
        return $obj;
    }

    /**
     * If $find is int this func create new array(primary_key => $find)
     * @param mixed $find
     * @return mixed
     */
    protected function _find($find)
    {
        if (is_int($find))
        {
            return array($this->primary_key => $find);
        }
        else
        {
            return $find;
        }
    }

    /**
     * Clear from $data elements if they in $others array.
     * @param array $data
     * @return array
     */
    private function clear_others($data)
    {
        foreach ($this->others as $other)
        {
            if (array_key_exists($other, $data))
            {
                unset($data[$other]);
            }
        }
        return $data;
    }

}

class ActiveArray implements ArrayAccess
{

    protected $array;

    public function __construct(&$array)
    {
        $this->array = &$array;
    }

    public function __set($name, $value)
    {
        $this->array[$name] = $value;
    }

    public function __get($name)
    {
        if(!array_key_exists($name, $this->array))
                return null;
        
        if (is_array($this->array[$name]))
        {
            return new self($this->array[$name]);
        }
        else
        {
            return $this->array[$name];
        }
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset))
        {
            $this->array[] = $value;
        }
        else
        {
            $this->array[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->array[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    public function __toString()
    {
        return 'ActiveArray';
    }

}

?>
