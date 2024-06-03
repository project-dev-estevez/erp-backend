<?php  
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login_model extends CI_Model {

    public function __construct() {
		parent::__construct();
		$this->load->database();
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
}