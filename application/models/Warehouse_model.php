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

	public function get_general_entries($id_general_wirehouse, $search_term = '') {
		$query = $this->db->query("SELECT tam.idtbl_almacen_movimientos, tam.uid, tam.fecha, ctd.tipo_documento, tam.numero_documento, tu.nombre, tam.folio, tp.nombre_proyecto, tp.numero_proyecto, tped.neodata_pedido, tped.tbl_proveedores_idtbl_proveedores, tpro.nombre_fiscal, dam.cantidad, dam.precio, tc.descripcion FROM tbl_almacen_movimientos tam LEFT JOIN ctl_tipo_documento ctd ON ctd.idctl_tipo_documento = tam.ctl_tipo_documento_idctl_tipo_documento LEFT JOIN tbl_users tu ON tu.idtbl_users = tam.tbl_users_idtbl_users LEFT JOIN tbl_proyectos tp ON tp.idtbl_proyectos = tam.tbl_proyectos_idtbl_proyectos LEFT JOIN dtl_almacen_movimientos dam ON dam.tbl_almacen_movimientos_idtbl_almacen_movimientos = tam.idtbl_almacen_movimientos LEFT JOIN tbl_catalogo tc ON tc.idtbl_catalogo = dam.tbl_catalogo_idtbl_catalogo LEFT JOIN tbl_pedidos tped ON tped.idtbl_pedidos = tam.parent LEFT JOIN tbl_proveedores tpro ON tpro.idtbl_proveedores = tped.tbl_proveedores_idtbl_proveedores WHERE tam.tbl_almacenes_idtbl_almacenes = $id_general_wirehouse AND tam.tipo = 'entrada-almacen' AND (tam.uid LIKE '$search_term%' OR tam.folio LIKE '$search_term%' OR tam.fecha LIKE '$search_term%' OR tu.nombre LIKE '$search_term%' OR tped.neodata_pedido LIKE '%$search_term%' OR tpro.nombre_fiscal LIKE '%$search_term%' OR tp.nombre_proyecto LIKE '$search_term%' OR ctd.tipo_documento LIKE '$search_term%') GROUP BY tam.uid ORDER BY tam.folio DESC");
		return $query->result();
	}

	public function get_general_exits($id_general_wirehouse, $search_term = '') {
		$query = $this->db->query("SELECT tam.idtbl_almacen_movimientos, tam.uid, tam.fecha, tam.numero_documento, tu.nombre, tam.folio, tp.nombre_proyecto, tp.numero_proyecto, cae.nombre AS nombre_entrega, CONCAT(tus.nombres,' ',tus.apellido_paterno,' ',tus.apellido_materno) AS nombre_recibe FROM tbl_almacen_movimientos tam LEFT JOIN tbl_users tu ON tu.idtbl_users = tam.tbl_users_idtbl_users LEFT JOIN ctl_autorizados_entrega cae ON cae.idctl_autorizados_entrega = tam.ctl_autorizados_entrega_idctl_autorizados_entrega LEFT JOIN tbl_usuarios tus ON tus.idtbl_usuarios = tam.tbl_usuarios_idtbl_usuarios LEFT JOIN tbl_solicitud_material tsm ON tsm.idtbl_solicitud_material = tam.parent LEFT JOIN tbl_proyectos tp ON tp.idtbl_proyectos = tsm.tbl_proyectos_idtbl_proyectos WHERE tam.tbl_almacenes_idtbl_almacenes = $id_general_wirehouse AND tam.tipo = 'salida-almacen' AND (tam.uid LIKE '$search_term%' OR tam.folio LIKE '$search_term%' OR tam.fecha LIKE '$search_term%' OR tu.nombre LIKE '$search_term%' OR tp.nombre_proyecto LIKE '$search_term%') GROUP BY tam.uid ORDER BY tam.folio DESC");
		return $query->result();
	}



    
}