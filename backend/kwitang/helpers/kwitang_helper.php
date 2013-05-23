<?php
if (! defined('FRONT_PATH')) {
    exit ('Kwitang ERROR..!!!');
}


if (! function_exists('user_log')) {
    /**
     * Menulis catatan log, wrapper method userLog() dari library Kwitang
     *
     * @package  Kwitang\Helpers\Kwitang
     * @param    $subject  String
     * @param    $event    String
     * @param    $message  String
     * @return   Boolean
     */
    function user_log($subject, $event, $message)
    {
        $CI =& get_instance();
        return $CI->kwitang->userLog($subject, $event, $message);
    }
}


if (! function_exists('asset_url')) {
    /**
     * Buat URL asset, wrapper method asseturl() dari library Kwitang
     *
     * @package  Kwitang\Helpers\Kwitang
     * @param    String             Relative path + nama file
     * @param    Boolean|String     Set false tidak mengambil dari frontend, String ContentType
     * @param    Boolean            Set true untuk penambahan timestamp ?v=XXX
     * @return   String  Full URL ke file
     */
    function asset_url ($file_uri = '', $param = true, $add_mtime = null)
    {
        $CI =& get_instance();
        return $CI->kwitang->assetUrl($file_uri, $param, $add_mtime);
    }
}


if (! function_exists('kconfig')) {
    /**
     * Ambil Konfigurasi berdasarkan namanya
     *
     * @package  Kwitang\Helpers\Kwitang
     *
     * @param    String Section name
     * @param    String Config keyname
     * @param    String Default value
     * @return   String
     */
    function kconfig($section, $name, $default_value = '')
    {
        $CI =& get_instance();
        return $CI->KConfig->get($section, $name, $default_value);
    }
}


if (! function_exists('user_config')) {
    /**
     * Get User Config
     *
     * @package  Kwitang\Helpers\Kwitang
     *
     * @param  String Username
     * @param  String Keyname
     * @param  String Default value if not exist
     * @return Mixed Return String if $keyname is specified, otherwise Array
     */
    function user_config($username, $keyname = '', $default_value = '')
    {
        $retval = null;
        $CI =& get_instance();

        if (empty($CI->Users)) {
            $CI->load->model('Users');
        }

        $retval = $CI->Users->getConfig($username, $keyname);

        if ($retval === null) {
            $retval = $default_value;
        }

        return $retval;
    }
}


if (! function_exists('kdate')) {
    /**
     * Format tanggal sessuai konfigurasi,
     *
     * Untuk konversi ke local/GMT silakan gunakan from_gmt() dan to_gmt()
     *
     * @package  Kwitang\Helpers\Kwitang
     *
     * @param   int|string $date_input   Tanggal yang akan di konversi
     * @return  string
     */
    function kdate($date_input = null)
    {
        if ($date_input === null) {
            $date_input = from_gmt(time_gmt());
        } elseif (is_string($date_input)) {
            $date_input = strtotime($date_input);
        }

        return date(kconfig('system', 'date_format', 'Y-m-d H:i:s'), $date_input);
    }
}


if (! function_exists('from_gmt')) {
    /**
     * Ubah tanggal dari GMT ke waktu 'lokal' (sesuai setting timezone di CMS)
     *
     * @package  Kwitang\Helpers\Kwitang
     *
     * @param    string|int
     * @return   int  Unix timestamp
     */
    function from_gmt($time = '')
    {
        if (empty($time)) {
            $time = local_to_gmt();
        } elseif (is_numeric($time)) {
            // the time on unix timestamp format
        } else {
            $time = mysql_to_unix($time);
        }
        // TODO: Error handling jika gagal merubah input $time

        $time += timezones(kconfig('system', 'timezones', 'UP7')) * 3600;

        if ((bool) kconfig('system', 'dst', false)) {
            $time += 3600;
        }

        return $time;
    }
}


if (! function_exists('to_gmt')) {
    /**
     * Ubah tanggal dari 'lokal' ke GMT ('lokal' = sesuai setting timezone di cms)
     *
     * @package  Kwitang\Helpers\Kwitang
     *
     * @param    int  Unix Timestamp
     * @return   int
     */
    function to_gmt($time = '')
    {
        if (empty($time)) {
            return local_to_gmt();
        } elseif (is_numeric($time)) {
            // the time on unix timestamp format
        } else {
            $time = mysql_to_unix($time);
        }

        $time -= timezones(kconfig('system', 'timezones', 'UP7')) * 3600;

        if ((bool) kconfig('system', 'dst', false)) {
            $time -= 3600;
        }

        return $time;
    }
}

