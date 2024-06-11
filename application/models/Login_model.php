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

    public function logout( $api_key ) {
        // Delete the API key from the database
        $this->db->where('my_key', $api_key);
        return $this->db->delete('api_keys');
    }

    public function get_user_by_username($username) {
        $this->db->where('username',$username);
        $query = $this->db->get('tbl_users');

        if($query->num_rows() != 1){
            return false;
        }

        return $query->row();
    }

    public function check_user_token( $token ) {
        $this->db->where('my_key',$token);
        $query = $this->db->get('api_keys');
        if($query->num_rows() != 1) {
            return false;
        }

        $token_data = $query->row();
        $date_created = $token_data->date_created;

        $created_date = new DateTime($date_created);
        $currentDate = new DateTime();
        $interval = date_diff($currentDate, $created_date);

        // Verifica si han pasado más de 30 días
        if ($interval->days > 30) {
            // Elimina el token de la base de datos
            $this->db->where('my_key', $token);
            $this->db->delete('api_keys');
            return false;
        }

        return true;
    }

    public function generate_random_token($length = 20) {
        $string = uniqid(rand());
        $randomString = substr($string, 0, $length);
        return $randomString;
    }
}