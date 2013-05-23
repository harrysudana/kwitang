<?php
if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/**
 * MySQL Forge Class Force MYISAM
 *
 * @package  Kwitang\Core
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 * @link http://ellislab.com/forums/viewthread/194955/
 */
class MY_CI_DB_mysqli_forge extends CI_DB_mysqli_forge
{
    /**
     * Create Table
     *
     * @access private
     * @param string  the table name
     * @param mixed   the fields
     * @param mixed   primary key(s)
     * @param mixed   key(s)
     * @param boolean should 'IF NOT EXISTS' be added to the SQL
     * @return bool
     */
    public function _create_table($table, $fields, $primary_keys, $keys, $if_not_exists)
    {
        $sql = 'CREATE TABLE ';

        if ($if_not_exists === true) {
            $sql .= 'IF NOT EXISTS ';
        }

        $sql .= $this->db->_escape_identifiers($table)." (";

        $sql .= $this->_process_fields($fields);

        if (count($primary_keys) > 0) {
            $key_name = $this->db->_protect_identifiers(implode('_', $primary_keys));
            $primary_keys = $this->db->_protect_identifiers($primary_keys);
            $sql .= ",\n\tPRIMARY KEY ".$key_name." (" . implode(', ', $primary_keys) . ")";
        }

        if (is_array($keys) && count($keys) > 0) {
            foreach ($keys as $key) {
                if (is_array($key)) {
                    $key_name = $this->db->_protect_identifiers(implode('_', $key));
                    $key = $this->db->_protect_identifiers($key);
                } else {
                    $key_name = $this->db->_protect_identifiers($key);
                    $key = array($key_name);
                }

                $sql .= ",\n\tKEY {$key_name} (" . implode(', ', $key) . ")";
            }
        }

        $sql .= "\n) ENGINE=MYISAM DEFAULT CHARACTER SET {$this->db->char_set} COLLATE {$this->db->dbcollat};";

        return $sql;
    }
}
