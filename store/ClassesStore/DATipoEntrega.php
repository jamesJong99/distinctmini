<?php

require_once("basedatos.php");
require_once("BETipoEntrega.php");

class DATipoEntrega{
    
	//Listado de departamentos vigentes
	public function obtenerListadoTipoEntrega($idDpto, $idProv, $idDist, &$funciono){
			
		$db = new BaseDatos();		
		$sql ="SELECT a.Id_TipoEntregaUbic , a.Nom_Zona, a.Id_TipoEntrega , b.NomTipoEntrega, b.CodCourier, c.NomCourier , a.Costo , a.TiempoEntrega, b.Consideraciones
FROM tipoentrega_ubicacion a
INNER JOIN tipoentrega b on (a.Id_TipoEntrega = b.Id_TipoEntrega)
INNER JOIN courier c on (b.CodCourier = c.CodCourier)
where a.Id_Dpto = :idDpto and a.Id_Prov IN(:idProv,-1) and a.Id_Dist IN(:idDist,-1)
AND a.Status ='V' and b.EstatusTipoEntrega ='V';";

		//echo $sql;
		$array = array (":idDpto" => $idDpto,":idProv" => $idProv,":idDist" => $idDist);
		//$array = array (":email" => $email);
		$result = $db->consulta2($sql,$array, $funciono);
		return $result;  
	}

	//Listado de departamentos vigentes
	public function obtenerTipoEntrega($idDpto, $idProv, $idDist ,  &$funciono){
			
		$db = new BaseDatos();		
		$sql ="SELECT a.Id_TipoEntregaUbic , a.Nom_Zona, a.Id_TipoEntrega , b.NomTipoEntrega, b.CodCourier, c.NomCourier , a.Costo , a.TiempoEntrega, b.Consideraciones
FROM tipoentrega_ubicacion a
INNER JOIN tipoentrega b on (a.Id_TipoEntrega = b.Id_TipoEntrega)
INNER JOIN courier c on (b.CodCourier = c.CodCourier)
where a.Id_Dpto = :idDpto and a.Id_Prov IN(:idProv,-1) and a.Id_Dist IN(:idDist,-1)	 
AND a.Status ='V' and b.EstatusTipoEntrega ='V';";

		//echo $sql;
		$array = array (":idDpto" => $idDpto,":idProv" => $idProv,":idDist" => $idDist);
		//$array = array (":email" => $email);
		$result = $db->consulta2($sql,$array, $funciono);
		return $result;  
	}


	//Listado de departamentos vigentes
	public function obtenerTipoEntregaUbic($idUbic ,  &$funciono){
			
		$db = new BaseDatos();		
		$sql ="SELECT a.Costo
		FROM tipoentrega_ubicacion a
		where a.Id_TipoEntregaUbic = :idUbic	 
		AND a.Status ='V';";

		//echo $sql;
		$array = array (":idUbic" => $idUbic);
		//$array = array (":email" => $email);
		$result = $db->consulta2($sql,$array, $funciono);
		return $result;  
	}


	//Listado de departamentos vigentes
	public function obtenerTipoEntregaUbic_Ubigeo($idUbic , $dpto, $prov, $dist, &$funciono){
			
		$db = new BaseDatos();		
		$sql ="SELECT a.Id_TipoEntrega , b.NomTipoEntrega , 
		obtenerDepartamento(:iddpto,0) as 'NomDpto' ,
		obtenerProvincia(:idprov,0) as 'NomProv' ,
		obtenerDistrito(:iddist,0) as 'NomDist'  ,
		a.TiempoEntrega , b.TipoEntrega
		FROM tipoentrega_ubicacion a
		INNER JOIN tipoentrega b on (a.Id_TipoEntrega = b.Id_TipoEntrega) 
		where a.Id_TipoEntregaUbic = :idUbic	 
		AND a.Status ='V';";

		//echo $sql;
		$array = array (":idUbic" => $idUbic,":iddpto" => $dpto,":idprov" => $prov,":iddist" => $dist);
		//$array = array (":email" => $email);
		$result = $db->consulta2($sql,$array, $funciono);
		return $result;  
	}


	//Listado de departamentos vigentes
	public function obtenerConsideracionTipoEntrega($idTipoEntrega , &$funciono){
			
		$db = new BaseDatos();		
		$sql ="SELECT a.Consideraciones
		FROM tipoentrega a
		WHERE a.Id_TipoEntrega = :idcod;";

		//echo $sql;
		$array = array (":idcod" => $idTipoEntrega);
		//$array = array (":email" => $email);
		$result = $db->consulta2($sql,$array, $funciono);
		return $result;  
	}


}
