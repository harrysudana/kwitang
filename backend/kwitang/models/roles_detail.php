<?php
if (! defined('FRONT_PATH')) {
    exit ('Kwitang ERROR..!!!');
}
/**
 * Roles detail model
 *
 * @package  Kwitang\Models
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class Roles_detail extends CI_Model
{
    public static $id               = 0;
    public static $role_id          = 0;
    public static $structurect_id   = 0;
    public static $permission       = 'noaccess';

    /**
     * Ambil seluruh data role detail, Setel parameter $role_id jika hanya
     * akan mengambil detail role pada role id tersebut.
     *
     * @param  Integer|Null
     * @return Array
     */
    public function all($role_id = null)
    {
        $this->db->order_by('structure_id');
        if (! empty($role_id)) {
            $this->db->where('role_id', $role_id);
        }
        $this->db->from('role_detail');
        $query = $this->db->get();

        $ret = false;
        if ($query and $query->num_rows()) {
            $ret = $query->result();
        }

        return $ret;
    }

    /**
     * Ambil Detail role berdasarkan ID nya
     *
     * @param  Integer
     * @return Object|false
     */
    public function get($id)
    {
        $this->db->from('role_detail');
        $this->db->where('id', $id);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->row() : false;
    }

    /**
     * Simpan role, jika array yang diberikan mempunyai key id $data['id'],
     * maka akan dilakukan update data, jika tidak, akan disimpan sebagai
     * data baru
     *
     * @param  Array
     * @return Boolean
     */
    public function save($data)
    {
        $retval = false;

        if (! empty ($data['id'])) {
            // update
            $this->db->where('id', $data['id']);
            $retval = $this->db->update('role_detail', $data);
        } elseif (! empty($data['role_id']) or ! empty($data['structure_id']) or ! empty($data['permission'])) {
            // insert
            $retval = $this->db->insert('role_detail', $data);
        }

        return $retval;
    }

    /**
     * Hapus detail role berdasarkan ID nya
     *
     * @param  Integer
     * @return Boolean
     */
    public function delete($id)
    {
        $lama = $this->get($id);
        $this->db->where('id', $id);
        $retval = $this->db->delete('role_detail');

        return $retval;
    }

    /**
     * Hapus seluruh detail role, jika $role_id disetel hanya detail role
     * yang dimiliki oleh role id tersebut.
     *
     * @param  Integer  Optional
     * @return Boolean
     */
    public function deleteall($role_id = null)
    {
        if (empty ($role_id)) {
            return false;
        }

        $this->db->where('role_id', $role_id);
        return $this->db->delete('role_detail');
    }
}
