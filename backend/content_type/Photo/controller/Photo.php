<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Kontroller pada tipe-konten Photo
 *
 * @package  ContentType\Photo\Controller
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class Photo extends ContentTypeController
{
    public function title()       { return 'Photo'; }
    public function description() { return "Photo Gallery +ReadCounter"; }
    public function version()     { return '1.0.1'; }
    public function mainModel()   { return 'PhotoModel'; }

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

    public function display($page_number=1)
    {
        $this->__view('list');
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
                $sort_x     = $this->input->get('iSortCol_'.$i, true);
                $sort_yn    = $this->input->get('bSortable_'.$sort_x, true);
                $sort_dir   = $this->input->get('sSortDir_'.$i, true);

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

        $controller_uri = 'admin/content/'.$this->vars['current_sct']->structure_id.'/'.$this->vars['current_sct']->id.'/';
        $langs = json_decode (kconfig ('system', 'langs'));
        $alang = array();
        if ($langs) {
            foreach ($langs as $val) {
                $alang[$val->code] = $val->name;
            }
        }

        foreach($content['data'] as $c) {
            $edit_url = site_url($controller_uri.'edit/'.$c->id);
            $del_url  = site_url($controller_uri.'delete/'.$c->id);

            $d = array(
                    kdate(from_gmt($c->pub_date)),
                    '<a href="'.$edit_url.'">'.$c->title.'</a>',
                    $c->author,
                    ($c->active ? '<i class="icon-green icon-ok"></i>' : '<i class="icon-red icon-remove"></i>'));
            if (priv ('manage')) {
                $d[] = '<a href="'.$del_url.'" title="Hapus '.$c->title.'" onclick="if( ! confirm(\'Hapus '.$c->title.'\')) return false;"><i class="icon-trash"></i></a>';
            }
            $json['aaData'][] = $d;
        }

        header('Content-type: application/json');
        echo json_encode($json);
    }

    /**
     * Form tambah Artikel Indonesia&Inggris
     */
    public function add()
    {
        $this->load->helper('form');
        $this->__view('addedit');
    }

    public function save()
    {
        if ( ! priv ('posting')) {
            $url = $this->session->userdata('referrer');
            redirect ($url);
        }

        $config['upload_path'] = rtrim (kconfig ('system', 'base_upload', 'upload'), '/').'/photos';
        if ( ! is_dir($config['upload_path'])) {
            if ( ! mkdir($config['upload_path'], DIR_WRITE_MODE, TRUE)) {
                show_error('<p>failed to create '.$config['upload_path'].' folder.</p>');
            }
        }

        $config['allowed_types']    = kconfig ('system', 'allowed_types', 'gif|jpg|png|jpeg');
        $config['max_size']         = kconfig ('system', 'image_max_size', 1024);
        $config['max_width']        = kconfig ('system', 'image_max_width', 1000);
        $config['max_height']       = kconfig ('system', 'image_max_height', 1000);

        $this->load->library('upload', $config);

        $data = array();

        for ($i=1; $i<=20; $i++) {
            $f = 'foto'.$i;
            if (! empty($_FILES[$f]['name'])) {
                if ( ! $this->upload->do_upload($f)) {
                    $error = $this->upload->display_errors();
                } else {
                    $fu = $this->upload->data();
                    $data[$f] = $config['upload_path'].'/'.$fu['file_name'];
                }
            }
        }

        // build array datanya
        // structure,title,slug,author,pub_date,thumbnail,foto,
        // description,body,tags,active)
        if (empty ($error)) {
            $data['sct_id'] = $this->vars['current_sct']->id;
            $data['author']         = kuser()->username;
            $data['title']          = $this->input->post('title');
            $data['pub_date']       = date('Y-m-d H:i:s', to_gmt($this->input->post('pub_date')));
            $data['description']    = $this->input->post('description');
            $data['tags']           = $this->input->post('tags');
            $data['active']         = (($this->input->post('active') == 'on' || $this->input->post('active') == 1) ? 1 : 0);

            for ($i=1; $i<=20; $i++) {
                $d = 'description'.$i;
                $data[$d] = $this->input->post($d);
            }

            $photo = $this->kwitang->ctModel('Photo', $this->mainModel());

            $ret = $photo->insert($data);
            if ( ! $ret) {
                $error = 'Gagal menambah data, silakan coba beberapa saat lagi';
            } else {
                $last_id = $ret;
            }
        }

        if ( ! empty ($error)) {
            show_error($error);
        } else {
            if ( ! empty($last_id)) {
                $close = $this->input->post('closethis');
                $current_sct = $this->vars['current_sct'];

                if (isset($last_id) && empty($close)) {
                    $this->session->keep_flashdata('referrer');
                    redirect(site_url('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/edit/'.$last_id), 'refresh');
                } else {
                    redirect(site_url('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id), 'refresh');
                }
            } else {
                redirect(site_url('/admin/content/'.$this->curr_sct->structure_id.'/'.$this->curr_sct->id), 'refresh');
            }
        }
    }

    /**
     * Form ubah Photo
     */
    public function edit($id)
    {
        if (empty ($id)) {
            show_404();
        }

        $this->load->helper('form');
        $photo = $this->__model($this->mainModel());
        $content = $photo->get($this->vars['current_sct']->id, $id, false);

        $this->__view('addedit', array('content'=>$content));
    }

    public function update()
    {
        if ( ! priv ('approve')) {
            $url = $this->session->userdata('referrer');
            redirect ($url);
        }

        $config['upload_path'] = rtrim (kconfig ('system', 'base_upload', 'upload'), '/').'/photos';
        if ( ! is_dir($config['upload_path'])) {
            if ( ! mkdir($config['upload_path'], DIR_WRITE_MODE, TRUE)) {
                show_error('<p>failed to create '.$config['upload_path'].' folder.</p>');
            }
        }

        $config['allowed_types']    = kconfig ('system', 'allowed_types', 'gif|jpg|png');
        $config['max_size']         = kconfig ('system', 'image_max_size', '1024');
        $config['max_width']        = kconfig ('system', 'image_max_width', '1000');
        $config['max_height']       = kconfig ('system', 'image_max_height', '800');

        $this->load->library('upload', $config);

        $photo = $this->kwitang->ctModel('Photo', $this->mainModel());
        $id    = $this->input->post('id');
        $lama  = $photo->get(null, $id);

        $data  = array();
        $error = '';
        $files_to_delete = array();

        for ($i=1; $i<=20; $i++) {
            $f = 'foto'.$i;
            if ( ! empty($_FILES[$f]['name'])) {
                $files_to_delete[] = $lama->$f;
                if (!$this->upload->do_upload($f)) {
                    $error .= $this->upload->display_errors().'<br><br>';
                } else {
                    $fu = $this->upload->data();
                    $data[$f] = $config['upload_path'].'/'.$fu['file_name'];
                }
            }
        }

        if ( ! empty($error)) {
            show_error($error);
        } else {
            $title       = $this->input->post('title');
            $pub_date    = date('Y-m-d H:i:s', to_gmt($this->input->post('pub_date')));
            $description = $this->input->post('description');
            $tags        = $this->input->post('tags');

            if( ! empty( $title))       $data['title']       = $title;
            if( ! empty( $pub_date))    $data['pub_date']    = $pub_date;
            if( ! empty( $description)) $data['description'] = $description;
            if( ! empty( $tags))        $data['tags']        = $tags;

            $data['author'] = kuser()->username;
            $data['active'] = (($this->input->post('active') == 'on' || $this->input->post('active') == 1) ? 1 : 0);

            for ($i=1; $i<=15; $i++) {
                $d = 'description'.$i;
                $tmp_d = $this->input->post($d);
                if( $tmp_d !== FALSE) $data[$d] = $tmp_d;
            }

            $ret = $photo->update($data, $id);
            if ( ! $ret) {
                show_error('Gagal mengupdate data, silakan coba beberapa saat lagi');
            }

            foreach ($files_to_delete as $val) {
                @unlink($val);
            }

            $current_sct = $this->vars['current_sct'];
            $close = $this->input->post('closethis');
            if (empty ($close)) {
                $url = site_url('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/edit/'.$this->input->post('id'));
            } else {
                $url = $this->session->userdata('referrer');
                if (empty ($url)) {
                    $url = site_url('/admin/content/'.$current_sct->structure_id);
                }
            }
        }

        redirect($url, 'refresh');
    }

    public function del_foto($param)
    {
        list($id, $number) = explode('-', $param);

        $ff = 'foto'.$number;
        $dd = 'description'.$number;
        $data = array($ff => '',$dd => '');

        if (priv ('approve')) {
            $photo = $this->kwitang->ctModel('Photo', $this->mainModel());
            $lama  = $photo->get(null, $id);

            $ret = $photo->update($data, $id);
            if ($ret) {
                @unlink($lama->$ff);
            }
        }

        $current_sct = $this->vars['current_sct'];
        $url = site_url('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/edit/'.$id);

        redirect($url, 'refresh');
    }

    public function delete($id)
    {
        $current_sct = $this->vars['current_sct'];
        $url = site_url('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id);

        if (priv ('manage')) {
            $photo = $this->kwitang->ctModel('Photo', $this->mainModel());
            $lama  = $photo->get(null, $id);

            $ret = $photo->delete($id);
            if ($ret) {
                for ($i=1;$i<=20;$i++) {
                    $ff = 'foto'.$i;
                    if ( ! empty ($lama->$ff)) {
                        @unlink($lama->$ff);
                    }
                }
            }
        }

        redirect($url, 'refresh');
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
