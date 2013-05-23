<?php
if (! defined('FRONT_PATH')) {
    exit ('Kwitang ERROR..!!!');
}

// front end helper

if (! function_exists('structure_url')) {
    /**
     * Get default language code, return '' isfnot set
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param
     * @return
     */
    function klang_default()
    {
        $retval       = '';
        $lang_default = kconfig('system', 'lang_default');
        $langs        = kconfig('system', 'langs');
        $langs        = json_decode($langs);
        if (is_array($langs) && array_key_exists($lang_default, $langs)) {
            $retval = $lang_default;
        }

        return $retval;
    }
}

/* Kwitang URL
 *
 * Helper untuk membuat URL ke salah satu:
 * - struktur
 * - konten
 * - indeks konten
 */
if (! function_exists('structure_url')) {
    /**
     * Format URL Kwitang CMS untuk halaman struktur
     * app/channel/[structure_name]
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param
     * @return
     */
    function structure_url($structure, $lang = '')
    {
        $lang = $lang != '' ? $lang : klang_default();
        $uri  = $lang != '' ? $lang.'/' : '';
        $uri .= 'channel/';

        if (is_object($structure)) {
            if (! empty($structure->name)) {
                $uri .= $structure->name;
            }
        } elseif (is_string($structure)) {
            $uri .= $structure;
        }

        return site_url($uri);
    }
}

if (! function_exists('content_url')) {
    /**
     * Format URL Kwitang CMS untuk konten
     * app/read/sct_name/content_id/[slug]
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param   object|string   $sct        Structure ContentType Object retrieved from get_sct_* function or SCT Name
     * @param   object|string   $content    Content Object retrieved from get_sct_* or id of object or URI with id of object ex: '6/this-is-a-slug'
     * @return  string  URL to the content
     */
    function content_url($sct, $content = null, $lang = '')
    {
        $lang = $lang != '' ? $lang : klang_default();
        $uri = '';

        if (! empty($sct->name)) {
            $uri .= $sct->name.'/';
        } elseif (is_string($sct)) {
            $uri .= $sct.'/';
        }

        if ($uri !== '' and $content !== null) {
            if (! empty($content->id)) {
                $uri .= $content->id.'/';

                if (! empty($content->slug)) {
                    $slug = var_lang($content->slug, $lang);
                    $uri .= url_title($slug).'/';
                }
            } elseif (is_array($content)) {
                $uri .= empty($content[0]) ? '' : $content[0].'/';
                $uri .= empty($content[1]) ? '' : $content[1].'/';
            } elseif (is_string($content) or is_int($content)) {
                $uri .= $content;
            }
        }

        $uri_lang = $lang != '' ? $lang.'/' : '';

        return($uri !== '') ? site_url($uri_lang.'read/'.$uri) : site_url();
    }
}

if (! function_exists('view_url')) {
    /**
     * Format URL Kwitang CMS untuk view
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param   Sstring
     * @param   Object|String
     * @param   Object|String
     * @return  String  URL to the content
     */
    function view_url($view_file, $sct = null, $content = null, $lang = '')
    {
        $lang = $lang != '' ? $lang : klang_default();
        $uri = $view_file.'/';

        if (! empty($sct->name)) {
            $uri .= $sct->name.'/';
        } elseif (is_string($sct)) {
            $uri .= $sct.'/';
        }

        if ($content !== null) {
            if (! empty($content->id)) {
                $uri .= $content->id.'/';

                if (! empty($content->slug)) {
                    $slug = var_lang($content->slug, $lang);
                    $uri .= url_title($slug).'/';
                }
            } elseif (is_array($content)) {
                $uri .= empty($content[0]) ? '' : $content[0].'/';
                $uri .= empty($content[1]) ? '' : $content[1].'/';
            } elseif (is_string($content) or is_int($content)) {
                $uri .= $content;
            }
        }

        $uri_lang = $lang != '' ? $lang.'/' : '';

        return site_url($uri_lang.'view/'.$uri);
    }
}

