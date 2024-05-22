<?php

require_once("basedatos.php");
require_once("BEUbigeo.php");

class DAUbigeo{
    
	//Listado de departamentos vigentes
	public function listaDptoVigente(&$funciono){
			
		$db = new BaseDatos();		
		$sql ="SELECT Id_Dpto, Nom_Dpto, Est_Dpto, Id_ProvSelecDefaul , Id_DistSelecDefaul FROM departamento WHERE Est_Dpto ='V' Order By Nom_Dpto ASC";

		//echo $sql;
		$array = array ();
		//$array = array (":email" => $email);
		$result = $db->consulta2($sql,$array, $funciono);
		return $result;	      
	}

    //Listado de provincias vigentes de 1 dpto especifico
	public function listaProvVigentexDpto($idDpto, &$funciono){
			
		$db = new BaseDatos();		
		$sql ="SELECT Id_Provincia, Nom_Provincia, Est_Prov FROM provincia WHERE Id_Dpto =:IdDepartamento and Est_Prov ='V' Order By Nom_Provincia ASC;";

		//echo $sql;
		$array = array (":IdDepartamento" => $idDpto);
		//$array = array (":email" => $email);
		$result = $db->consulta2($sql,$array, $funciono);
		return $result;	      
	}

    //Listado de distritos vigentes de 1 prov especifica
	public function listaDistVigentexProv($idProv, &$funciono){
			
		$db = new BaseDatos();		
		$sql ="SELECT Id_Distrito, Nom_Distrito, Est_Distrito FROM distrito WHERE Id_Provincia = :IdProvincia and Est_Distrito ='V' Order By Nom_Distrito ASC;";

		//echo $sql;
		$array = array (":IdProvincia" => $idProv);
		//$array = array (":email" => $email);
		$result = $db->consulta2($sql,$array, $funciono);
		return $result;	      
	}



}
