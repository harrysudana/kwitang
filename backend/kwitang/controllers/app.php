<?php
if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/**
 * FronteEnd Controller
 *
 * @package  Kwitang\Controllers
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class App extends CI_Controller
{
    public $vars = array();
    private $__change_lang = '';

    public function __construct()
    {
        parent::__construct();
        $is_logged_in = $this->kwitang->authenticate();
        $current_user = $this->kwitang->currentUser();

        $this->vars['is_logged_in'] = $is_logged_in;
        $this->vars['current_user'] = $current_user;

        $this->load->library('user_agent');

        $this->load->helper('kwitang_fe');
        $this->load->helper('form');
        $this->load->helper('text');

        if (! empty($_GET['lang'])) {
            $lang = $_GET['lang'];
            $this->__change_lang = $lang;
            $this->kwitang->lang = $lang;
            ksetcookie('_klang', $lang);
        } else {
            $lang = $this->kwitang->lang;
        }

        $this->vars['lang']  = $lang;

        load_lang($lang);

        // $cache_lifetime in minutes
        $cache_lifetime = kconfig('system', 'cache_lifetime', 0);
        if ($cache_lifetime > 0) {
            $this->output->cache($cache_lifetime);
        }

        if (kconfig('system', 'profiler', 0) == 1) {
            $this->output->enable_profiler(true);
        }
    }


    // Display content
    // -------------------------------------------------------------------------

    /**
     * Display main page
     *
     *
     */
    public function index()
    {
        $this->__do_stats();
        $this->vars['page_type'] = 'landing';

        $this->kwitang->feView('index', $this->vars);
    }

    /**
     * Method untuk menampilkan konten
     *
     * URL: /read/sct_name/content_id/[slug]
     *
     * / 1   / 2    / 3        / 4          / 5
     * / app / read / sct_name / content_id / [slug]
     *
     * @return void
     */
    public function read($lang = '', $sct_name = '', $content_id = '', $slug = '')
    {
        if (empty($sct_name)) {
            $this->error404();
        }

        if ($this->__change_lang != '') {
            redirect(content_url($sct_name, array($content_id, $slug), $this->__change_lang));
        }

        if ($lang !== '') {
            $this->__set_lang($lang);
        }

        $view_file = 'index';
        $is_exist  = false;

        $this->vars['sct_name']   = $sct_name;
        $this->vars['content_id'] = $content_id;

        $this->load->model('Structure');
        $current_sct = $this->Structure->sctGet($sct_name);
        if (! empty($current_sct->content_type)) {
            $this->vars['current_sct'] = $current_sct;
            $this->vars['current_structure'] = $this->Structure->get($current_sct->structure_id);
            $view_file = $current_sct->view_content;

            $control = $this->kwitang->ctController($current_sct->content_type);

            if ($control) {
                if (method_exists($control, 'pre_read')) {
                    $control->pre_read();
                }

                $content = $control->get($current_sct->id, $content_id);

                if ($content) {
                    $is_exist = true;

                    $this->__do_stats();
                    $this->vars['content'] = $content;
                    $this->vars['page_type'] = 'content';

                    $this->kwitang->feView($view_file, $this->vars);
                }

                if (method_exists($control, 'post_read')) {
                    $control->post_read();
                }
            }
        }

        if (! $is_exist) {
            $this->error404();
        }
    }

    /**
     * Method untuk menampilkan arsip konten
     *
     * URL: /archive/sct_name/content_id/[slug]
     *
     * / 1   / 2       / 3          / 4            / 6
     * / app / archive / [sct_name] / [page_number] / [item_perpage]
     *
     * @return void
     */
    public function content_index($lang = '', $sct_name = '', $page_number = 1, $item_perpage = '', $offset = 0)
    {
        if (empty($sct_name)) {
            $this->error404();
        }

        if ($this->__change_lang != '') {
            redirect(index_url($sct_name, $page_number, $item_perpage, $this->__change_lang));
        }

        if ($lang !== '') {
            $this->__set_lang($lang);
        }

        $view_file = 'index';
        $is_exist  = false;

        if ($page_number < 1) {
            $page_number = 1;
        }

        $this->vars['sct_name']    = $sct_name;
        $this->vars['page_number'] = $page_number;

        if (empty($this->Structure)) {
            $this->load->model('Structure');
        }
        $current_sct = $this->Structure->sctGet($sct_name);
        if (! empty($current_sct->content_type)) {
            $this->vars['current_sct'] = $current_sct;
            $this->vars['current_structure'] = $this->Structure->get($current_sct->structure_id);
            $view_file = $current_sct->view_index;

            $control = $this->kwitang->ctController($current_sct->content_type);

            if ($control) {
                if (method_exists($control, 'pre_index')) {
                    $control->pre_index();
                }

                $item_perpage = $item_perpage !== '' ? $item_perpage : kconfig('system', 'item_perpage', 10);
                $this->vars['item_perpage'] = $item_perpage;
                $offset +=($page_number - 1) * $item_perpage;

                $content = $control->getAll($current_sct->id, $item_perpage, $offset);

                if ($content) {
                    $is_exist = true;
                    $this->__do_stats();
                    $this->vars['content']   = $content;
                    $this->vars['page_type'] = 'index';

                    $this->kwitang->feView($view_file, $this->vars);
                }

                if (method_exists($control, 'post_index')) {
                    $control->post_index();
                }
            }
        }

        if (! $is_exist) {
            $this->error404();
        }
    }

    /**
     * Display archive by year-month-day or year-month or year
     * this require model has pub_date filed.
     *
     * URL:
     *
     * / 1   / 2       / 3          / 4                                / 5             / 6
     * / app / archive / [sct_name] / [year-month-day|year-month|year] / [page_number] / [item_perpage]
     *
     * @return void
     */
    public function archive($lang = '', $sct_name = '', $ymd = '', $page_number = 1, $item_perpage = '', $offset = 0)
    {
        if (empty($sct_name)) {
            $this->error404();
        }

        if ($this->__change_lang != '') {
            redirect(index_url($sct_name, $page_number, $item_perpage, $this->__change_lang));
        }

        if ($lang !== '') {
            $this->__set_lang($lang);
        }

        $view_file = 'index';
        $is_exist  = false;

        $ymd_pieces = explode('-', $ymd);
        if (empty($ymd_pieces[0])) {
            $ymd_pieces[0] = date('Y');
        }

        if (isset($ymd_pieces[2])) {
            $date_start = str_pad($ymd_pieces[0], 4, '0', STR_PAD_LEFT).'-'
                          .str_pad($ymd_pieces[1], 2, '0', STR_PAD_LEFT).'-'
                          .str_pad($ymd_pieces[2], 2, '0', STR_PAD_LEFT);

            $date_end   = date('Y-m-d', strtotime($date_start) + 86400);
        } elseif (isset($ymd_pieces[1])) {
            $date_start = str_pad($ymd_pieces[0], 4, '0', STR_PAD_LEFT).'-'
                          .str_pad($ymd_pieces[1], 2, '0', STR_PAD_LEFT).'-00';
            $month_num  = array(0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
            if ($ymd_pieces[0]%4==0) {
                $month_num[2] = 29;
            }
            $month_time =($month_num[intval($ymd_pieces[1])]+1) * 86400;
            $date_end   = date('Y-m-d', strtotime($date_start) + $month_time);
        } else {
            $date_start = str_pad($ymd_pieces[0], 4, '0', STR_PAD_LEFT).'-00-00';
            $date_end   = str_pad(intval($ymd_pieces[0])+1, 4, '0', STR_PAD_LEFT).'-00-00';
        }

        if ($page_number < 1) {
            $page_number = 1;
        }

        $this->vars['sct_name']    = $sct_name;
        $this->vars['page_number'] = $page_number;

        if (empty($this->Structure)) {
            $this->load->model('Structure');
        }
        $current_sct = $this->Structure->sctGet($sct_name);
        if (! empty($current_sct->content_type)) {
            $this->vars['current_sct'] = $current_sct;
            $this->vars['current_structure'] = $this->Structure->get($current_sct->structure_id);
            $view_file = $current_sct->view_index;

            $control = $this->kwitang->ctController($current_sct->content_type);

            if ($control) {
                // check if the model has pub_date property
                // if pub_date is not exist, return 404 page
                $the_model = $this->kwitang->ctModel($current_sct->content_type, $control->mainModel());

                if (isset($the_model->fields['pub_date'])) {
                    if (method_exists($control, 'pre_archive')) {
                        $control->pre_archive();
                    }

                    $item_perpage = $item_perpage !== '' ? $item_perpage : kconfig('system', 'item_perpage', 10);
                    $this->vars['item_perpage'] = $item_perpage;
                    $offset +=($page_number - 1) * $item_perpage;

                    $content = $control->getAll($current_sct->id, $item_perpage, $offset, null, array('pub_date >=' => $date_start, 'pub_date <' => $date_end));

                    if ($content) {
                        $is_exist = true;
                        $this->__do_stats();
                        $this->vars['content']   = $content;
                        $this->vars['page_type'] = 'archive';

                        $this->kwitang->feView($view_file, $this->vars);
                    }

                    if (method_exists($control, 'post_archive')) {
                        $control->post_archive();
                    }
                }
            }
        }

        if (! $is_exist) {
            $this->error404();
        }
    }

    /**
     * Method untuk menampilkan file view secara langsung, jika $sct_name dan
     * atau $content_id ditentukan maka data akan tersedia pada view.
     *
     * URL: /view/[sct_name]/[content_id]
     *
     * / 1   / 2    / 3          / 4
     * / app / view / [sct_name] / [content_id]
     *
     * @return void
     */
    public function view($lang = '', $view_file = '', $sct_name = '', $content_id = '')
    {
        if ($view_file == '') {
            $this->error404();
        }

        if ($this->__change_lang != '') {
            $uri = $this->__change_lang.'/view/'.$view_file;
            if ($sct_name != '') {
                $uri .= '/'.$sct_name;
            }
            if ($content_id != '') {
                $uri .= '/'.$content_id;
            }
            redirect($uri);
        }

        if ($lang !== '') {
            $this->__set_lang($lang);
        }

        if (empty($this->Structure)) {
            $this->load->model('Structure');
        }

        if (! empty($sct_name)) {
            $current_sct = $this->Structure->sctGet($sct_name);

            if (! empty($current_sct->content_type)) {
                $this->vars['current_sct'] = $current_sct;
                $this->vars['current_structure'] = $this->Structure->get($current_sct->structure_id);
                $control = $this->kwitang->ctController($current_sct->content_type);

                if ($control) {
                    if (method_exists($control, 'pre_read')) {
                        $control->pre_read();
                    }

                    if (! empty($content_id)) {
                        $content = $control->get($content_id);
                    } else {
                        $content = $control->get();
                    }

                    if ($content) {
                        $this->__do_stats();
                        $this->vars['content'] = $content;
                    }

                    if (method_exists($control, 'post_read')) {
                        $control->post_read();
                    }
                }
            }
        }

        $this->vars['page_type'] = 'view';

        $this->kwitang->feView($view_file, $this->vars);
    }

    /**
     * Method untuk menampilkan struktur, Mengambil data sesuai dengan
     * structure_name(atau id) yang diberikan.
     *
     * URL: /channel/structure_name
     *
     * / 1   / 2         / 3
     * / app / channel   / [structure_name]
     * / app / kategori  / [structure_name]
     *
     * @return void
     */
    public function structure($lang = '', $structure_name = '')
    {
        if ($structure_name == '') {
            $this->error404();
        }

        if ($this->__change_lang != '') {
            redirect(structure_url($structure_name, $this->__change_lang));
        }

        if ($lang !== '') {
            $this->__set_lang($lang);
        }

        if (empty($this->Structure)) {
            $this->load->model('Structure');
        }

        $structure = $this->Structure->get($structure_name);
        $data = $this->__structure_child($structure);

        if (! empty($data['view_file'])) {
            $this->__do_stats();
            $this->vars['current_structure'] = $data['current_structure'];
            $this->vars['page_type'] = 'structure';

            $this->kwitang->feView($data['view_file'], $this->vars);
        } elseif (! empty($data['current_sct'])) {
            if ($data['current_structure']->view_type == 'single') {
                redirect(content_url($data['current_sct'], null, $lang));
            } else {
                redirect(index_url($data['current_sct'], 1, null, $lang));
            }
        } else {
            $this->error404();
        }
    }

    /**
     * Pencarian
     */
    public function search()
    {
        $this->__do_stats();
        $result = array();

        // search query
        $searchq = $this->input->get_post('q');
        $this->vars['q'] = $searchq;
        // search section(content_type), comma separated:
        // CTName1-ModelName1,CTName2-ModelName2
        $section = $this->input->get_post('s');
        $this->vars['s'] = $section;
        // view name
        $var_v = $this->input->get_post('v');
        $view_file = empty($var_v) ? 'search' : $var_v;

        $page = $this->input->get_post('p');
        $item_perpage = $this->input->get_post('i');

        $search_ct = array();
        $section_arr = explode(',', $section);
        if (! empty($section) and is_array($section_arr)) {
            foreach ($section_arr as $section_value) {
                $search_ct[$section_value] = $section_value;
            }
        } else {
            $search_ct_tmp = $this->kwitang->ctList(true);
            foreach ($search_ct_tmp as $value) {
                $search_ct = array_merge($search_ct, $value);
            }
        }

        if (! empty($search_ct)) {
            foreach ($search_ct as $value) {
                $control = $this->kwitang->ctController($value);
                if ($control and method_exists($control, 'search')) {
                    $tmp = $control->search($searchq, $page, $item_perpage);
                    if ($tmp !== null) {
                        $result[$value] = $tmp;
                    }
                }
            }

        }


        $this->vars['page_type'] = 'search';
        $this->vars['result'] = $result;
        $this->kwitang->feView($view_file, $this->vars);
    }


    // Receive User Input
    // -------------------------------------------------------------------------

    /**
     * Captcha penjumlahan
     */
    public function captcha_add()
    {
        $a = mt_rand(0, 9);
        $b = mt_rand(0, 9);
        $c= $a + $b;
        $this->session->set_userdata('capadd', $c);
        $this->session->set_userdata('capadd_try', 0);

        echo $a.' + '.$b.' = ';
    }

    /**
     * Terima data
     */
    public function post()
    {
        // 3x trial
        $try = $this->session->userdata('capadd_try');
        $try++;
        if ($try > 3) {
            $this->session->unset_userdata('capadd');
        } else {
            $this->session->set_userdata('capadd_try', $try);
        }

        $all_post = $this->input->post();

        $data['status'] = 1;
        $data['message'] = $all_post;

        if (empty($all_post['sct']) or empty($all_post['capadd'])) {
            $data['status'] = 0;
            $data['message'] = 'Silakan lengkapi form data';
        } elseif ($all_post['capadd'] != $this->session->userdata('capadd')) {
            $data['status'] = 0;
            $data['message'] = 'CAPTCHA yang Anda masukkan salah atau sudah kadaluarsa. Coba muat ulang halaman untuk mendapatkan yang baru.';
        } else {
            $sct_name = $all_post['sct'];
            unset($all_post['sct']);
            $capadd = $all_post['capadd'];
            unset($all_post['capadd']);

            $this->load->model('Structure');
            $sct = $this->Structure->sctGet($sct_name);
            if (! empty($sct)) {
                $control = $this->kwitang->ctController($sct->content_type);
                $the_model = $this->kwitang->ctModel($sct->content_type, $control->mainModel());

                if (method_exists($the_model, 'insert')) {
                    $all_post['sct_id'] = $sct->id;
                    $ret = $the_model->insert($all_post);
                    if ($ret) {
                        $data['status'] = 1;
                        $data['message'] = 'Terimakasih, Data sudah disimpan';
                        $this->session->unset_userdata('capadd');
                    } else {
                        $data['status'] = 0;
                        $data['message'] = 'Tidak dapat menyimpan data';
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = 'Tipe konten tidak mendukung insert data.';
                }
            } else {
                $data['status'] = 0;
                $data['message'] = 'Terjadi Kesalahan, coba kembali di lain waktu.';
            }
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }


    // Utility
    // -------------------------------------------------------------------------

    /**
     * Tampilkan Pratinjau FrontEnd yang ada
     */

    public function preview()
    {
        $methods = array('structure'=>'channel',
                         'read'=>'read',
                         'archive'=>'archive',
                         'view'=>'view',
                         'error404'=>'not_found');
        $_uri = $this->uri->ruri_string();

        $params = explode('/', $_uri);
        $params = array_slice($params, 3);

        $frontend = array_shift($params);
        $this->kwitang->frontend = $frontend;
        $this->config->set_item('index_page', 'preview/'.$frontend.'/');

        if (isset($params[0])) {
            $action = array_search($params[0], $methods);
            if ($action !== false) {
                $_buang = array_shift($params);
                array_unshift($params, $this->vars['lang']);
                call_user_func_array(array($this, $action), $params);
            } elseif (isset($params[1])) {
                $action = array_search($params[1], $methods);
                if ($action !== false) {
                    $lang = array_shift($params);
                    $_buang = array_shift($params);
                    array_unshift($params, $lang);
                    call_user_func_array(array($this, $action), $params);
                }
            }
        } else {
            $this->kwitang->frontend = $frontend;
            $this->kwitang->feView('index', $this->vars);
        }
    }

    /**
     * Tampilkan error 404(Page Not found)
     */
    public function error404()
    {
        $view_file = '404';
        if (@file_exists(FRONT_PATH . $this->kwitang->frontend . '/views/404.php')) {
            set_status_header(404);
            $this->kwitang->feView($view_file, $this->vars);
        } else {
            show_404();
        }
    }

    public function logout()
    {
        $this->kwitang->logout();
    }


    // Internal
    // -------------------------------------------------------------------------

    /**
     * Catat kunjungan dan lakukan penghitungan statistik, jika webstat
     * di aktifkan
     *
     * @return void
     */
    private function __do_stats()
    {
        $enable_stat = kconfig('system', 'enable_stat');
        if ($enable_stat and ! $this->input->is_ajax_request() and ! $this->input->is_cli_request()) {
            $stats = $this->__webstat();
            $this->vars['stats'] = $stats;
        }
    }

    /**
     * Menggenerate statistik website
     *
     * @return void
     **/
    private function __webstat()
    {
        $counter = $this->kwitang->visitorCounter(true);

        $d_date  = $this->kwitang->timeGmt();
        $d_year  = date('Y', $d_date);
        $d_month = date('n', $d_date);
        $d_day   = date('j', $d_date);

        $visitor = 0;
        if (! empty($counter['visit_y']) && is_array($counter['visit_y'])) {
            foreach ($counter['visit_y'] as $value) {
                $visitor = $visitor + $value;
            }
        }
        $visitor_month = ! empty($counter['visit_m'][$d_year][$d_month]) ? $counter['visit_m'][$d_year][$d_month] : 0;
        $visitor_day   = ! empty($counter['visit_d'][$d_year][$d_month][$d_day]) ? $counter['visit_d'][$d_year][$d_month][$d_day] : 0;

        $hits = 0;
        if (! empty($counter['hits_y']) && is_array($counter['hits_y'])) {
            foreach ($counter['hits_y'] as $value) {
                $hits = $hits + $value;
            }
        }
        $hits_day   = ! empty($counter['hits_m'][$d_year][$d_month]) ? $counter['hits_m'][$d_year][$d_month] : 0;
        $hits_month = ! empty($counter['hits_d'][$d_year][$d_month][$d_day]) ? $counter['hits_d'][$d_year][$d_month][$d_day] : 0;

        $user_online = ! empty($counter['user_online']) ? $counter['user_online'] : 0;

        $stats = array(
                    'visitor'           => $visitor,
                    'hits'              => $hits,
                    'hits_day'          => $hits_day,
                    'hits_month'        => $hits_month,
                    'remote_ip_address' => $this->input->ip_address(),
                    'user_online'       => $user_online,
                    'visitor_month'     => $visitor_month,
                    'visitor_day'       => $visitor_day
                  );

        return $stats;
    }

    /**
     * Cek kode bahasa
     *
     * @param  String  Kode Bahasa
     * @return void
     */
    private function __set_lang($lang)
    {
        $langs = kconfig('system', 'langs');
        $langs = json_decode($langs);
        if ($langs) {
            $is_lang_exist = false;
            foreach ($langs as $value) {
                if ($value->code == $lang) {
                    $is_lang_exist = true;
                    break;
                }
            }

            if ($is_lang_exist) {
                $this->vars['lang'] = $lang;
            }
        }
    }

    /**
     *
     */
    private function __structure_child($structure)
    {
        $retval = array();

        if (! empty($structure->view_file)) {
            $retval['current_structure'] = $structure;
            $retval['view_file']         = $structure->view_file;
        } elseif (! empty($structure->view_sct)) {
            $retval['current_structure'] = $structure;

            $current_sct = $this->Structure->sctGet($structure->view_sct);
            if ($current_sct !== false) {
                $retval['current_sct'] = $current_sct;
            }
        } else {
            // try 1st sct
            $all_sct = $this->Structure->sctAll($structure->id);

            if (! empty($all_sct) && is_array($all_sct)) {
                $retval['current_structure'] = $structure;
                $retval['current_sct'] = $all_sct[0];
            } else {
                $childs = $this->Structure->getChilds($structure->id);
                if (! empty($childs) && is_array($childs)) {
                    $retval = $this->__structure_child($childs[0]);
                }
            }
        }

        return $retval;
    }
}

/* End of file backend/kwitang/controllers/app.php */
