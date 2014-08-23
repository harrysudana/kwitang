<?php
if (! defined('FRONT_PATH')) {
    exit('Kwitang ERRor..!!!');
}
/**
 * Structure model, Skeleton for organizing content
 *
 * @package  Kwitang\Models
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class Structure extends CI_Model
{
    private $__cache_structure = null;    // for caching all() resultset

    /**
     * Ambil semua data struktur, diurutkan berdasar parent_id, order dan title
     *
     * Return value dari resultset ActiveRecord CodeIgniter
     *
     * @return Array Array of Structure Objects
     */
    public function all()
    {
        if (is_null($this->__cache_structure)) {
            $this->db->from('structure');
            $this->db->order_by("parent_id", "asc");
            $this->db->order_by("order", "asc");
            $this->db->order_by("title", "asc");

            $query = $this->db->get();
            $this->__cache_structure = $query->result();
        }

        return $this->__cache_structure;
    }

    /**
     * Semua data Structure format Tree-Child
     *
     * @param   Integer
     * @param   Boolean  Set true untuk menampilkan semuanya
     * @return  Array
     */
    public function allTree($parent_id = null, $show_all = false)
    {
        if ($this->__cache_structure === null) {
            $this->all();
        }

        $retval = $this->__allTreeRecrusive($parent_id, $show_all);

        if (! empty($parent_id)) {
            $childs = $retval;
            $retval = $this->get($parent_id);
            $retval->childs = $childs;
        }

        return $retval;
    }

    /**
     * Recrusive tree
     *
     * @param   Integer
     * @param   Boolean  Set true untuk menampilkan semuanya
     * @return  Array
     */
    private function __allTreeRecrusive($parent_id = null, $show_all = false)
    {
        if ($this->__cache_structure === null) {
            $this->all();
        }

        $retval = array();
        foreach ($this->__cache_structure as $s) {
            if ($s->parent_id == $parent_id) {
                if ($show_all == true) {
                    $_get_childs = true;
                } elseif ($s->in_menu == 1) {
                    $_get_childs = true;
                } else {
                    $_get_childs = false;
                }

                if ($_get_childs) {
                    $s->childs = $this->__allTreeRecrusive($s->id, $show_all);
                    $retval[] = $s;
                }
            }
        }

        return $retval;
    }

    /**
     * Ambil struktur yang menuju pada $structure_id yang diberikan.
     *
     * @param   Integer
     * @return  Array
     */
    public function getBreadcrumb($structure_id)
    {
        $retval = array();

        foreach ($this->all() as $sc) {
            if ($sc->id == $structure_id) {
                if (! empty ($sc->parent_id)) {
                    $tmp = $this->getBreadcrumb($sc->parent_id);
                    if (! empty ($tmp)) {
                        $retval = array_merge($retval, $tmp);
                    }
                }
                $retval[] = $sc;
                break;
            }
        }

        return $retval;
    }

    /**
     * Ambil struktur berdasarkan *name* atau *id* nya
     *
     * @param  String  String Nama atau Integer ID
     * @return Object
     */
    public function get($name_or_id)
    {
        // Jika sebelumnya sudah ada request ke all() pake hasil dari situ aja
        if ($this->__cache_structure !== null) {
            foreach ($this->__cache_structure as $s) {
                if ($s->id == $name_or_id or $s->name == $name_or_id) {
                    return $s;
                }
            }
        }

        $this->db->from('structure');
        $this->db->where('name', $name_or_id);
        $this->db->or_where('id', $name_or_id);
        $query = $this->db->get();

        return $query ? $query->row() : null;
    }

    /**
     * Ambil nomor urut *(order)* tertinggi
     *
     * @param  Integer  ID struktur parent nya
     * @return Integer  Nomor urut paling tinggi
     */
    public function maxOrder($sructure_id = null)
    {
        $this->db->select_max('order', 'maxorder');
        $this->db->from('structure');
        $this->db->where('parent_id', $sructure_id);
        $query = $this->db->get();

        $r = $query->row();

        return $r->maxorder ? $r->maxorder : 0;
    }

    /**
     * Ambil struktur dibawah ID struktur yang diberikan
     *
     * @param  Integer  ID Struktur yang akan parentnya
     * @return Array
     */
    public function getChilds($structure_id)
    {
        $this->db->from('structure');
        $this->db->where('parent_id', $structure_id);
        $this->db->order_by("order, title", "asc");

        $retval = false;
        $query = $this->db->get();

        return $query ? $query->result() : null;
    }

    /**
     * Simpan atau Perbaharui data struktur
     *
     * Jika array $data yang dikirimkan mengandung indeks 'id', operasi yang
     * akan dilakukan adalah update. Jika tidak ada 'id', maka akan disimpan
     * sebagai data baru.
     *
     * @param  Array  Associative array, sesuai dengan field pada database
     * @return Integer|False  False if failed, jika sukses ID (baru) struktur
     */
    public function save($data)
    {
        if (isset($data['order']) and ! is_numeric($data['order'])) {
            $data['order'] = 1;
        }

        $retval = false;
        if (empty ($data['id'])) {
            // Check required fields
            if (! isset ($data['name']) or ! isset ($data['title'])) {
                return false;
            }

            $retval = $this->db->insert('structure', $data);
            if ($retval) {
                $retval = $this->db->insert_id();
                user_log('structure', 'add', 'id='.$retval.';title='.$data['title']);
            }
        } else {
            if (count($data) < 2) {
                return false;
            }

            $id = $data['id'];
            unset($data['id']);
            $lama = $this->get($id);

            if (isset($data['name']) and $data['name'] == $lama->name) {
                unset($data['name']);
            }

            $this->db->where('id', $id);
            $retval = $this->db->update('structure', $data);

            if ($retval) {
                user_log('structure', 'update', 'id='.$lama->id.';title='.$lama->title);
            }
        }

        return $retval;
    }

    /**
     * Hapus Data struktur berdasarkan ID nya
     *
     * @param  Integer
     * @return Boolean
     */
    public function delete($id)
    {
        $retval = $this->__delchild($id);

        return $retval;
    }

    /**
     * Hapus Struktur dibawah $id struktur yang diberikan
     *
     * @param  Integer
     * @return Boolean
     */
    private function __delchild($id)
    {
        $retval = true;

        $child = $this->getChilds($id);
        if (! empty($child)) {
            foreach ($child as $c) {
                $retval = $this->__delchild($c->id);
            }
        }

        if ($retval) {
            $data = $this->get($id);
            if (! empty($data)) {
                $this->sctDeleteAll($id);
                $this->db->where('id', $id);
                $retval = $this->db->delete('structure');
                if ($retval) {
                    @unlink($data->icon);
                    @unlink($data->foto);
                    user_log('structure', 'delete', $data->name);
                }
            }
        }

        return $retval;
    }


    //==========================================================================

    /**
     * Ambil semua SCT dari sebuah struktur
     *
     * @param  Integer
     * @return Array
     */
    public function sctAll($structure_id)
    {
        $retval = false;

        $this->db->from('sct');
        $this->db->where('structure_id', $structure_id);
        $this->db->order_by('order', 'asc');
        $query = $this->db->get();

        return $query ? $query->result() : null;
    }

    /**
     * Ambil data SCT
     *
     * @param  String|Integer  Nama SCT atau ID SCT
     * @return Object
     */
    public function sctGet($sct_name_or_id)
    {
        if (empty ($sct_name_or_id)) {
            return false;
        }

        $retval = false;
        $this->db->from('sct');
        $this->db->where('id', $sct_name_or_id);
        $this->db->or_where('name', $sct_name_or_id);
        $query = $this->db->get();

        // double? should be impossible!
        if ($query->num_rows() == 1) {
            $retval = $query->row();
        }

        return $retval;
    }

    /**
     * Simpan data SCT ke tabel structure_ct
     *
     * SCT name *harus* menggunakan karakter alfanumerik, jika menggunakan angka
     * sebagai SCT name, kemungkinan terjadi kemelut :)
     *
     * @param  Array  Associative array sesuai dengan field pada database.
     * @return Integer|False  id yang dimasukkan, false jika gagal
     */
    public function sctSave($data)
    {
        if (empty ($data['id'])) {
            // Required fields
            if (empty ($data['structure_id']) or empty ($data['name']) or empty ($data['content_type'])  or empty ($data['title'])) {
                return false;
            }
            $retval = $this->db->insert('sct', $data);
            if ($retval) {
                $retval = $this->db->insert_id();
                user_log('sct', 'add', 'id='.$retval.';'.$data['name'].';'.$data['title'].';'.$data['content_type']);
            }
        } else {
            if (empty ($data['id']) or count($data) < 2) {
                return false;
            }
            $this->db->where('id', $data['id']);
            $retval = $this->db->update('sct', $data);
            if ($retval) {
                $retval = $data['id'];
                user_log('sct', 'update', 'id='.$data['id']);
            }
        }

        return $retval;
    }

    /**
     * Hapus data SCT
     *
     * @param  Integer
     * @return Boolean
     */
    public function sctDelete($sct_id)
    {
        $sct = $this->sctGet($sct_id);
        if (empty ($sct)) {
            return true;
        }

        $this->db->where('id', $sct_id);
        $retval = $this->db->delete('sct');
        if ($retval) {
            user_log('sct', 'delete', 'id='.$sct_id.';name='.$sct->name);
        }

        return $retval;
    }

    /**
     * Hapus seluruh data SCT di dalam sebuah struktur
     *
     * @param  Integer
     * @return Boolean
     */
    public function sctDeleteAll($structure_id)
    {
        $_all_sct = $this->sctAll($structure_id);
        if (empty ($_all_sct)) {
            return true;
        }

        foreach ($_all_sct as $value) {
            $retval = $this->sctDelete($value->id);
            if (! $retval) {
                break;
            }
        }

        return $retval;
    }

    /**
     * Ambil semua Konfigurasi sebuah SCT
     *
     * @param   Integer
     * @return  Array|False
     */
    public function sctConfigAll($sct_id)
    {
        $retval = false;

        $this->db->where('sct_id', $sct_id);
        $rs = $this->db->get('sct_config');

        if ($rs->num_rows() > 0) {
            foreach ($rs->result() as $r) {
                $retval[$r->keyname] = $r->value;
            }
        }

        return $retval;
    }

    /**
     * Ambil item konfigurasi sebuah SCT
     *
     * @param   Integer
     * @param   String
     * @return  String|False
     */
    public function sctConfigGet($sct_id, $keyname)
    {
        $retval = false;

        $this->db->select('value');
        $this->db->where('sct_id', $sct_id);
        $this->db->where('keyname', $keyname);
        $rs = $this->db->get('sct_config');

        if ($rs->num_rows() > 0) {
            $r = $rs->row();
            $retval = $r->value;
        }

        return $retval;
    }

    /**
     * Simpan sebuah konfigurasi SCT
     *
     * @param  Integer
     * @param  String
     * @param  String
     * @return Boolean
     */
    public function sctConfigSave($sct_id, $keyname, $value)
    {
        $retval = false;
        $curr_val = $this->sctConfigGet($sct_id, $keyname);
        if ($curr_val === false) {
            $data = array('sct_id' => $sct_id,
                          'keyname' => $keyname,
                          'value' => $value);
            $retval = $this->db->insert('sct_config', $data);
            if ($retval) {
                user_log('sct_config', 'add', "id: $sct_id $keyname= $value");
            }
        } else {
            $data = array('value' => $value);
            $this->db->where('sct_id', $sct_id);
            $this->db->where('keyname', $keyname);
            $retval = $this->db->update('sct_config', $data);
            if ($retval) {
                user_log('sct_config', 'update', "id: $sct_id $keyname= $value");
            }
        }

        return $retval;
    }

    /**
     * Hapus sebuah konfigurasi SCT
     *
     * @param   Integer
     * @param   String
     * @return  Boolean
     */
    public function sctConfigDelete($sct_id, $keyname)
    {
        $this->db->where('sct_id', $sct_id);
        $this->db->where('keyname', $keyname);

        return $this->db->delete('sct_config');
    }
}