if (! function_exists('custom_url')) {
    /**
     * Build URL to view file, with additional user supplied URI
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param   String  View filename
     * @param   String  Query part of URI
     * @param   String  Fragment part of URI
     * @return  String  URL to the content
     */
    function custom_url($view_file, $query = '', $fragment = '')
    {
        $uri_base = site_url('v/'.$view_file);
        $uri_add  = '';
        if (! empty($query)) {
            if (strpos($uri_base, '?') === false) {
                $uri_add .= '?'.ltrim($query, '?');
            } else {
                $uri_add .= '/&'.ltrim($query, '?');
            }
        }
        if (! empty($fragment)) {
            $uri_add .= '#'.ltrim($fragment, '#');
        }

        return $uri_base.$uri_add;
    }
}

if (! function_exists('index_url')) {
    /**
     * Format URL Kwitang CMS untuk halaman index
     * app/read/sct_name/page/[page_number]/[item_perpage]
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param
     * @return
     */
    function index_url($sct, $page_number = 1, $item_perpage = null, $lang = '')
    {
        if ($lang == '') {
            $lang = klang_default();
        }
        $uri = $lang != '' ? $lang.'/' : '';
        $uri.= 'index/';

        if (! empty($sct->name)) {
            $uri .= $sct->name.'/'.$page_number;
        } elseif (is_string($sct)) {
            $uri .= $sct.'/'.$page_number;
        }

        if (! empty($item_perpage)) {
            $uri .= '/'.$item_perpage;
        }

        return site_url($uri);
    }
}

if (! function_exists('archive_url')) {
    /**
     * Build URL for acces archive page
     *
     * URL:
     *
     * app/archive/[sct_name]/[year-month-day|year-month|year]/[page_number]/[item_perpage]
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param
     * @return
     */
    function archive_url($sct, $ymd = '', $page_number = 1, $item_perpage = null, $lang = '')
    {
        if ($lang == '') {
            $lang = klang_default();
        }
        $uri = $lang != '' ? $lang.'/' : '';
        $uri.= 'archive/';

        if (! empty($sct->name)) {
            $uri .= $sct->name;
        } elseif (is_string($sct)) {
            $uri .= $sct;
        }

        if (empty($ymd)) {
            $uri .= '/'.date('Y').'/'.$page_number;
        } else {
            $uri .= '/'.$ymd.'/'.$page_number;
        }

        if (! empty($item_perpage)) {
            $uri .= '/'.$item_perpage;
        }

        return site_url($uri);
    }
}

/* Widget
 *
 * Bagian halaman yang digunakan di beberapa tempat sebaiknya dibuat widget
 */
if (! function_exists('print_widget')) {
    /**
     * Tampilkan widget
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param  String
     * @param  Array
     * @return void
     */
    function print_widget($widget_filename, $data = null)
    {
        $CI =& get_instance();

        if ($data) {
            foreach ($data as $k => $v) {
                $$k = $v;
            }
        }
        // tampilkan widget
        $path = FRONT_PATH.$CI->kwitang->frontend.'/widgets/'.$widget_filename.'.php';
        if (@file_exists($path)) {
            include($path);
        }
    }
}

/* Ambil data
 *
 * Sebuah struktur dapat memiliki beberapa SCT, dan pada sebuah SCT dapat
 * memiliki beberapa Tipe-Konten (CT)
 *
 * Jika Struktur dianggap Kategori utama, maka SCT adalah sub kategori dari
 * kategori utama tersebut. Kemudian Tipe-konten adalah jenis konten yang
 * dimiliki oleh kategori utama/subkategori.
 *
 * Struktur
 *      |
 *    (sct - ... - sct)
 *      |
 *     (ct - ... - ct)
 */

