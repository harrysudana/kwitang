<?php
if (! defined('FRONT_PATH')) {
    exit('Kwitang ERRor..!!!');
}
/**
 * Model data pengguna
 *
 * @package     Kwitang
 * @subpackage  Models
 * @author      Iyan Kushardiansah <iyank4@gmail.com>
 */
class Users extends CI_Model
{
    /**
     * Simpan data pengguna
     *
     * @param  Array
     * @return Boolean
     */
    public function insert($data)
    {
        if (empty($data['username']) or empty($data['password'])
             or empty($data['fullname']) or empty($data['email'])) {
            return false;
        }

        $data['password'] = $this->__hashPassword($data['password']);

        if (isset($data['config']) and is_array($data['config'])) {
            $data['config'] = serialize($data['config']);
        }

        $ret = $this->db->insert('user', $data);

        if ($ret) {
            user_log('user', 'add', $data['username'].' - '.$data['fullname'].' <'.$data['email'].'>');
        }

        return $ret ? true : false;
    }


    /**
     * Perbaharui data user
     *
     * @param  Array
     * @return Boolean
     */
    public function update ($data)
    {
        if (empty($data['username']) and count($data) < 2) {
            return false;
        }

        // tambahkan field new_username untuk mengganti username
        if (! empty($data['new_username'])) {
            $username = $data['username'];
            $data['username'] = $data['new_username'];
            unset($data['new_username']);
        } else {
            $username = $data['username'];
            unset($data['username']);
        }

        if (isset($data['password'])) {
            $data['password'] = $this->__hashPassword($data['password']);
        }

        // disable forget password request
        $data['reset_time'] = null;

        $this->db->where('username', $username);
        $retval = $this->db->update('user', $data);

        if ($retval) {
            $message = '';
            foreach ($data as $key => $value) {
                $message .= $key.'='.$value.';';
            }
            user_log('user', 'update', $message);
        }

        return $retval;
    }


    /**
     * Hapus user
     *
     * @param  String
     * @return Boolean
     */
    public function delete ($username)
    {
        $retval = false;
        $user = $this->get($username, false);
        if ($user) {
            $this->db->where('username', $user->username);
            $retval = $this->db->delete('user');

            if ($retval) {
                user_log('user', 'delete', $user->username.' - '.$user->fullname.' <'.$user->email.'>');
            }
        }

        return $retval;
    }


    /**
     * Ambil semua user
     *
     * Untuk kebaikan, secara default query dibatasi 1000 data.
     *
     * @param  Integer
     * @param  String  Field name
     * @return Array
     */
    public function all($limit = 1000, $order_by = 'fullname')
    {
        $this->db->select('user.*,role.title as role_name');
        $this->db->from('user');
        $this->db->join('role', 'user.role_id=role.id', 'left');
        $this->db->order_by($order_by);
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        $query = $this->db->get();

        return $query->result();
    }


    public function get($username_or_email, $only_active = true)
    {
        $this->db->select('user.*,role.title as role_name');
        $this->db->from('user');
        $this->db->join('role', 'user.role_id=role.id', 'left');
        $tmp = $this->db->escape($username_or_email);
        $this->db->where("(user.username=$tmp or user.email=$tmp)");

        if ($only_active === true) {
            $this->db->where('active', true);
        }

        $q = $this->db->get();
        if ($q->num_rows() == 1) {
            $ret = $q->row();
        } else {
            $ret = false;
        }

        return $ret;
    }


    public function getByUsername ($username, $only_active = true)
    {
        $this->db->select('user.*,role.title as role_name');
        $this->db->from('user');
        $this->db->join('role', 'user.role_id=role.id', 'left');
        $this->db->where('user.username', $username);
        if ($only_active === true) {
            $this->db->where('user.active', true);
        }

        $q = $this->db->get();
        if ($q->num_rows() == 1) {
            $ret = $q->row();
        } else {
            $ret = false;
        }

        return $ret;
    }


    public function getByEmail($email, $only_active = true)
    {
        $this->db->select('user.*,role.title as role_name');
        $this->db->from('user');
        $this->db->join('role', 'user.role_id=role.id', 'left');
        $this->db->where('user.email', $email);
        if ($only_active === true) {
            $this->db->where('user.active', true);
        }

        $q = $this->db->get();
        if ($q->num_rows() == 1) {
            $ret = $q->row();
        } else {
            $ret = false;
        }

        return $ret;
    }


