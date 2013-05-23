<?php
if (! defined('FRONT_PATH')) {
    exit ('Kwitang ERROR..!!!');
}
/**
 * Manage Roles data
 *
 * @package  Kwitang\Models
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class Roles extends CI_Model
{
    /**
     * Ambil semua data hak akses
     *
     * @param   Integer  Batas jumlah data yang diambil
     * @return  Array  ActiveRecord result Object
     */
    public function all($limit = 1000)
    {
        $this->db->from('role');
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        $this->db->order_by('title', 'asc');
        $query = $this->db->get();

        return $query->result();
    }


    /**
     * Ambil satu hak akses berdasarkan `ID` nya
     *
     * @param   Integer
     * @return  Object
     */
    public function get($role_id)
    {
        $this->db->from('role');
        $this->db->where('id', $role_id);
        $query = $this->db->get();

        return $query->row();
    }


    /**
     * Ambil satu hak akses berdasarkan `title` nya
     *
     * @param   String
     * @return  Object
     */
    public function getByName($title)
    {
        $this->db->from('role');
        $this->db->where('title', $title);
        $query = $this->db->get();

        return $query->row();
    }


    /**
     * Simpan hak akses
     *
     * @param   Array
     * @return  Object
     */
    public function insert($data)
    {
        if (empty ($data)) {
            return false;
        }

        if (empty ($data['title'])) {
            return false;
        }

        $ret = $this->db->insert('role', $data);
        if ($ret) {
            $ret = $this->db->insert_id();
            user_log('role', 'add', $data['title']);
        }

        return $ret;
    }


    /**
     * Perbaharui data hak akses
     *
     * Parameter yang diberikan harus menyertakan ID data hak akses yang akan
     * di perbaharui.
     *
     * @param   Array  Harus memiliki $data['id'] = id role yang akan diubah
     * @return  Object
     */
    public function update($data)
    {
        if (empty ($data['id'])) {
            return false;
        }

        $id = $data['id'];
        unset($data['id']);

        $this->db->where('id', $id);
        $ret = $this->db->update('role', $data);
        if ($ret) {
            user_log('role', 'update', $data['title']);
        }

        return $ret;
    }


    /**
     * Hapus data hak akses
     *
     * @param   Integer
     * @return  Boolean
     */
    public function delete($id)
    {
        $lama = $this->get($id);

        $this->db->where('role_id', $id);
        $ret = $this->db->delete('role_detail');

        if ($ret) {
            $this->db->where('id', $id);
            $ret = $this->db->delete('role');
        }

        if ($ret) {
            user_log('role', 'delete', 'Menghapus Roles: '.$lama->title);
        }

        return $ret;
    }
}