if (! function_exists('time_gmt()')) {
    /**
     * Ambil waktu GMT
     *
     * @package  Kwitang\Helpers\Kwitang
     *
     * @return   int
     */
    function time_gmt()
    {
        $CI =& get_instance();
        return $CI->kwitang->timeGmt();
    }
}

if (! function_exists('kaddslashes')) {
    /**
     * Sanitize string for save to database
     *
     * @package  Kwitang\Helpers\Kwitang
     *
     * @param  Mixed
     * @return Mixed
     */
    function kaddslashes($data)
    {
        $CI =& get_instance();

        if (is_string($data)) {
            $data = preg_replace('/[^\P{C}\n]+/u', '', $data);
            $data = $CI->db->escape_str($data);
        } elseif (is_array($data)) {
            $_tmp = array();
            foreach ($data as $key => $value) {
                $_tmp[$key] = kaddslashes($value);
            }
            $data = $_tmp;
            unset ($_tmp);
        } elseif (is_object($data)) {
            $dataClass = get_class($data);
            $_tmp = new $dataClass();
            foreach ($data as $key => $value) {
                $_tmp->$key = kaddslashes($value);
            }
            $data = $_tmp;
            unset ($_tmp);
        }

        return $data;
    }
}

if (! function_exists('kserialize')) {
    /**
     * Serialize data untuk disimpan pada database.
     *
     * @package  Kwitang\Helpers\Kwitang
     *
     * @param  Mixed
     * @return String
     */
    function kserialize($data)
    {
        return serialize(kaddslashes($data));
    }
}


if (! function_exists('kstripslashes')) {
    /**
     * Remove slashes
     *
     * @package  Kwitang\Helpers\Kwitang
     *
     * @param  Mixed
     * @return Mixed
     */
    function kstripslashes($data)
    {
        if (is_string($data)) {
            $data = stripslashes(str_replace('\n', "\n", $data));
            $data = stripslashes($data); // re-strip required for html content
        } elseif (is_array($data)) {
            $_tmp = array();
            foreach ($data as $key => $value) {
                $_tmp[$key] = kstripslashes($value);
            }
            $data = $_tmp;
            unset ($_tmp);
        } elseif (is_object($data)) {
            $dataClass = get_class($data);
            $_tmp = new $dataClass();
            foreach ($data as $key => $value) {
                $_tmp->$key = kstripslashes($value);
            }
            $data = $_tmp;
            unset ($_tmp);
        }

        return $data;
    }
}


if (! function_exists('kunserialize')) {
    function kunserialize_callback($matches)
    {
        return 's:'.strlen($matches[2]).':"'.$matches[2].'";';
    }
    /**
     * Unserialize data.
     *
     * Gunakan untuk unserialize data yg diambil dari database.
     *
     * @package  Kwitang\Helpers\Kwitang
     *
     * @param  String
     * @return Mixed
     */
    function kunserialize($string)
    {
        if (! is_string($string)) {
            return false;
        }
        if (! preg_match('/^([abdiOs]:|N;)/', $string)) {
            return '';
        }

        $cleaned = preg_replace_callback('/s:(\d+):"(.*?)";/s', 'kunserialize_callback', $string);
        $data    = unserialize($cleaned);

        return kstripslashes($data);
    }
}


if (! function_exists('kbyte_size')) {
    /**
     * Ubah size dalam Byte ke bentuk yang lebih mudah dibaca (KB, MB, GB dll)
     *
     * @package  Kwitang\Helpers\Kwitang
     *
     * @param    int     $bytes
     * @param    int     $decimals
     * @param    String
     * @return   string
     */
    function kbyte_size($bytes, $decimals = 2, $default_on_error = 'NaN', $size_array = null)
    {
        if (is_numeric($bytes)) {
            if ($size_array !== null and is_array($size_array)) {
                $sz = $size_array;
            } else {
                $sz = array('Byte','KB','MB','GB','TB','PB');
            }
            $factor = floor((strlen($bytes) - 1) / 3);
            return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) .' '. @$sz[$factor];
        } else {
            return $default_on_error;
        }
    }
}


// USER & AUTHORIZATION =======================================================

if (! function_exists('kuser')) {
    /**
     * Ambil data user yang sedang login
     *
     * @package  Kwitang\Helpers\Kwitang
     * @return   Object|null
     */
    function kuser()
    {
        $CI =& get_instance();
        return $CI->kwitang->currentUser();
    }
}

