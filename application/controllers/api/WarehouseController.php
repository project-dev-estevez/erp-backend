<?php

require_once APPPATH . 'validations/WareHouseValidation.php';
require APPPATH . 'libraries/RestController.php';
use chriskacerguis\RestServer\RestController;

defined('BASEPATH') or exit('No direct script access allowed');

class WarehouseController extends RestController{

    protected static $warehouseValidationInstance;

    public function __construct(){
        parent::__construct();
        $this->load->model('warehouse_model');
        
        if (!isset(self::$warehouseValidationInstance)) {
            self::$warehouseValidationInstance = new WareHouseValidation();
        }
        $this->warehousevalidation = self::$warehouseValidationInstance;
    }

    public function activate_or_desactivate_put( $uid ){

        //***********VALIDATION***********VALIDATION***********
        $data = $this->input->post();
        $validation = $this->warehousevalidation
                           ->validate_activate_or_desactivate( $uid, $data );
        
        if($validation['error']){
            $this->response($validation, RestController::HTTP_INTERNAL_ERROR);
        }
        //-----------VALIDATION-----------VALIDATION-----------

        $check = $this->warehouse_model
                      ->activate_or_desactivate( $uid );
        
        // No se pudo realizar el proceso
        if(!$check){
            $response = [
                'error'     => true,
                'message'   => $check
            ];

            $this->response($response, RestController::HTTP_INTERNAL_ERROR);
        }

        // Si se pudo realizar el proceso
        $response = [
            'error'     => false,
            'message'   => 'Almacén activado correctamente.'
        ];

        $this->response($response, RestController::HTTP_OK);
    }

    public function transfer_arm_post(){
        
        $check = $this->warehouse_model->transfer_arm();

        // No se pudo realizar el proceso
        if(!$check){
            $response = [
                'error'     => true,
                'message'   => 'Ocurrio un problema intente nuevamente.l3'
            ];

            $this->response($response, RestController::HTTP_INTERNAL_ERROR);
        }

        // Si se pudo realizar el proceso
        $response = [
            'error'     => false,
            'message'   => 'Se aprobó correctamente'
        ];

        $this->response($response, RestController::HTTP_OK);
    }

}