<?php
class MY_Input extends CI_Input
{
    public function __construct()
    {
        parent::__construct();
    }

    public function post($index = NULL, $xss_clean = NULL)
    {
        // Si la solicitud es de tipo JSON
        if ($this->server('CONTENT_TYPE') == 'application/json') {
            $json_data = json_decode(trim(file_get_contents('php://input')), true);
            if (is_null($json_data)) {
                return NULL;
            }
            if ($index === NULL) {
                return $json_data;
            }
            return isset($json_data[$index]) ? $json_data[$index] : NULL;
        }
        // Para todas las demás solicitudes
        return parent::post($index, $xss_clean);
    }
}
