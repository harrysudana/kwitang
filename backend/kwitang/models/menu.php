<?php
if (! defined('FRONT_PATH')) {
    exit ('Kwitang ERROR..!!!');
}
/**
 * Menu model, front end menu
 *
 * @package  Kwitang\Models
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class Menu extends CI_Model
{
    public function all($limit = 100)
    {
        $this->db->from('menu');
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        $query = $this->db->get();

        return $query->result();
    }

    public function get($menu_id)
    {
        $this->db->from('menu');
        $this->db->where('id', $menu_id);
        $query = $this->db->get();

        return $query->row();
    }

    public function insert($data)
    {
        if (empty ($data['title'])) {
            return false;
        }

        $retval = $this->db->insert('menu', $data);
        if ($retval) {
            user_log('menu', 'add', $data['title']);
        }

        return $retval;
    }

    public function update($data)
    {
        if (! isset($data['id']) and count($data) < 2) {
            return false;
        }

        $id = $data['id'];
        unset ($data['id']);
        $old = $this->get($id);

        $this->db->where('id', $id);
        $retval = $this->db->update('menu', $data);

        if ($retval) {
            user_log('menu', 'update', $old->title);
        }

        return $retval;
    }

    public function delete($id)
    {
        $old = $this->get($id);

        $this->db->where('menu_id', $id);
        $retval = $this->db->delete('menu_detail');

        if ($retval) {
            $this->db->where('id', $id);
            $retval = $this->db->delete('menu');
        }

        if ($retval) {
            user_log('menu', 'delete', $old->title);
        }

        return $retval;
    }
}
