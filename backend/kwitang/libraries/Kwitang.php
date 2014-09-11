<?php
if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/**
 * Library Kwitang, menyediakan fungsi dasar bagi Kwitang CMS
 *
 * Hal yang ditangani oleh Library ini adalah:
 * - FrontEnd
 * - Tipe-Konten
 * - Authentication & Authorization
 *
 * Struktur Folder:
 * <pre>
 * [install_folder]         => ROOT_PATH
 *   + frontend             => FRONT_PATH == ROOT_PATH + FRONT_FOLDER
 *     + [frontend_name]
 *       - assets
 *       - views
 *       - content_type
 *         - NAME_OF_CT
 *           - assets
 *           - controller
 *           - models
 *           - views
 *   + backend
 *     + content_type       => KWITANG_CT_PATH
 *       + NAME_OF_CT
 *         - assets
 *         - controller
 *         - models
 *         - views
 *     + kwitang
 *     + system
 * </pre>
 *
 * @package  Kwitang\Libraries
 * @author   Iyan Kushardiansah <iyank4@gamil.com>
 */
class Kwitang
{
    private $CI             = null;
    private $current_user   = null;
    private $raw_privileges = null;
    private $counter_file   = 'counter.raw';

    public $version  = '1.0.0-rc4';
    public $frontend = '';  // frontend yang sedang digunakan
    public $timezone = '';  // UP7 as default (Asia/Jakarta)
    public $lang     = 'id';


    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('KConfig');
        $this->CI->load->driver('Session');

        $this->counter_file = BACK_PATH.'counter.raw';

        if (empty ($this->frontend)) {
            $this->frontend = $this->CI->KConfig->get('system', 'frontend', 'maintenance');
            // jika folder frontend tidak ada, kembalikan ke default
            if (! @is_dir(FRONT_PATH.$this->frontend)) {
                $this->frontend = 'maintenance';
            }
        }
        if (empty ($this->timezone)) {
            $this->timezone = $this->CI->KConfig->get('system', 'timezone', 'UP7');
        }

        $cookie_name = $this->CI->config->item('cookie_prefix').'_klang';
        if (isset($_COOKIE[$cookie_name])) {
            $this->lang = $_COOKIE[$cookie_name];
        } else {
            $this->lang = $this->CI->KConfig->get('system', 'lang_default', 'id');
        }

