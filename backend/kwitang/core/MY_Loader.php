<?php
if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}
/**
 * Extends CodeIgniter Loader
 *
 * @package     Kwitang\Core
 * @author      Iyan Kushardiansah <iyank4@gmail.com>
 */
class MY_Loader extends CI_Loader
{
    /**
     * Keep track of which sparks are loaded. This will come in handy for being
     *  speedy about loading files later.
     *
     * @var array
     */
    private $_ci_loaded_sparks = array();

    /**
     * Is this version less than CI 2.1.0? If so, accomodate
     * @bubbafoley's world-destroying change at: http://bit.ly/sIqR7H
     * @var bool
     */
    private $_is_lt_210 = false;

    /**
     * Constructor. Define SPARKPATH if it doesn't exist, initialize parent
     */
    public function __construct()
    {
        if (!defined('SPARKPATH')) {
            define('SPARKPATH', 'backend/sparks/');
        }

        $this->_is_lt_210 = (is_callable(array('CI_Loader', 'ci_autoloader'))
                            || is_callable(array('CI_Loader', '_ci_autoloader')));

        parent::__construct();
    }

    /**
     * To accomodate CI 2.1.0, we override the initialize() method instead of
     *  the ci_autoloader() method. Once sparks is integrated into CI, we
     *  can avoid the awkward version-specific logic.
     * @return Loader
     */
    public function initialize()
    {
        parent::initialize();

        if (!$this->_is_lt_210) {
            $this->ci_autoloader();
        }

        return $this;
    }

    /**
     * Load a spark by it's path within the sparks directory defined by
     *  SPARKPATH, such as 'markdown/1.0'
     * @param string $spark    The spark path withint he sparks directory
     * @param <type> $autoload An optional array of items to autoload
     *  in the format of:
     *   array (
     *     'helper' => array('somehelper')
     *   )
     * @return <type>
     */
    public function spark($spark, $autoload = array())
    {
        if (is_array($spark)) {
            foreach ($spark as $s) {
                $this->spark($s);
            }
        }

        $spark = ltrim($spark, '/');
        $spark = rtrim($spark, '/');

        $spark_path = SPARKPATH . $spark . '/';
        $parts      = explode('/', $spark);
        $spark_slug = strtolower($parts[0]);

        # If we've already loaded this spark, bail
        if (array_key_exists($spark_slug, $this->_ci_loaded_sparks)) {
            return true;
        }

        # Check that it exists. CI Doesn't check package existence by itself
        if (!file_exists($spark_path)) {
            show_error("Cannot find spark path at $spark_path");
        }

        if (count($parts) == 2) {
            $this->_ci_loaded_sparks[$spark_slug] = $spark;
        }

        $this->add_package_path($spark_path);

        foreach ($autoload as $type => $read) {
            if ($type == 'library') {
                $this->library($read);
            } elseif ($type == 'model') {
                $this->model($read);
            } elseif ($type == 'config') {
                $this->model($read);
            } elseif ($type == 'helper') {
                $this->model($read);
            } elseif ($type == 'view') {
                $this->model($read);
            } else {
                show_error("Could not autoload object of type '$type' ($read) for spark $spark");
            }
        }

        // Looks for a spark's specific autoloader
        $this->ci_autoloader($spark_path);

        return true;
    }

    /**
     * Pre-CI 2.0.3 method for backward compatility.
     *
     * @param  null $basepath
     * @return void
     */
    public function _ci_autoloader($basepath = null)
    {
        $this->ci_autoloader($basepath);
    }

