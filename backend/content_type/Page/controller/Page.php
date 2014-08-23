<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Kontroller pada tipe-konten Page
 *
 * @package  ContentType\Page\Controller
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class Page extends ContentTypeController
{
    public function title()       { return 'Page'; }
    public function description() { return "Fit Single Page Content +ReadCounter"; }
    public function version()     { return '1.0.1'; }
    public function mainModel()   { return 'PageModel'; }

    /**
     * Persiapan
     */
    public function prepare($action)
    {
        switch ($action) {
            case 'save':
            case 'update':
            case 'delete':
            case 'data_json':
                $this->vars['admin_header'] = false;
                $this->vars['admin_footer'] = false;
                break;
            case 'add':
            case 'edit':
            case 'display':
                $this->vars['js_files'][]  = asset_url('dtables/js/jquery.dataTables.min.js');
                $this->vars['js_files'][] = asset_url('ckeditor/ckeditor.js');
                $this->vars['js_files'][] = asset_url('js/bootstrap-tagmanager.js');
                $this->vars['css_files'][] = asset_url('dtables/css/jquery.dataTables.min.css');
                $this->vars['css_files'][] = asset_url('css/bootstrap-tagmanager.css');
                break;
        }
    }

    /**
     * Halaman untuk menampilkan data page
     */
    public function display($page_number=1)
    {
        $article = $this->__model('PageModel');
        $content = $article->get($this->vars['current_sct']->id, 'last', false);

        $this->load->helper('form');
        $this->__view('addedit', array('content'=>$content));
    }

    /**
     * Form tambah page
     */
    public function add()
    {
        if ( ! priv ('posting')) {
            redirect ('admin');
        }

        $this->load->helper('form');
        $this->__view('addedit');
    }

    /**
     * Simpan page baru
     */
    public function save()
    {
        if ( ! priv ('posting')) {
            $url = $this->session->userdata('referrer');
            redirect ($url);
        }

        $data = array();
        $data['title']      = substr($this->input->post('title'), 0, 120);
        $data['slug']       = substr(url_title($data['title']), 0, 120);
        $data['author']     = $this->vars['current_user']->fullname;
        $pub_date = $this->input->post('pub_date');
        $pub_date = to_gmt(strtotime($pub_date));
        $data['pub_date']   = date('Y-m-d H:i:s', $pub_date);
        $data['body']       = $this->input->post('body', false);
        $data['tags']       = $this->input->post('tags');
        $data['active']     = ($this->input->post('active') == 'on' ? 1 : 0);

        $data['sct_id'] = $this->input->post('sct_id');
        $data['description']    = $this->input->post('description');

        $article = $this->kwitang->ctModel('Page', 'PageModel');
        $ret = $article->insert($data);
        if ( ! $ret) {
            show_error('Gagal menambah data, silakan coba beberapa saat lagi');
        } else {
            $last_id = $ret;

            $close = $this->input->post('closethis');
            $current_sct = $this->vars['current_sct'];

            if(isset($last_id) && empty($close)) {
                redirect(site_url('/admin/content/' . $current_sct->structure_id . '/' . $current_sct->id . '/edit/' . $last_id), 'refresh');
            } else {
                redirect(site_url('/admin/content/' . $current_sct->structure_id . '/' . $current_sct->id), 'refresh');
            }
        }
    }

    /**
     * Form ubah page
     */
    public function edit($article_id)
    {
        if (empty($article_id)) {
            show_404();
        }

        $this->load->helper('form');
        $article = $this->__model('PageModel');
        $content = $article->get($this->vars['current_sct']->id, $article_id, false);

        $this->__view('addedit', array('content'=>$content));
    }

    /**
     * Ubah page
     */
    public function update()
    {
        if ( ! priv ('approve')) {
            $url = $this->session->userdata('referrer');
            redirect ($url);
        }

        $article = $this->__model('PageModel');
        $lama = $article->get($this->vars['current_sct']->id, $this->input->post('id'), false);

        $data = array();
        $id                 = $this->input->post('id');
        $data['title']      = substr($this->input->post('title'), 0, 120);
        $data['slug']       = substr(url_title($data['title']), 0, 120);
        $data['author']     = $this->vars['current_user']->fullname;
        $pub_date = $this->input->post('pub_date');
        $pub_date = to_gmt(strtotime($pub_date));
        $data['pub_date']   = date('Y-m-d H:i:s', $pub_date);
        $data['body']       = $this->input->post('body', false);
        $data['tags']       = $this->input->post('tags');
        $data['active']     = ($this->input->post('active') == 'on' ? 1 : 0);

        $data['description']    = $this->input->post('description');
        $data['sct_id'] = $this->vars['current_sct']->id;

        $ret = $article->update($data, $id);
        if ( ! $ret) {
            show_error('Gagal mengupdate data, silakan coba beberapa saat lagi');
        } else {
            $close = $this->input->post('closethis');
            $current_sct = $this->vars['current_sct'];

            if ( empty($close)) {
                $url = site_url('/admin/content/' . $current_sct->structure_id . '/' . $current_sct->id . '/edit/' . $this->input->post('id'));
            } else {
                $url = $this->session->userdata('referrer');
                if( empty($url))
                    $url = site_url('/admin/content/' . $current_sct->structure_id);
            }

            redirect($url, 'refresh');
        }
    }

    /**
     * Hapus page
     */
    public function delete($article_id)
    {
        $url = null;
        $current_sct = $this->vars['current_sct'];

        if (priv ('manage')) {
            $article = $this->__model('PageModel');
            $lama    = $article->get($current_sct->id, $article_id);
            $ret     = $article->delete($article_id);

            $url = $this->session->userdata('referrer');
        }

        if ( empty ($url)) {
            $url = site_url ('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id);
        }

        redirect ($url, 'refresh');
    }


    /**
     * Output json untuk digunakan pada datatables
     */
    public function data_json()
    {
        $kolom        = array('pub_date', 'title', 'author', 'active');
        $disp_start   = intval($this->input->get('iDisplayStart', true));
        $item_perpage = intval($this->input->get('iDisplayLength', true));
        $orders       = array();
        $searchs      = array();
        $current_sct  = $this->vars['current_sct'];

        // searching
        $tmp = $this->input->get('sSearch', true);
        if ( ! empty ($tmp)) {
            $searchs = array('title' => $tmp);
        }

        // ordering
        $sort_total = intval($this->input->get('iSortingCols', true));
        if ( $sort_total > 0 ) {
            for ( $i=0 ; $i<count($kolom) ; $i++ ) {
                $sort_x     = $this->input->get('iSortCol_' . $i, true);
                $sort_yn    = $this->input->get('bSortable_' . $sort_x, true);
                $sort_dir   = $this->input->get('sSortDir_' . $i, true);

                if ( $sort_yn == "true" ) {
                    $orders[$kolom[$sort_x]] = $sort_dir;
                }
            }
        }

        $article = $this->__model('PageModel');
        $content = $article->all($current_sct->id, $item_perpage, $disp_start, $orders, $searchs, false);

        $json = array(
                    "sEcho"                => intval ($this->input->get('sEcho', true)),
                    "iTotalRecords"        => $content['total_all'],
                    "iTotalDisplayRecords" => $content['total_found'],
                    "aaData" => array()
                );

        $controller_uri = 'admin/content/' . $this->vars['current_sct']->structure_id . '/' . $this->vars['current_sct']->id . '/';
        foreach($content['data'] as $c) {
            $edit_url = site_url($controller_uri . 'edit/' . $c->id);
            $del_url  = site_url($controller_uri . 'delete/' . $c->id);
            $d = array(
                    kdate(from_gmt($c->pub_date)),
                    '<a href="' . $edit_url . '">' . $c->title .'</a>',
                    $c->author,
                    ($c->active ? '<i class="icon-green icon-ok"></i>' : '<i class="icon-red icon-remove"></i>')
                 );
            if (priv('manage')) {
                $d[] = '<a href="' . $del_url . '" title="Hapus ' . $c->title . '" onclick="if( ! confirm(\'Hapus ' . $c->title . '\')) return false;"><i class="icon-trash"></i></a>';
            }

            $json['aaData'][] = $d;
        }

        header('Content-type: application/json');
        echo json_encode($json);
    }


    /**
     * Increment the counter after someone access to it.
     *
     * @return void
     */
    public function post_read()
    {
        if ( ! empty($this->vars['content']->id)) {
            $model   = $this->__model($this->mainModel());
            $counter = ($this->vars['content']->counter * 1) + 1;
            $ret = $model->update(array('counter' => $counter), $this->vars['content']->id);
        }
    }
}
