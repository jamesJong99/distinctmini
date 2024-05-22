<?php

require_once("basedatos.php");

class DACuponImagen{
    
	//buscarcupones usado en index para el cliente y adm_mostrarcliente.php
    public function buscarcupones($codcliente ,$tipo, &$funciono){
        
		$db = new BaseDatos();
		$timezone  = -5; //(GMT -5:00) EST (U.S. & Canada)
		$fecha= gmdate("Y/m/j", time() + 3600*($timezone+date("I")));

		$sql = "CALL uspObtenerListadCuponImagen(:codigocliente,:tipo)";

        $array = array (":codigocliente" => $codcliente ,":tipo" => $tipo);
		$result = $db->consulta2($sql,$array, $funciono);
		return $result;	      
    }
	

	//buscar si codigo de cupon existe
    public function buscarCuponPorCodigo($codigocupon , &$funciono){
        
		$db = new BaseDatos();
		$timezone  = -5; //(GMT -5:00) EST (U.S. & Canada)
		$fecha= gmdate("Y/m/j", time() + 3600*($timezone+date("I")));

		$sql ="SELECT IdCupon, CodigoCupon, TipoDcto, CantidadDcto, 
		TipoAplicacion, ValorAplicacion, MontoMinimo, CuponPublico, CuponNecesitaCumplirCond 
		FROM cupon 
		WHERE CodigoCupon =:code and StatusCupon ='V' 
		AND FecInicio <= :fecha AND FecFin >=  :fecha";

        $array = array (":code" => $codigocupon ,":fecha" => $fecha);
		$result = $db->consulta2($sql,$array, $funciono);
		return $result;	      
    }


}