if (! function_exists('priv')) {
    /**
     * Check privilege of logged in user to a structure. Return true if
     * user has equal or more higher privilege.
     *
     * But if $equality is set to true, will return true only if checked
     * privilege is equal with their privilege.
     *
     * The privilege level is:
     * - view
     * - posting
     * - approve
     * - manage
     *
     * @package  Kwitang\Helpers\Kwitang
     *
     * @param    String|int     Privilege level tested
     * @param    Object|int     Structure, default to current structure
     * @param    Boolean        Return false if privilege is equal
     * @return   Boolean
     */
    function priv($privilege_level, $structure = null, $equality = false)
    {
        $CI =& get_instance();

        if ($structure == null and ! empty($CI->vars['current_structure'])) {
            $structure = $CI->vars['current_structure']->id;
        } else if ($structure == null and ! empty($CI->current_structure)) {
            $structure = $CI->current_structure->id;
        }

        if (is_object($structure) and ! empty($structure->id)) {
            $structure = $structure->id;
        }

        if (intval($structure) > 0) {
            return $CI->kwitang->checkPrivilege($structure, $privilege_level, '', $equality);
        } else {
            return false;
        }
    }
}

if (! function_exists('is_admin')) {
    /**
     * Cek apakah user yang sedang login adalah Admin
     *
     * @package  Kwitang\Helpers\Kwitang
     * @return   Boolean  TRUE jika user yang login adalah Admin
     */
    function is_admin()
    {
        $CI =& get_instance();
        $user = $CI->kwitang->currentUser();
        return (! empty($user->level) && $user->level == 'ADMIN') ? true : false;
    }
}

if (! function_exists('is_author')) {
    /**
     * Cek apakah user yang sedang login adalah Author
     *
     * @package  Kwitang\Helpers\Kwitang
     * @return   Boolean  TRUE jika user yang login adalah Author
     */
    function is_author()
    {
        $CI =& get_instance();
        $user = $CI->kwitang->currentUser();
        return (! empty($user->level) && $user->level == 'AUTHOR') ? true : false;
    }
}

if (! function_exists('is_member')) {
    /**
     * Cek apakah user yang sedang login adalah Member
     *
     * @package  Kwitang\Helpers\Kwitang
     * @return   Boolean  TRUE jika user yang login adalah Member
     */
    function is_member()
    {
        $CI =& get_instance();
        $user = $CI->kwitang->currentUser();
        return (! empty($user->level) && $user->level == 'MEMBER') ? true : false;
    }
}

// OTHER ======================================================================

if (! function_exists('var_lang')) {
    /**
     * Ambil teks salah satu bahasa dari sebuah variabel
     *
     * Digunakan pada Structure->title dan Structure->description
     * keduanya disimpan dalam bentuk serialized, dan gunakan fungsi ini untuk
     * membacanya.
     *
     * @package  Kwitang\Helpers\Kwitang
     * @param    String  Serialized/plain string
     * @param    String  Language code, default current lang_default
     * @param    Mixed   Default value
     * @return   String
     */
    function var_lang($var, $lang = null, $default = '')
    {
        $tmp = kunserialize($var);
        if (! $tmp) {
            return empty($var) ? $default : $var;
        } else {
            if ($lang === null) {
                $CI =& get_instance();
                $lang = $CI->kwitang->lang;
            }

            if (empty($lang)) {
                return htmlspecialchars_decode(array_shift($tmp));
            } else {
                return ! empty($tmp[$lang]) ? htmlspecialchars_decode($tmp[$lang]) : $default;
            }
        }
    }
}

if (! function_exists('ksetcookie')) {
    /**
     * Set the cookie
     *
     * @package  Kwitang\Helpers\Kwitang
     * @param    String
     * @param    String
     * @return   void
     */
    function ksetcookie($name, $value, $expire = null)
    {
        $CI =& get_instance();
        if ($expire === null) {
            $expire = ($CI->config->item('sess_expire_on_close') === true) ? 0 : $CI->config->item('sess_expiration') + time();
        } elseif ($expire > 0) {
            $expire += time();
        }

        // Set the cookie
        setcookie(
            $CI->config->item('cookie_prefix').$name,
            $value,
            $expire,
            $CI->config->item('cookie_path'),
            $CI->config->item('cookie_domain'),
            $CI->config->item('cookie_secure')
        );
    }
}

if (! function_exists('kgetcookie')) {
    /**
     * Set the cookie
     *
     * @package  Kwitang\Helpers\Kwitang
     * @param    String
     * @param    String
     * @return   String|null
     */
    function kgetcookie($name)
    {
        $CI =& get_instance();

        if (isset($_COOKIE[$CI->config->item('cookie_prefix').$name])) {
            return $_COOKIE[$CI->config->item('cookie_prefix').$name];
        }

        return null;
    }
}

if (! function_exists('error404')) {
    /**
     * Redirect to 404 error page.
     *
     * @package  Kwitang\Helpers\Kwitang
     * @return void
     */
    function error404()
    {
        redirect(site_url('not_found'));
    }
}

/* End of file helpers/kwitang_helper.php */
