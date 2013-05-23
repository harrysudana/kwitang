<?php
if (! defined('BASEPATH')) {
    exit ('No direct script access allowed');
}

/**
 * Authentication Controller
 *
 * @package  Kwitang\Controllers
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class User extends CI_Controller
{
    public $vars = array();

    public function __construct()
    {
        parent::__construct();

        $is_logged_in = $this->kwitang->authenticate();
        $admin_lang   = 'indonesia';

        if ($is_logged_in) {
            $current_user = $this->kwitang->currentUser();
            $admin_lang   = user_config($current_user->username, 'language', 'indonesia');

            if ($current_user->level != 'ADMIN'
                and $current_user->level != 'AUTHOR'
                and current_url() != site_url('admin/logout')) {
                redirect(site_url());
            } elseif (current_url() == site_url('admin/login') or current_url() == site_url('admin/validate')) {
                redirect(site_url('admin'));
            }
        }

        $this->load->library('user_agent');

        $this->load->helper('language');
        $this->lang->load('admin', $admin_lang);

        $this->load->helper('kwitang_fe');
        $this->load->helper('form');
        $this->load->helper('text');

        // Meta data
        $this->vars['title']       = 'Kwitang | Web Content Management System';
        $this->vars['description'] = 'Administration page of kwitang';

        // $cache_lifetime in minutes
        $cache_lifetime = kconfig('system', 'cache_lifetime', 0);
        if ($cache_lifetime > 0) {
            $this->output->cache($cache_lifetime);
        }

        if (kconfig('system', 'profiler', 0) == 1) {
            $this->output->enable_profiler(true);
        }
    }


    /**
     * Index
     */
    public function index()
    {
        redirect(site_url());
    }


    /**
     * Display login form
     * @return [type] [description]
     */
    public function login()
    {
        $this->load->helper('form');
        $this->__view('login');
    }

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
}