// Structure
if (! function_exists('get_structure')) {
    /**
     * Ambill data struktur
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param    String|Integer
     * @return   Object|false
     */
    function get_structure($name_or_id = '', $show_all = false)
    {
        $parent = 0;
        $CI =& get_instance();
        if (empty($CI->Structure)) {
            $CI->load->model('Structure');
        }

        if (! empty($name_or_id)) {
            $search_parent = $CI->Structure->get($name_or_id);
            if (empty($search_parent->id)) {
                return false;
            } else {
                $parent = $search_parent->id;
            }
        }

        $structure = $CI->Structure->allTree($parent, $show_all);

        return empty($structure) ? false : $structure;
    }
}


if (! function_exists('get_structure_sct')) {
    /**
     * Ambil SCT (subkategori) dari debuah struktur
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param    Integer
     * @return   Array|false
     */
    function get_structure_sct($name_or_id)
    {
        $CI =& get_instance();
        if (empty($CI->Structure)) {
            $CI->load->model('Structure');
        }
        $structure = $CI->Structure->get($name_or_id);
        $sct = $CI->Structure->sctAll($structure->id);

        return empty($sct) ? false : $sct;
    }
}


if (! function_exists('get_content')) {
    /**
     * Ambil konten tunggal
     *
     * @package  Kwitang\Helpers\KwitangFe
     *
     * @param   string       $sct_name    Name of Structure Content Type
     * @param   string|int   $content_id  Content ID, 'first' or 'last'
     * @return  object|null
     */
    function get_content($sct_name, $content_id = 'last', $only_active = true)
    {
        $CI =& get_instance();
        $data = null;

        if (empty($CI->Structure)) {
            $CI->load->model('Structure');
        }
        $sct = $CI->Structure->sctGet($sct_name);
        if ($sct) {
            $control = $CI->kwitang->ctController($sct->content_type);

            if ($control) {
                $data['sct'] = $sct;

                $data['content'] = $control->get($sct->id, $content_id, $only_active);
            }
        }

        return $data;
    }
}


if (! function_exists('get_content_page')) {
    /**
     * Ambil data konten per halaman
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param  String
     * @param  Array
     * @return Array
     */
    function get_content_page($sct_name, $params = null)
    {
        $CI =& get_instance();
        $data = null;

        if (empty($CI->Structure)) {
            $CI->load->model('Structure');
        }
        $sct = $CI->Structure->sctGet($sct_name);

        if (! empty($sct)) {
            $data['sct'] = $sct;
            $control = $CI->kwitang->ctController($sct->content_type);

            if ($control) {
                // $params
                if (! empty($params['item_perpage']) and intval($params['item_perpage']) > 0) {
                    $item_perpage = intval($params['item_perpage']);
                } else {
                    $item_perpage = kconfig('system', 'item_perpage', 10);
                }

                $page_number = (! empty($params['page_number']) and intval($params['page_number']) > 0) ? intval($params['page_number']) : 1;
                $orders      = ! empty($params['orders']) ? $params['orders'] : null;
                $searchs     = ! empty($params['searchs']) ? $params['searchs'] : null;
                $only_active = ! empty($params['only_active']) ? $params['only_active'] : true;
                $offset      = ! empty($params['offset']) ? $params['offset'] : 0;

                $offset     += ($page_number -1) *$item_perpage;

                $data['content'] = $control->getAll($sct->id, $item_perpage, $offset, $orders, $searchs, $only_active);
            }
        }

        return $data;
    }
}

// ----------------------------------------------------------------------------

