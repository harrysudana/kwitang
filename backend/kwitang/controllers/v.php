<?php
if (! defined('BASEPATH')) {
    exit ('No direct script access allowed');
}
/**
 * V - the custom view controller.
 *
 * @package  Kwitang\Controllers
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class V extends CI_Controller
{
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

        // $cache_lifetime in minutes
        $cache_lifetime = kconfig('system', 'cache_lifetime', 0);
        if ($cache_lifetime > 0) {
            $this->output->cache($cache_lifetime);
        }

        if (kconfig('system', 'profiler', 0) == 1) {
            $this->output->enable_profiler(true);
        }
    }

    public function index()
    {
        if (empty ($this->Structure)) {
            $this->load->model('Structure');
        }

        $view_file =  trim(uri_string(), 'v/');
        $this->vars['page_type'] = 'custom_view';

        // security ..?
        if (empty($view_file)) {
            redirect(site_url('not_found'));
        }
        $this->kwitang->feView($view_file, $this->vars);
    }
}

/* End of file backend/kwitang/controllers/v.php */
