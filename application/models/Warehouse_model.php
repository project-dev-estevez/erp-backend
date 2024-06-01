<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Warehouse_model extends CI_Model {

    public function __construct() {
		parent::__construct();
		$this->load->database();
	}

	public function get_warehouse_by_uid($uid) {
        // Consulta la base de datos para obtener el almacén con el UID proporcionado
        $this->db->where('uid', $uid);
        $query = $this->db->get('tbl_almacenes');

        // Verifica si se encontró algún almacén con el UID proporcionado
        if ($query->num_rows() > 0) {
            // Devuelve el resultado como un objeto
            return $query->row();
        } else {
            // Devuelve null si no se encontró ningún almacén con el UID proporcionado
            return null;
        }
    }

	public function activate_or_desactivate( $uid ){
		$data = [
			'estatus' => $this->input->post('estatus')
		];
		$this->db->set($data);
		$this->db->where('uid', $uid);
		$result = $this->db->update('tbl_almacenes');
		return  $result;
	}

	//Traspasa material de un brazo a otro
	public function transfer_arm() {

		$segmento = $this->input->post('segmento');
		$queryalmacen = $this->db->query("SELECT idtbl_almacenes, uid FROM tbl_almacenes WHERE tbl_segmentos_proyecto_idtbl_segmentos_proyecto = $segmento");
				
		foreach($this->input->post('iddtl_solicitud_material') AS $key => $value){
            if ($this->input->post('cantidad_justificacion')[$key] > 0) {
                $this->db->set('cantidad', 'cantidad - ' . $this->input->post('cantidad_justificacion')[$key], false);
				$this->db->set('entregado', 'entregado - ' . $this->input->post('cantidad_justificacion')[$key], false);
				$this->db->where('iddtl_solicitud_material', $this->input->post('iddtl_solicitud_material')[$key]);
				$this->db->update('dtl_solicitud_material');

				$this->db->set('cantidad', 'cantidad - ' . $this->input->post('cantidad_justificacion')[$key], false);
				$this->db->where('iddtl_asignacion', $this->input->post('iddtl_asignacion')[$key]);
				$this->db->update('dtl_asignacion');
            }
		}
		
		$data = array(
			'estatus_solicitud' => 'S',
			'fecha_creacion' => date('Y-m-d H:i:s'),
			'uid' => uniqid(),
			'tbl_usuarios_idtbl_usuarios' => $this->session->userdata('id_usuario'),
			'tbl_users_idtbl_users_autor' => $this->session->userdata('id'),
			'tbl_proyectos_idtbl_proyectos' => $this->input->post('proyecto'),
			'tbl_segmentos_proyecto_idtbl_segmentos_proyecto' => $this->input->post('segmento'),
			'tbl_usuarios_idtbl_usuarios_supervisor' => $this->session->userdata('id_usuario'),
			'uid_almacen_seleccionado' => $queryalmacen->result()[0]->uid,
			'fecha_modificacion' => date('Y-m-d H:i:s'),
			'tipo_producto' => 'Almacen General',
			'tbl_mantenimientos_idtbl_mantenimientos' => $this->input->post('brazo_destino')
		);
		$this->db->insert('tbl_solicitud_material', $data);
		$insert_id = $this->db->insert_id();

		foreach($this->input->post('iddtl_solicitud_material') AS $key => $value){
            if ($this->input->post('cantidad_justificacion')[$key] > 0) {
                $data_dtl = array(
					'cantidad' => $this->input->post('cantidad_justificacion')[$key],
					'tbl_solicitud_material_idtbl_solicitud_material' => $insert_id,
					'tbl_catalogo_idtbl_catalogo' => $this->input->post('producto')[$key],
					'entregado' => $this->input->post('cantidad_justificacion')[$key]
				);
				$this->db->insert('dtl_solicitud_material', $data_dtl);
            }
		}

		$querysalida = $this->db->query("SELECT COUNT(idtbl_almacen_movimientos) as total FROM `tbl_almacen_movimientos` WHERE `tipo` = 'salida-almacen'");

		$data_almacen = array(
			'fecha' => date('Y-m-d H:i:s'),
			'tbl_almacenes_idtbl_almacenes' => $queryalmacen->result()[0]->idtbl_almacenes,
			'uid' => uniqid(),
			'tipo' => 'salida-almacen',
			'tbl_usuarios_idtbl_usuarios' => $this->session->userdata('id_usuario'),
			'estatus' => 1,
			'tbl_users_idtbl_users' => $this->session->userdata('id'),
			'folio' => $querysalida->result()[0]->total + 1,
			'tbl_proyectos_idtbl_proyectos' => $this->input->post('proyecto'),
			'tbl_segmentos_proyecto_idtbl_segmentos_proyecto' => $this->input->post('segmento'),
			'parent' => $insert_id,
			'movimiento_virtual' => 0			
		);

		$result = $this->db->insert('tbl_almacen_movimientos', $data_almacen);
		$insert_id_almacen = $this->db->insert_id();

		$idtbl_almacenes = $queryalmacen->result()[0]->idtbl_almacenes;
		foreach($this->input->post('iddtl_solicitud_material') AS $key => $value){
            if ($this->input->post('cantidad_justificacion')[$key] > 0) {
				$idtbl_catalogo = $this->input->post('producto')[$key];
				$querydtl = $this->db->query("SELECT iddtl_almacen FROM dtl_almacen WHERE tbl_almacenes_idtbl_almacenes = $idtbl_almacenes AND tbl_catalogo_idtbl_catalogo = $idtbl_catalogo AND estatus = 'almacen'");
                $data_dtl = array(
					'fecha_asignacion' => date('Y-m-d H:i:s'),
					'dtl_almacen_iddtl_almacen' => $querydtl->result()[0]->iddtl_almacen,
					'tbl_usuarios_idtbl_usuarios' => $this->session->userdata('id_usuario'),
					'tbl_almacen_movimientos_idtbl_almacen_movimientos' => $insert_id_almacen,
					'cantidad' => $this->input->post('cantidad_justificacion')[$key]
				);
				$this->db->insert('dtl_asignacion', $data_dtl);
            }
		}
		
		return  $result;
	}



    
}