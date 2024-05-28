<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

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
            $this->log_response_headers($response, RestController::HTTP_OK);
            $this->response($response, RestController::HTTP_OK);
        } else {
            // Manejar el error de datos de entrada no válidos
            $response = [
                'status' => false,
                'message' => 'Datos de entrada no válidos'
            ];
            $this->log_response_headers($response, RestController::HTTP_BAD_REQUEST);
            $this->response($response, RestController::HTTP_BAD_REQUEST);
        }
    }

private function log_response_headers($response, $status_code)
{
    // Obtener los headers actuales
    $headers = $this->output->set_status_header($status_code)->get_headers();

    // Registrar los headers en el log
    foreach ($headers as $header => $value) {
        log_message('error', "$header: $value");
    }

    // Opcional: Registrar el cuerpo de la respuesta también
    log_message('error', 'Response Body: ' . json_encode($response));
}

}