<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Kontroller pada tipe-konten Video
 *
 * @package  ContentType\Video\Controller
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class Video extends ContentTypeController
{
    public function title()       { return 'Video'; }
    public function description() { return "Video File Or Youtube  +ReadCounter"; }
    public function version()     { return '1.0.1'; }
    public function mainModel()   { return 'VideoModel'; }

    private function __upload_image($field_name, $folder_name)
    {
        $retval = false;

        $config['upload_path'] = $this->base_upload.$folder_name;
        if ( ! is_dir($config['upload_path']))
            mkdir($config['upload_path'], DIR_WRITE_MODE, true);
        $config['allowed_types'] = kconfig ('system', 'allowed_types', 'gif|jpg|png|jpeg');
        $config['max_size']      = kconfig ('system', 'image_max_size', '1024');
        $config['max_width']     = kconfig ('system', 'image_max_width', '1000');
        $config['max_height']    = kconfig ('system', 'image_max_height', '800');
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ( ! empty($_FILES[$field_name]['name'])) {
            if (!$this->upload->do_upload($field_name)) {
                show_error($this->upload->display_errors());
            } else {
                $data = $this->upload->data();
                $retval = $config['upload_path'].'/'.$data['file_name'];
            }
        }

        return $retval;
    }

    private function __upload_video($field_name, $folder_name)
    {
        $retval = false;

        $config['upload_path'] = $this->base_upload.$folder_name;
        if ( ! is_dir($config['upload_path']))
            mkdir($config['upload_path'], DIR_WRITE_MODE, true);
        $config['allowed_types'] = kconfig ('system', 'video_allowed_types', 'mp4|flv|wmv');
        $config['max_size']      = kconfig ('system', 'video_max_size', '10240');
        $config['overwrite']     = false;
        $config['remove_spaces'] = true;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ( ! empty($_FILES[$field_name]['name'])) {
            if (!$this->upload->do_upload($field_name)) {
                show_error($this->upload->display_errors());
            } else {
                $data = $this->upload->data();
                $retval = $config['upload_path'].'/'.$data['file_name'];
            }
        }

        return $retval;
    }

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

        $current_sct = $this->vars['current_sct'];
        $data = array(
                    'sct_id'      => $current_sct->id,
                    'author'      =>$this->vars['current_user']->fullname,
                    'title'       => substr($this->input->post('title'), 0, 120),
                    'pub_date'    => date('Y-m-d H:i:s', to_gmt($this->input->post('pub_date'))),
                    'description' => $this->input->post('description'),
                    'tags'        => $this->input->post('tags'),
                    'youtube_id'  => $this->input->post('youtube_id'),
                    'active'      => ($this->input->post('active') == 'on' || $this->input->post('active') == 1? 1 : 0)
                );

        $im = $this->__upload_image('image', 'video');
        if ( $im) $data['image'] = $im;
        $vi = $this->__upload_video('video_file', 'video');
        if ( $vi) $data['video_file'] = $vi;

        $video = $this->kwitang->ctModel('Video', $this->mainModel());

        $ret = $video->insert($data);
        if (!$ret) {
            $error = 'Gagal menambah data, silakan coba beberapa saat lagi';
        }

        if (!empty($error)) {
            show_error($error);
        } else {
            $close = $this->input->post('closethis');
            if ( empty($close)) {
                $url = site_url('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/edit/'.$ret);
            } else {
                $url = $this->session->userdata('referrer');
                if( empty($url))
                    $url = site_url('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id);
            }

            redirect($url, 'refresh');
        }
    }

    /**
     * Form ubah Video
     */
    public function edit($id)
    {
        if (empty ($id)) {
            show_404();
        }

        $this->load->helper('form');
        $video = $this->__model($this->mainModel());
        $content = $video->get($this->vars['current_sct']->id, $id, false);

        $this->__view('addedit', array('content'=>$content));
    }


    public function update()
    {
        if ( ! priv ('approve')) {
            $url = $this->session->userdata('referrer');
            redirect ($url);
        }

        $video = $this->kwitang->ctModel('Video', $this->mainModel());
        $current_sct = $this->vars['current_sct'];

        $delete_image = false;
        $delete_video = false;

        $id         = $this->input->post('id');
        $lama       = $video->get(null, $id);

        $title      = substr($this->input->post('title'), 0, 120);
        $pub_date   = date('Y-m-d H:i:s', to_gmt($this->input->post('pub_date')));
        $description= $this->input->post('description');
        $tags       = $this->input->post('tags');
        $youtube_id = $this->input->post('youtube_id');
        $active     = ($this->input->post('active') == 'on' OR $this->input->post('active') == 1) ? '1' : false;

        if( isset($title))         $data['title']          = $title;
        if( isset($pub_date))      $data['pub_date']       = $pub_date;
        if( isset($description))   $data['description']    = $description;
        if( isset($tags))          $data['tags']           = $tags;
        if( isset($youtube_id))    $data['youtube_id']     = $youtube_id;
        if( isset($active))        $data['active']         = $active;

        $data['author'] = $this->vars['current_user']->fullname;

        $im = $this->__upload_image('image', 'video');
        if ( $im) {
            $data['image'] = $im;
            $delete_image  = true;
        }
        $vi = $this->__upload_video('video_file', 'video');
        if ( $vi) {
            $data['video_file'] = $vi;
            $delete_video       = true;
        }

        $ret = $video->update($data, $id);
        if ( ! $ret) {
            show_error ('Gagal mengupdate data, silakan coba beberapa saat lagi');
        } else {
            if ($delete_image) {
                @unlink($lama->image);
            }
            if ($delete_video) {
                @unlink($lama->video_file);
            }

            $close = $this->input->post('closethis');
            if ( empty($close)) {
                $url = site_url('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/edit/'.$this->input->post('id'));
            } else {
                $url = $this->session->userdata('referrer');
                if( empty($url))
                    $url = site_url('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id);
            }

            redirect($url, 'refresh');
        }
    }

    public function del_image($id)
    {
        $current_sct = $this->vars['current_sct'];

        if (priv ('manage')) {
            $video = $this->kwitang->ctModel('Video', $this->mainModel());
            $lama       = $video->get(null, $id);

            if ($lama) {
                $data = array('image' => '');
                $ret = $video->update($data, $id);
                if ($ret) {
                    @unlink ($lama->image);
                }
            }
        }

        $url = site_url('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/edit/'.$id);

        redirect($url);
    }

    public function del_video($id)
    {
        $current_sct = $this->vars['current_sct'];

        if (priv ('manage')) {
            $video = $this->kwitang->ctModel('Video', $this->mainModel());
            $lama       = $video->get(null, $id);

            if ($lama) {
                $data = array('video_file' => '');
                $ret = $video->update($data, $id);
                if ($ret) {
                    @unlink ($lama->video_file);
                }
            }
        }

        $url = site_url('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id.'/edit/'.$id);

        redirect($url);
    }

    public function delete($id)
    {
        if (priv ('manage')) {
            $video = $this->kwitang->ctModel('Video', $this->mainModel());
            $video->delete($id);
        }

        $current_sct = $this->vars['current_sct'];
        $url = site_url('/admin/content/'.$current_sct->structure_id.'/'.$current_sct->id);
        redirect ($url);
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