        if (empty ($this->CI->encrypt)) {
            $this->CI->load->library('encrypt');
        }
    }


    /**
     * Susun array berupa daftar nama file dari folder yang diberikan.
     *
     * Format array yang di kembalikan:
     * <pre>
     * array('Nama_Folder1'=>'Nama_Folder1', 'Nama_Folder2'=>'Nama_Folder2')
     * </pre>
     *
     * @param   String
     * @return  Array
     */
    private function __listFolders($root_folder)
    {
        $ret = array();
        $root_folder = rtrim($root_folder, '/').'/';
        if (@is_dir($root_folder) and $h = @opendir($root_folder)) {
            while (false !== ($entry = readdir($h))) {
                if (@is_dir($root_folder.$entry) and  substr($entry, 0, 1) != '.') {
                    $ret[$entry] = $entry;
                }
            }
            closedir($h);
        }

        return $ret;
    }


    /**
     * Susun array berupa daftar nama file dari folder yang diberikan.
     *
     * default $recrusive = true, akan mengambil nama file pada folder di
     * bawahnya
     *
     * Format array yang di kembalikan:
     * <pre>
     * array('Nama_File' => 'Nama_File',
     *        'Nama_Folder' => array('Nama_File' => 'Nama_File'))
     * </pre>
     *
     * @param   String
     * @param   Boolean
     * @param   String
     * @param   Integer  Untuk menghitung kedalaman rekrusif
     * @return  Array
     */
    private function __listFiles($root_folder, $recrusive = true, $file_ext = 'php', $deep = 1)
    {
        // keep safe, prevent unlimited recrusive
        if ($deep > 30) {
            return;
        }

        $ret = array();
        $root_folder = rtrim($root_folder, '/').'/';

        if (@is_dir($root_folder) and $h = opendir($root_folder)) {
            while (false !== ($entry = readdir($h))) {
                $ext = substr($entry, -1 * strlen($file_ext));
                if (@is_dir($root_folder.$entry) and $recrusive and substr($entry, 0, 1) != '.') {
                    $child = $this->__listFiles($root_folder.$entry, $recrusive, $file_ext, $deep+1);
                    if (! empty($child)) {
                        foreach ($child as $k => $v) {
                            $ret[$entry.'/'.$k] = $entry.'/'.$v;
                        }
                    }
                } elseif ($ext == $file_ext) {
                    $file = substr($entry, 0, -1 * (strlen($file_ext)+1));
                    $ret[$file] = $file;
                }
            }
            closedir($h);
        }

        return $ret;
    }


    /**
     * Ambil data roles_detail berdasarkan ID sebuah role
     *
     * Data dari fungsi ini akan digunakan pada fungsi checkPrivilege()
     *
     * @param   Integer
     * @return  Array
     */
    private function __rawPrivilege($role_id = null)
    {
        if ($role_id === null and ! empty($this->current_user->role_id)) {
            $role_id = $this->current_user->role_id;
        }

        if (! empty ($this->raw_privileges[$role_id])) {
            return $this->raw_privileges[$role_id];
        }

        $raw = array();
        $this->CI->load->model('Roles_detail');
        $role_detail = $this->CI->Roles_detail->all($role_id);
        if (! empty($role_detail)) {
            foreach ($role_detail as $r) {
                $raw[$r->structure_id] = empty($r->permission) ? 'NOACCESS' : $r->permission;
            }
        }

        $this->raw_privileges[$role_id] = $raw;

        return $raw;
    }


    /**
     * Membuat login token
     *
     * @return  String
     */
    private function __buildToken($username, $login_time, $valid_day, $token = null, $serial = 1)
    {
        if ($token === null) {
            $token = sha1(uniqid($_SERVER['SERVER_NAME'], true));
        }

        $login_token = serialize(array($username, $token, $serial, $login_time, $valid_day));

        $sess_encrypt_cookie = $this->CI->config->item('sess_encrypt_cookie');
        if ($sess_encrypt_cookie) {
            $login_token = $this->CI->encrypt->encode($login_token);
            $login_token = base64_encode($login_token);
        }

        return array(
                    'token'       => $token,
                    'username'    => $username,
                    'serial'      => $serial,
                    'login_time'  => $login_time,
                    'valid_day'   => $valid_day,
                    'login_token' => $login_token
                );
    }


    /**
     * Membuat login token
     *
     *
     * @return  String
     */
    private function __readToken($login_token)
    {
        $sess_encrypt_cookie = $this->CI->config->item('sess_encrypt_cookie');
        if ($sess_encrypt_cookie) {
            $login_token = base64_decode($login_token);
            $login_token = $this->CI->encrypt->decode($login_token);
        }

        $tmp = @unserialize($login_token);

        if (is_array($tmp) and count($tmp) == 5) {
            list ($username, $token, $serial, $login_time, $valid_day) = $tmp;
        } else {
            return false;
        }

        return array(
                    'token'       => $token,
                    'username'    => $username,
                    'serial'      => $serial,
                    'login_time'  => $login_time,
                    'valid_day'   => $valid_day,
                    'login_token' => $login_token
                );
    }


    //=========================================================================
    // Method yang berhubungan dengan FrontEnd (fe)
    //=========================================================================


    /**
     * Ambil semua nama frontend
     *
     * @return  Array
     */
    public function feList()
    {
        $folders = $this->__listFolders(FRONT_PATH);
        asort($folders);

        return $folders;
    }


    /**
     * Ambil semua nama view dari sebuah frontend
     *
     * @param  String  Nama frontend
     * @return Array
     */
    public function feViewList($frontend = null)
    {
        $fe = is_null($frontend) ? $this->frontend : $frontend;
        $files = $this->__listFiles(FRONT_PATH.$fe.'/views/');
        asort($files);

        return $files;
    }


    /**
     * Menampilkan view dari front end
     *
     * @param   String  Nama File view tanpa exstensi .php
     * @param   mixed   Variable yang dikirim ke view
     * @return  void
     **/
    public function feView($view_filename, $vars = null, $frontend = null)
    {
        $this->CI->load->helper('kwitang_fe');
        $fe = is_null($frontend) ? $this->frontend : $frontend;

        $library_var = array();
        $library_var['current_user'] = $this->current_user;

        if ($vars !== null) {
            if (is_object($vars)) {
                $vars = get_object_vars($vars);
            }
            $view_vars = array_merge($library_var, $vars);
        } else {
            $view_vars = $vars;
        }

        $path = FRONT_PATH.$fe.'/views/';
        $this->CI->load->viewPath($path, $view_filename, $view_vars);
    }

    //=========================================================================
    // Method yang berhubungan dengan ContentType (ct)
    //=========================================================================

    /**
     * Periksa apakah Tipe-konten sudah di pasang
     *
     * @param   String  Nama tipe-konten
     * @return  Boolean true jika telah diinstall sempurna (semua tabel ada)
     */
    public function ctIsInstalled($ct_name)
    {
        $all_exist = null;

        $list = $this->ctModelList($ct_name);
        if ($list) {
            $all_exist = true;
            foreach ($list as $key => $value) {
                $m = $this->ctModel($ct_name, $key);
                if (is_object($m) and ! $m->tableExist()) {
                    $all_exist = false;
                    break;
                } elseif (! is_object($m)) {
                    return false;
                }
            }
        }

        return $all_exist;
    }

    /**
     * Ambil semua nama tipe-konten, dari frontend dan backend.
     *
     * Nilai yang dikembalikan berupa array('frontend' => ? , 'backend' => ?)
     * dimana isinya berupa array dari nama tipe-konten yang ada.
     *
     * Jika dipanggil tanpa parameter, akan mengambil tipe-koten dari frontend
     * yang sedang digunakan, Jika ingin mengambil dari frontend lain, isi
     * parameter $frontend dengan nama frontend yang dikehendaki.
     *
     * @param   Boolean
     * @param   String
     * @return  Array
     */
    public function ctList($only_installed = true, $frontend = null)
    {
        $ret = array();
        $fe = is_null($frontend) ? $this->frontend : $frontend;

        $frontend = $this->__listFolders(FRONT_PATH.$fe.'/content_type');
        $backend  = $this->__listFolders(KWITANG_CT_PATH);

        if ($only_installed === true) {
            if ($frontend) {
                $tmp = array();
                foreach ($frontend as $key => $value) {
                    if ($this->ctIsInstalled($key)) {
                        $tmp[$key] = $key;
                    }
                }
                if ($tmp) {
                    $ret['frontend'] = $tmp;
                }
            }

            unset($backend['common']);
            if ($backend) {
                $tmp = array();
                foreach ($backend as $key => $value) {
                    if ($this->ctIsInstalled($key)) {
                        $tmp[$key] = $key;
                    }
                }
                if ($tmp) {
                    $ret['backend'] = $tmp;
                }
            }
        } else {
            if ($frontend) {
                $ret['frontend'] = $frontend;
            }
            unset($backend['common']);
            if ($backend) {
                $ret['backend'] = $backend;
            }
        }

        return $ret;
    }


    /**
     * Ambil semua nama kontroller dari sebuah tipe-konten.
     *
     * Jika dipanggil tanpa parameter $frontend, akan mengambil dari frontend
     * yang sedang digunakan, Jika ingin mengambil dari frontend lain, isi
     * parameter $frontend dengan nama frontend yang dikehendaki.
     *
     * @param   String
     * @param   String
     * @return  Array
     */
    public function ctControllerList($content_type, $frontend = null)
    {
        $fe      = is_null($frontend) ? $this->frontend : $frontend;
        $fe_path = FRONT_PATH.$fe.'/content_type/'.$content_type.'/controller';

        if (@is_dir($fe_path)) {
            $view_dir = $fe_path;
        } elseif (@is_dir(KWITANG_CT_PATH.$content_type.'/controller')) {
            $view_dir = KWITANG_CT_PATH.$content_type.'/controller';
        }
        return $this->__listFiles($view_dir, false);
    }


    /**
     * Load/Muat kontroller dari sebuah tipe-konten,
     *
     * Nama kelas kontroller harus sama persis dengan nama filenya,
     * misal untuk kelas `CamelCase` nama filenya harus `CamelCase.php`
     *
     * Cari di frontend, jika tidak ditemukan maka akan mencari di backend.
     *
     * Jika Nama kontroller tidak di isi, akan mengambil kontroller yang
     * namanya sama dengan nama tipe-konten
     *
     * @param   String
     * @param   String
     * @return  Object|false
     */
    public function ctController($content_type, $controller = null)
    {
        if (! class_exists('ContentTypeController')) {
            require_once KWITANG_CT_PATH.'/common/ContentTypeController.php';
        }
        $ct_name      = $controller === null ? $content_type : $controller;
        $path_to_file = $content_type.'/controller/'.$ct_name.'.php';

        $filepath = FRONT_PATH.$this->frontend.'/content_type/'.$path_to_file;
        if (! @file_exists($filepath)) {
            $filepath = KWITANG_CT_PATH.$path_to_file;
            if (! @file_exists($filepath)) {
                $filepath = null;
            }
        }

        if ($filepath !== null) {
            require_once ($filepath);
            return new $ct_name();
        }

        return false;
    }


    /**
     * Ambil semua nama model dari sebuah tipe-konten.
     *
     * Jika dipanggil tanpa parameter $frontend, akan mengambil dari frontend
     * yang sedang digunakan, Jika ingin mengambil dari frontend lain, isi
     * parameter $frontend dengan nama frontend yang dikehendaki.
     *
     * @param  String
     * @param  String
     * @return Array
     */
    public function ctModelList($content_type, $frontend = null)
    {
        $fe      = is_null($frontend) ? $this->frontend : $frontend;
        $fe_path = FRONT_PATH.$fe.'/content_type/'.$content_type.'/models';

        $view_dir = false;
        if (@is_dir($fe_path)) {
            $view_dir = $fe_path;
        } elseif (@is_dir(KWITANG_CT_PATH.$content_type.'/models')) {
            $view_dir = KWITANG_CT_PATH.$content_type.'/models';
        }

        return $view_dir ? $this->__listFiles($view_dir, false) : false;
    }


    /**
     * Load/Muat model dari sebuah tipe-konten,
     *
     * Nama kelas model harus sama persis dengan nama filenya,
     * misal untuk kelas `CamelCase` nama filenya harus `CamelCase.php`
     *
     * Cari di frontend, jika tidak ditemukan maka akan mencari di backend.
     *
     * Jika Nama model tidak di isi, akan mengambil model yang namanya sama
     * dengan nama tipe-konten
     *
     * @param   String
     * @param   String
     * @return  Object|false
     */
    public function ctModel($content_type, $model = null)
    {
        if (! class_exists('ContentTypeModel')) {
            require_once KWITANG_CT_PATH.'/common/ContentTypeModel.php';
        }
        if ($model === null) {
            $controller = $this->ctController($content_type);
            $m_name     = $controller->mainModel();
        } else {
            $m_name = $model;
        }
        $path_to_file  = $content_type.'/models/'.$m_name.'.php';

        $filepath = FRONT_PATH.$this->frontend.'/content_type/'.$path_to_file;
        if (! @file_exists($filepath)) {
            $filepath = KWITANG_CT_PATH.$path_to_file;
            if (! @file_exists($filepath)) {
                $filepath = null;
            }
        }

        if ($filepath !== null) {
            return $this->CI->load->modelPath(dirname($filepath), $m_name);
        }

        return false;
    }


    /**
     * Ambil semua nama view dari sebuah tipe-konten.
     *
     * Jika dipanggil tanpa parameter $frontend, akan mengambil dari frontend
     * yang sedang digunakan, Jika ingin mengambil dari frontend lain, isi
     * parameter $frontend dengan nama frontend yang dikehendaki.
     *
     * @param  String
     * @param  String
     * @return Array
     */
    public function ctViewList($content_type, $frontend = null)
    {
        $fe      = is_null($frontend) ? $this->frontend : $frontend;
        $fe_path = FRONT_PATH.$fe.'/content_type/'.$content_type.'/views';

        if (@is_dir($fe_path)) {
            $view_dir = $fe_path;
        } elseif (@is_dir($dir = KWITANG_CT_PATH.$content_type.'/views')) {
            $view_dir = $dir;
        }

        return $this->__listFiles($view_dir, true);
    }


    /**
     * Menampilkan view dari tipe konten.
     *
     * @param   String
     * @param   String
     * @param   Object|Array  Parameter yang di teruskan ke view
     * @param   Boolean
     * @return  mixed
     */
    public function ctView($content_type, $view_name, $var = null, $return = false)
    {
        $folder = $content_type.'/views';
        $path   = FRONT_PATH.$this->frontend.'/content_type/'.$folder;

        if (! @is_dir($path)) {
            $path = KWITANG_CT_PATH.$folder;
            if (! @is_dir($path)) {
                $path = null;
            }
        }

        if ($path === null) {
            $this->systemLog('error', 'Tidak menemukan folder views: '.' pada tipe-konten `'.$content_type.'`');
            return;
        } elseif (! @file_exists($path.'/'.$view_name.'.php')) {
            $this->systemLog('error', 'Tidak menemukan view `'.$view_name.'` pada tipe-konten `'.$content_type.'`');
            return;
        }

        return $this->CI->load->viewPath($path, $view_name, $var, $return);
    }


    // ========================================================================
    //   Method untuk Authentication dan Authorization
    // ========================================================================


    /**
     * Cek apakah user sudah login
     *
     * @return Boolean
     */
    public function authenticate()
    {
        // has login token ?
        $cookie_name = $this->CI->config->item('sess_cookie_name').'p';
        $token_raw   = $this->CI->input->cookie($cookie_name);

        if (empty ($token_raw)) {
            return false;
        }

        $token_array = $this->__readToken($token_raw);
        if ($token_array) {
            $token      = $token_array['token'];
            $username   = $token_array['username'];
            $serial     = $token_array['serial'];
            $login_time = $token_array['login_time'];
            $valid_day  = $token_array['valid_day'];
        } else {
            return false;
        }

        $this->CI->load->model('Users');
        $user = $this->CI->Users->get($username);
        if (empty($user->id)) {
            return false;
        }

        $this->CI->db->where('user_id', $user->id);
        $this->CI->db->where('token', $token);
        $query = $this->CI->db->get('user_session');

        // found row ...?
        if ($query->num_rows() !== 1) {
            return false;
        }

        $row = $query->row();

        // Terdapat kemungkinan cookie tidak sampai ke client
        // karena itu nomor seri sessi boleh tertinggal 7 nomor.
        // perketat keamanan dengan memberi nilai lebih kecil
        $serial_lag = $this->CI->KConfig->get('system', 'session_serial_lag', 7);
        if ($serial >= ($row->serial - $serial_lag) and $serial <= $row->serial) {
            $serial_ok = true;
        } else {
            $serial_ok = false;
        }

        if ($serial_ok and $token == $row->token) {
            $new_serial  = $row->serial + 1;
            $login_token = $this->__buildToken($username, $login_time, $valid_day, $token, $new_serial);
            $new_login_token = $login_token['login_token'];

            $this->CI->load->model('Users');
            $user = $this->CI->Users->get($username);

            if ($user) {
                $this->current_user = $user;
                // update table user_session
                $data = array(
                    'serial'        => $new_serial,
                    'ip_address'    => $this->CI->session->userdata('ip_address'),
                    'last_activity' => $this->timeGmt(),
                );
                $this->CI->db->where('token', $token);
                $this->CI->db->update('user_session', $data);
                // then set corresponding cookie
                // Jika session tidak auto extend, user harus login lagi
                // setelah login_remember_day terlewati
                if ($valid_day > 0) {
                    // persistent login
                    $expire = $valid_day * (24*3600);
                    $extend = $this->CI->KConfig->get('system', 'login_auto_extend', 1);
                    if ($extend != 1) {
                        $now = $this->timeGmt();
                        $expire = ($login_time + $expire) - $now;
                    }
                } else {
                    $expire = 0;
                }

                $cookie = array(
                    'name'   => $cookie_name,
                    'value'  => $new_login_token,
                    'expire' => $expire
                );
                $this->CI->input->set_cookie($cookie);

                return true;
            }
        } elseif ($username == $row->username and $token == $row->token) {
            // serialnya salah
            $this->logout(true, $username);
        }

        // anything else we refuse to authenticate
        return false;
    }


    /**
     * Periksa keabsahan user, Lakukan proses login jika tidak ditemukan
     * masalah
     *
     * @param String   Username atau email
     * @param String
     * @param Boolean  Apakah login ini akan di ingat
     * @return Boolean return true jika sukses login
     */
    public function validateUser($username, $password, $persistent = true)
    {
        $this->CI->load->model('Users');
        $isvalid = $this->CI->Users->validateUser($username, $password);

        if (! $isvalid) {
            return $isvalid; // kembalikan null atau false
        }

        // User OK, sekarang lakukan proses login
        $user = $this->CI->Users->get($username);

        if ($persistent) {
            $day    = $this->CI->KConfig->get('system', 'login_remember_day', 14);
            $expire = $day > 0 ? $day * (24*3600) : 0;
        } else {
            $day    = 0;
            $expire = 0;
        }

        $gmt_time    = $this->timeGmt();
        $login_time  = date('Y-m-d H:i:s', $gmt_time);
        $token_array = $this->__buildToken($username, $gmt_time, $day);

        // set data on table user_session
        $data = array(
            'user_id'       => $user->id,
            'token'         => $token_array['token'],
            'login_time'    => $login_time,
            'serial'        => $token_array['serial'],
            'ip_address'    => $this->CI->session->userdata('ip_address'),
            'user_agent'    => $this->CI->session->userdata('user_agent'),
            'last_activity' => $this->CI->session->userdata('last_activity'),
        );
        $this->CI->db->insert('user_session', $data);
        // then set corresponding cookie
        $expire = $day > 0 ? $day * (24*3600) : 0;
        $cookie = array(
            'name'   => $this->CI->config->item('sess_cookie_name').'p',
            'value'  => $token_array['login_token'],
            'expire' => $expire,
            'domain' => $this->CI->config->item('cookie_domain'),
            'path'   => $this->CI->config->item('cookie_path'),
            'prefix' => $this->CI->config->item('cookie_prefix'),
            'secure' => $this->CI->config->item('cookie_secure')
        );
        $this->CI->input->set_cookie($cookie);

        $this->current_user = $user;
        // update last login
        $this->CI->Users->update(array('username'=>$username,
                'last_login'=>$login_time));

        $message = $this->CI->session->userdata('ip_address')
                  .'##'.$this->CI->session->userdata('user_agent')
                  .'##'.$token_array['token'].'##'.$day;
        $this->userLog('system', 'login', $message);

        return $user;
    }


    /**
     * Keluar dari Sistem, atau menghapus session aktif
     *
     * @param   String
     * @param   String
     * @return  void
     */
    public function logout($all_session = false, $username = null, $token = null)
    {
        if ($username === null or $token === null) {
            $cookie_name = $this->CI->config->item('sess_cookie_name').'p';
            $user_token  = $this->CI->input->cookie($cookie_name, true);
            if (empty ($user_token)) {
                return false;
            }

            // bongkar, gampangnya pake explode()
            $pos1     = strpos($user_token, ';');
            $pos2     = strpos($user_token, ';', $pos1 + 1);
            $pos3     = strpos($user_token, ';', $pos2 + 1);
            $username = substr($user_token, 0, $pos1);
            $serial   = substr($user_token, $pos1 + 1, $pos2 - $pos1 - 1);
            $token    = substr($user_token, $pos2 + 1, $pos3 - $pos2 - 1);
            $persistent_day = substr($user_token, $pos3 + 1);

            $cookie = array(
                'name'   => $cookie_name,
                'value'  => 'logout',
                'expire' => 0,
                'domain' => $this->CI->config->item('cookie_domain'),
                'path'   => $this->CI->config->item('cookie_path'),
                'prefix' => $this->CI->config->item('cookie_prefix'),
                'secure' => $this->CI->config->item('cookie_secure')
            );
            $this->CI->input->set_cookie($cookie);

            // destroy current session
            $this->CI->session->sess_destroy();
        }

        $this->CI->db->where('username', $username);
        if (! $all_session) {
            $this->CI->db->where('token', $token);
        }
        //$success = $this->CI->db->delete ('user_session');
        $success = true;

        if ($success) {
            $this->userLog('kwitang', 'logout', $username.' logout from '.$this->CI->input->ip_address());
        } else {
            $this->userLog('kwitang', 'logout', $username.' failed logout from '.$this->CI->input->ip_address());
        }
    }


    /**
     * Ambil data user yang sedang login
     *
     * Jika belum login, akan mengembalikan nilai null
     *
     * @return Object|null
     */
    public function currentUser()
    {
        $user = $this->current_user;
        if (is_object($user)) {
            unset($user->password);
            unset($user->reset_token);
            unset($user->reset_time);
            unset($user->active);
        }

        return $user;
    }


    /**
     * Check wheter user has privilege to a structure.
     * Will return true if user has privilege equal or higer than asked,
     * except if $equality set to true.
     *
     * @param   Object|int  Target Stucture, object or structure id
     * @param   String      Asked privilege: view, posting, approve, manage
     * @param   String      Username of user checked, default to current user
     * @param   Boolean     Set true to check specific privilege, default false
     * @return  Boolean|null  true=Has privileges, false=No, null=User not exist
     */
    public function checkPrivilege($structure, $priv_asked, $username = '', $equality = false)
    {
        $user = null;

        if (! is_string($priv_asked) and  ! is_int($priv_asked)) {
            return false;
        }

        if ($this->current_user !== null) {
            $user = $this->current_user;
        } elseif ($this->current_user === null and $username !== '') {
            $this->CI->load->model('Users');
            $user = $this->CI->Users->getByUsername($username);

            if (! $user) {
                return null;  // no user
            }
        } else {
            return null;  // maybe not login
        }

        if (is_object($structure)) {
            if (! empty($structure->id)) {
                $id = intval($structure->id);
            }
        } else {
            $id = intval($structure);
        }

        // User with Admin level, always has the privilege
        if ($user->level == 'ADMIN') {
            return true;
        }

        $raw_privilege = $this->__rawPrivilege($user->role_id);
        if (! isset($raw_privilege[$id])) {
            return false;
        }

        $role_id       = $user->role_id;
        $raw_privilege = $this->__rawPrivilege($role_id);
        $priv_in_db    = strtolower($raw_privilege[$id]);
        $priv_asked    = strtolower($priv_asked);

        $p = 0;
        switch ($priv_in_db) {
            case 'noaccess':
                $p = 0;
                break;
            case 'view':
                $p = 1;
                break;
            case 'posting':
                $p = 2;
                break;
            case 'approve':
                $p = 3;
                break;
            case 'manage':
                $p = 4;
                break;
            default:
                $p = 0;
        }

        $a = 5;
        switch ($priv_asked) {
            case 'noaccess':
                $a = 0;
                break;
            case '0':
                $a = 0;
                break;
            case 'view':
                $a = 1;
                break;
            case '1':
                $a = 1;
                break;
            case 'posting':
                $a = 2;
                break;
            case '2':
                $a = 2;
                break;
            case 'approve':
                $a = 3;
                break;
            case '3':
                $a = 3;
                break;
            case 'manage':
                $a = 4;
                break;
            case '4':
                $a = 4;
                break;
            default:
                $a = 5;
        }

        if ($a == 5) {
            return false;
        }

        if ($equality) {
            return ($p == $a) ? true : false;
        } else {
            return ($p >= $a) ? true : false;
        }
    }


    // ========================================================================
    //   Lainnya
    // ========================================================================

    /**
     * Menyimpan catatan (log) aktifitas
     *
     * $subject : 'auth',  $sct_title
     * $event   : 'login,  'add', 'update', 'delete', 'login'
     *
     * Untuk catatan yang tidak berkaitan dengan pengguna, gunakan systemLog()
     *
     * @param   String
     * @param   String
     * @param   String
     * @return  Boolean true jika sukses menyimpan log
     */
    public function userLog($subject, $event, $message)
    {
        if (empty ($this->current_user->username)) {
            $user_id = 0;
        } else {
            $user_id = $this->current_user->id;
        }

        $data = array(
                    'user_id'  => substr($user_id, 0, 30),
                    'timestamp' => date('Y-m-d H:i:s', $this->timeGmt()),
                    'subject'   => strtolower(substr($subject, 0, 60)),
                    'event'     => strtolower(substr($event, 0, 60)),
                    'message'   => substr($message, 0, 255)
              );

        return $this->CI->db->insert('user_log', $data);
    }


    /**
     * menyimpan catatan (log) yang berkaitan dengan masalah sistem
     *
     * @param   String
     * @param   String
     * @param   String
     * @return  Boolean  true jika sukses menyimpan log
     */
    public function systemLog($event, $message, $subject = 'system')
    {
        $now      = time();
        $gmt_time = mktime(
            gmdate("H", $now),
            gmdate("i", $now),
            gmdate("s", $now),
            gmdate("m", $now),
            gmdate("d", $now),
            gmdate("Y", $now)
        );
        if (strlen($gmt_time) < 10) {
            $gmt_time = time();
        }

        $data = array(
                    'timestamp' => date('Y-m-d H:i:s', $gmt_time),
                    'username'  => 'system', // keep it simple cyiin
                    'subject'   => strtolower(substr($subject, 0, 60)),
                    'event'     => strtolower(substr($event, 0, 60)),
                    'message'   => substr($message, 0, 255)
              );

        return $this->CI->db->insert('user_log', $data);
    }


    /**
     * Membuat URL ke file asset.
     *
     * Secara otomatis ditambahkan ?v=`filemtime($file)` dibelakang url, untuk
     * menghindari masalah cache di browser.
     *
     * Anda dapat menonaktifkan penambahan filemtime ini dengan menyetel
     * konfigurasi 'asset_add_mtime' menjadi false. Namun Jika kemudian fungsi
     * ini dipanggil dengan parameter $add_mtime, maka konfigurasi tadi akan
     * diabaikan.
     *
     * Setel @param === false agar tidak mengambil dari FRONT_FOLDER
     * Setel $param = Nama tipe-konten, untuk mengambil berkas dari folder
     * assets pada tipe-konten tersebut.
     *
     * @param   String          path/ke/file.ext di bawah assets/
     * @param   Boolean|String  default true, String nama tipe-konten
     * @param   Boolean         default null
     * @return  String
     */
    public function assetUrl($file_uri = '', $param = true, $add_mtime = null)
    {
        if (empty ($file_uri)) {
            return base_url('assets');
        }

        $fp = null;

        if ($param === true) {
            if (! @file_exists($fp = FRONT_FOLDER.'/'.$this->frontend.'/assets/'.$file_uri)) {
                if (! @file_exists($fp = 'assets/'.$file_uri)) {
                    $fp = null;
                    $this->systemLog('info', 'Tidak menemukan berkas aset '.$file_uri, 'assets');
                }
            }
        } elseif ($param === false) {
            if (! @file_exists($fp = 'assets/'.$file_uri)) {
                $fp = null;
                $this->systemLog('info', 'Tidak menemukan berkas aset '.$file_uri, 'assets');
            }
        } elseif ($param !== null) {
            if (! @file_exists($fp = FRONT_FOLDER.'/'.$this->frontend.'/'.$param.'/assets/'.$file_uri)) {
                if (! @file_exists($fp = 'backend/content_type/'.$param.'/assets/'.$file_uri)) {
                    $fp = null;
                    $this->systemLog('info', 'Tidak menemukan berkas aset '.$file_uri.' dari tipe-konten '.$param, 'assets');
                }
            }
        }

        if ($add_mtime === null) {
            $add_mtime = $this->CI->KConfig->get('system', 'asset_add_mtime', true);
        }
        if ($add_mtime and ! is_null($fp)) {
            $fp .= '?v='.filemtime($fp);
        }

        return is_null($fp) ? '' : base_url($fp);
    }


    /**
     * Mengambil waktu GMT
     *
     * @return int
     */
    public function timeGmt()
    {
        $gmt = now();

        if (strlen($gmt) < 10) {
            $time = time();
            $gmt  = mktime(
                gmdate("H", $time),
                gmdate("i", $time),
                gmdate("s", $time),
                gmdate("m", $time),
                gmdate("d", $time),
                gmdate("Y", $time)
            );
        }

        return $gmt;
    }


    /**
     * Web Counter
     *
     * @return void
     **/
    public function visitorCounter($do_count = false)
    {
        $d_date  = $this->timeGmt();
        $d_year  = date('Y', $d_date);
        $d_month = date('n', $d_date);
        $d_day   = date('j', $d_date);

        $new_visitor = false;
        $filename    = $this->counter_file;
        $is_robot    = $this->CI->agent->is_robot();
        $is_counted  = $this->CI->session->userdata('track_id');

        if ($do_count and ! $is_robot) {
            $fd          = @fopen($filename, 'rw+') ;
            $filecontent = @fread($fd, filesize($filename)) ;
            $counter     = kunserialize($filecontent);
            @fseek($fd, 0);
        } else {
            $fd          = @fopen($filename, 'r') ;
            $filecontent = @fread($fd, filesize($filename)) ;
            $counter     = kunserialize($filecontent);
            @fclose($fd);
        }

        $user_track = empty($counter['user_track']) ? array() : $counter['user_track'];

        if (! $is_counted) {
            $new_visitor = true;
            $prefix      = substr(str_replace(array(' ', 'a', 'i', 'u', 'e', 'o'), '', strtolower(kconfig('system', 'site_name', 'default'))), 0, 6);
            $track_id    = uniqid($prefix, true);

            $this->CI->session->set_userdata('track_id', $track_id);
        }


        $user_online_count = 0;
        $user_track_new    = array();
        $max_last_activity = $d_date - 900; // 15 * 60 = 900;

        $track_id = $this->CI->session->userdata('track_id');
        $user_track[$track_id] = $d_date;

        // remove data > 15 minutes
        foreach ($user_track as $key => $value) {
            if ($value > $max_last_activity) {
                $user_track_new[$key] = $value;
                $user_online_count++;
            }
        }

        // hits
        $counter['hits_y'][$d_year] = empty($counter['hits_y'][$d_year]) ? 0 : $counter['hits_y'][$d_year];
        $counter['hits_m'][$d_year][$d_month] = empty($counter['hits_m'][$d_year][$d_month]) ? 0 : $counter['hits_m'][$d_year][$d_month];
        $counter['hits_d'][$d_year][$d_month][$d_day] = empty($counter['hits_d'][$d_year][$d_month][$d_day]) ? 0 : $counter['hits_d'][$d_year][$d_month][$d_day];
        // visitor
        $counter['visit_y'][$d_year] = empty($counter['visit_y'][$d_year]) ? 0 : $counter['visit_y'][$d_year];
        $counter['visit_m'][$d_year][$d_month] = empty($counter['visit_m'][$d_year][$d_month]) ? 0 : $counter['visit_m'][$d_year][$d_month];
        $counter['visit_d'][$d_year][$d_month][$d_day] = empty($counter['visit_d'][$d_year][$d_month][$d_day]) ? 0 : $counter['visit_d'][$d_year][$d_month][$d_day];
        // online user
        $counter['user_track']  = $user_track_new;
        $counter['user_online'] = $user_online_count;

        if ($do_count and ! $is_robot) {
            $counter['hits_y'][$d_year]++;
            $counter['hits_m'][$d_year][$d_month]++;
            $counter['hits_d'][$d_year][$d_month][$d_day]++;

            if ($new_visitor) {
                $counter['visit_y'][$d_year]++;
                $counter['visit_m'][$d_year][$d_month]++;
                $counter['visit_d'][$d_year][$d_month][$d_day]++;
            }

            $filecontent = serialize($counter);
            @fwrite($fd, $filecontent);
            @fclose($fd);
        }

        return $counter;
    }
}


/* End of file libraries/Kwitang.php */
