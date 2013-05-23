<?php
if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/**
 * Admin Interface Controller
 *
 * @package  Kwitang\Controllers
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class Admin extends CI_Controller
{
    public $vars        = array();  // variable yang akan di kirim ke view
    public $base_upload = 'upload/';
    public $user_levels = array('ADMIN'  => 'Admin',
                                'AUTHOR' => 'Author',
                                'MEMBER' => 'Member'
                            );

    public function __construct()
    {
        parent::__construct();

        $is_logged_in = $this->kwitang->authenticate();
        $current_user = $this->kwitang->currentUser();

        if (is_object($current_user)) {
            if ($current_user->level != 'ADMIN' and $current_user->level != 'AUTHOR') {
                 // to homepage
                 redirect(site_url());
            }

            $this->load->helper('text');
            $this->load->helper('kwitang_fe');

            // Common required data
            $this->load->model('Structure');

            $this->vars['structure']      = $this->Structure->all();
            $this->vars['structure_tree'] = $this->Structure->allTree(0, true);
            $this->vars['current_user']   = $current_user;
            $this->vars['breadcrumb']     = array(site_url('admin') => 'Admin');
        } else {
            redirect(site_url('user/login'));
        }

        $language = user_config($current_user->username, 'language', 'indonesia');
        $this->load->helper('language');
        $this->lang->load('admin', $language);

        // Meta data
        $this->vars['title']       = 'Kwitang | Web Content Management System';
        $this->vars['description'] = 'Administration page of kwitang';

        if (kconfig('system', 'profiler', 0) == 1) {
            $this->output->enable_profiler(true);
        }
    }


    // MAIN PAGES
    // ========================================================================


    /**
     * Index page
     *
     * @return void
     */
    public function index()
    {
        $this->dashboard();
    }


    /**
     * Dashboard Page
     *
     * @return void
     */
    public function dashboard()
    {
        $this->session->set_userdata('return_uri', $this->uri->uri_string());

        $this->vars['head_title']   = 'Dashboard';
        $this->vars['breadcrumb'][] = lang('k_dashboard');

        $this->__view('dashboard', $this->vars);
    }


    // UTILITY
    // ========================================================================


    /**
     * Private function for uploading an image.
     *
     * Return NULL if no file uploaded.
     *
     * @param  string $field_name  Form field name
     * @param  string $folder_name Folder name to store uploaded file
     * @return string              URI to uploaded file
     */
    private function __upload_image($field_name, $folder_name = '')
    {
        $retval = null;

        if (! empty($_FILES[$field_name]['name'])) {
            $this->load->helper('path');

            $real_upload_path = set_realpath($this->base_upload.$folder_name.'/');
            $config['upload_path'] = $this->base_upload.$folder_name.'/';
            if (! @is_dir($real_upload_path)) {
                if (! mkdir($real_upload_path, DIR_WRITE_MODE, true)) {
                    show_error('<p>Failed to create '.$config['upload_path'].' folder.</p>');
                }
            }
            $config['allowed_types']    = kconfig('system', 'allowed_types', 'gif|jpg|png');
            $config['max_size']         = kconfig('system', 'image_max_size', '1024');
            $config['max_width']        = kconfig('system', 'image_max_width', '1000');
            $config['max_height']       = kconfig('system', 'image_max_height', '800');
            $this->load->library('upload', $config);

            if (! $this->upload->do_upload($field_name)) {
                show_error($this->upload->display_errors());
            } else {
                $data = $this->upload->data();
                $retval = rtrim($config['upload_path'], '/').'/'. $data['file_name'];
            }
        }

        return $retval;
    }

    /**
     * Traverse Up structure tree
     *
     * @param  int   $structure_id  Givend structure id, (leaf)
     * @param  array $all_structure All structure array
     * @return array                Array from the top structure down to the given $structure_id
     */
    private function __parent_breadcrumb($structure_id, $all_structure = null)
    {
        $retval = array();
        $all_structure = $all_structure == null ? $this->vars['structure'] : $all_structure;

        foreach ($all_structure as $sc) {
            if ($sc->id == $structure_id) {
                $retval[site_url('admin/content/'.$sc->id)] = var_lang($sc->title);
                if (! empty($sc->parent_id)) {
                    $tmp = $this->__parent_breadcrumb($sc->parent_id, $all_structure);
                    if (! empty($tmp)) {
                        $retval = array_merge($retval, $tmp);
                    }
                }
                break;
            }
        }

        return $retval;
    }

    /**
     * Prevent non ADMIN level to enter. Redirect if $current_user is not admin.
     *
     * @return void
     */
    private function __prevent_non_admin()
    {
        if (kuser()->level !== 'ADMIN') {
            if (kuser()->level == 'AUTHOR') {
                redirect(site_url('admin'));
            } else {
                redirect(site_url());
            }
        }
    }

    /**
     * Just allow ADMIN and Specific username. Redirect if not allowed.
     *
     * @param  string $username Username of allowed User
     * @return void
     */
    private function __allow_admin_and($username)
    {
        if (kuser()->username != $username and kuser()->level != 'ADMIN') {
            redirect('admin');
            exit();
        }
    }


    /**
     * Save Structure-Content-Type (SCT) data
     *
     * @param  array $data SCT Data
     * @return bool        TRUE if success
     */
    private function __save_sct($data)
    {
        $this->vars['view_files'] = $this->kwitang->feViewList();
        $this->load->model('Structure');

        if (! isset($data['view_index'])) {
            $data['view_index'] = strtolower($data['content_type']);
        }
        if (! isset($data['view_content'])) {
            $data['view_content']   = strtolower($data['content_type'].'-detail');
        }

        if (empty($data['content_type'])
            or empty($data['name'])
            or empty($data['title'])
            or empty($data['structure_id'])
            or empty($data['view_index'])
            or empty($data['view_content'])) {
            show_error('Maaf, silakan periksa lagi data yang dimasukkan.');
        }

        return $this->Structure->sctSave($data);
    }

    /**
     * Tampilkan view admin ke user browser.
     *
     * FrontEnd dapat meng-override view ini dengan membuat file penggantinya
     * pada folder *frontend[frontend_name]/admin/* path dan nama filenya harus
     * sama dengan yang ada di folder *application/views/admin*
     *
     * Cek apakah ada file $view_name di folder frontend, jika ada gunakan
     * file itu, jika tidak ada gunakan file dari folder application/views.
     *
     * @param  string $view_name View file name
     * @param  array  $vars      Data send to view.
     * @return void
     */
    private function __view($view_name, $vars = null)
    {
        $path = FRONT_PATH.$this->kwitang->frontend.'/admin/';
        $file = $path.$view_name.'.php';

        if (@file_exists($file)) {
            $this->load->viewPath($path, $view_name, $vars);
        } else {
            $this->load->view('admin/'.$view_name, $vars);
        }
    }


    // CONTENT MANAGER
    // ========================================================================


    /**
     * @param   $structure_id
     * @param   $sct_id
     * @param   $action = display, add, save, edit, update, delete
     * $param => bisa $page_number, atau $content_id serta $confirmation
     */
    public function content($structure_id = null, $sct_id = null, $action = 'display', $param = null)
    {
        if ($structure_id === null) {
            show_error('Silakan masukkan struktur.');
        }

        if (substr($action, 0, 1) == '_') {
            show_error('Method tidak dapat diakses.');
        }

        // Ambil struktur yang diminta
        $current_structure = $this->Structure->get($structure_id);
        if (empty($current_structure)) {
            show_error('Struktur tidak ditemukan.');
        }

        if (! priv('view', $current_structure)) {
            redirect('admin/');
        }

        // ambil semua tipe-konten yang ada pada $current_struktur
        $data_sct    = $this->Structure->sctAll($structure_id);
        $current_sct = null;

        // tentukan tipe-konten yang akan di tampilkan
        if (! empty($data_sct) and is_array($data_sct)) {
            foreach ($data_sct as $d) {
                if ($d->id === $sct_id) {
                    $current_sct = $d;
                    break;
                }
            }
        }

        // pilih tipe konten yang pertama jika belum ditentukan
        if ($current_sct === null and ! empty($data_sct) and is_array($data_sct)) {
            $current_sct = $data_sct[0];
            $sct_id = $data_sct[0]->id;
        }

        if (! empty($current_sct->content_type) and strtolower($action) == 'setting') {
            redirect('admin/setting_ct/'.$current_sct->content_type);
        }

        // populate variabel untuk view dan tambahkan ke $this->vars
        //---------------------------------------------------------------------
        // breadcrumb
        if (! empty($current_structure->parent_id)) {
            $tmp = $this->__parent_breadcrumb($current_structure->parent_id);
            $tmp = array_reverse($tmp);
            $this->vars['breadcrumb'] = array_merge($this->vars['breadcrumb'], $tmp);
        }
        $this->vars['breadcrumb'][site_url('admin/content/'.$current_structure->id)] = var_lang($current_structure->title);


        if (! empty($current_structure)) {
            $this->vars['current_structure'] = $current_structure;
            $this->vars['title']        = var_lang($current_structure->title);
            $this->vars['description']  = var_lang($current_structure->description);
        }

        if (! empty($current_sct)) {
            $this->vars['current_sct'] = $current_sct;
            $this->vars['title']      .= ' - '.var_lang($current_sct->title);
        }

        if (! empty($data_sct)) {
            $this->vars['data_sct'] = $data_sct;
        }

        // calculate parameter untuk method pada kontroller tipe-konten
        $segments = $this->uri->rsegment_array();
        $params = array();
        for ($i=6; $i<=count($segments); $i++) {
            $params[] = $segments[$i];
        }

        if (! empty($current_sct->content_type)) {
            $this->__view_ct($current_sct->content_type, $action, $params);
        } else {
            $this->__view('blank', $this->vars);
        }
    }

    //-------------------------------------------------------------------------

    /**
     * Kelola tipe konten
     *
     * Halaman untuk memasang/melepas tipe konten, dan menampilkan pengaturan
     * untuk tipe-konten tersebut(jika ada)
     *
     * @return  void
     */
    public function content_type()
    {
        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/content_type')] = lang('k_content_type');

        $tmp = array();
        $list = $this->kwitang->ctList(false);
        foreach ($list as $ct_place => $list_inside) {
            foreach ($list_inside as $key => $value) {
                $ct = $this->kwitang->ctController($key);
                if (! is_object($ct)) {
                    show_error('Error loading Content-Type <strong>'.$key.'</strong>.');
                }
                $item = array(
                    'name'      => $key,
                    'title'     => $ct->title(),
                    'version'   => $ct->version(),
                    'setting'   => $ct->hasSetting(),
                    'installed' => $this->kwitang->ctIsInstalled($key)
                );
                $tmp[$ct_place][] = $item;
            }
        }

        $this->vars['content_types'] = $tmp;

        $this->__view('content-type', $this->vars);
    }

    /**
     * Pasang tipe konten
     *
     * @param   String  Nama tipe-konten
     * @param   Boolean
     * @return  void
     */
    public function install_ct($ct_name, $force = false)
    {
        $this->__prevent_non_admin();

        $ct = $this->kwitang->ctController($ct_name);

        $result = false;
        $result = $this->__install_ct($ct_name, (bool) $force);
        if ($result) {
            user_log($ct_name, 'install', 'Pasang tipe-konten '.$ct_name);
        } else {
            $error_message = '<p>Kesalahan, Gagal memasang tipe-konten '.$ct_name.'</p>';
            $error_message.= '<p>'.$this->db->_error_message().'</p>';
            show_error($error_message);
        }

        redirect(site_url('admin/content_type').'?result='.($result?'1':'0').'&ct='.$ct_name);
    }

    /**
     * Lepas tipe-konten
     *
     * @param   String  Nama tipe-konten
     * @return  void
     */
    public function uninstall_ct($ct_name)
    {
        $this->__prevent_non_admin();

        $ct = $this->kwitang->ctController($ct_name);

        $result = $this->__uninstall_ct($ct_name);
        if ($result) {
            user_log($ct_name, 'uninstall', 'Lepas tipe-konten '.$ct_name);
        } else {
            show_error('Kesalahan, Gagal melepas tipe-konten '.$ct_name);
        }

        redirect(site_url('admin/content_type').'?result='.($result?'1':'0').'&ct='.$ct_name);
    }

    /**
     * Setting tipe-konten
     */
    public function setting_ct($ct_name, $action = 'setting')
    {
        $segments = $this->uri->rsegment_array();
        $params = array();
        for ($i=5; $i<=count($segments); $i++) {
            $params[] = $segments[$i];
        }

        if (count($params) > 0) {
            $this->__view_ct($ct_name, $action, $params);
        } else {
            $this->__view_ct($ct_name, $action);
        }
    }

    /**
     * Pasang tipe-konten
     *
     * @param  Boolean  Jika di set true, hapus tabel yang ada terlebih dahulu
     * @return Boolean  true jika sukses memasang
     */
    private function __install_ct($ct_name, $force = false)
    {
        $success = true;

        $list = $this->kwitang->ctModelList($ct_name);
        foreach ($list as $key => $value) {
            $m = $this->kwitang->ctModel($ct_name, $key);
            if (! is_object($m)) {
                return false;
            } elseif ($force) {
                $system_table = array('config', 'menu', 'menu_detail', 'roles', 'role_detail', 'sct', 'sct_config', 'stats', 'structure', 'users', 'user_log', 'user_session');
                if (in_array($m->table_name, $system_table)) {
                    show_error('Tipe-konten yang akan Anda pasang tidak diperbolehkan menggunakan nama tabel sistem.');
                } else {
                    $m->drop();
                    $success = $m->create();
                }
            } elseif (! $m->tableExist()) {
                $success = $m->create();
            }

            if (! $success) {
                return false;
            }
        }

        return true;
    }

    /**
     * Lepas tipe-konten
     *
     * @return Boolean  true jika sukses melepas
     */
    private function __uninstall_ct($ct_name)
    {
        $success = true;

        $list = $this->kwitang->ctModelList($ct_name);
        foreach ($list as $key => $value) {
            $m = $this->kwitang->ctModel($ct_name, $key);
            if (! is_object($m)) {
                return false;
            } else {
                $success = $m->drop();
            }

            if (! $success) {
                return false;
            }
        }

        return true;
    }

    /**
     * Halaman Tipe-konten
     */
    private function __view_ct($ct_name, $action, $params = null)
    {
        // Header dan footer pada admin, set false agar tidak ditampilkan
        // disable admin_header hanya bisa dilakukan pada method prepare()
        $this->vars['admin_header'] = true;
        $this->vars['admin_footer'] = true;
        $this->vars['admin_headless'] = false;

        $controller = $ct_name;
        $_action    = explode('-', $action);
        if (count($_action) == 2) {
            $controller = $_action[0];
            $action = $_action[1];
        }

        // load kontroller yang namanya identik dengan nama tipe-kontennya
        $ct_instance = $this->kwitang->ctController($ct_name, $controller);
        if (! $ct_instance) {
            show_error('Tipe-konten <strong>'.$ct_name.'</strong> belum di pasang.');
        }

        if (! method_exists($ct_instance, $action)) {
            show_404();
        }

        if (! empty($this->vars['current_sct']->title)) {
            $this->vars['breadcrumb'][] = var_lang($this->vars['current_sct']->title);
        }

        $ct_instance->prepare($action);

        // build the view
        if ($this->vars['admin_header']) {
            if ($this->vars['admin_headless']) {
                $this->__view('headless', $this->vars);
            } else {
                $this->__view('header', $this->vars);
            }
        }

        if (! empty($ct_instance) and @method_exists($ct_instance, $action)) {
            // jika SCT ada dan method(action) yang diminta juga ada
            if (! $params) {
                $params = array();
            }

            call_user_func_array(array($ct_instance, $action), $params);

        } else {
            $this->__view('blank', $this->vars);
        }

        if ($this->vars['admin_footer']) {
            if ($this->vars['admin_headless']) {
                $this->__view('footless', $this->vars);
            } else {
                $this->__view('footer', $this->vars);
            }
        }
    }


    // CONFIGURATION
    // ========================================================================


    public function config()
    {
        $this->common();
    }

    public function common()
    {
        $this->__prevent_non_admin();
        $this->load->helper('form');

        $this->vars['breadcrumb'][]    = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/common')] = lang('k_common');
        $this->vars['head_title']      = lang('k_setting').' &raquo; '.lang('k_common');

        $this->vars['frontends'] = $this->kwitang->feList();

        $this->__view('common', $this->vars);
    }

    public function common_update()
    {
        $this->__prevent_non_admin();
        $input = $this->input->post(null, false);

        $return_url = site_url('admin/common');
        if (isset($input['return_url'])) {
            $return_url = $input['return_url'];
            unset($input['return_url']);
        }

        $this->load->model('KConfig');

        $retval    = false;
        $error_msg = 'Pengaturan berhasil disimpan.';

        foreach ($input as $config_name => $value) {
            if (is_array($value)) {
                foreach ($value as $key => $val) {
                    $retval = $this->KConfig->set($config_name, $key, $val);
                }
                if (! $retval) {
                    $error_msg = 'Gagal mengubah pengaturan <strong>'.$config_name.' - '.$key.'</strong>';
                    break;
                }
            } else {
                $retval = $this->KConfig->set('system', $config_name, $value);
            }

            if (! $retval) {
                $error_msg = 'Gagal mengubah pengaturan <strong>'.$config_name.'</strong>';
                break;
            }
        }

        if (! $this->input->is_ajax_request()) {
            if (! $retval) {
                show_error($error_msg);
            } else {
                redirect($return_url);
            }
        } else {
            $data = array('status' =>($retval ? true : false),
                          'message' => $error_msg);
            header('Content-type: application/json');
            echo json_encode($data);
        }
    }

    //-------------------------------------------------------------------------

    public function structure()
    {
        $this->__prevent_non_admin();

        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/structure')] = lang('k_structure');

        $this->vars['head_title'] = lang('k_structure').' | '.kconfig('system', 'site_name');

        $this->__view('structure', $this->vars);
    }

    public function structure_add($parent_id = null)
    {
        $this->__prevent_non_admin();

        $this->load->helper('form');

        if ($parent_id !== null) {
            $this->vars['parent_data'] = $this->Structure->get($parent_id);
        } else {
            $parent_id = 0;
        }

        $this->vars['neworderval']   = $this->Structure->maxOrder($parent_id) + 1;
        $this->vars['content_types'] = $this->kwitang->ctList();
        $this->vars['view_files']    = $this->kwitang->feViewList();

        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/structure')] = lang('k_structure');
        $this->vars['breadcrumb'][] = lang('k_add').' '.((! empty($this->parent_data)) ? $this->parent_data->name : lang('k_parent'));

        $this->vars['head_title'] = lang('k_add').' '.lang('k_structure');
        $this->__view('structure-add', $this->vars);
    }

    public function structure_save()
    {
        $this->__prevent_non_admin();

        $data['name']       = strtolower(url_title(substr($this->input->post('name'), 0, 30)));
        $data['title']      = $this->input->post('title');
        $data['description'] = $this->input->post('description');
        $data['order']      = $this->input->post('order');
        $data['parent_id']  = $this->input->post('parent_id');
        $in_menu = $this->input->post('in_menu');
        $data['in_menu']    =($in_menu == 'on' or $in_menu == '1' ? 1 : 0);
        $icon_uploaded      = $this->__upload_image('icon', 'structure');
        if (! empty($icon_uploaded)) {
            $data['icon']   = $icon_uploaded;
        }
        $foto_uploaded      = $this->__upload_image('foto', 'structure');
        if (! empty($foto_uploaded)) {
            $data['foto']   = $foto_uploaded;
        }
        $data['view_file']  = $this->input->post('view_file');
        $data['view_type']  = $this->input->post('view_type');
        if (is_array($data['title'])) {
            $data['title'] = serialize($data['title']);
        }
        if (is_array($data['description'])) {
            $data['description'] = serialize($data['description']);
        }

        if (empty($data['name']) or empty($data['title'])) {
            show_error('Maaf, Silakan isi judul dan nama unik untuk Struktur yang akan dibuat.');
        }

        $ct = $this->input->post('content_type');
        if (! empty($ct) and $ct !== 'none') {
            $data['view_sct']    = $data['name'];
        }
        $this->load->model('Structure');
        $ret = $this->Structure->save($data);

        // simpan tipe konten, jika ada yang dipilih
        if (! empty($ct) and $ct !== 'none' and $ret) {
            $data1 = array();
            $data1['content_type']   = $ct;
            $data1['name']           = $data['name'];
            $data1['title']          = $data['title'];
            $data1['structure_id']   = $ret;
            $data1['notes']          = $data['description'];

            $ret1 = $this->__save_sct($data1);
        }

        if (! $ret) {
            show_error('Maaf, Sistem tidak dapat menyimpan data yang Anda masukkan, silakan coba beberapa saat lagi dan pastikan tidak ada duplikasi data.');
        }

        redirect('admin/structure');
    }

    public function structure_edit($id)
    {
        $this->__prevent_non_admin();

        $this->vars['content_types'] = $this->kwitang->ctList();

        // ambil nama-nama views dari front application
        $this->vars['view_files'] = $this->kwitang->feViewList();

        $this->load->helper('form');
        $this->load->model('Structure');
        $this->vars['data']    = $this->Structure->get($id);
        $this->vars['data_sct_edit'] = $this->Structure->sctAll($id);

        $structure_childs = $this->Structure->getChilds($id);
        $view_childs      = array('' => 'Default');
        if (! empty($this->vars['data_sct_edit'])) {
            foreach ($this->vars['data_sct_edit'] as $v) {
                $view_childs[$v->name] = var_lang($v->title);
            }
        }
        foreach ($structure_childs as $val) {
            $tmp     = $this->Structure->sctAll($val->id);
            $tmp_val = array();
            if ($tmp) {
                foreach ($tmp as $v) {
                    $tmp_val[$v->name] = var_lang($v->title);
                }
            }
            if ($tmp_val) {
                $view_childs[var_lang($val->title)] = $tmp_val;
            }
        }

        $this->vars['view_childs'] = $view_childs;

        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/structure')] = lang('k_structure');
        $this->vars['breadcrumb'][] = lang('k_edit').' '.var_lang($this->vars['data']->title);

        $this->vars['head_title'] = lang('k_edit').' '.lang('k_structure').' &raquo; '.var_lang($this->vars['data']->title);

        $this->__view('structure-edit', $this->vars);
    }

    public function structure_update()
    {
        $this->__prevent_non_admin();

        $data['id']         = $this->input->post('id');
        $data['name']       = strtolower(url_title(substr($this->input->post('name'), 0, 30)));
        $data['title']      = $this->input->post('title');
        $data['description']= $this->input->post('description');
        $data['order']      = $this->input->post('order');
        $data['parent_id']  = $this->input->post('parent_id');
        $data['in_menu']    =($this->input->post('in_menu') == 'on' ? 1 : 0);
        $data['view_file']  = $this->input->post('view_file');
        $data['view_sct']   = $this->input->post('view_sct');
        $data['view_type']  = $this->input->post('view_type');
        if (is_array($data['title'])) {
            $data['title'] = serialize($data['title']);
        }
        if (is_array($data['description'])) {
            $data['description'] = serialize($data['description']);
        }

        $icon_uploaded      = $this->__upload_image('icon', 'structure');
        if (! empty($icon_uploaded)) {
            $data['icon'] = $icon_uploaded;
        }
        $image_uploaded = $this->__upload_image('image', 'structure');
        if (! empty($image_uploaded)) {
            $data['image'] = $image_uploaded;
        }

        $this->load->model('Structure');
        $lama = $this->Structure->get($data['id']);

        if (! empty($lama->icon) and ! empty($data['icon'])) {
            @unlink($lama->icon);
        }
        if (! empty($lama->image) and ! empty($data['image'])) {
            @unlink($lama->image);
        }

        $ret = $this->Structure->save($data);

        redirect('admin/structure_edit/'.$data['id'], 'refresh');
    }

    public function delicon($id)
    {
        $this->__prevent_non_admin();

        $data['id']         = $id;
        $data['icon']       = '';

        $this->load->model('Structure');
        $lama = $this->Structure->get($data['id']);
        @unlink($lama->icon);

        $ret = $this->Structure->save($data);

        redirect('admin/structure_edit/'.$data['id'], 'refresh');
    }

    public function delimage($id)
    {
        $this->__prevent_non_admin();

        $data['id']    = $id;
        $data['image'] = '';

        $this->load->model('Structure');
        $lama = $this->Structure->get($data['id']);
        @unlink($lama->image);

        $ret = $this->Structure->update($data);

        redirect('admin/structure_edit/'.$data['id'], 'refresh');
    }

    public function structure_delete($id, $confirmed = '')
    {
        $this->__prevent_non_admin();
        $this->load->model('Structure');

        // 86: just an unique confirmation code

        if ($confirmed !== '86') {
            $data = $this->Structure->getChilds($id);
            if (! empty($data)) {
                echo '<p>You will also delete these structure:</p>';
                echo '<ul>';
                foreach ($data as $d) {
                    echo '<li>'.$d->name.' - '.$d->title.'</li>';
                }
                echo '</ul>';
                echo '<a href="'.site_url('admin/structure').'">Cancel Deletion</a> ';
                echo ' | ';
                echo '<a href="'.site_url('admin/structure_delete/'.$id.'/86').'">Continue, And Delete All Structure &rarr;</a>';
            } else {
                $confirmed = '86';
            }
        }

        if ($confirmed === '86') {
            $ret = $this->Structure->delete($id);
            if (! $ret) {
                show_error('Gagal menghapus data struktur.');
            }
            redirect('admin/structure');
        }
    }

    public function structure_ct_edit($sct_id, $st_id = 0)
    {
        $this->__prevent_non_admin();
        $this->load->helper('form');
        $this->load->model('Structure');

        $sct = $this->Structure->sctGet($sct_id);
        $sc  = $this->Structure->get($sct->structure_id);
        $this->vars['edit_sct']   = $sct;
        $this->vars['edit_sc']    = $sc;
        $this->vars['view_files'] = $this->kwitang->feViewList();

        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/structure')] = lang('k_structure');
        $this->vars['breadcrumb'][site_url('admin/structure_edit/'.$sct->structure_id)] = var_lang($sc->title);
        $this->vars['breadcrumb'][] = ' &rarr; '.var_lang($sct->title);

        $this->vars['head_title'] = lang('k_edit').' '.lang('k_content_type').' &raquo; '.var_lang($sct->title);
        $this->__view('structure-ct-edit', $this->vars);
    }

    public function structure_ct_update()
    {
        $this->__prevent_non_admin();

        $data['id']            = $this->input->post('id');
        $data['title']         = $this->input->post('title');
        $data['name']          = strtolower(url_title(substr($this->input->post('name'), 0, 30)));
        $data['structure_id']  = $this->input->post('structure_id');
        $data['view_index']    = $this->input->post('view_index');
        $data['view_content']  = $this->input->post('view_content');
        if (is_array($data['title'])) {
            $data['title'] = serialize($data['title']);
        }

        $this->load->model('Structure');
        $ret = $this->Structure->sctSave($data);

        if ($ret) {
            redirect('admin/structure_edit/'.$data['structure_id']);
        } else {
            show_error('Failed to update Content-Type '.$data['name']);
        }
    }

    public function structure_ct_save()
    {
        $this->__prevent_non_admin();
        $data = array();

        $data['content_type']  = $this->input->post('content_type');
        $data['name']          = strtolower(url_title(substr($this->input->post('ct_name'), 0, 30)));
        $data['title']         = $this->input->post('ct_title');
        $data['structure_id']  = $this->input->post('id');
        $data['notes']         = $this->input->post('notes');
        if (is_array($data['title'])) {
            $data['title'] = serialize($data['title']);
        }

        if ($this->__save_sct($data)) {
            redirect('admin/structure_edit/'.$data['structure_id']);
        } else {
            show_error('Failed to save Content-Type '.$data['name']);
        }
    }

    public function structure_ct_delete($id, $structure_id, $confirmed = '')
    {
        $this->__prevent_non_admin();
        $this->load->model('Structure');

        if ($confirmed !== '86') {
            $data = $this->Structure->getall_byparent($id);
            if (! empty($data)) {
                echo '<p>Anda juga akan menghapus:</p>';
                echo '<ul>';
                foreach ($data as $d) {
                    echo '<li>'.$d->title.'</li>';
                }
                echo '</ul>';
                echo '<a href="'.site_url('admin/structure_delete/'.$id.'/86').'">Lanjutkan &rarr;</a>';
            } else {
                $confirmed = '86';
            }
        }

        if ($confirmed === '86') {
            $this->Structure->sctDelete($id);
            redirect('admin/structure_edit/'.$structure_id);
        }
    }

    //-------------------------------------------------------------------------

    public function menu()
    {
        $this->__prevent_non_admin();
        $this->load->model('Menu');

        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/menu/')] = lang('k_menu');
        $this->vars['head_title'] = lang('k_setting').' &raquo; '.lang('k_menu');

        $this->vars['menu'] = $this->Menu->all();

        $this->__view('menu', $this->vars);
    }

    public function menu_add()
    {
        $this->__prevent_non_admin();
        $this->load->helper('form');

        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/menu')] = lang('k_menu');
        $this->vars['breadcrumb'][] = lang('k_add').' '.lang('k_menu');

        $this->__view('menu-add', $this->vars);
    }

    public function menu_save()
    {
        $this->__prevent_non_admin();

        $id = $this->input->post('id');
        if (! empty($id)) {
            $data['id'] = $id;
        }

        $data['title']  = $this->input->post('title');
        $data['description']  = $this->input->post('description');

        if (empty($data['title'])) {
            show_error('Maaf, Silakan isi judul untuk menu yang akan dibuat.');
        }

        $this->load->model('Menu');
        if (! isset($data['id'])) {
            $ret = $this->Menu->insert($data);
        } else {
            $ret = $this->Menu->update($data);
        }

        if ($ret) {
            redirect(site_url('admin/menu'), 'refresh');
        } else {
            show_error('Maaf, terjadi kendala saat menambahkan menu baru, silakan coba beberapa saat lagi dan pastikan tidak ada dupliklasi data.');
        }
    }

    public function menu_edit($menu_id)
    {
        if (is_null($menu_id)) {
            redirect('admin/menu');
        }
        $this->__prevent_non_admin();

        $this->load->helper('form');
        $this->load->model('Menu');
        $this->load->model('Menu_detail');

        $menu = $this->Menu->get($menu_id);
        $this->vars['menu'] = $menu;
        $this->vars['menu_detail'] = $this->Menu_detail->getall($menu_id);

        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/menu')] = lang('k_menu');
        $this->vars['breadcrumb'][site_url('admin/menu_edit/'.$menu->id)] = $menu->title;
        $this->vars['breadcrumb'][] = lang('k_edit');

        $this->vars['head_title'] = lang('k_setting').' &raquo; '.lang('k_menu');

        $this->__view('menu-detail', $this->vars);
    }

    public function menu_del($id)
    {
        $this->__prevent_non_admin();

        $this->load->model('Menu');
        $curr_menu = $this->Menu->get($id);

        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/menu')] = lang('k_menu');
        $this->vars['breadcrumb'][site_url('admin/menu_edit/'.$curr_menu->id)] = $curr_menu->title;
        $this->vars['breadcrumb'][] = lang('k_delete').' '.lang('k_menu');

        $menu_id_to_be_delete = $id;

        $this->vars['curr_menu'] = $curr_menu;
        $this->vars['menu_id_to_be_delete'] = $menu_id_to_be_delete;

        $this->__view('menu-delete', $this->vars);
    }

    public function menu_del_confirm($id)
    {
        $this->__prevent_non_admin();

        $this->load->model('Menu');
        $ret = $this->Menu->delete($id);

        if ($ret) {
            redirect('admin/menu/', 'refresh');
        } else {
            show_error('Failed to delete menu.');
        }
    }

    // menu detail

    public function menu_detail_add($menu_id, $parent_id = 0)
    {
        $this->__prevent_non_admin();

        $this->load->helper('form');
        $this->load->model('Menu');
        $this->load->model('Menu_detail');

        $menu = $this->Menu->get($menu_id);
        $this->vars['neworderval'] = $this->Menu_detail->get_max_order($menu_id, $parent_id);
        $this->vars['curr_menu'] = $menu;
        $this->vars['parent_id'] = $parent_id;

        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/menu')] = lang('k_menu');
        $this->vars['breadcrumb'][site_url('admin/menu_edit/'.$menu->id)] = $menu->title;
        $this->vars['breadcrumb'][] = lang('k_add');

        $this->vars['head_title'] = lang('k_add').' '.lang('k_menu');
        $this->__view('menu-detail-addedit', $this->vars);
    }

    public function menu_detail_save()
    {
        $this->__prevent_non_admin();

        $id         = $this->input->post('id');
        $menu_id    = $this->input->post('menu_id');
        $title       = $this->input->post('title');
        $icon       = $this->__upload_image('icon', 'menu');
        $url        = $this->input->post('url');
        $parent_id  = $this->input->post('parent_id');
        $order      = $this->input->post('order');

        if (! empty($id)) {
            $data['id'] = $id;
        }
        if (! empty($menu_id)) {
            $data['menu_id'] = $menu_id;
        }
        if (! empty($title)) {
            $data['title'] = $title;
        }
        if (! empty($icon)) {
            $data['icon'] = $icon;
        }
        if (! empty($url)) {
            $data['url'] = $url;
        }
        if (! empty($parent_id)) {
            $data['parent_id'] = $parent_id;
        }
        if (! empty($order)) {
            $data['order'] = $order;
        }

        if (empty($data['title']) or empty($data['url'])) {
            show_error('Maaf, Silakan isi nama dan URL untuk menu detail tersebut.');
        }

        $this->load->model('Menu_detail');
        if (! empty($data['id'])) {
            $lama = $this->Menu_detail->get($data['id']);
        }

        $ret = $this->Menu_detail->save($data);

        if ($ret) {
            if (! empty($lama->icon) and ! empty($data['icon'])) {
                @unlink($lama->icon);
            }
        } else {
            show_error('Gagal menyimpan menu.');
        }

        //$menu_detail = $this->Menu_detail->get($menu_id);

        redirect(site_url('admin/menu_edit/'.$menu_id), 'refresh');
    }

    public function menu_detail_edit($id)
    {
        $this->__prevent_non_admin();

        $this->load->helper('form');
        $this->load->model('Menu');
        $this->load->model('Menu_detail');

        $curr_menu_detail = $this->Menu_detail->get($id);
        $curr_menu = $this->Menu->get($curr_menu_detail->menu_id);
        $this->vars['curr_menu_detail'] = $curr_menu_detail;
        $this->vars['curr_menu'] = $curr_menu;

        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/menu')] = lang('k_menu');
        $this->vars['breadcrumb'][site_url('admin/menu_edit/'.$curr_menu->id)] = $curr_menu->title;
        $this->vars['breadcrumb'][] = lang('k_edit');

        $this->vars['head_title'] = lang('k_add').' '.lang('k_menu');
        $this->__view('menu-detail-addedit', $this->vars);
    }

    public function menu_detail_delete($id)
    {
        $this->__prevent_non_admin();
        $this->load->model('Menu_detail');
        $mdet = $this->Menu_detail->get($id);
        $ret = $this->Menu_detail->delete($id);

        if ($ret) {
            redirect(site_url('admin/menu_edit/'.$mdet->menu_id), 'refresh');
        } else {
            show_error('Failed to delete menu item.');
        }
    }

    public function menu_detail_del_confirm($id)
    {
        $this->__prevent_non_admin();

        $this->load->model('Menu');
        $ret = $this->Menu->get($id);

        // really delete the data
    }

    //-------------------------------------------------------------------------

    public function language()
    {
        $this->__prevent_non_admin();

        $this->load->helper('form');
        $this->vars['breadcrumb'][] = lang('k_language');
        $this->vars['breadcrumb'][site_url('admin/language')] = lang('k_language');

        $this->vars['head_title'] = lang('k_language');

        $this->__view('language', $this->vars);
    }

    //-------------------------------------------------------------------------

    public function user()
    {
        $this->__prevent_non_admin();

        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/user')] = lang('k_users');

        $this->vars['head_title'] = lang('k_users').' | '.kconfig('system', 'site_name');
        $this->load->model('Users');
        $this->vars['users'] = $this->Users->all();

        $this->__view('user', $this->vars);
    }

    public function user_add()
    {
        $this->__prevent_non_admin();

        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/user')] = lang('k_users');
        $this->vars['breadcrumb'][] = lang('k_add');

        $this->load->helper('form');
        $this->vars['user_levels'] = $this->user_levels;
        $this->load->model('Roles');
        $this->vars['roles'] = $this->Roles->all();

        $this->__view('user-add', $this->vars);
    }

    /**
     * Simpan data pengguna
     *
     * @return  void
     */
    public function user_save()
    {
        $this->__prevent_non_admin();

        $aac = $this->input->post('isactive');
        if (! empty($aac) and $aac == 'active') {
            $data['active'] = 1;
        } else {
            $data['active'] = 0;
        }

        $data['fullname']   = $this->input->post('fullname');
        $data['username']   = $this->input->post('username');
        $data['password']   = $this->input->post('pass');
        $data['level']      = $this->input->post('level');
        $data['role_id']    = $this->input->post('role_id');
        $data['gender']     = $this->input->post('gender');
        $data['birth_date'] = $this->input->post('birth_date');
        $data['address']    = $this->input->post('address');
        $data['phone']      = $this->input->post('phone');
        $data['mobile']     = $this->input->post('mobile');
        $data['email']      = $this->input->post('email');
        $data['website']    = $this->input->post('website');
        $data['avatar']     = $this->input->post('avatar');
        $data['notes']      = $this->input->post('notes');

        $this->load->model('Users');
        $ret = $this->Users->insert($data);

        if ($ret) {
            redirect(site_url('admin/user'), 'refresh');
        } else {
            show_error('Maaf, terjadi kendala saat menambahkan pengguna baru, silakan coba beberapa saat lagi.');
        }
    }

    public function user_edit($username)
    {
        $this->__allow_admin_and($username);

        $this->vars['breadcrumb'][''] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/user')] = lang('k_users');
        $this->vars['breadcrumb'][''] = lang('k_edit');

        $this->load->helper('form');
        $this->load->model('Users');
        $this->load->model('Roles');

        $user = $this->Users->get($username, false);

        if (empty($user)) {
            redirect('admin/user');
        }

        $this->vars['user_levels'] = $this->user_levels;
        $this->vars['user_edit']   = $user;
        $this->vars['roles']       = $this->Roles->all();

        $this->__view('user-edit', $this->vars);
    }

    public function user_update()
    {
        $pass = $this->input->post('pass');
        $pass1 = $this->input->post('pass1');
        if (! empty($pass) and($pass == $pass1)) {
            $data['password'] = $pass;
        }

        if (kuser()->level == 'ADMIN') {
            $aac = $this->input->post('isactive');
            if (! empty($aac) and $aac == 'active') {
                $data['active'] = 1;
            } else {
                $data['active'] = 0;
            }
        }

        $all_post = $this->input->post();
        if (isset($all_post['fullname'])) {
            $data['fullname'] = $this->input->post('fullname');
        }
        if (isset($all_post['username'])) {
            $data['username'] = $this->input->post('username');
        }
        if (isset($all_post['new_username'])) {
            $data['new_username'] = $this->input->post('new_username');
        }
        if (isset($all_post['level'])) {
            $data['level'] = $this->input->post('level');
        }
        if (isset($all_post['created'])) {
            $data['created'] = $this->input->post('created');
        }
        if (isset($all_post['role_id'])) {
            $data['role_id'] = $this->input->post('role_id');
        }
        if (isset($all_post['gender'])) {
            $data['gender'] = $this->input->post('gender');
        }
        if (isset($all_post['birth_date'])) {
            $data['birth_date'] = $this->input->post('birth_date');
        }
        if (isset($all_post['phone'])) {
            $data['phone'] = $this->input->post('phone');
        }
        if (isset($all_post['mobile'])) {
            $data['mobile'] = $this->input->post('mobile');
        }
        if (isset($all_post['email'])) {
            $data['email'] = $this->input->post('email');
        }
        if (isset($all_post['website'])) {
            $data['website'] = $this->input->post('website');
        }
        if (isset($all_post['notes'])) {
            $data['notes'] = $this->input->post('notes');
        }
        if (isset($all_post['address'])) {
            $data['address'] = $this->input->post('address');
        }

        $this->__allow_admin_and($data['username']);

        $file_uri = $this->__upload_image('avatar', 'users');
        if (! empty($file_uri)) {
            $data['identity_file'] = $file_uri;
        } elseif (isset($all_post['avatar'])) {
            $data['avatar'] = $this->input->post('avatar');
        }

        $this->load->model('Users');
        $ret = $this->Users->update($data);

        if ($ret) {
            $user_config = null;
            if (! empty($all_post['config'])) {
                $user_config = $all_post['config'];
            }
            if (! empty($user_config)) {
                foreach ($user_config as $key => $value) {
                    $this->Users->setConfig($data['username'], $key, $value);
                }
            }

            if (kuser()->level == 'ADMIN') {
                redirect(site_url('admin/user'));
            } else {
                redirect(site_url('admin/user_edit/'.$data['username']));
            }
        } else {
            show_error('Maaf, terjadi kendala saat mengubah pengguna, silakan coba beberapa saat lagi.');
        }
    }

    public function user_delete($username)
    {
        $this->__prevent_non_admin();

        $this->load->model('Users');
        $ret = $this->Users->delete($username);

        if ($ret) {
            redirect(site_url('admin/user'), 'refresh');
        } else {
            show_error('Maaf, terjadi kendala saat menghapus pengguna, silakan coba beberapa saat lagi.');
        }
    }

    public function user_log($username, $page_number = 1)
    {
        $this->__allow_admin_and($username);

        $user_log = $this->Users->getLog($username, $page_number, kconfig('system', 'item_perpage_medium', 50));

        $this->vars['username']   = $username;
        $this->vars['page_number']= $page_number;
        $this->vars['total_page'] = $user_log['total_page'];
        $this->vars['userlog']    = $user_log['data'];

        $this->vars['breadcrumb'][''] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/user')] = lang('k_users');
        $this->vars['breadcrumb'][''] = lang('k_log').' '.$username;

        $this->__view('user-log', $this->vars);
    }

    //-------------------------------------------------------------------------

    public function roles()
    {
        $this->__prevent_non_admin();
        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][] = lang('k_privileges');

        $this->load->model('Roles');
        $this->vars['roles'] = $this->Roles->all();
        $this->vars['head_title'] = lang('k_privileges') . ' | ' . kconfig('system', 'site_name');

        $this->__view('roles', $this->vars);
    }

    public function roles_add()
    {
        $this->__prevent_non_admin();
        $this->load->helper('form');
        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/roles')] = lang('k_privileges');
        $this->vars['breadcrumb'][] = lang('k_add');

        $this->vars['head_title'] = lang('k_setting').' &raquo; '.lang('k_privileges');

        $this->__view('roles_addedit', $this->vars);
    }

    public function roles_edit($id)
    {
        $this->__prevent_non_admin();
        $this->load->helper('form');

        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/roles')] = lang('k_privileges');
        $this->vars['breadcrumb'][] = lang('k_edit');

        $this->vars['head_title'] = lang('k_setting').' &raquo; '.lang('k_privileges');

        $this->load->model('Roles');
        $this->load->model('Roles_detail');
        $this->vars['roles_edit']       = $this->Roles->get($id);
        $this->vars['role_detail_edit'] = $this->Roles_detail->all($id);

        $this->__view('roles_addedit', $this->vars);
    }

    public function roles_save()
    {
        $this->__prevent_non_admin();
        $data['title'] = $this->input->post('role_name');
        $data['notes'] = $this->input->post('notes');
        $role = $this->input->post('role');

        $this->load->model('Roles');
        $this->load->model('Roles_detail');

        $flag = true;
        $ret = $this->Roles->insert($data);
        if ($ret) {
            $role_id = $ret;
            if (! empty($role)) {
                foreach ($role as $k => $v) {
                    $ret2 = $this->Roles_detail->save(array('role_id' => $role_id, 'structure_id' => $k, 'permission' => strtolower($v)));
                    if (! $ret2) {
                        $flag = false;
                    }
                }
            }
        } else {
            $flag = false;
        }

        if ($flag) {
            redirect(site_url('admin/roles'));
        } else {
            show_error('Maaf, terjadi kendala saat menambahkan Roles baru, silakan coba beberapa saat lagi.');
        }
    }

    public function roles_update()
    {
        $this->__prevent_non_admin();
        $data['id']    = $this->input->post('id');
        $data['title'] = $this->input->post('role_name');
        $data['notes'] = $this->input->post('notes');
        $role = $this->input->post('role');

        $this->load->model('Roles');
        $this->load->model('Roles_detail');

        $flag = true;
        $ret = $this->Roles->update($data);
        if ($ret) {
            $role_id = $data['id'];
            $this->Roles_detail->deleteall($role_id);
            foreach ($role as $k => $v) {
                $ret2 = $this->Roles_detail->save(array('role_id' => $role_id,
                                                        'structure_id' => $k,
                                                        'permission' => strtoupper($v)));
                if (! $ret2) {
                    $flag = false;
                }
            }
        } else {
            $flag = false;
        }

        if ($flag) {
            redirect(site_url('admin/roles'));
        } else {
            show_error('Maaf, terjadi kendala saat menambahkan Roles baru, silakan coba beberapa saat lagi.');
        }
    }

    public function roles_delete($id)
    {
        $this->__prevent_non_admin();
        $this->load->model('Roles');

        $this->Roles->delete($id);
        redirect('admin/roles');
    }


    //-------------------------------------------------------------------------

    public function frontend_options()
    {
        $this->__prevent_non_admin();
        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/frontend_options')] = lang('k_frontend_options');

        $this->load->helper('form');
        $this->vars['options_file'] = FRONT_PATH.$this->kwitang->frontend.'/admin/options.php';

        $this->vars['frontends'] = $this->kwitang->feList();

        $this->__view('frontend-options', $this->vars);
    }

    public function frontend_editor()
    {
        $this->__prevent_non_admin();
        $this->load->helper('form');

        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/frontend_editor')] = lang('k_frontend_editor');

        $this->__view('frontend-editor', $this->vars);
    }


    public function frontend_preview()
    {
        $this->__prevent_non_admin();
        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/frontend_preview')] = lang('k_frontend_preview');

        $this->vars['frontends'] = $this->kwitang->feList();

        $this->__view('frontend-preview', $this->vars);
    }


    public function filetree()
    {
        if (kuser()->level !== 'ADMIN') {
            return;
        }
        $dir = $this->input->post('dir');
        $root = FRONT_PATH.$this->input->post('root');

        if (@file_exists($root.$dir)) {
            $files = scandir($root.$dir);
            natcasesort($files);
            if (count($files) > 2) {
                echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
                // All dirs
                foreach ($files as $file) {
                    if (@file_exists($root.$dir.$file) and $file != '.' and $file != '..' and @is_dir($root.$dir.$file)) {
                        echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"".htmlentities($dir.$file)."/\">".htmlentities($file)."</a></li>";
                    }
                }
                // All files
                foreach ($files as $file) {
                    if (@file_exists($root.$dir.$file) and $file != '.' and $file != '..' and !@is_dir($root.$dir.$file)) {
                        $ext = preg_replace('/^.*\./', '', $file);
                        echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"".htmlentities($dir.$file)."\">".htmlentities($file)."</a></li>";
                    }
                }
                echo "</ul>";
            }
        }
    }


    public function file_get()
    {
        if (kuser()->level !== 'ADMIN') {
            return;
        }

        $filename = FRONT_PATH.$this->input->post('filename');
        if (@file_exists($filename)) {
            echo file_get_contents($filename);
        } else {
            echo 'Not accessible';
        }
    }


    public function file_save()
    {
        if (kuser()->level !== 'ADMIN') {
            return;
        }

        $filename = FRONT_PATH.$this->input->post('filename');
        $file_content = $this->input->post('file_content', false);

        $result = file_put_contents($filename, html_entity_decode($file_content));
        //$result = file_put_contents($filename, $file_content);

        if ($result) {
            echo 'File telah disimpan.';
        } else {
            echo 'Error...\r\n\r\nGagal menyimpan File';
        }
    }


    //-------------------------------------------------------------------------


    public function filemanager()
    {
        if (kuser()->level !== 'ADMIN') {
            return;
        }

        $this->vars['breadcrumb'][] = lang('k_setting');
        $this->vars['breadcrumb'][site_url('admin/filemanager')] = lang('k_filemanager');

        $this->__view('filemanager', $this->vars);
    }


    public function elfinder()
    {
        if (kuser()->level !== 'ADMIN') {
            return;
        }
        $this->__view('elfinder', $this->vars);
    }


    public function elfinder_connector()
    {
        if (kuser()->level !== 'ADMIN') {
            return;
        }
        include_once ROOT_PATH.'assets/elfinder/php/elFinder.class.php';
        include_once ROOT_PATH.'assets/elfinder/php/elFinderConnector.class.php';
        include_once ROOT_PATH.'assets/elfinder/php/elFinder.class.php';
        include_once ROOT_PATH.'assets/elfinder/php/elFinderVolumeDriver.class.php';
        include_once ROOT_PATH.'assets/elfinder/php/elFinderVolumeLocalFileSystem.class.php';

        $opts = array(
                    'roots' => array(
                        array(
                            'driver' => 'LocalFileSystem',
                            'path'   => rtrim(ROOT_PATH.$this->base_upload, '/').'/',
                            'URL'    => rtrim(base_url($this->base_upload), '/').'/'
                        )
                    )
                );

        $connector = new elFinderConnector(new elFinder($opts));
        $connector->run();
    }
}

/* end file kwitang/controllers/admin.php */
