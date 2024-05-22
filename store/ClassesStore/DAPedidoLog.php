<?php

require_once("basedatos.php");
require_once("BEPedidoLog.php");

class DAPedidoLog{
    

    //LOG
	public function Log($ped, $cod, $descrip, $codusu){
        
        $BEPedidoLog = new BEPedidoLog();

        $timezoneLog  = -5; //(GMT -5:00) EST (U.S. & Canada)
        $fechaHoyLog= gmdate("Y-m-d H:i:s", time() + 3600*($timezoneLog+date("I")));

        $numpedLog = $ped;
        $codAccionLog = $cod;
        $descripcionLog = $descrip;
        $descripcionLog = substr($descripcionLog, 0, 300);
        $codigoUsuarioLog =$codusu;

        $BEPedidoLog->setNumPed($numpedLog);
        $BEPedidoLog->setCodAccion($codAccionLog);
        $BEPedidoLog->setDescripcion($descripcionLog);
        $BEPedidoLog->setCodUsuario($codigoUsuarioLog);
        $BEPedidoLog->setFecHora($fechaHoyLog);

        $bolLog =0;
        $this->registrarLog($BEPedidoLog, $bolLog);

    }


    //Registrar un pedido 
	public function registrarLog($BEPedidoLog, &$funciono){
        $db = new BaseDatos();

		$entidad = new BEPedidoLog();
		$entidad = $BEPedidoLog;

        $sql ="INSERT INTO pedido_log (NumPed, CodAccion, Descripcion, CodUsuario, FecHora) 
        VALUES (:NumPed, :CodAccion , :Descrip , :CodUsuario , :FecHora);";


		$array = array (":NumPed" => $entidad->getNumPed() ,":CodAccion" => $entidad->getCodAccion() 
         ,":Descrip" => $entidad->getDescripcion() ,":CodUsuario" => $entidad->getCodUsuario() 
        ,":FecHora" => $entidad->getFecHora()  );
		
		//echo $sql;
		//print_r($array);
		$db->ejecutar2($sql,$array, $funciono);
		//echo "Funciono ($funciono)";
    }

    //Obtener pedidos item de para gestionar pedidos en base a fecha
    public function obtenerLogNumPedIn($NumPedIN , &$funciono){
        
		$db = new BaseDatos();

		$sql ="SELECT a.NumPed, b.CodAccion, b.IdPedidoLog , b.Descripcion, b.CodUsuario, c.NomUsuario, b.FecHora  
		FROM pedido a INNER JOIN pedido_log b on (a.NumPed = b.NumPed)  
        INNER JOIN usuario c on (b.CodUsuario = c.CodUsuario) 
        WHERE a.NumPed IN($NumPedIN) ORDER BY a.NumPed DESC , b.FecHora DESC;";

		$array = array (":Dias" => "");

		//print_r($array);

		$result = $db->consulta2($sql,$array, $funciono);
		//print_r($result);
		return $result;	      
    }


	


}
