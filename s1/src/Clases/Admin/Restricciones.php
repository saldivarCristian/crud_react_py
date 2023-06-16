<?php 
	// //--validar--//
	// $rol_superior  = $conexion->consulta("SELECT * FROM roles_superiores rs JOIN roles r ON r.id_rol=rs.id_rol JOIN usuarios u ON u.id_rol=r.id_rol WHERE  rs.id_rol_superior=".$_SESSION['id_rol'],"");
	// $rol_supe_sub  = $conexion->consulta("SELECT * FROM roles_superiores r JOIN usuarios u  ON u.id_rol=r.id_rol_superior WHERE  r.id_rol=".$_SESSION['id_rol'],"");
	// $creador  = $conexion->consulta("SELECT id_rol FROM roles_superiores WHERE id_rol_superior=".$_SESSION['id_rol'],"");


	// $suc_superrior  = $conexion->consulta("SELECT * FROM sucursales WHERE  id_superior_sucursal=".$_SESSION['id_sucursal'],"");
	// $sucursales  = $conexion->consulta("SELECT id_sucursal FROM sucursales WHERE  id_sucursal=".$_SESSION['id_sucursal'],"");

	// if (!count($creador)) {
	// 	$ocultar_creador='false';
	// }
	// //ACEESO AL LOS MODULOS SEGUN SU ROL
	// if (!count($roles)){
	//     header("HTTP/1.0 404 Not Found");
	// }elseif ($roles[0]['permiso']==0){
	// 	header("HTTP/1.0 404 Not Found");
	// }elseif ($roles[0]['permiso']==1){
	// 	$validar='false';
	// }
	// //VISAULIZACION DE MODULOS
	// $ver_modulo=' AND(';
	// if ($modulos[0]['choose']==0) {
	// 	$ver_modulo='';
	// }elseif ($modulos[0]['choose']==1){
	// 	$ver_modulo.=' '.$alias.'.id_creador='.$_SESSION['id_usu'];
	// 	if (count($rol_superior)) {
	// 		foreach ($rol_superior as $id => $valor) {
	// 			$ver_modulo.=' or '.$alias.'.id_creador='.$valor['id_usu'];
	// 		}
	// 	}
	// 	$ver_modulo.=' )';

	// 	if (count($suc_superrior)) {
	// 		$ver_modulo .=' and ( '.$alias.'.sucursal='.$sucursales[0]['id_sucursal'].'';
	// 		foreach ($suc_superrior as $id_cuc => $val_suc) {
	// 			$ver_modulo.=' or '.$alias.'.sucursal='.$val_suc['id_sucursal'];
	// 		}
	// 	}else{
	// 		$ver_modulo .=' AND ( '.$alias.'.sucursal='.$sucursales[0]['id_sucursal'].'';
	// 	}

	// 	$ver_modulo.=' )';
		
	// }elseif ($modulos[0]['choose']==2){
	// 	$ver_modulo.=' '.$alias.'.id_creador='.$_SESSION['id_usu'];
	// 	if (count($rol_superior)) {
	// 		foreach ($rol_superior as $id => $valor) {
	// 			$ver_modulo.=' or '.$alias.'.id_creador='.$valor['id_usu'];
	// 		}
	// 	}
	// 	if (count($rol_supe_sub)) {
	// 		foreach ($rol_supe_sub as $ids => $val) {
	// 			$ver_modulo.=' or '.$alias.'.id_creador='.$val['id_usu'];
	// 		}
	// 	}
	// 	$ver_modulo.=' )';

	// 	if (count($suc_superrior)) {
	// 		$ver_modulo .=' or( '.$alias.'.sucursal='.$sucursales[0]['id_sucursal'].'';
	// 		foreach ($suc_superrior as $id_cuc => $val_suc) {
	// 			$ver_modulo.=' or '.$alias.'.sucursal='.$val_suc['id_sucursal'];
	// 		}
	// 	}else{
	// 		$ver_modulo .=' AND ( '.$alias.'.sucursal='.$sucursales[0]['id_sucursal'].'';
	// 	}
	// 	$ver_modulo.=' )';

	// }elseif ($modulos[0]['choose']==3){
	// 	// if (count($sucursales)) {
	// 	// 	$ver_modulo=' AND ('.$alias.'.id_sucursal='.$sucursales[0]['id_sucursal'].')';
	// 	// }

	// 	$ver_modulo =' AND( '.$alias.'.sucursal='.$sucursales[0]['id_sucursal'].'';
	// 	if (count($suc_superrior)) {
	// 		foreach ($suc_superrior as $id_cuc => $val_suc) {
	// 			$ver_modulo.=' or '.$alias.'.sucursal='.$val_suc['id_sucursal'];
	// 		}
	// 	}
	// 		$ver_modulo.=' )';
	// 	// echo $ver_modulo;
	// }
	// //----//


    namespace Clases\Admin;
    use \PDO;
    use \Exception;
    use \Error;
    class Restricciones {
        public static function verificarAccesoAlModulo($db,$idRol,$idMenu){
			
			$sql = "SELECT * FROM ".DB_CON.".base_roles_permisos WHERE id_rol=$idRol and id_menu=$idMenu ";
			$stmt = $db->query($sql);
			$data = $stmt->fetchAll(PDO::FETCH_OBJ);
			$count = $stmt->rowCount();
			if(!$count){
				throw new Error("Acceso no permitido. Comuníquese con el proveedor.", 10);
			}
			return $data;
		}
    }

?>