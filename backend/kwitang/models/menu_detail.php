<?php
if (! defined('FRONT_PATH')) {
    exit ('Kwitang ERROR..!!!');
}
/**
 * Menu detail model, front end menu
 *
 * @package  Kwitang\Models
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class Menu_detail extends CI_Model
{
    public static $id        = 0;
    public static $menu_id   = 0;
    public static $title     = '';
    public static $icon      = '';
    public static $url       = '';
    public static $parent_id = null;
    public static $order     = 0;

    /**
     * Get menu detail
     *
     * @return array
     **/
    public function getall($menu_id = null)
    {
        $this->db->order_by('order');
        if (! empty($menu_id)) {
            $this->db->where('menu_id', $menu_id);
        }
        $this->db->from('menu_detail');
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->result() : false;
    }

    public function get($id)
    {
        $this->db->from('menu_detail');
        $this->db->where('id', $id);
        $query = $this->db->get();

        return ($query->num_rows() > 0) ? $query->row() : false;
    }

    public function get_max_order($menu_id, $parent_id)
    {
        $this->db->select('MAX(`order`) as maxorder');
        $this->db->from('menu_detail');
        $this->db->where('menu_id', $menu_id);
        $this->db->where('parent_id', $parent_id);
        $query = $this->db->get();

        $r = $query->row();
        $maxorder = $r->maxorder;
        $maxorder++;

        return $maxorder;
    }

    public function save($data)
    {
        $retval = false;

        if (! empty ($data['id'])) {
            // update
            $this->db->where('id', $data['id']);
            $retval = $this->db->update('menu_detail', $data);
        } elseif (! empty($data['menu_id']) or ! empty($data['title']) or ! empty($data['url'])) {
            // insert
            $retval = $this->db->insert('menu_detail', $data);
        }

        return $retval;
    }

    public function delete($id)
    {
        // TODO: hapus secara rekrusif, seperti menghapus struktur
        $this->db->where('parent_id', $id);
        $child = $this->db->get('menu_detail');

        if ($child->num_rows() > 0) {
            show_error('Silakan Hapus seluruh menu di bawah menu yang akan Anda hapus.');
        } else {
            $lama = $this->get($id);
            @unlink($lama->icon);
            $this->db->where('id', $id);
            $retval = $this->db->delete('menu_detail');
        }

        return $retval;
    }
}
