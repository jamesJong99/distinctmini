<?php

require_once("basedatos.php");
require_once("BEPedidoItem.php");
require_once("BEPedidoItemDet.php");

class DAPedidoItemDet{


	//Obtener pedidos item de para gestionar pedidos en base a fecha
    public function obtenerItemDet($nomProdWix , $varianteWix , $fecPedido, &$funciono){
        
		$db = new BaseDatos();
		
		//echo "BD".DBNAME." ";
		$sql = "CALL uspBuscarCodProdItem(:nom , :variante , :fec)";
		
		//$fecPedido="Aug 5, 2023";

		//echo $sql;
		//echo " '$nomProdWix', ";
		//echo " '$varianteWix', ";
		//echo " '$fecPedido' ";
		
		//$fecPedido="Jun 6, 2023";
		$array = array (":nom" => $nomProdWix, ":variante" => $varianteWix, ":fec" => $fecPedido);

		//echo "<br> ($nomProdWix) ($varianteWix) ($fecPedido)";

		//print_r($array);
		$result = $db->consulta2($sql,$array, $funciono);
		//print_r($result);
		return $result;	      
    }

	//Registrar un pedido item
	public function registrarPedidoItemDetalle($BEPedidoItemDet,  &$funciono){
        $db = new BaseDatos();

		$entidad = new BEPedidoItemDet();
		$entidad = $BEPedidoItemDet;

        $sql ="INSERT INTO `pedido_item_det`(`NumPed`, `CodItem`, `Cantidad`) VALUES (:numped, :coditem , :cantidad)";
		$array = array (":numped" => $entidad->getNumPed() ,":coditem" => $entidad->getCodItem() ,":cantidad" => $entidad->getCantidad() );
		
		//echo $sql;
		//print_r($array);
		$db->ejecutar2($sql,$array, $funciono);
		//echo "Funciono ($funciono)";
    }
    


}