    /**
     * Specific Autoloader (99% ripped from the parent)
     *
     * The config/autoload.php file contains an array that permits sub-systems,
     * libraries, and helpers to be loaded automatically.
     *
     * @param  array|null $basepath
     * @return void
     */
    public function ci_autoloader($basepath = null)
    {
        if ($basepath !== null) {
            $autoload_path = $basepath.'config/autoload.php';
        } else {
            $autoload_path = APPPATH.'config/autoload.php';
        }

        if (! file_exists($autoload_path)) {
            return false;
        }

        include($autoload_path);

        if (! isset($autoload)) {
            return false;
        }

        if ($this->_is_lt_210 || $basepath !== null) {
            // Autoload packages
            if (isset($autoload['packages'])) {
                foreach ($autoload['packages'] as $package_path) {
                    $this->add_package_path($package_path);
                }
            }
        }

        // Autoload sparks
        if (isset($autoload['sparks'])) {
            foreach ($autoload['sparks'] as $spark) {
                $this->spark($spark);
            }
        }

        if ($this->_is_lt_210 || $basepath !== null) {
            if (isset($autoload['config'])) {
                // Load any custom config file
                if (count($autoload['config']) > 0) {
                    $CI =& get_instance();
                    foreach ($autoload['config'] as $key => $val) {
                        $CI->config->load($val);
                    }
                }
            }

            // Autoload helpers and languages
            foreach (array('helper', 'language') as $type) {
                if (isset($autoload[$type]) and count($autoload[$type]) > 0) {
                    $this->$type($autoload[$type]);
                }
            }

            // A little tweak to remain backward compatible
            // The $autoload['core'] item was deprecated
            if (! isset($autoload['libraries']) and isset($autoload['core'])) {
                $autoload['libraries'] = $autoload['core'];
            }

            // Load libraries
            if (isset($autoload['libraries']) and count($autoload['libraries']) > 0) {
                // Load the database driver.
                if (in_array('database', $autoload['libraries'])) {
                    $this->database();
                    $autoload['libraries'] = array_diff($autoload['libraries'], array('database'));
                }

                // Load all other libraries
                foreach ($autoload['libraries'] as $item) {
                    $this->library($item);
                }
            }

            // Autoload models
            if (isset($autoload['model'])) {
                $this->model($autoload['model']);
            }
        }
    }


    /**
     * Load View dari folder yang ditentukan
     *
     *
     * @param string   Path ke folder dimana file view berada
     * @param string   View file tanpa ekstensi .php
     * @param mixed    (optional) array|object  Variable yang diteruskan ke file view
     * @param boolean  (optional)
     * @return void
     * */
    public function viewPath($path, $view, $vars = null, $return = false)
    {
        $to_load = array(
            '_ci_view' => $view,
            '_ci_return' => $return
        );

        if (is_array($vars)) {
            $to_load['_ci_vars'] = $vars;
        } elseif (is_object($vars)) {
            $to_load['_ci_vars'] = $this->_ci_object_to_array($vars);
        }

        // add ontent_type view path
        if (! empty ($path)) {
            $this->_ci_view_paths = array_merge(
                $this->_ci_view_paths,
                array(rtrim($path, '/').'/' => true)
            );
        }

        return $this->_ci_load($to_load);
    }


    /**
     * Load Model dari folder yang ditentukan
     *
     *
     * @param string  Path ke folder model
     * @param string  File model tanpa ekstensi .php
     * @param mixed   (optional)
     * @return object|false
     * */
    public function modelPath($path, $model, $db_conn = false)
    {
        $file = null;
        $path = rtrim($path, '/').'/';
        $path_to = '';
        // Is the model in a sub-folder? If so, parse out the filename and path.
        if (($last_slash = strrpos($model, '/')) !== false) {
            // The path is in front of the last slash
            $path_to = substr($model, 0, $last_slash + 1);
            // And the model name behind it
            $model = substr($model, $last_slash + 1);
        }

        if (@file_exists($path.$path_to.$model.'.php')) {
            $file = $path.$model.'.php';
        } else {
            $model_name = strtolower($model);
            if (@file_exists($path.$path_to.$model_name.'.php')) {
                $file = $path.$path_to.$model_name.'.php';
            }
        }

        $CI =& get_instance();
        if ($file !== null) {
            if ($db_conn !== false and ! class_exists('CI_DB')) {
                if ($db_conn === true) {
                    $db_conn = '';
                }
                $CI->load->database($db_conn, false, true);
            }
            if (! class_exists('CI_Model')) {
                load_class('Model', 'core');
            }

            require_once $file;

            return new $model();
        }

        return false;
    }


    /**
     * Load the Database Forge Class
     *
     * @return string
     */
    /*
    public function dbforge()
    {
        if (! class_exists('CI_DB')) {
            $this->database();
        }

        $CI =& get_instance();

        require_once BASEPATH.'database/DB_forge.php';
        require_once BASEPATH.'database/drivers/'.$CI->db->dbdriver.'/'.$CI->db->dbdriver.'_forge.php';
        // Look for overload files in the /application/core folder
        if (file_exists(APPPATH.'core/MY_CI_DB_'.$CI->db->dbdriver.'_forge.php')) {
            require_once APPPATH.'core/MY_CI_DB_'.$CI->db->dbdriver.'_forge.php';
            $class = 'MY_CI_DB_'.$CI->db->dbdriver.'_forge';
        } else {
            $class = 'CI_DB_'.$CI->db->dbdriver.'_forge';
        }

        $CI->dbforge = new $class();
    }
    */
}
