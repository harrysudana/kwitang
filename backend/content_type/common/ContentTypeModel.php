<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Template model
 *
 * Nama Class harus unik, tidak boleh ada yang sama. Oleh karena itu disarankan
 * menggunakan suffix Model dibelakang class model.
 *
 * @package ContentType\Common
 * @author Iyan Kushardiasah <iyank4@gmail.com>
 */
abstract class ContentTypeModel extends CI_Model
{
    /** Fields yang dibutuhkan oleh sistem */
    public $fields_required = array(
                                'id' => array(
                                            'type' => 'BIGINT',
                                            'unsigned' => TRUE,
                                            'auto_increment' => TRUE
                                        ),
                                'sct_id' => array(
                                            'type' => 'INT',
                                            'constraint' => 10,
                                            'unsigned' => TRUE,
                                            'default' => 0
                                        ),
                                'active' => array(
                                            'type' => 'INT',
                                            'constraint' => 1,
                                            'default' => 0
                                        )
                              );

    /** Indeks dan primary key yang dibutuhkan oleh sistem */
    public $keys_required = array(
                                'id' => true,
                                'sct_id' =>false,
                                'active' => false
                            );

    // must implement
    public $table_name = '';
    public $fields = array();
    public $keys   = array();
    public $order_by   = '';
    public $sort_by    = '';

    // optional, default value
    public $order      = 'asc';
    public $limit      = 1000;


    // Definition
    // ------------------------------------------------------------------------


    /**
     * Buat table
     *
     * @return  Boolean
     */
    public function create($if_not_exist = true)
    {
        $this->load->dbforge();
        $this->dbforge->add_field($this->fields_required);
        $this->dbforge->add_field($this->fields);

        foreach ($this->keys_required as $key => $value) {
            $this->dbforge->add_key($key, $value);
        }
        if (is_array ($this->keys)) {
            foreach ($this->keys as $value) {
                $this->dbforge->add_key($value, false);
            }
        }

        return $this->dbforge->create_table($this->table_name, $if_not_exist);
    }


    /**
     * Hapus tabel
     * @return  Boolean
     */
    public function drop()
    {
        $this->load->dbforge();
        return $this->dbforge->drop_table($this->table_name);
    }


    /**
     * Periksa apakah tabel sudah ada di database
     *
     * @return  Boolean
     */
    public function tableExist($table_name = null)
    {
        if ($table_name === null) {
            $table_name = $this->table_name;
        }

        return $this->db->table_exists($table_name);
    }


    // Manipulation
    // ------------------------------------------------------------------------


    /**
     * Tambah ke basis data, jika berhasil kembalikan ID data yang barusaja
     * ditambahkan
     *
     * @param   Array
     * @return  int|false
     */
    public function insert($data)
    {
        $ret = $this->db->insert($this->table_name, $data);

        if( $ret) {
            $ret = $this->db->insert_id();
            $class_name = get_class($this);
            user_log($class_name, 'add', 'id='.$ret);
        }

        return $ret;
    }


    /**
     * Update data
     *
     * @param   Array
     * @return  Boolean
     */
    public function update($data, $id)
    {
        if (empty ($data)) {
            return false;
        }

        $ret  = false;
        $lama = $this->get(null, $id, false);

        if ($lama) {
            $this->db->where('id', $id);
            $this->db->limit(1);
            $ret = $this->db->update($this->table_name, $data);

            if ($ret) {
                $class_name = get_class($this);
                user_log($class_name, 'update', 'id='.$id);
            }
        }

        return $ret;
    }


    /**
     * Hapus data berdasarkan ID nya
     *
     * @param   Integer
     * @return  Boolean
     */
    public function delete($id)
    {
        $ret  = false;
        $lama = $this->get(null, $id, false);

        if ($lama) {
            $this->db->where('id', $id);
            $this->db->limit(1);
            $ret = $this->db->delete($this->table_name);
            if ($ret) {
                $class_name = get_class($this);
                user_log($class_name, 'delete', 'id='.$lama->id);
            }
        }

        return $ret;
    }


    // Retrieve
    // ------------------------------------------------------------------------


    /**
     * Ambil satu data berdasarkan ID nya
     *
     * @return  Object|false
     */
    public function get($sct_id = null, $id = 'last', $only_active = true)
    {
        // dalam kondisi normal, input terakhir memiliki ID paling tinggi
        if (strtolower ($id) == 'last' || empty($id)) {
            $this->db->order_by('id', 'desc');
        } elseif (strtolower($id) == 'first') {
            $this->db->order_by('id', 'asc');
        } else {
            $id = intval($id);
            if ($id <= 0) {
                return false;
            }
            $this->db->where('id', $id);
        }
        $this->db->limit(1);

        if ($sct_id) {
            $this->db->where ('sct_id', $sct_id);
        }
        if ($only_active) {
            $this->db->where('active', true);
        }

        $query = $this->db->get($this->table_name);

        return $query ? $query->row() : false;
    }


    /**
     * Ambil semua data
     *
     * @param   Integer  Id tipe-konten pada sebuah struktur
     * @param   Integer  Jumlah data yang diminta
     * @param   Integer  Offset data yang diminta
     * @param   Array    Order by, format: array('field'=>'asc|desc')
     * @param   Array    Where Like, format: array('field'=>'%search_key%')
     * @param   Boolean
     * @return  Array
     */
    public function all($sct_id = null, $limit = 1000, $offset = 0, $orders = null, $searchs = null, $only_active = true)
    {
        // count total_found
        if ( ! empty ($sct_id)) {
            $this->db->where ('sct_id', $sct_id);
        }
        if ( ! empty ($searchs) AND is_array($searchs)) {
            foreach ($searchs as $key => $value) {
                $key = trim ($key);
                if (strpos($key, ' ')) {
                    $this->db->where($key, $value);
                } else {
                    $this->db->like($key, $value);
                }
            }
        }
        if ($only_active) {
            $this->db->where('active', true);
        }
        $total_found = $this->db->count_all_results ($this->table_name);


        // retrieve the data
        if ( ! empty ($sct_id)) {
            $this->db->where ('sct_id', $sct_id);
        }
        if ( ! empty ($searchs) AND is_array($searchs)) {
            foreach ($searchs as $key => $value) {
                $key = trim ($key);
                if (strpos($key, ' ')) {
                    $this->db->where($key, $value);
                } else {
                    $this->db->like($key, $value);
                }
            }
        }
        if ($only_active) {
            $this->db->where('active', true);
        }
        if ($limit > 0 OR $offset > 0) {
            $this->db->limit ($limit, $offset);
        }
        if ( ! empty ($orders) AND is_array($orders)) {
            foreach ($orders as $key => $value) {
                $this->db->order_by($key, $value);
            }
        } else {
            $this->db->order_by('id', 'desc');
        }
        $query = $this->db->get ($this->table_name);

        $total_page  = ceil ( $total_found / $limit );
        $page_number = $limit > 0 ? (( $offset / $limit) + 1) : 1;
        $total_all   = $this->db->count_all($this->table_name);

        $retval = array('data'       => $query->result(),
                        'total_data' => $query->num_rows(),
                        'total_found'=> $total_found,
                        'total_all'  => $total_all,
                        'page_number'=> $page_number,
                        'total_page' => $total_page
                        );

        return $retval;
    }
}
