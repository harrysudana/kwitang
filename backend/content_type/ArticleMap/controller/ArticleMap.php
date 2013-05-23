<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Kontroller pada tipe-konten ArticleMap
 *
 * @package  ContentType\ArticleMap\Controller
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class ArticleMap extends ContentTypeController
{
    public function title()       { return 'Artikel-Peta'; }
    public function description() { return "Tipe Konten Artikel-Peta"; }
    public function version()     { return '1.0.0'; }
    public function mainModel()   { return 'ArticleMapModel'; }

    /**
     * Persiapan
     */
    public function prepare($action)
    {
        switch ($action) {
            case 'display':
                $this->session->set_userdata('referrer', current_url());
                $this->vars['css_files'][] = asset_url('dtables/css/jquery.dataTables.css');
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
                $this->vars['js_files'][] = asset_url('ckeditor/ckeditor.js');
                $this->vars['js_files'][] = asset_url('js/bootstrap-tagmanager.js');
                $this->vars['css_files'][] = asset_url('css/bootstrap-tagmanager.css');
                break;
        }
    }

    /**
     * Halaman untuk menampilkan data artikel
     */
    public function display($page_number=1)
    {
        $this->__view('list');
    }

    /**
     * Form tambah artikel
     */
    public function add()
    {
        $this->load->helper('form');
        $this->__view('addedit');
    }

    /**
     * Simpan artikel baru
     */
    public function save()
    {
        $uploader = $this->__upload_image('foto', 'articles');

        $data = array();
        $data['title']      = substr($this->input->post('title'), 0, 120);
        $data['slug']       = substr(url_title($data['title']), 0, 120);
        $data['author']     = $this->vars['current_user']->fullname;
        $pub_date = $this->input->post('pub_date');
        $pub_date = to_gmt(strtotime($pub_date));
        $data['pub_date']   = date('Y-m-d H:i:s', $pub_date);
        $data['body']       = $this->input->post('body');
        $data['tags']       = $this->input->post('tags');
        $data['lat']        = $this->input->post('lat');
        $data['lng']        = $this->input->post('lng');
        $data['active']     = ($this->input->post('active') == 'on' ? 1 : 0);
        $data['thumbnail']  = $uploader['thumbnail'];
        $data['foto']       = $uploader['foto'];

        $data['sct_id'] = $this->input->post('sct_id');
        $data['description']    = $this->input->post('description');

        $article = $this->kwitang->ctModel('ArticleMap', 'ArticleMapModel');
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
     * Form ubah artikel
     */
    public function edit($article_id)
    {
        if (empty($article_id)) {
            show_404();
        }

        $this->load->helper('form');
        $article = $this->__model('ArticleMapModel');
        $content = $article->get(null, $article_id, false);

        $this->__view('addedit', array('content'=>$content));
    }

    /**
     * Ubah artikel
     */
    public function update()
    {
        $article = $this->__model('ArticleMapModel');
        $lama = $article->get(null, $this->input->post('id'), false);
        $uploader = $this->__upload_image('foto', 'articles');

        $data = array();
        $id                 = $this->input->post('id');
        $data['title']      = substr($this->input->post('title'), 0, 120);
        $data['slug']       = substr(url_title($data['title']), 0, 120);
        $data['author']     = $this->vars['current_user']->fullname;
        $pub_date = $this->input->post('pub_date');
        $pub_date = to_gmt(strtotime($pub_date));
        $data['pub_date']   = date('Y-m-d H:i:s', $pub_date);
        $data['body']       = $this->input->post('body');
        $data['tags']       = $this->input->post('tags');
        $data['lat']        = $this->input->post('lat');
        $data['lng']        = $this->input->post('lng');
        $data['active']     = ($this->input->post('active') == 'on' ? 1 : 0);

        $data['description']    = $this->input->post('description');
        $data['sct_id'] = $this->vars['current_sct']->id;

        if( ! empty($uploader['thumbnail']))
            $data['thumbnail']  = $uploader['thumbnail'];
        if( ! empty($uploader['foto']))
            $data['foto']       = $uploader['foto'];

        $ret = $article->update($data, $id);
        if ( ! $ret) {
            show_error('Gagal mengupdate data, silakan coba beberapa saat lagi');
        } else {
            if( ! empty($uploader['foto']) AND $lama->foto != $uploader['foto'] )
                @unlink($lama->foto);
            if( ! empty($uploader['thumbnail']) AND $lama->thumbnail != $uploader['thumbnail'] )
                @unlink($lama->thumbnail);

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
     * Hapus artikel beserta thumbnail dan fotonya
     */
    public function delete($article_id)
    {
        $article = $this->__model('ArticleMapModel');
        $lama    = $article->get(null, $article_id);
        $ret     = $article->delete($article_id);

        if ($ret) {
            @unlink($lama->thumbnail);
            @unlink($lama->foto);
        }

        $url = $this->session->userdata('referrer');
        if ( empty ($url)) {
            $current_sct = $this->vars['current_sct'];
            $url         = site_url ('/admin/content/'
                                    .$current_sct->structure_id.'/'
                                    .$current_sct->id);
        }

        redirect ($url, 'refresh');
    }

    /**
     * Hapus foto dan thumbnail
     */
    public function delg($article_id)
    {
        $article = $this->__model('ArticleMapModel');
        $lama    = $article->get(null, $article_id, false);

        $data['thumbnail'] = '';
        $data['foto']      = '';

        $ret = $article->update($data, $article_id);

        if ($ret) {
            @unlink($lama->thumbnail);
            @unlink($lama->foto);
        }

        redirect (site_url ('/admin/content/'
                            .$this->vars['current_sct']->structure_id.'/'
                            .$this->vars['current_sct']->id.'/edit/'
                            .$article_id));
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

        $article = $this->__model('ArticleMapModel');
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
     * Upload foto dan thumbnail
     */
    private function __upload_image($field_name, $folder_destination)
    {
        $retval = array('thumbnail' => '', 'foto' => '');

        if ( !  empty($_FILES[$field_name]['name'])) {
            $config['upload_path'] = rtrim (kconfig ('system', 'base_upload', 'upload'), '/').'/'.$folder_destination;
            if ( ! is_dir($config['upload_path'])) {
                $r = mkdir($config['upload_path'], DIR_WRITE_MODE, true);
                if ( ! $r) {
                    show_error('<p>Failed to create ' . $config['upload_path'] . ' folder.</p>');
                }
            }

            $config['allowed_types']    = kconfig ('system', 'allowed_types', 'gif|jpg|png|jpeg');
            $config['max_size']         = kconfig ('system', 'image_max_size', 1024);
            $config['max_width']        = kconfig ('system', 'image_max_width', 1000);
            $config['max_height']       = kconfig ('system', 'image_max_height', 1000);

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload($field_name)) {
                show_error($this->upload->display_errors());
            } else {
                $data = $this->upload->data();
                $retval['foto'] = $config['upload_path'] . '/' . $data['file_name'];

                // crop gambar tadi, simpan jadi thumbnail
                $config['image_library']    = 'gd2';
                $config['source_image']     = $retval['foto'];
                $config['create_thumb']     = true;
                $config['maintain_ratio']   = true;
                $config['width']            = 107;
                $config['height']           = 107;

                $this->load->library('image_lib', $config);
                $this->image_lib->resize();
                $ext = '.jpg';

                if (substr($retval['foto'], -5, 1) == '.') {
                    $ext = substr($retval['foto'], -5);
                    $thumb_name = substr(basename($retval['foto']), 0, -5) . '_thumb' . $ext;
                } else  if (substr($retval['foto'], -4, 1) == '.') {
                    $ext = substr($retval['foto'], -4);
                    $thumb_name = substr(basename($retval['foto']), 0, -4) . '_thumb' . $ext;
                }
                $retval['thumbnail'] = $config['upload_path'] . '/' . $thumb_name;
            }
        }

        return $retval;
    }
}
