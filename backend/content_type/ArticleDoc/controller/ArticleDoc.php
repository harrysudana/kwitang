<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Kontroller pada tipe-konten ArticleDoc
 *
 * @package  ContentType\ArticleDoc\Controller
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class ArticleDoc extends ContentTypeController
{
    public function title()       { return 'ArticleDoc'; }
    public function description() { return "Article with attarched Files (was:CT Download) +ReadCounter"; }
    public function version()     { return '1.0.1'; }
    public function mainModel()   { return 'ArticleDocModel'; }

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
            case 'del_file':
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
        if ( ! priv ('posting')) {
            redirect ('admin');
        }

        $this->load->helper('form');
        $this->__view('addedit');
    }

    /**
     * Simpan artikel baru
     */
    public function save()
    {
        if ( ! priv ('posting')) {
            $url = $this->session->userdata('referrer');
            redirect ($url);
        }

        $uploader = $this->__upload_image('foto', 'article_doc');

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
        $data['thumbnail']  = $uploader['thumbnail'];
        $data['foto']       = $uploader['foto'];

        $data['sct_id'] = $this->input->post('sct_id');
        $data['description']    = $this->input->post('description');

        $file1 = $this->__upload_file('file1', 'article_doc');
        $file2 = $this->__upload_file('file2', 'article_doc');
        $file3 = $this->__upload_file('file3', 'article_doc');
        $file4 = $this->__upload_file('file4', 'article_doc');
        $file5 = $this->__upload_file('file5', 'article_doc');

        $data['file1'] = $file1 ? $file1 : '';
        $data['file2'] = $file2 ? $file2 : '';
        $data['file3'] = $file3 ? $file3 : '';
        $data['file4'] = $file4 ? $file4 : '';
        $data['file5'] = $file5 ? $file5 : '';

        $article = $this->kwitang->ctModel('ArticleDoc', 'ArticleDocModel');
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
        $article = $this->__model($this->mainModel());
        $content = $article->get($this->vars['current_sct']->id, $article_id, false);

        $this->__view('addedit', array('content'=>$content));
    }

    /**
     * Ubah artikel
     */
    public function update()
    {
        if ( ! priv ('approve')) {
            $url = $this->session->userdata('referrer');
            redirect ($url);
        }

        $article = $this->__model($this->mainModel());
        $lama = $article->get($this->vars['current_sct']->id, $this->input->post('id'), false);
        $uploader = $this->__upload_image('foto', 'article_doc');

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

        if( ! empty($uploader['thumbnail']))
            $data['thumbnail']  = $uploader['thumbnail'];
        if( ! empty($uploader['foto']))
            $data['foto']       = $uploader['foto'];

        $file1 = $this->__upload_file('file1', 'article_doc');
        $file2 = $this->__upload_file('file2', 'article_doc');
        $file3 = $this->__upload_file('file3', 'article_doc');
        $file4 = $this->__upload_file('file4', 'article_doc');
        $file5 = $this->__upload_file('file5', 'article_doc');

        if ($file1) $data['file1'] = $file1;
        if ($file2) $data['file2'] = $file2;
        if ($file3) $data['file3'] = $file3;
        if ($file4) $data['file4'] = $file4;
        if ($file5) $data['file5'] = $file5;

        $ret = $article->update($data, $id);
        if ( ! $ret) {
            show_error('Gagal mengupdate data, silakan coba beberapa saat lagi');
        } else {
            if( ! empty($uploader['foto']) AND $lama->foto != $uploader['foto'] )
                @unlink($lama->foto);
            if( ! empty($uploader['thumbnail']) AND $lama->thumbnail != $uploader['thumbnail'] )
                @unlink($lama->thumbnail);

            if ($file1) @unlink($lama->file1);
            if ($file2) @unlink($lama->file2);
            if ($file3) @unlink($lama->file3);
            if ($file4) @unlink($lama->file4);
            if ($file5) @unlink($lama->file5);

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
        $url = null;
        $current_sct = $this->vars['current_sct'];

        if (priv ('manage')) {
            $article = $this->__model($this->mainModel());
            $lama    = $article->get($current_sct->id, $article_id);
            $ret     = $article->delete($article_id);

            if ($ret) {
                @unlink($lama->thumbnail);
                @unlink($lama->foto);
            }

            $url = $this->session->userdata('referrer');
        }

        if ( empty ($url)) {
            $url = site_url ('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id);
        }

        redirect ($url, 'refresh');
    }

    /**
     * Hapus foto dan thumbnail
     */
    public function delg($article_id)
    {
        $current_sct = $this->vars['current_sct'];

        if (priv ('approve')) {
            $article = $this->__model($this->mainModel());
            $lama    = $article->get($current_sct->id, $article_id, false);

            $data['thumbnail'] = '';
            $data['foto']      = '';

            $ret = $article->update($data, $article_id);

            if ($ret) {
                @unlink($lama->thumbnail);
                @unlink($lama->foto);
            }
        }

        redirect (site_url ('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/edit/'.$article_id));
    }

    /**
     * Hapus file
     */
    public function del_file($article_id, $number = null)
    {
        $current_sct = $this->vars['current_sct'];

        if (priv ('approve') AND $number !== null) {
            $article = $this->__model($this->mainModel());
            $lama    = $article->get($current_sct->id, $article_id, false);

            $data['file'.$number] = '';

            $ret = $article->update($data, $article_id);

            if ($ret) {
                $file = 'file'.$number;
                @unlink($lama->$file);
            }
        }

        redirect (site_url ('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/edit/'.$article_id));
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

        $article = $this->__model($this->mainModel());
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
            return;
        }

        $retval = array('thumbnail' => '', 'foto' => '');

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
                $retval['foto'] = $config['upload_path'] . '/' . $data['file_name'];

                // crop gambar tadi, simpan jadi thumbnail
                $config['image_library']  = 'gd2';
                $config['source_image']   = $retval['foto'];
                $config['create_thumb']   = true;
                $config['maintain_ratio'] = true;
                $config['width']          = kconfig ('system', 'thumbnail_width', 107);
                $config['height']         = kconfig ('system', 'thumbnail_height', 107);

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

    private function __upload_file($field_name, $folder_name)
    {
        $retval = false;

        if ( ! empty($_FILES[$field_name]['name'])) {
            $config['upload_path'] = $this->base_upload.$folder_name;
            if ( ! is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], DIR_WRITE_MODE, true);
            }

            $config['allowed_types'] = '*';
            $config['overwrite']     = false;
            $config['remove_spaces'] = true;
            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload($field_name)) {
                show_error($this->upload->display_errors());
            } else {
                $data = $this->upload->data();
                $retval = $config['upload_path'].'/'.$data['file_name'];
            }
        }

        return $retval;
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
