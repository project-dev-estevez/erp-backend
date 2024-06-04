<?php

require_once APPPATH . 'validations/AuthenticationValidation.php';
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;

defined('BASEPATH') OR exit('No direct script access allowed');

class AuthenticationController extends RestController
{

    protected static $authenticationValidationInstance;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('login_model');
        $this->methods['login_post']['key'] = false;

        if (!isset(self::$authenticationValidationInstance)) {
            self::$authenticationValidationInstance = new AuthenticationValidation();
        }
        $this->authenticationValidation = self::$authenticationValidationInstance;
    }

    public function login_post()
    {
        //***********VALIDATION***********VALIDATION***********
        $data = $this->input->post();
        $validation = $this->authenticationValidation
                           ->validate_login_data( $data );
        
        if($validation['error']){
            $this->response($validation, RestController::HTTP_BAD_REQUEST);
        }
        //-----------VALIDATION-----------VALIDATION-----------

        //***********[DB]VALIDATION PASSWORD***********[DB]VALIDATION PASSWORD***********
        $validation = $this->authenticationValidation
                           ->validate_password( $data );
        
        if($validation['error']){
            $this->response($validation, RestController::HTTP_BAD_REQUEST);
        }
        //-----------[DB]VALIDATION PASSWORD-----------[DB]VALIDATION PASSWORD-----------

        // Si la contraseña es correcta.
        $user_data = $validation['user_data'];

        // 1. generar un token y cifrarlo con PHP
        $random_token = $this->login_model->generate_random_token();
        // $token_hash = password_hash( $random_token, PASSWORD_DEFAULT ); --> Refinar Con Esto

        // 2. en el modelo de login, crear función para cear registros en la tabla api_keys.
        $is_logued = $this->login_model->login( $user_data->id, $random_token, 1 );

        log_message('info', "[Estevez] " . $user_data->nombre . " - " . "Inició Sesión!");
        $response = [
            'status' => true,
            'message' => 'Login exitoso!',
            'data' => $user_data,
            'token' => $random_token
        ];
        $this->response($response, RestController::HTTP_OK);
    }

}