// Ambil data menu
if (! function_exists('get_menu')) {
    /**
     * Ambil data menu
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param  String
     * @param  Array   Data menu, digunakan untuk pemanggilan rekrusif
     * @param  Integer
     * @param  Integer  Kedalaman rekrusif maksimal adalah 30
     * @return Array
     */
    function get_menu($menu_name, $menu = null, $parent = 0, $depth = 0)
    {
        $retval = array();

        if ($menu == null) {
            $CI =& get_instance();

            if (empty($CI->Menu)) {
                $CI->load->model('Menu');
            }
            if (empty($CI->Menu_detail)) {
                $CI->load->model('Menu_detail');
            }

            $mm = $CI->Menu->getByName($menu_name);
            if ($mm) {
                $raw = $CI->Menu_detail->getall($mm->id);

                if (! empty($raw)) {
                    $menu = array();
                    foreach ($raw as $r) {
                        $menu[$r->parent_id][] = $r;
                    }
                } else {
                    return $retval;
                }
            } else {
                return $retval;
            }
        }

        if (! empty($menu[$parent])) {
            foreach ($menu[$parent] as $s) {
                $d = $s;
                $d->childs = null;
                $new_parent = $s->id;

                if ($depth <= 30) {  // max 30 leaves
                    $new_depth = $depth + 1;
                    $d->childs = get_menu($menu_name, $menu, $new_parent, $new_depth);
                }
                $retval[] = $d;
            }
        }

        return $retval;
    }
}

if (! function_exists('get_breadcrumb')) {
    /**
     * Hasilkan struktur breadcrumb ke sebuah struktur yang diberikan
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param  Object|int  Objek atau ID Struktur
     * @return Array
     */
    function get_breadcrumb($structure)
    {
        $CI =& get_instance();

        if (empty($CI->Structure)) {
            $CI->load->model('Structure');
        }

        if (is_object($structure)) {
            $id = $structure->id;
        } else {
            $id = $structure;
        }

        return $CI->Structure->getBreadcrumb($id);
    }
}

if (! function_exists('load_lang')) {
    /**
     * Muat terjemahan, jika ada.
     *
     * Terjemahan untuk bahasa yang sedang digunakan akan di ambil secara
     * otomatis, jadi Anda tidak perlu memuat terjemahan untuk bahasa yang
     * sedang digunakan (memanggil fungsi ini dari front end)
     *
     * Jika frontend menggunakan fungsi t() untuk mengambil terjemahan, dan
     * bahasa utama tidak sesuai dengan yang diharapkan, Anda dapat memaksa
     * untuk mengubah bahasa utama dengan menambahkan parameter
     * $forced_default_lang sesuai dengan yang di maksud oleh fungsi t(). Dan
     * tempatkan fungsi ini `load_lang($lang, 'kode')` sebelum fungsi t()
     *
     * $forced_default_lang hanya berdampak pada fungsi t()
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param  String
     * @param  String  Paksa default_hash dibuat dari bahasa ini
     * @return void
     */
    function load_lang($lang_code, $forced_default_lang = '')
    {
        $CI =& get_instance();

        if (! isset($CI->translation)) {
            $CI->translation = array();
        }

        $target = FRONT_PATH.$CI->kwitang->frontend.'/language/'.$lang_code.'.php';
        if (@file_exists($target)) {
            include($target);
            if (! empty($fe_lang) and is_array($fe_lang)) {
                $CI->translation[$lang_code] = $fe_lang;

                // kode dibawah, dipanggil juga oleh fungsi t()
                $forced = false;
                if (! empty($forced_default_lang)) {
                    $lang_default = $forced_default_lang;
                    $target = FRONT_PATH.$CI->kwitang->frontend.'/language/'.$forced_default_lang.'.php';
                    if (@file_exists($target)) {
                        include($target);
                        $forced = true;
                    }
                } else {
                    $lang_default = kconfig('system', 'lang_default', 'id');
                }

                if ($forced or $lang_code == $lang_default) {
                    $def_hash_lang = array();
                    $CI->translation['default_hash'] = array();
                    foreach ($fe_lang as $key => $value) {
                        $def_hash_lang[md5($value)] = $key;
                    }
                    $CI->translation['default_hash'] = $def_hash_lang;
                }

                unset($fe_lang);
            }
        }
    }
}