    public function getByResetToken($reset_token)
    {
        $this->db->from('user');
        $this->db->where('reset_token', $reset_token);
        $q = $this->db->get();

        if ($q->num_rows() == 1) {
            $row = $q->row();
            /** TODO: Work on GMT ?, Custom config untuk waktu valid token */
            $jem = time() - strtotime($row->reset_time);
            // token valid untuk 24 jam
            $jam = 24;
            $max_valid = $jam * 3600;
            if ($jem < $max_valid) {
                return $row;
            }
        }

        return false;
    }

    public function getLog($username, $page_number = 1, $item_perpage = 50)
    {
        $user_obj = $this->getByUsername($username, false);
        $ret = array(
            'user_id'      => $user_obj->id,
            'username'     => $username,
            'page_number'  => $page_number,
            'item_perpage' => $item_perpage
        );

        $sql = 'SELECT COUNT(*) AS total FROM user_log WHERE user_id = ? LIMIT 1';
        $r = $this->db->query($sql, array($user_obj->id));
        $q = $r->row();

        $ret['total'] = $q->total;
        $ret['total_page'] = ceil($q->total / $item_perpage);

        $start = ($page_number - 1) * $item_perpage;
        $this->db->order_by('timestamp', 'desc');
        $userlog = $this->db->get_where('user_log', array('user_id' => $user_obj->id), $item_perpage, $start);

        $ret['data'] = $userlog->result();

        return $ret;
    }

    public function getConfig($username, $config_key = '', $limit = 1000)
    {
        $retval   = null;
        $user_obj = $this->getByUsername($username, false);

        if (! empty($user_obj->id)) {
            $this->db->where('user_id', $user_obj->id);

            if ($config_key == '') {
                // return all
                $this->db->limit($limit);
                $query = $this->db->get('user_config');
                $retval = $query->result();
            } else {
                $this->db->where('keyname', $config_key);
                $this->db->limit(1);
                $query = $this->db->get('user_config');
                if ($query->num_rows() > 0) {
                    $row    = $query->row();
                    $retval = $row->value;
                }
            }
        }

        return $retval;
    }

    public function setConfig($username, $config_key, $config_value)
    {
        $retval   = null;
        $user_obj = $this->getByUsername($username, false);

        if (! empty($user_obj->id)) {
            $oldval   = $this->getConfig($username, $config_key);

            if ($oldval === null) {
                $data = array('user_id'=>$user_obj->id,
                              'keyname'=>$config_key,
                              'value'=>$config_value);
                $retval = $this->db->insert('user_config', $data);
            } else {
                $this->db->where('user_id', $user_obj->id);
                $this->db->where('keyname', $config_key);
                $retval = $this->db->update('user_config', array('value'=>$config_value));
            }
        }

        return $retval;
    }

    /**
     * Cek apakah username diperbolehkan.
     *
     * Digunakan untuk membuat user baru.
     *
     * @param  String
     * @return Boolean
     */
    public function isValidUsername ($username, $min_length = 4, $blacklist_name = null)
    {
        if (! is_array($blacklist_name)) {
            $blacklist_name = array('admin', 'administrator', 'system', 'root', 'toor');
        }

        $retval = false;
        if (strlen($username) < $min_length) {
            $retval = false;
        } elseif (in_array(strtolower($username), $blacklist_name)) {
            $retval = false;
        } else {
            $this->db->from('user');
            $this->db->where('username', $username);

            $query = $this->db->get();
            if ($query and $query->num_rows() == 0) {
                $retval = true;
            }
        }

        return $retval;
    }

    /**
     * Cek apakah user ada di database, dan user tersebut boleh login
     *
     * return null jika user tersebut tidak ada di database
     *
     * @param  String
     * @param  String
     * @return Boolean|Null
     */
    public function validateUser($username_or_email, $password)
    {
        $user = $this->get($username_or_email, true);

        if (! $user) {
            return null;
        }

        $prefix = substr($user->password, 0, 3);
        $hashed = '';
        switch($prefix) {
            case '00:':
                $hashed = $this->__hashPassword($password);
                break;
            default:
                $hashed = $this->__hashPasswordOld($password);
        }

        return ($user->password === $hashed) ? true : false;
    }

    /**
     * Hash the password
     *
     * @param  String
     * @return String
     */
    private function __hashPassword($password)
    {
        $retval = '00:'.sha1($password);

        return $retval;
    }

    private function __hashPasswordOld($password)
    {
        // weird?
        return rawurlencode(base64_encode(md5(sha1($password))));
    }
}
