<?php
/**
 * Kelas abstrak yang harus di extends oleh Controller
 *
 * Nama class harus unik, tidak boleh ada yang sama. Oleh karena itu disarankan
 * menggunakan prefix untuk class controller.
 *
 * Untuk sebuat tipe-konten, harus mempunyai class kontroller yang namanya sama
 * dengan nama folder tipe-konten.
 *
 * Nama file kontroller harus identik dengan nama class, serta kontroller utama
 * harus identik dengan nama folder tipe-konten. Dan juga mengenai BesarKecil
 * huruf yang digunakan haruslah sama (identik).
 *
 * @package ContentType\Common
 * @author  Iyan Kushardiasah <iyank4@gmail.com>
 */
abstract class ContentTypeController
{
    private $CI;

    /**
     * @return String
     */
    abstract public function title();


    /**
     * @return String
     */
    abstract public function description();


    /**
     * @return String
     */
    abstract public function version();


    /**
     * @return String
     */
    abstract public function mainModel();


    /**
     * Dipanggil oleh kontroller admin untuk menampilkan data
     *
     * @param  Integer
     * @return void
     */
    abstract public function display($page_number = 1);


    public function __construct()
    {
        $this->CI =& get_instance();
    }


    /**
     * __get
     *
     * Access CI's loaded classes using the same
     * syntax as controllers.
     *
     * @param   string
     * @access private
     */
    function &__get($key)
    {
        return $this->CI->$key;
    }


    /**
     * Method yang dipanggil sesaat setelah tipe-koten dipanggil oleh
     * kontroller admin
     *
     * @param   String  Method yang dipanggil
     * @return  void
     */
    //public function prepare($action)
    //{
        // disini Anda bisa men-disable admin_header
        // menambahkan js_files/css_files untuk ditampilkan pada admin_header
    //}


    /**
     * Nama class tipe-konten
     *
     * @return  String
     */
    public function name()
    {
        return get_class($this);
    }


    /**
     * Periksa apakah tipe-konten memiliki halaman setting
     *
     * Kembalikan true jika Anda membuat setting untuk tipe-konten yang dibuat
     *
     * @return  Boolean
     */
    public function hasSetting()
    {
        return false;
    }


    /**
     * Tampilkan halaman setting untuk tipe konten
     *
     * @return  void
     */
    //public function setting()
    //{
        // Silakan buat halaman setting
        // jika hasSetting() Anda setting true
    //}


    /**
     * Simpan setting
     *
     * @return  void
     */
    //public function settingSave()
    //{
        // Silakan buat halaman setting
        // jika hasSetting() Anda setting true
    //}


    /**
     * Dipanggil oleh helper get_content()
     */
    public function get($sct_id = null, $id = 'last', $only_active = true)
    {
        $id = empty ($id) ? 'last' : $id;
        $model = $this->__model($this->mainModel());
        return $model->get($sct_id, $id, $only_active);
    }


    /**
     * Dipanggil oleh helper get_content_page()
     */
    public function getAll($sct_id = null, $limit = 1000, $offset = 0, $orders = null, $searchs = null, $only_active = true)
    {
        $model = $this->__model($this->mainModel());
        return $model->all($sct_id, $limit, $offset, $orders, $searchs, $only_active);
    }


    /**
     * Tampilkan view dari tipe-konten
     *
     * @return  void
     */
    public function __view($view_name, $vars = null)
    {
        if ($vars !== null AND is_array($vars)) {
            $vars = array_merge($this->vars, $vars);
        } else {
            $vars = $this->vars;
        }

        if ( ! empty ($this->content_type)) {
            $_content_type = $this->content_type;
        } else {
            $_content_type = $this->name();
        }

        $this->kwitang->ctView($_content_type, $view_name, $vars);
    }

    /**
     * Muat class model dari tipe konten
     *
     * @return  Object|false
     */
    public function __model($model_name)
    {
        if ( ! empty ($this->content_type)) {
            $_content_type = $this->content_type;
        } else {
            $_content_type = $this->name();
        }

        return $this->kwitang->ctModel($_content_type, $model_name);
    }

    /**
     * You can optionally implement this method if you wish to implement search
     * functionality on your ContentType
     *
     * This method must return
     * array(
     *   'total' => integer,
     *   'data'  => array(original fields and sct.name AS sct_name, sct.title AS sct_title)
     * )
     *
     * Code written here, just for your reference
     *
     * public function search($q, $page, $item_perpage, $only_active = true) {
     *     $page = $page ? $page : 1;
     *     $item_perpage = $item_perpage ? $item_perpage : kconfig ('system', 'search_perpage', 25)
     *
     *    return null;
     * }
     */
}
