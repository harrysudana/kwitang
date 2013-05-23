<?php
if (! defined('BASEPATH')) {
    exit ('No direct script access allowed');
}


/**
 * Authentication Controller
 *
 * @package  Kwitang\Controllers
 * @author   Iyan Kushardiansah <iyank4@gmail.com>
 */
class Auth extends CI_Controller
{
    public function __construct() {
        parent::__construct();

        $this->load->helper('language');
        $this->load->helper('kwitang_fe');
        $this->load->helper('form');
        $this->load->helper('text');
    }

    public function index()
    {
        redirect(site_url('user/login'));
    }


    /**
     * Periksa keabsahan data pengguna yang dikirim melalui POST, dan lakukan
     * proses login
     *
     * Menerima variabel yang dikirimkan menggunakan form POST:
     * - username
     * - password
     * - remember      Akan di ubah menjadi boolean, (1 atau 'on' = true)
     * - redirect_url  Jika pengguna berhasil login, kembali ke halaman ini
     * - login_url     Jika pengguna tidak sah, kembali ke halaman ini
     *
     * jika tidak ditentukan, maka
     * redirect_url : /admin/dashboard
     * login_url    : /user/login
     *
     * Jika gagal melakukan login, akan kembali ke login_url dengan ditambahkan
     * variable GET "?failed=X", dimana:
     * X = 1 Pengguna tidak ditemukan
     * X = 2 Password salah
     *
     * @return  void
     */
    public function validate()
    {
        $username  = $this->input->post('username');
        $password  = $this->input->post('password');
        $remember  = $this->input->post('remember');
        $remember  = ($remember == 'on' or $remember == 1) ? true : false;
        $redirect  = $this->input->post('redirect_url');
        $login_url = $this->input->post('login_url');
        $status    = false;
        $data_json = array();

        $ret = $this->kwitang->validateUser($username, $password, $remember);

        if ($ret) {
            $data_json['status'] = true;
            $redirect_url = empty($redirect) ? site_url('admin/dashboard') : $redirect;
        } else {
            if ($ret === null) {
                $failed  = 1;
                $message = lang('k_login_fail_user');
            } else {
                $failed  = 2;
                $message = lang('k_login_fail_password');
            }

            $redirect_url = empty($login_url) ? site_url('user/login') : $login_url;
            $redirect_url.= '?failed='.$status;
            $redirect_url.= '&message='.$message;

            $data_json['status']  = false;
            $data_json['failed']  = $failed;
            $data_json['message'] = $message;
        }

        if (! $this->input->is_ajax_request()) {
            redirect($redirect_url);
        } else {
            $data_json['redirect_url'] = $redirect_url;

            header('Content-type: application/json');
            echo json_encode($data_json);

            exit();
        }
    }


    /**
     * Proses mengeluarkan pengguna dari sistem
     *
     * Menghapus sesi login yang sedang berjalan
     *
     * @param   String  base64_encoded URL
     * @return  void
     */
    public function logout()
    {
        $this->kwitang->logout();
        $redirect_url = $this->input->get('redirect');
        $url = empty ($redirect_url) ? site_url('user/login') : $redirect_url;
        redirect($url);
    }
}
