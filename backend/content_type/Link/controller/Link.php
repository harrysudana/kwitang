<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Kontroller pada tipe-konten Link
 *
 * @package  ContentType\Link\Controller
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class Link extends ContentTypeController
{
    public function title()       { return 'Link'; }
    public function description() { return "Tipe Konten Link"; }
    public function version()     { return '1.0.0'; }
    public function mainModel()   { return 'LinkModel'; }

    /**
     * Persiapan
     */
    public function prepare($action)
    {
        switch ($action) {
            case 'display':
                $this->session->set_userdata('referrer', current_url());
                $this->vars['css_files'][] = asset_url('dtables/css/jquery.dataTables.min.css');
                $this->vars['js_files'][]  = asset_url('dtables/js/jquery.dataTables.min.js');
                break;
            case 'save':
            case 'update':
            case 'delete':
            case 'delg':
            case 'data_json':
                $this->vars['admin_header'] = false;
                $this->vars['admin_footer'] = false;
                break;
            case 'add':
            case 'edit':
                $this->vars['return_url'] = $this->session->userdata('referrer');
                break;
        }
    }

    /**
     * Halaman untuk menampilkan data teks
     */
    public function display($page_number=1)
    {
        $this->__view('list');
    }

    /**
     * Form tambah teks
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
     * Simpan teks baru
     */
    public function save()
    {
        if ( ! priv ('posting')) {
            $url = $this->session->userdata('referrer');
            redirect ($url);
        }

        $data = array();
        $data['title']      = substr($this->input->post('title'), 0, 120);
        $data['url']        = $this->input->post('url', false);
        $data['active']     = ($this->input->post('active') == 'on' ? 1 : 0);
        $image = $this->__upload_image('image', 'link');
        if ($image) {
            $data['image'] = $image;
        }

        $data['sct_id']     = $this->vars['current_sct']->id;

        $text = $this->kwitang->ctModel('Link', 'LinkModel');
        $ret = $text->insert($data);
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
     * Form ubah teks
     */
    public function edit($id)
    {
        if (empty($id)) {
            show_404();
        }

        $this->load->helper('form');
        $text = $this->__model('LinkModel');
        $content = $text->get($this->vars['current_sct']->id, $id, false);

        $this->__view('addedit', array('content'=>$content));
    }

    /**
     * Ubah teks
     */
    public function update()
    {
        if ( ! priv ('approve')) {
            $url = $this->session->userdata('referrer');
            redirect ($url);
        }

        $text = $this->__model('LinkModel');
        $lama = $text->get($this->input->post('id'), false);

        $data = array();
        $id                 = $this->input->post('id');
        $data['title']      = substr($this->input->post('title'), 0, 120);
        $data['url']        = $this->input->post('url', false);
        $data['active']     = ($this->input->post('active') == 'on' ? 1 : 0);
        $image = $this->__upload_image('image', 'link');
        if ($image) {
            $data['image'] = $image;
        }

        $data['sct_id']     = $this->vars['current_sct']->id;

        $ret = $text->update($data, $id);
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
     * Hapus teks beserta thumbnail dan fotonya
     */
    public function delete($id)
    {
        $current_sct = $this->vars['current_sct'];
        $url  = null;
        $text = $this->__model('LinkModel');

        if (priv ('manage')) {
            $lama    = $text->get($current_sct->id, $id);
            $ret     = $text->delete($id);

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
        $kolom        = array('title', 'active');
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

        $text = $this->__model('LinkModel');
        $content = $text->all($current_sct->id, $item_perpage, $disp_start, $orders, $searchs, false);

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
                    '<a href="' . $edit_url . '">' . $c->title .'</a>',
                    ($c->active ? '<i class="icon-green icon-ok"></i>' : '<i class="icon-red icon-remove"></i>'));
            if (priv ('manage')) {
                $d[] = '<a href="' . $del_url . '" title="Hapus ' . $c->title . '" onclick="if( ! confirm(\'Hapus ' . $c->title . '\')) return false;"><i class="icon-trash"></i></a>';
            }
            $json['aaData'][] = $d;
        }

        header('Content-type: application/json');
        echo json_encode($json);
    }

    /**
     * Upload foto dan thumbnail
     */
    private function __upload_image($field_name, $folder_destination)
    {
        if ( ! priv ('posting')) {
            return false;
        }

        $retval = false;
        if ( ! empty($_FILES[$field_name]['name'])) {
            $config['upload_path'] = rtrim (kconfig ('system', 'base_upload', 'upload'), '/').'/'.$folder_destination;
            if ( ! is_dir($config['upload_path'])) {
                $r = mkdir($config['upload_path'], DIR_WRITE_MODE, true);
                if ( ! $r) {
                    show_error('<p>Failed to create ' . $config['upload_path'] . ' folder.</p>');
                }
            }

            $config['allowed_types'] = kconfig ('system', 'allowed_types', 'gif|jpg|png|jpeg');
            $config['max_size']      = kconfig ('system', 'image_max_size', 1024);
            $config['max_width']     = kconfig ('system', 'image_max_width', 1000);
            $config['max_height']    = kconfig ('system', 'image_max_height', 1000);

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload($field_name)) {
                show_error($this->upload->display_errors());
            } else {
                $data = $this->upload->data();
                $retval = $config['upload_path'] . '/' . $data['file_name'];
            }
        }

        return $retval;
    }
}
