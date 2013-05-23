<?php
if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/**
 * RSS Feed Generator
 *
 * @package  Kwitang\Controllers
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class Rss extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

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
     * Generate RSS feed based on two parameter received.
     *
     * if no, or single parameter is passed, will generate html page consisting
     * list of available rss link.
     *
     * @param String $s_name   Structure name
     * @param String $sct_name SCT name
     */
    public function index($s_name = '', $sct_name = '')
    {
        $this->load->helper('html');
        $this->load->model('Structure');

        $data['feed_title']     = kconfig('system', 'site_name', site_url());
        $data['description']    = kconfig('system', 'site_slogan', 'Really Simple Syndication - RSS');

        if (empty($s_name)) {
            $data['st12'] = $this->Structure->allTree();
            $this->kwitang->feView('rss/index', $data);
        } elseif (empty($sct_name)) {
            // Struktur
            $data['struc']      = $this->Structure->get($s_name);
            $data['feed_url']   = base_url('rss/' . $s_name);
            $arr_sct            = $this->Structure->sctAll($data['struc']->id);

            $this->load->helper('kwitang_fe_helper');
            $data1 = array();
            foreach ($arr_sct as $ar) {
                $data2 = array();
                $data2[] = get_content_page($ar->name, array('item_perpage' => 10));
                $data1 = array_merge($data1, $data2);
            }

            $data['data'] = $data1;

            header('Content-Type: application/rss+xml');
            $this->kwitang->feView('rss/rss-multi', $data);
        } else {
            $data['struc'] = $this->Structure->get($s_name);
            $data['feed_url']       = base_url('rss/' . $s_name . '/' . $sct_name);

            $this->load->helper('kwitang_fe_helper');
            $data['data'] = get_content_page($sct_name, array('item_perpage' => 10));

            header('Content-Type: application/rss+xml');
            $this->kwitang->feView('rss/rss', $data);
        }
    }
}
