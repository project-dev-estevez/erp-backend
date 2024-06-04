<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_model extends CI_Model {

    public function __construct() {
		parent::__construct();
		$this->load->database();
	}

    public function login( $user_id, $key, $level ) {
        
        date_default_timezone_set('America/Mexico_City');

        $data = [
            'user_id' => $user_id,
            'my_key' => $key,
            'level' => $level,
            'date_created' => date('Y-m-d H:i:s')
        ];

        $this->db->insert('api_keys', $data);

        return $this->db->insert_id();
    }

    public function get_user_by_username($username) {
        $this->db->where('username',$username);
        $query = $this->db->get('tbl_users');
        if($query->num_rows() == 1) {
            return $query->row();
        } else {
            return false;
        }
    }

    public function generate_random_token($length = 20) {
        $string = uniqid(rand());
        $randomString = substr($string, 0, $length);
        return $randomString;
    }
}