if (! function_exists('_t')) {
    /**
     * Ambil terjemahan dari koleksi bahasa di front end
     *
     * Sebaiknya gunakan fungsi ini dalam pembuatan front end, namun jika Anda
     * cukup PeDe dengan setting bahasa pada instalasi CMS ini, dan bahasa
     * utama tidak akan diubah di kemudian hari silakan gunakan fungsi t()
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param  String  Language Key
     * @param  String
     * @param  String  Ambil dari kode bahasa ini
     * @return String
     */
    function _t($fe_lang_key, $default = '', $lang_code = '')
    {
        $CI =& get_instance();

        $lang_code = empty($lang_code) ? $CI->vars['lang'] : $lang_code;

        if (! empty($CI->translation[$lang_code][$fe_lang_key])) {
            return $CI->translation[$lang_code][$fe_lang_key];
        } else {
            return $default;
        }
    }
}

if (! function_exists('t')) {
    /**
     * Ambil terjemahan berdasarkan String dalam bahasa utama
     *
     * Koleksi terjemahan dalama bahasa utama harus lebih lengkap dari bahasa
     * lainnya, jika suatu terjemahan yang diminta terdapat pada bahasa lain
     * namun tidak ada pada bahasa utama, maka tidak akan didapatkan hasil yang
     * diinginkan
     *
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param  String  Kata pada bahasa utama
     * @param  String  Ambil dari kode bahasa ini
     * @return String
     */
    function t($string = '', $lang_code = '')
    {
        $CI =& get_instance();

        if (! isset($CI->translation['default_hash'])) {
            $lang_default = kconfig('system', 'lang_default', 'id');
            load_lang($lang_default);
        }

        if (isset($CI->translation['default_hash'][md5($string)])) {
            // language key berdasarkan hash md5 string bahasa utama
            // paramenter $string harus identik dengan yang ada di terjemahan
            $key = $CI->translation['default_hash'][md5($string)];
            return _t($key, $string, $lang_code);
        } else {
            return $string;
        }
    }
}

if (! function_exists('view_path')) {
    /**
     * Ambil path ke folder views frontend yang sedang digunakan
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param  String Opsional, nama frontend
     * @return String
     */
    function view_path($frontend_name = '')
    {
        $CI =& get_instance();
        $_fr =($frontend_name == '' ? $CI->kwitang->frontend : $frontend_name);
        return FRONT_PATH.$_fr.'/views/';
    }
}

if (! function_exists('get_sct')) {
    /**
     * Get SCT Object
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param  Mixed SCT name Or SCT ID
     * @return Object
     */
    function get_sct($sct_name_or_id)
    {
        $CI =& get_instance();

        if (empty($CI->Structure)) {
            $CI->load->model('Structure');
        }

        return $CI->Structure->sctGet($sct_name_or_id);
    }
}

if (! function_exists('get_sct_config')) {
    /**
     * Get SCT Config for $keyname, if $keyname is not set, will return all
     * configs in array
     *
     * @package  Kwitang\Helpers\KwitangFe
     * @param  Mixed SCT Object Or SCT ID
     * @param  String If empty will return all configs in array
     * @return Mixed String, array if $keyname not set, or false if $keyname not exist
     */
    function get_sct_config($sct_obj_or_id, $keyname = null)
    {
        $CI =& get_instance();

        if (is_object($sct_obj_or_id)) {
            if (! empty($sct_obj_or_id->id)) {
                $sct_id = $sct_obj_or_id->id;
            } else {
                return false;
            }
        } elseif (is_numeric($sct_obj_or_id)) {
            $sct_id = floor($sct_obj_or_id * 1);
        } else {
            return false;
        }

        if (empty($CI->Structure)) {
            $CI->load->model('Structure');
        }

        if ($keyname === null) {
            return $CI->Structure->sctConfigAll($sct_id);
        } else {
            return $CI->Structure->sctConfigGet($sct_id, $keyname);
        }
    }
}

/* End of file helpers/kwitang_fe_helpers.php */
