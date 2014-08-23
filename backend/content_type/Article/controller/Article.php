<?php if ( ! defined('ROOT_PATH')) exit('No direct script access allowed');
/**
 * Kontroller pada tipe-konten Article
 *
 * @package  ContentType\Article\Controller
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class Article extends ContentTypeController
{
    public function title()       { return 'Article'; }
    public function description() { return "Article Single/Multi Language +ReadCounter"; }
    public function version()     { return '1.2.1'; }
    public function mainModel()   { return 'ArticleModel'; }

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
            case 'headless':
                $this->vars['admin_headless'] = true;
                $current_url = str_replace('headless', 'display', current_url());
                $this->session->set_userdata('referrer', $current_url);
                $this->vars['css_files'][] = asset_url('dtables/css/jquery.dataTables.min.css');
                $this->vars['js_files'][]  = asset_url('dtables/js/jquery.dataTables.min.js');
                break;
            case 'save':
            case 'update':
            case 'delete':
            case 'delg':
            case 'data_json':
            case 'setting_save':
                $this->vars['admin_header'] = false;
                $this->vars['admin_footer'] = false;
                break;
            case 'add':
            case 'edit':
            case 'edit_lang':
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
     * Halaman untuk menampilkan data artikel
     */
    public function headless($page_number=1)
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

        $uploader = $this->__upload_image('foto', 'articles');

        $data = array();
        $data['title']       = substr($this->input->post('title'), 0, 120);
        $data['slug']        = substr(url_title($data['title']), 0, 120);
        $data['author']      = $this->vars['current_user']->fullname;

        $pub_date            = $this->input->post('pub_date');
        $pub_date            = to_gmt(strtotime($pub_date));
        $data['pub_date']    = date('Y-m-d H:i:s', $pub_date);
        $data['body']        = $this->input->post('body', false);
        $data['tags']        = $this->input->post('tags');
        $data['active']      = ($this->input->post('active') == 'on' ? 1 : 0);
        $data['thumbnail']   = $uploader['thumbnail'];
        $data['foto']        = $uploader['foto'];

        $data['lang']        = $this->input->post('lang');
        $data['lang_group']  = $this->input->post('lang_group');

        $data['sct_id']      = $this->input->post('sct_id');
        $data['description'] = $this->input->post('description');

        $data['foto_description'] = $this->input->post('foto_description');
        $data['source']           = $this->input->post('source');
        $slink = trim($this->input->post('source_link'));
        $sprefix = substr($slink, 0, 3);
        if ($sprefix != 'htt' && $sprefix != 'ftp') {
            // != https:// http:// ftp:// ftps://
            $slink = 'http://'.$slink;
        }
        $data['source_link']      = $slink;

        $article = $this->kwitang->ctModel('Article', 'ArticleModel');
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
    public function edit($article_id, $lang = 'id')
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
     * Form ubah artikel lang
     */
    public function edit_lang($lang_group, $lang_request = 'id')
    {
        if (empty($lang_group)) {
            show_404();
        }

        $this->load->helper('form');
        $article = $this->__model($this->mainModel());
        $content = $article->getLang($this->vars['current_sct']->id, $lang_group, $lang_request, false);

        $this->__view('addedit', array('content'=>$content, 'lang_request'=>$lang_request, 'lang_group'=>$lang_group));
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
        $uploader = $this->__upload_image('foto', 'articles');

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
        $tmp_is_active = $this->input->post('active');
        $data['active']     = empty($tmp_is_active) ? 0 : 1;

        $data['lang']        = $this->input->post('lang');
        $data['lang_group']  = $this->input->post('lang_group');

        $data['description']    = $this->input->post('description');
        $data['sct_id'] = $this->vars['current_sct']->id;

        $data['foto_description'] = $this->input->post('foto_description');
        $data['source']           = $this->input->post('source');
        $slink = trim($this->input->post('source_link'));
        $sprefix = substr($slink, 0, 3);
        if ($sprefix != 'htt' && $sprefix != 'ftp') {
            // != https:// http:// ftp:// ftps://
            $slink = 'http://'.$slink;
        }
        $data['source_link']      = $slink;

        if( ! empty($uploader['thumbnail']))
            $data['thumbnail']  = $uploader['thumbnail'];
        if( ! empty($uploader['foto']))
            $data['foto']       = $uploader['foto'];

        $ret = $article->update($data, $id);
        if ( ! $ret) {
            show_error('Gagal mengupdate data, silakan coba beberapa saat lagi');
        } else {
            // Set aktif untuk Artikel bahasa lainnya
            $article->updateActive($data['lang_group'], $data['active']);

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
    public function delete($lang_group)
    {
        $url = null;
        $current_sct = $this->vars['current_sct'];

        if (priv ('manage')) {
            $article = $this->__model($this->mainModel());
            $lama    = $article->getLang($current_sct->id, $lang_group);
            $ret     = $article->delete($lang_group);

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
     * Output json untuk digunakan pada datatables
     */
    public function data_json()
    {
        $langs = json_decode (kconfig ('system', 'langs'));

        if (is_array($langs) && count($langs) > 1) {
            $kolom    = array('pub_date', 'lang', 'title', 'author', 'active');
        } else {
            $kolom    = array('pub_date', 'title', 'author', 'active');
        }
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
            $del_url  = site_url($controller_uri . 'delete/' . $c->lang_group);
            $edit_lang = '';
            if ($langs) {
                foreach ($langs as $val) {
                    $edit_lang .= '<a href="'.site_url($controller_uri . 'edit_lang/'.$c->lang_group.'/'.$val->code).'"><img src="'.asset_url($val->code.'_flag.png', 'Article').'" alt="'.strtoupper($val->code).'"></a> &nbsp;';
                }
            }

            $d = array();
            $d[] = kdate(from_gmt($c->pub_date));

            if (is_array($langs) && count($langs) > 1) {
                $d[] = $edit_lang;
            }

            $d[] = (( ! empty($c->thumbnail)) ? '<img class="pull-left img-thumbnail" src="' . base_url($c->thumbnail) . '" alt="" style="float: left; width:60px; margin: 0 8px 3px 0;">' : '')
                   .'<a href="' . $edit_url . '">' . $c->title .'</a>';
            $d[] = $c->author;
            $d[] = ($c->active ? '<i class="icon-green icon-ok"></i>' : '<i class="icon-red icon-remove"></i>');
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

    public function setting($structure_id, $sct_id)
    {
        //if ( ! priv ('approve')) {
        //    redirect ('admin');
        //}

        $this->vars['current_structure'] = $this->Structure->get($structure_id);
        $this->vars['current_sct']       = $this->Structure->sctGet($sct_id);
        $this->vars['sct_config']        = $this->Structure->sctConfigAll($sct_id);

        $this->load->helper('form');
        $this->__view('setting', $this->vars);
    }

    public function setting_save()
    {
        $post         = $this->input->post();
        $structure_id = $post['structure_id'];
        $sct_id       = $post['sct_id'];

        $i = 1;
        foreach ($post['feed_url'] as $value) {
            if (! empty($value)) {
                $this->Structure->sctConfigSave($sct_id, 'feed_url_'.$i, $value);
            }
            $i++;
        }
        $i = 1;
        foreach ($post['feed_lang'] as $value) {
            if (! empty($value)) {
                $this->Structure->sctConfigSave($sct_id, 'feed_lang_'.$i, $value);
            }
            $i++;
        }
        $i = 1;
        foreach ($post['feed_name'] as $value) {
            if (! empty($value)) {
                $this->Structure->sctConfigSave($sct_id, 'feed_name_'.$i, $value);
            }
            $i++;
        }

        redirect('admin/content/'.$structure_id.'/'.$sct_id);
    }

    public function get_rss($sct_id)
    {
        $current_sct = $this->Structure->sctGet($sct_id);
        $article     = $this->__model($this->mainModel());

        $this->load->spark('ci-simplepie/1.0.1');
        $this->load->spark('curl/1.2.1');

        $sct_config = $this->Structure->sctConfigAll($current_sct->id);
        $vars['current_sct'] = $current_sct;

        if ( ! empty($sct_config)) {
            foreach ($sct_config as $key => $value) {
                if(substr($key, 0, 8) == 'feed_url') {
                    $feeds_url[substr($key, 9)] = $value;
                }
                if(substr($key, 0, 9) == 'feed_lang') {
                    $feeds_lang[substr($key, 10)] = $value;
                }
                if(substr($key, 0, 9) == 'feed_name') {
                    $feeds_name[substr($key, 10)] = $value;
                }
            }

            $result_feed = array();
            foreach ($feeds_url as $key => $feed_url) {
                $succes_feed = array();
                $failed_feed = array();

                $feed = new $this->cisimplepie();
                $feed->set_feed_url($feed_url);
                $feed->cache_location = APPPATH.'cache/';
                $feed->init();
                $feed->handle_content_type();
                foreach ( $feed->get_items(0, 20) as $item) {
                    $author = $item->get_author();
                    $data = array();
                    $data['sct_id']      = $current_sct->id;
                    $data['active']      = 1;
                    $data['title']       = $item->get_title();
                    $data['slug']        = url_title($data['title'], '_', TRUE);
                    $data['author']      = $feeds_name[$key];
                    $data['pub_date']    = date('Y-m-d H:i:s', strtotime($item->get_gmdate()));
                    $data['description'] = $item->get_description();
                    $data['body']        = $item->get_content();
                    $data['guid']        = $item->get_id();
                    $data['lang']        = $feeds_lang[$key];

                    $cl = $item->get_enclosure();
                    $data['permalink'] = $item->get_link();

                    if ( ! empty($cl->link)) {
                        $folder_bp = rtrim(kconfig ('system', 'upload_folder', 'upload'), '/') . '/article_rss/';
                        if ( ! is_dir($folder_bp)) {
                            mkdir($folder_bp, DIR_WRITE_MODE, TRUE);
                        }

                        $tujuan  = $folder_bp . basename($cl->link);
                        $rawfile = $this->curl->simple_get($cl->link);

                        $file = fopen($tujuan, "w+");
                        fputs($file, $rawfile);
                        fclose($file);

                        $data['foto']      = $tujuan;
                        $data['thumbnail'] = $tujuan;
                    }

                    if ( ! empty($data)) {
                        //echo var_dump($data);

                        $rr = $article->insert($data);
                        if ( $rr)
                            $succes_feed[] = $data['title'];
                        else
                            $failed_feed[] = $data['title'];
                    }
                }

                $result_feed[] = array('feed_url' => $feed_url
                                      ,'success'  => $succes_feed
                                      ,'failed'   => $failed_feed);
            }

            $vars['result_feed'] = $result_feed;

            $this->__view('get-rss', $vars);
        } else {
            $this->__view('get-rss', $vars);
        }
    }

    /**
     * Mencari jarum dalam jerami...
     *
     * This method must return $result[] = array(title_link, description, ...and the rest)
     *
     */
    public function search($q, $page, $item_perpage, $only_active = true) {
        $page = $page ? $page : 1;
        $item_perpage = $item_perpage ? $item_perpage : kconfig ('system', 'search_perpage', 25);
        $disp_start   = ($page - 1) * $item_perpage;

        $article = $this->__model($this->mainModel());
        $content = $article->search($q, $page, $item_perpage, array('tags', 'title', 'description'), $only_active);

        return $content;
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
