<?php

require_once("basedatos.php");
require_once("BEPedidoItem.php");

class DAPedidoItem{


	//Obtener pedidos item de para gestionar pedidos en base a fecha
    public function obtenerPedidosPedidosIN($NumPedIN , &$funciono){
        
		$db = new BaseDatos();
		//$sql = "SELECT a.codcliente FROM cliente a WHERE a.email =:email";
		//$array = array (":email" => $email);

		
		$sql ="SELECT a.NumPed, b.CodProd, b.NomProd, b.Variante, b.Cantidad  
		FROM pedido a INNER JOIN pedido_item b on (a.NumPed = b.NumPed)  
		WHERE a.NumPed IN($NumPedIN) ORDER BY a.NumPed DESC";

		//echo $sql;

		$array = array (":Dias" => "");

		//print_r($array);

		$result = $db->consulta2($sql,$array, $funciono);
		//print_r($result);
		return $result;	      
    }
    

	//Obtener pedidos item de cliente para buscar clientes
    public function obtenerPedidosItemCliente($codcliente , &$funciono){
        
		$db = new BaseDatos();
		//$sql = "SELECT a.codcliente FROM cliente a WHERE a.email =:email";
		//$array = array (":email" => $email);

		$sql = "CALL UspObtPedidosItemDeCliente (:codcli)";
		$array = array (":codcli" => $codcliente);

		//print_r($array);

		$result = $db->consulta2($sql,$array, $funciono);
		//print_r($result);
		return $result;	      
    }

	//BuscarCliente
    public function validarNombresWix($listadProdConcat , &$funciono){
        
		$db = new BaseDatos();
		//$sql = "SELECT a.codcliente FROM cliente a WHERE a.email =:email";
		//$array = array (":email" => $email);


		$sql = "select count(CodProd) as 'cantidad' from producto_wix where NomProdWix in($listadProdConcat)";
		$array = array (":listado" => $listadProdConcat);

		//print_r($array);

		$result = $db->consulta2($sql,$array, $funciono);
		//print_r($result);
		return $result;	      
    }
    
	//Registrar un pedido item
	public function registrarPedidoItem($BEPedidoItem,  &$funciono){
        $db = new BaseDatos();

		$entidad = new BEPedido_Item();
		$entidad = $BEPedidoItem;

        $sql ="INSERT INTO `pedido_item`(`NumPed`, `CodProd`, `NomProd`, `Variante`, `SKU`, `Cantidad`, `PrecioProd`, `Ahorro`) VALUES (:numped, :codprod ,:nomprod,:variante,:sku,:cantidad,:precioprod,:ahorroprod)";
		$array = array (":numped" => $entidad->getNumPed() ,":codprod" => $entidad->getCodProd()  ,":nomprod" => $entidad->getNomProd()  ,":variante" => $entidad->getVariante()  ,":sku" => $entidad->getSku()  ,":cantidad" => $entidad->getCantidad() ,":precioprod" => $entidad->getPrecio(),":ahorroprod" => $entidad->getAhorro());
		
		

		//echo $sql;
		//print_r($array);
		$db->ejecutar2($sql,$array, $funciono);
		//echo "Funciono ($funciono)";
    }

	//Obtener Listado Pedidos Items según Listado de Pedidos
	public function obtenerPedidosItemPorLista($listadoPedido, $diasMax, &$funciono){
        $db = new BaseDatos();

        $sql ="select a.NumPed, a.NomProd , a.Variante, a.Cantidad from pedido_item a inner join pedido b on(a.NumPed = b.NumPed) where a.numped IN($listadoPedido)";
		$array = array ();
		
		//echo $sql;
		//print_r($array);
		$result = $db->ejecutarobtener2($sql,$array, $funciono);
		return $result;	
		//echo "Funciono ($funciono)";
    }


}
