<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Model pada tipe-konten Article
 *
 * @package  ContentType\Article\Models
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class ArticleModel extends ContentTypeModel
{
    public $table_name = 'ct_articles';
    public $fields = array(
                        'title' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 160
                                ),
                        'slug' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 160
                                ),
                        'author' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 65
                                ),
                        'pub_date' => array(
                                    'type' => 'DATETIME'
                                ),
                        'thumbnail' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'foto_description' => array(
                                    'type' => 'TEXT'
                                ),
                        'description' => array(
                                    'type' => 'TEXT'
                                ),
                        'body' => array(
                                    'type' => 'LONGTEXT'
                                ),
                        'tags' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'guid' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'permalink' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'null' => TRUE
                                ),
                        'counter' => array(
                                    'type' => 'BIGINT',
                                    'unsigned' => TRUE,
                                    'default' => 0
                                ),
                        'lang' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 2,
                                    'default' => 'id'
                                ),
                        'lang_group' => array(
                                    'type' => 'BIGINT',
                                    'null' => TRUE
                                ),
                        'source' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'default' => ''
                                ),
                        'source_link' => array(
                                    'type' => 'VARCHAR',
                                    'constraint' => 255,
                                    'default' => ''
                                )
                     );

    public $keys = array('slug', 'pub_date', 'tags', 'guid', 'lang', 'lang_group');

    public function get($sct_id = null, $id = 'last', $only_active = true)
    {
        $lang = ! empty($this->vars['lang']) ? $this->vars['lang'] : 'id';

        // ambil lang_group
        $id = intval($id);
        if ($id > 0) {
            $this->db->where('id', $id);
            $query = $this->db->get($this->table_name);
            if (! $query) {
                return false;
            }
            $row        =  $query->row();
            $lang_group = $row->lang_group;

            $this->db->where('lang_group', $lang_group);
            $this->db->where('lang', $lang);
        } elseif (strtolower ($id) == 'last' || empty($id)) {
            $this->db->order_by('pub_date', 'desc');
            $this->db->limit(1);
        } elseif (strtolower($id) == 'first') {
            $this->db->order_by('pub_date', 'asc');
            $this->db->limit(1);
        } else {
            return false;
        }

        $this->db->where('lang', $lang);

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
     * Ambil satu data berdasarkan lang_group dan lang nya
     *
     * @return  Object|false
     */
    public function getLang($sct_id = null, $lang_group = 'last', $lang_request = 'id', $only_active = true)
    {
        // dalam kondisi normal, input terakhir memiliki ID paling tinggi
        if (strtolower ($lang_group) == 'last') {
            $this->db->order_by('pub_date', 'desc');
            $this->db->where('lang', $lang_request);
            $this->db->limit(1);
        } elseif (strtolower($lang_group) == 'first') {
            $this->db->order_by('pub_date', 'asc');
            $this->db->where('lang', $lang_request);
            $this->db->limit(1);
        } else {
            $lang_group = intval($lang_group);
            if ($lang_group <= 0) {
                return false;
            }
            $this->db->where('lang_group', $lang_group);
            $this->db->where('lang', $lang_request);
        }

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
     * Tambah ke basis data, jika berhasil kembalikan ID data yang barusaja
     * ditambahkan
     *
     * @param   Array
     * @return  int|false
     */
    public function insert($data)
    {
        if ( ! empty($data['guid'])) {
            $this->db->where('guid', $data['guid']);
            $this->db->from('ct_articles');
            $rs = $this->db->get();

            if($rs->num_rows() > 0) {

                return false;
            }
        }

        $ret = $this->db->insert($this->table_name, $data);

        if( $ret) {
            $ret = $this->db->insert_id();

            // update lang_group
            if (empty($data['lang_group'])) {
                $newdata = array('lang_group' => $ret);
                $this->db->where('id', $ret);
                $reret = $this->db->update($this->table_name, $newdata);
            }

            $class_name = get_class($this);
            user_log($class_name, 'add', 'id='.$ret);
        }

        return $ret;
    }


    /**
     * Hapus data berdasarkan ID nya
     *
     * @param   Integer
     * @return  Boolean
     */
    public function delete($lang_group)
    {
        $ret  = false;
        $lama = $this->getLang(null, $lang_group, 'id', false);

        if ($lama) {
            $this->db->where('lang_group', $lang_group);
            $ret = $this->db->delete($this->table_name);
            if ($ret) {
                $class_name = get_class($this);
                user_log($class_name, 'delete', 'lang_group='.$lama->lang_group);
            }
        }

        return $ret;
    }


    /**
     * Get all related content
     *
     *
     */
    public function related($sct_id= NULL, $search_field, $search_text, $exclude_id = '', $limit = 10, $only_active = true)
    {
        $this->db->from($this->table_name);
        // DB forge tidak support create FULLTEXT index
        // $this->db->where('MATCH(`title`, `author`, `description`, `body`, `tags`) AGAINST  (' . $this->db->escape($search_text) . ')');

        $search_text = strtolower($search_text);
        $_search = explode(' ', $search_text);
        $_search = explode(',', $search_text);

        // min 3 huruf
        $_tmp = array();
        foreach ($_search as $value) {
            if (strlen ($value) > 2) {
                $_tmp[] = $value;
            }
        }
        $_search = $_tmp;

        // buang kata penghubung
        $remove = array('dan', 'yang', 'atau', 'tetapi', 'sesudah', 'jika', 'agar', 'supaya',
                        'dengan', 'bahwa', 'karena', 'ketika', 'maka', 'juga',  'sedangkan',
                        'hingga', 'meski', 'lalu', 'sambil', 'serta', 'apabila',
                        'lagi', 'pula', 'andaikata', 'sebab', 'sebelum', 'selama',
                        'sehingga', 'seandainya', 'sekiranya', 'melainkan', 'semenjak',
                        'andaikan', 'bagaikan', 'asalkan', 'jangankan', 'walaupun',
                        'meskipun', 'kendatipun', 'lagi', 'hanya', 'sekalipun', 'sungguhpun',
                        'melainkan', 'sampai', 'tatkala', 'kecuali', 'seraya', 'sambil');
        /*
        'dan', 'dengan', 'serta', ' atau', ' tetapi', 'namun', 'sedangkan', 'sebaliknya', 'melainkan',
        'hanya', 'bahwa', 'malah', 'lagi', 'pula', 'apa', 'apalagi', 'jangan', 'kecuali', 'hanya',
        'lalu', 'kemudian', 'selanjutnya', 'yaitu', 'yakni', 'adalah', 'bahwa', 'ialah', 'jadi',
        'karena', 'oleh', 'ini', 'itu', 'sebab', 'karena', 'kalau', 'jikalau', 'jika', 'bila',
        'apabila', 'asal', 'agar', 'supaya', 'ketika', 'sewaktu', 'sebelum', 'sesudah', 'tatkala',
        'selama', 'sampai', 'hingga', 'sehingga', 'untuk', 'guna', 'seperti', 'laksana', 'sebagai',
        'tempat'
        */
        $_tmp = array();
        $_regexp = '';
        foreach ($_search as $value) {
            if ( ! in_array($value, $remove)) {
                $_regexp .= $value.'|';
            }
        }
        $_regexp = rtrim($_regexp, '|');

        if ( ! empty ($_regexp)) {
            $this->db->where('title REGEXP '.$this->db->escape($_regexp));
        }

        $this->db->order_by('pub_date', 'desc');

        if ($sct_id)
            $this->db->where('sct_id', $sct_id);

        if ($only_active)
            $this->db->where('active', true);

        if ($exclude_id)
            $this->db->where('id !=', $exclude_id);

        if ($limit > 0)
            $this->db->limit($limit);

        $query = $this->db->get();

        return $query->result();
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
        $lang = ! empty ($this->vars['lang']) ? $this->vars['lang'] : 'id';

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
        $this->db->where('lang', $lang);
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
        $this->db->where('lang', $lang);
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

    /**
     * Ambil semua data
     *
     * @param   Integer  Id lang_group
     * @param   Integer  Status Aktif 0 atau 1
     * @return  Boolean
     */
    public function updateActive($lang_group, $active)
    {
        $this->db->where('lang_group', $lang_group);

        return $this->db->update($this->table_name, array('active' => $active));
    }

    public function search($search_text, $page_number, $item_perpage, $fields = 'title', $only_active = true)
    {
        $search_text = strtolower($search_text);
        $search_text = str_replace('+', ' ', $search_text);
        $_search = explode(' ', $search_text);

        // buang kata penghubung, trivia step, just bahasa
        $remove = array('dan','yang','atau','tetapi','sesudah','namun','jika','agar','supaya','sebaliknya',
                        'dengan','bahwa','karena','ketika','maka','juga',  'sedangkan','kemudian','selanjutnya',
                         'yaitu','yakni','adalah','ialah','jadi','bila','jikalau','kalau','oleh','ini','itu',
                        'hingga','meski','lalu','sambil','serta','apabila','apalagi','apa',
                        'lagi','pula','andaikata','sebab','sebelum','selama','malah',
                        'sehingga','seandainya','sekiranya','semenjak','jangan','seperti','laksana','sebagai',
                        'andaikan','bagaikan','asalkan','jangankan','walaupun','guna','sewaktu',
                        'meskipun','kendatipun','hanya','sekalipun','sungguhpun','asal',
                        'melainkan','sampai','tatkala','kecuali','seraya');

        $_tmp = array();
        $_regexp = '';
        foreach ($_search as $value) {
            $value = trim ($value);
            // min 3 char and not in kata penghubung
            if (strlen ($value) > 2 AND ! in_array($value, $remove)) {
                $_regexp .= $value.'|';
            }
        }
        $_regexp = rtrim($_regexp, '|');

        $i = 1;
        $sql_select = '';
        $sql_having = '';
        $sql_order  = '';

        if ( ! empty ($_regexp)) {
            if ( is_array($fields)) {
                foreach ($fields as $value) {
                    $this->db->or_where($this->table_name.'.'.$value.' REGEXP '.$this->db->escape($_regexp));
                    $sql_select .= $this->table_name.'.'.$value.' REGEXP '.$this->db->escape($_regexp).' AS compare'.$i.', ';
                    $sql_having .= 'compare'.$i.' = 1 OR ';
                    $sql_order .= 'compare'.$i.'+';
                    $i++;
                }
            } else {
                $this->db->where($this->table_name.'.'.$fields.' REGEXP '.$this->db->escape($_regexp));
                $sql_select .= $this->table_name.'.'.$fields.' REGEXP '.$this->db->escape($_regexp).' AS compare'.$i.', ';
                $sql_having .= 'compare'.$i.' = 1 OR ';
                $sql_order .= 'compare'.$i.'+';
            }
        }
        $sql_select = rtrim($sql_select, ', ');
        $sql_having = rtrim($sql_having, 'OR ');
        $sql_order  = '('.rtrim($sql_order, '+').') DESC, `pub_date` DESC';

        if ($only_active) {
            $this->db->where('active', true);
        }

        // count found data.
        $this->db->from($this->table_name);
        $this->db->join('sct', $this->table_name.'.sct_id=sct.id', 'left');
        $total_result = $this->db->count_all_results();


        // build select data
        // ---------------------------------------------------------------------
        $this->db->select($this->table_name.'.*, sct.name AS sct_name, sct.title AS sct_title');
        $this->db->select($sql_select);
        $this->db->from($this->table_name);
        $this->db->join('sct', $this->table_name.'.sct_id=sct.id', 'left');
        $this->db->having($sql_having);
        $this->db->order_by($sql_order);

        if ($only_active) {
            $this->db->where('active', true);
        }

        if ($item_perpage > 0) {
            $start = ($page_number-1) * $item_perpage;
            $this->db->limit($item_perpage, $start);
        }

        $query = $this->db->get();
        $data  = $query ? $query->result() : null;

        return array('total' => $total_result , 'data' => $data);
    }
}
