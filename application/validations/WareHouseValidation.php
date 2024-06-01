<?php

defined('BASEPATH') or exit('No direct script access allowed');

class WareHouseValidation{

    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();
        $this->CI->load->model('warehouse_model');
    }

    public function validate_activate_or_desactivate( $uid, $data )
    {
        // Verificar si el UID existe en la tabla "tbl_almacenes"
        $almacen_exist = $this->CI->warehouse_model->get_warehouse_by_uid($uid);
        if (!$almacen_exist) {
            return [
                'error' => true,
                'message' => 'El UID no es válido'
            ];
        }

        // Verificar si el campo "estatus" está presente y es 0 o 1
        if(!isset($data['estatus']) || ($data['estatus'] !== 0 && $data['estatus'] !== 1)){
            return [
                'error' => true,
                'message' => 'Verifique el campo estatus'
            ];
        }

        return [ 'error' => false ];
    }
}