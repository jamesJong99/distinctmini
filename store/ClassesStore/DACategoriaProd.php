<?php

require_once("basedatos.php");
require_once("BECategoriaProd.php");

class DACategoriaProd{
    
	//Buscar informacion de un cliente sin password
	

	//Buscar informacion de un cliente sin password
	public function obtenerCategoria($idcateg, &$funciono ){
        
		$db = new BaseDatos();
		$sql ="SELECT IdCategoria , NomCategoria, TipoFotoVideo, RutaFotoCategoria , EstCategoria, DescCategoria FROM categoriaprod where IdCategoria =:ide;";
			
		$array = array (":ide" => $idcateg);
		$result = $db->consulta2($sql,$array, $funciono);
		return $result;	
			
    }



}
