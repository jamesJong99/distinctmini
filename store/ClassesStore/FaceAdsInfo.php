<?php

require_once("basedatos.php");
require_once("BEUbigeo.php");

class FaceAdsInfo{
    
	// get user location information by id 
	public function getLocationByClinent($codCliente, &$funciono){
		$db = new BaseDatos();		
		$sql ="SELECT a.CodCliente , b.NumPed , a.FechaPedido ,
        b.Id_Dpto , obtenerDepartamento(b.Id_Dpto, 0) as 'NomDpto' ,
        b.Id_Provincia , obtenerProvincia(b.Id_Dpto, 0) as 'NomProv',
        b.Id_Distrito , obtenerDistrito(b.Id_Dpto, 0) as 'NomDist'
        FROM pedido a INNER JOIN pedido_envio b on(a.NumPed = b.NumPed) WHERE a.CodCliente = :code ORDER BY a.NumPed DESC LIMIT 1;";
		//echo $sql;
		$array = array (":code" => $codCliente);
		$result = $db->consulta2($sql, $array, $funciono);
		return $result;	      
	}
}
