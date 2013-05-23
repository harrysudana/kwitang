<?php
if (! defined('FRONT_PATH')) {
    exit ('Kwitang ERROR..!!!');
}
/**
 * Konfigurasi CMS
 *
 * @package  Kwitang\Models
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class KConfig extends CI_Model
{
    /**
     * Summary
     *
     * @param   Integer
     * @return  Array   Konfigurasi array(keyname => value)
     */
    public function all($section, $limit = 1000)
    {
        $this->db->where('section', $section);

        if (! isset($this->$section)) {
            $tmp_arr = array();

            if ($limit > 0) {
                $this->db->limit($limit);
            }

            $query = $this->db->get('site_config');

            if (! empty($query)) {
                $rs = $query->result();
                if (! empty($rs)) {
                    foreach ($rs as $row) {
                        $tmp_arr[$row->keyname] = kstripslashes($row->value);
                    }
                }
            }

            $this->$section = $tmp_arr;
        }

        return $this->$section;
    }


    /**
     * Summary
     *
     * @param   String
     * @param   String
     * @return  String
     */
    public function get($section, $keyname, $default_value = '')
    {
        // if still empty, generate the cache
        if (! isset($this->$section)) {
            $this->all($section);
        }

        // Note:
        // $this->$section['keyname'] will raise an error
        $tmp_arr =& $this->$section;

        return isset ($tmp_arr[$keyname]) ? $tmp_arr[$keyname] : $default_value;
    }


    /**
     * Summary
     *
     * @param   String
     * @param   String
     * @return  Boolean    When succes return true
     */
    public function set($section, $keyname, $value)
    {
        $retval    = false;
        $the_value = $this->get($section, $keyname, false);

        $this->db->set('value', kaddslashes($value));

        if ($the_value !== false) {
            $retval = true;
            if ($value != $the_value) {
                $this->db->where('section', $section);
                $this->db->where('keyname', $keyname);
                $retval = $this->db->update('site_config');
            }
        } else {
            $this->db->set('section', $section);
            $this->db->set('keyname', $keyname);
            $retval = $this->db->insert('site_config');
        }

        if ($retval) {
            $tmp_arr           =& $this->$section;
            $tmp_arr[$keyname] = $value;
        }

        return $retval;
    }


    /**
     * Summary
     *
     * @param   String
     * @return  Boolean
     */
    public function delete($section, $keyname)
    {
        $retval    = false;
        $the_value = $this->get($section, $keyname, false);

        if ($the_value !== false) {
            $this->db->where('section', $section);
            $this->db->where('keyname', $keyname);
            $retval = $this->db->delete('site_config');
            $tmp_arr           =& $this->$section;
            unset($tmp_arr[$keyname]);
        }

        return $retval;
    }
}

/* End of file site_config4.php */
