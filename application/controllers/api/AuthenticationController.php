<?php

require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;

defined('BASEPATH') OR exit('No direct script access allowed');

class AuthenticationController extends RestController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function login_post()
    {
        log_message('error', "llamadaa");
        $username = $this->input->post('username');
        
        if ($username) {
            log_message('error', $username);
            // Procesar el login aquí con el $username
            $response = [
                'status' => true,
                'message' => 'Login exitoso',
                'username' => $username
            ];
            $this->response($response, RestController::HTTP_OK);
        } else {
            // Manejar el error de datos de entrada no válidos
            $response = [
                'status' => false,
                'message' => 'Datos de entrada no válidos'
            ];
            $this->response($response, RestController::HTTP_BAD_REQUEST);
        }
    }

}