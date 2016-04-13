<?php

class Sql
{
    const INSERT = 1;
    const UPDATE = 2;
    const DELETE = 3;
    const SELECT = 4;

    protected $is_copy = false;
    protected $type = 0;
    protected $select = '*';
    protected $from_as = '';
    protected $from_table = '';
    protected $insert_keys = '';
    protected $insert_values = '';
    protected $update = '';
    protected $join = array();
    protected $where = '';
    protected $group = '';
    protected $order = '';
    protected $limit = '';    

    public function __construct()
    {
        $this->is_copy = false;
    }

    /**
     * Return new copy of object if this is initial object.
     * @return Sql
     */
    public function copy()
    {
        if($this->is_copy)
        {
            return $this;
        }
        else
        {
            $obj = clone $this;
            $obj->is_copy = true;
            return $obj;
        }
    }
    
    public function select($need = null)
    {
        $this->type = self::SELECT;
        if ($need != null)
        {
            $this->select = $need;
        }
        return $this->copy();
    }

    public function select_add($need = null)
    {
        $this->type = self::SELECT;
        if ($need != null)
        {
            $this->select .= $need;
        }
        return $this->copy();
    }

    public function from($table, $as = '')
    {
        $this->from_table = $table;
        $this->from_as = $as;
        return $this->copy();
    }

    public function from_as($as = '')
    {
        $this->from_as = $as;
        return $this->copy();
    }

    public function insert($array)
    {
        $this->type = self::INSERT;

        if (is_array($array))
        {
            $values = array_values($array);
            $keys = array_keys($array);

            foreach ($values as &$v)
            {
                $v = '\'' . mysql_real_escape_string($v) . '\'';
            }

            foreach ($keys as &$k)
            {
                $k = '`' . mysql_real_escape_string($k) . '`';
            }

            $this->insert_values = '( ' . implode(' , ', $values) . ' )';
            $this->insert_keys = '( ' . implode(' , ', $keys) . ' )';
        }
        else
        {
            $this->insert_values = ' ' . $array . ' ';
            $this->insert_keys = '';
        }

        return $this->copy();
    }

    public function update($var)
    {
        $this->type = self::UPDATE;

        if (is_array($var))
        {
            if (empty($var))
            {
                $this->update = '';
            }
            else
            {
                $a = array();
                foreach ($var as $key => $value)
                {
                    $a[] = '`' . mysql_real_escape_string($key) . '` = \'' . mysql_real_escape_string($value) . '\'';
                }

                $this->update = implode(' , ', $a);
            }
        }
        else
        {
            if (trim($var) == '')
            {
                $this->update = '';
            }
            else
            {
                $this->update = $var;
            }
        }

        return $this->copy()->from_as(''); // In this query do not use prefix for columns: table.id -> id
    }

    public function delete()
    {
        $this->type = self::DELETE;
        return $this->copy()->from_as(''); // In this query do not use prefix for columns: table.id -> id
    }

    public function join($table = null, $on = null, $side = 'LEFT')
    {
        if ($table === null)
        {
            $this->join = array();
        }
        else
        {
            $this->join[] = $side . ' JOIN ' . $table . ' ON (' . $on . ')';
        }

        return $this->copy();
    }

    public function where($var)
    {
        if (is_array($var))
        {
            if (empty($var))
            {
                $this->where = '';
            }
            else
            {
                $at_table = ( $this->from_as == '' ) ? '' : $this->from_as . '.' ;

                $a = array();
                foreach ($var as $key => $value)
                {
                    $value = $this->mysql_conversion_values($value);
                    $a[] = $at_table . '`' . mysql_real_escape_string($key) . '` = \'' . mysql_real_escape_string($value) . '\'';
                }

                $this->where = 'WHERE ( ' . implode(' AND ', $a) . ' )';
            }
        }
        else if(is_string($var))
        {
            if (trim($var) == '')
            {
                $this->where = '';
            }
            else
            {
                $this->where = 'WHERE ( ' . $var . ' )';
            }
        }
        else
        {
            $this->where = '';
        }

        return $this->copy();
    }

    protected function mysql_conversion_values($var)
    {
        if(is_bool($var))
        {
            return $var ? '1' : '0';
        }
        else
        {
            return $var;
        }
    }

    public function group($by = null)
    {
        if ($by === null)
        {
            $this->group = '';
        }
        else
        {
            $this->group = 'GROUP BY ' . $by;
        }

        return $this->copy();
    }

    public function order($by = null, $way = 'ASC')
    {
        if ($by === null)
        {
            $this->order = '';
        }
        else
        {
            $this->order = 'ORDER BY ' . $by . ' ' . $way;
        }

        return $this->copy();
    }

    public function limit($a = null, $b = null)
    {
        if ($a === null && $b === null)
        {
            $this->limit = '';
        }
        else if ($b === null)
        {
            $this->limit = 'LIMIT ' . intval($a);
        }
        else
        {
            $this->limit = 'LIMIT ' . intval($a) . ', ' . intval($b);
        }

        return $this->copy();
    }

    public function no_limit()
    {
        $this->limit = '';
        return $this->copy();
    }    

    public function __toString()
    {
        return $this->generate();
    }

    public function generate()
    {
        $query = '';
        if ($this->type == self::SELECT)
        {
            $joins = implode(' ', $this->join);
            $query = "SELECT $this->select FROM $this->from_table $this->from_as $joins $this->where $this->group $this->order $this->limit";
        }
        else if ($this->type == self::UPDATE)
        {
            $query = "UPDATE $this->from_table SET $this->update $this->where $this->limit";
        }
        else if ($this->type == self::INSERT)
        {
            $query = "INSERT INTO $this->from_table $this->insert_keys SET $this->insert_values";
        }
        else if ($this->type == self::DELETE)
        {
            $query = "DELETE FROM $this->from_table $this->where $this->limit";
        }       

        return $query;
    }

}

?>
