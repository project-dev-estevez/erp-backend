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
            $this->response($validation, RestController::HTTP_BAD_REQUEST);
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

    public function general_entries_get(){

        $query_params = $this->input->get();

        //***********VALIDATION***********VALIDATION***********
        // TODO
        //-----------VALIDATION-----------VALIDATION-----------

        $search_term = $query_params['search_term'];

        $list_of_items = $this->warehouse_model->get_general_entries(ID_ALMACEN_GENERAL, $search_term );
        $total_items = count( $list_of_items );

        $response = [
            'list_of_items' => $list_of_items,
            'total_items' => $total_items
        ];

        $this->response($response, RestController::HTTP_OK);
    }

}