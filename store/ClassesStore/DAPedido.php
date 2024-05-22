<?php

require_once("basedatos.php");
require_once("BEPedido.php");

class DAPedido{
    

	//Obtener pedidos de cliente
    public function obtenerPedidosCliente($codcliente , &$funciono){
        
		$db = new BaseDatos();
		//$sql = "SELECT a.codcliente FROM cliente a WHERE a.email =:email";
		//$array = array (":email" => $email);

		$sql = "CALL UspObtPedidosDeCliente (:codcli)";
		$array = array (":codcli" => $codcliente);

		//print_r($array);

		$result = $db->consulta2($sql,$array, $funciono);
		//print_r($result);
		return $result;	      
    }

	//BuscarCliente
    public function validarPedidoRegistrado($numeroPedido , &$funciono){
        
		$db = new BaseDatos();
		//$sql = "SELECT a.codcliente FROM cliente a WHERE a.email =:email";
		//$array = array (":email" => $email);

		$sql = "CALL uspExistepedido (:numPedid)";
		$array = array (":numPedid" => $numeroPedido);

		//print_r($array);

		$result = $db->consulta2($sql,$array, $funciono);
		//print_r($result);
		return $result;	      
    }
    
	
    //Registrar un pedido. Es para WIX
	public function registrarPedido($BEPedido,  &$funciono){
        $db = new BaseDatos();

		$entidad = new BEPedido();
		$entidad = $BEPedido;

        $sql ="INSERT INTO `pedido`
		(`NumPed`, `CodCliente`, `PaisDelivery`, `DptoDelivery`, `CiudadDelivery`, 
		 `DIreccionDelivery`, `Email`, `Celular`, `MontoPedido`, `MontoPedidoProductos`, 
		 `MontoEnvioPedido`, `MetodoEnvio`, `Cupon`, `DctoCupon`, `MontoDctoCupon`, 
		 `MetodoPago`, `CostoOpeOnlinePedido`, `CostoVentaPedido`, `UtilidadPedido`, `Tipo`,
		 `FechaPedidoWix`, `TimePedidoWix`, `FechaPedido`, `PeriodoPedido`, `NotaPedido`, 
		 `EstadoPago`, `EstadoEntrega`,`CodCanalVenta`,`CodUsuarioRegistro` ) 
		 VALUES 
		 (:numped,:codcliente,:paisdeliv,:dptodeliv,:ciudaddeliv,
		  :direcion,:email,:celular,:montopedido,0,
		  :montoenvio,:metodoenvio,:cupon,0,0,
		  :metodopago,0,0,0,NULL,
		  :fechawix,:horawix,NULL,'',:notapedido,
		  :estadopago,:estadoentrega, :codcanalventa, :codusuarioregistro)";
		$array = array (":numped" => $entidad->getNumPed() ,":codcliente" => $entidad->getCodCliente()  ,":paisdeliv" => $entidad->getPaisDelivery() ,":dptodeliv" => $entidad->getDptoDelivery()  ,":ciudaddeliv" => $entidad->getCiudadDelivery()  
		,":direcion" => $entidad->getDireccionDelivery() ,":email" => $entidad->getEmail() ,":celular" => $entidad->getCelular() ,":montopedido" => $entidad->getMontoPedido()
		,":montoenvio" => $entidad->getMontoEnvioPed() ,":metodoenvio" => $entidad->getMetodoEnvio() ,":cupon" => $entidad->getCupon() 
		,":metodopago" => $entidad->getMetodoPago() 
		,":fechawix" => $entidad->getFechaPedido() ,":horawix" => $entidad->getHoraPedido() ,":notapedido" => $entidad->getNota() 
		,":estadopago" => $entidad->getEstadoPago() ,":estadoentrega" => $entidad->getEstadoEntrega() ,":codcanalventa" => $entidad->getCodCanalVenta() ,":codusuarioregistro" => $entidad->getCodUsuarioRegistro()
		);
		
		//echo $sql;
		//print_r($array);
		$db->ejecutar2($sql,$array, $funciono);
		//echo "Funciono ($funciono)";
    }

	//Registrar un pedido desde SIA
	public function registrarPedidoSIA($BEPedido,  &$funciono){
        $db = new BaseDatos();

		$entidad = new BEPedido();
		$entidad = $BEPedido;

		//20230626 Se incluyo datos de cupón

        $sql ="INSERT INTO `pedido`
		(`NumPed`, `CodCliente`, `PaisDelivery`, `DptoDelivery`, `CiudadDelivery`, 
		 `DIreccionDelivery`, `Email`, `Celular`, `MontoPedido`, `MontoPedidoProductos`, 
		 `MontoEnvioPedido`, `MetodoEnvio`, `Cupon`, `DctoCupon`, `MontoDctoCupon`, 
		 `MetodoPago`, `CostoOpeOnlinePedido`, `CostoVentaPedido`, `UtilidadPedido`, `Tipo`,
		 `FechaPedido`, `PeriodoPedido`, `NotaPedido`, 
		 `EstadoPago`, `EstadoEntrega`,`CodCanalVenta`,`CodUsuarioRegistro` ,`SistemaRegPedido` ,
		 `MontoAhorroPedido` ) 
		 VALUES 
		 (:numped,:codcliente,:paisdeliv,:dptodeliv,:ciudaddeliv,
		  :direcion,:email,:celular,:montopedido,:montoprod,
		  :montoenvio,:metodoenvio,:cupon,:valorcupon,:dctocupon,
		  :metodopago,0,0,0,NULL,
		  :fechapedido, NULL , :notapedido,
		  :estadopago,:estadoentrega, :codcanalventa, :codusuarioregistro , :sistemreg,
		  :mntAhorro)";
		$array = array (":numped" => $entidad->getNumPed() ,":codcliente" => $entidad->getCodCliente()  ,":paisdeliv" => $entidad->getPaisDelivery() ,":dptodeliv" => $entidad->getDptoDelivery()  ,":ciudaddeliv" => $entidad->getCiudadDelivery()  
		,":direcion" => $entidad->getDireccionDelivery() ,":email" => $entidad->getEmail() ,":celular" => $entidad->getCelular() ,":montopedido" => $entidad->getMontoPedido(),":montoprod" => $entidad->getMontoProducto()
		,":montoenvio" => $entidad->getMontoEnvioPed() ,":metodoenvio" => $entidad->getMetodoEnvio() ,":cupon" => $entidad->getCupon() ,":valorcupon" => $entidad->getValorDctoCupon() ,":dctocupon" => $entidad->getMontoDctoCupon() 
		,":metodopago" => $entidad->getMetodoPago() 
		,":fechapedido" => $entidad->getFechaPedido()  ,":notapedido" => $entidad->getNota() 
		,":estadopago" => $entidad->getEstadoPago() ,":estadoentrega" => $entidad->getEstadoEntrega() ,":codcanalventa" => $entidad->getCodCanalVenta() ,":codusuarioregistro" => $entidad->getCodUsuarioRegistro() ,":sistemreg" => $entidad->getSistemaRegPedido()
		,":mntAhorro" => $entidad->getAhorroPedido()
		);
		
		//echo $sql;
		//print_r($array);
		$db->ejecutar2($sql,$array, $funciono);
		//echo "Funciono ($funciono)";
    }


	//Registrar un pedido desde Distinct.PE
	public function registrarPedidoDistinctPE($BEPedido,  &$funciono){
        $db = new BaseDatos();

		$entidad = new BEPedido();
		$entidad = $BEPedido;

		//20230626 Se incluyo datos de cupón

        $sql ="INSERT INTO `pedido`
		(`NumPed`, `CodCliente`, `PaisDelivery`, `DptoDelivery`, `CiudadDelivery`, 
		 `DIreccionDelivery`, `Email`, `Celular`, `MontoPedido`, `MontoPedidoProductos`, 
		 `MontoEnvioPedido`, `MetodoEnvio`, `Cupon`, `DctoCupon`, `MontoDctoCupon`, 
		 `MetodoPago`, `CostoOpeOnlinePedido`, `CostoVentaPedido`, `UtilidadPedido`, `Tipo`,
		 `FechaPedido`, `PeriodoPedido`, `NotaPedido`, 
		 `EstadoPago`, `EstadoEntrega`,`CodCanalVenta`,`CodUsuarioRegistro` ,`SistemaRegPedido` ) 
		 VALUES 
		 (:numped,:codcliente,:paisdeliv,:dptodeliv,:ciudaddeliv,
		  :direcion,:email,:celular,:montopedido,:montoprod,
		  :montoenvio,:metodoenvio,:cupon,:valorcupon,:dctocupon,
		  :metodopago,0,0,0,NULL,
		  :fechapedido, NULL , :notapedido,
		  :estadopago,:estadoentrega, :codcanalventa, :codusuarioregistro , :sistemreg)";
		$array = array (":numped" => $entidad->getNumPed() ,":codcliente" => $entidad->getCodCliente()  ,":paisdeliv" => $entidad->getPaisDelivery() ,":dptodeliv" => $entidad->getDptoDelivery()  ,":ciudaddeliv" => $entidad->getCiudadDelivery()  
		,":direcion" => $entidad->getDireccionDelivery() ,":email" => $entidad->getEmail() ,":celular" => $entidad->getCelular() ,":montopedido" => $entidad->getMontoPedido(),":montoprod" => $entidad->getMontoProducto()
		,":montoenvio" => $entidad->getMontoEnvioPed() ,":metodoenvio" => $entidad->getMetodoEnvio() ,":cupon" => $entidad->getCupon() ,":valorcupon" => $entidad->getValorDctoCupon() ,":dctocupon" => $entidad->getMontoDctoCupon() 
		,":metodopago" => $entidad->getMetodoPago() 
		,":fechapedido" => $entidad->getFechaPedido()  ,":notapedido" => $entidad->getNota() 
		,":estadopago" => $entidad->getEstadoPago() ,":estadoentrega" => $entidad->getEstadoEntrega() ,":codcanalventa" => $entidad->getCodCanalVenta() ,":codusuarioregistro" => $entidad->getCodUsuarioRegistro() ,":sistemreg" => $entidad->getSistemaRegPedido()
		);
		
		//echo $sql;
		//print_r($array);
		$db->ejecutar2($sql,$array, $funciono);
		//echo "Funciono ($funciono)";
    }
	

	//Validar Pedido generado de trama de Wix. Opción Subir Pedido
	public function validarPedido($numeroped, $numitem, $numitemdet, &$funciono){
        $db = new BaseDatos();

        $sql ="CALL uspValidarPedido (:numped, :numitem, :numitemdet);";
		$array = array (":numped" => $numeroped ,":numitem" => $numitem ,":numitemdet" => $numitemdet  );
		
		//echo $sql;
		//print_r($array);
		$result = $db->ejecutarobtener2($sql,$array, $funciono);
		return $result;	
		//echo "Funciono ($funciono)";
    }

	//Validar Pedido generado de trama de Wix. Opción Subir Pedido
	public function uspValidarPedidoGenSia($numeroped, $numitem, $numitemdet, &$funciono){
        $db = new BaseDatos();

        $sql ="CALL uspValidarPedidoGenSia (:numped, :numitem, :numitemdet);";
		$array = array (":numped" => $numeroped ,":numitem" => $numitem ,":numitemdet" => $numitemdet  );
		
		//echo $sql;
		//print_r($array);
		$result = $db->ejecutarobtener2($sql,$array, $funciono);
		return $result;	
		//echo "Funciono ($funciono)";
    }

	//Obtener Listado Pedidos según Listado de Pedidos. Usado para generar etiqueta opción Administrador
	public function obtenerPedidosPorLista($listadoPedido, $diasMax, &$funciono){
        $db = new BaseDatos();

        $sql ="select a.NumPed, a.CodCliente, b.Apenom, a.CiudadDelivery , a.DireccionDelivery, a.Celular, b.TipoDoc, b.NumDoc, a.MetodoEnvio , a.MontoEnvioPedido , a.NotaPedido
		from pedido a inner join cliente b on (a.codcliente = b.codcliente)
		where a.numped IN($listadoPedido) order by a.numped asc";
		$array = array ();
		
		//echo $sql;
		//print_r($array);
		$result = $db->ejecutarobtener2($sql,$array, $funciono);
		return $result;	
		//echo "Funciono ($funciono)";
    }

	//Obtener Numero de Pedido. No generados por Wix
	public function obtenerCorrelativoPedido(&$funciono){
        $db = new BaseDatos();

        $sql ="SELECT IFNULL(Max(NumPed), 39999) +1 as 'NuevoCodPed' FROM pedido WHERE CodCanalVenta = 'C001' and NumPed >= 40000;";
		$array = array ();
		
		//echo $sql;
		//print_r($array);
		$result = $db->ejecutarobtener2($sql,$array, $funciono);
		return $result;	
		//echo "Funciono ($funciono)";
    }



	//Obtener Listado Pedidos según Listado de Pedidos. Usado para generar etiqueta opción Administrador
	public function obtenerCanalUsuarioxClaveRegistro($clave, &$funciono){
        $db = new BaseDatos();

        $sql ="SELECT CodCanalVenta, CodUsuario FROM canalventausuario WHERE CodClaveWix=:codigoclave";
		
		$array = array (":codigoclave" => $clave );
		
		//echo $sql;
		//print_r($array);
		$result = $db->ejecutarobtener2($sql,$array, $funciono);
		return $result;	
		//echo "Funciono ($funciono)";
    }


	//Obtener Listado Pedidos según Listado de Pedidos. Usado para generar etiqueta opción Vendedor
	//Necesita información en tabla pedido_envio
	public function obtenerPedidosPorLista2($listadoPedido, $diasMax, &$funciono){
        $db = new BaseDatos();

        $sql ="select a.NumPed, a.CodCliente, b.Apenom, a.CiudadDelivery , a.DireccionDelivery, a.Celular, b.TipoDoc, b.NumDoc, a.MetodoEnvio , a.MontoEnvioPedido , a.NotaPedido
		, c.TipoEntrega, c.CodCourier , obtenerNombreCourier(c.CodCourier) as 'NomCourier' , 
		obtenerDepartamento(c.Id_Dpto, c.IdEnvio) as 'Departamento', obtenerProvincia(c.Id_Provincia, c.IdEnvio) as 'Provincia', obtenerDistrito(c.Id_Distrito, c.IdEnvio) as 'Distrito', 
		c.Comentario , a.StatusEntrega , c.Muestra
		from pedido a 
		inner join cliente b on (a.codcliente = b.codcliente) 
		inner join pedido_envio c on (a.NumPed = c.NumPed) 
		where a.numped IN($listadoPedido) and c.Estatus IN(1,2,3) order by a.numped asc";
		$array = array ();
		
		//echo $sql;
		//print_r($array);
		$result = $db->ejecutarobtener2($sql,$array, $funciono);
		return $result;	
		//echo "Funciono ($funciono)";
    }

	//Obtener Listado Pedidos para GestionPedidos
	public function obtenerPedidosGestionPedido($vista, $filtroStatusPed , $filtroCourier , $CodCanalIN, $filtroCanal, &$funciono){
        $db = new BaseDatos();

		//$vista = 1 ordenado por pedido
		$order ="order by a.numped DESC";
		if ($vista ==2)
		{
			$order ="order by a.StatusEntrega ASC, a.numped DESC";
		}

		//$filtroStatusPed = 0
		$whereStatus =" a.StatusEntrega IN(1,2,3,4,5,6)";
		if ($filtroStatusPed ==1 or $filtroStatusPed ==2 or $filtroStatusPed ==3 or $filtroStatusPed ==4 or $filtroStatusPed ==5 or $filtroStatusPed ==6)
		{
			$whereStatus =" a.StatusEntrega IN($filtroStatusPed)";
		}

		$innerPedidoEnvio ="";
		$whereCourier ="";

		//echo "filtroCourier: ($filtroCourier)";
		//echo "filtroStatusPed: ($filtroStatusPed)";
		//echo "vista: ($vista)";

		if ($filtroCourier !=0 and ( $filtroStatusPed ==0 or $filtroStatusPed ==3 or $filtroStatusPed ==4 or $filtroStatusPed ==5 or $filtroStatusPed ==6 or $filtroStatusPed ==7) )
		{
			$innerPedidoEnvio ="INNER JOIN pedido_envio c on(a.NumPed = c.NumPed)";

			//$filtroCourier = 0
			$whereCourier ="AND c.TipoEntrega IN('R','C')";
			if ($filtroCourier == -1)
			{
				$whereCourier = "AND c.TipoEntrega IN('R') and c.Estatus IN(1,2,3)";
			}
			if ($filtroCourier >=1 )
			{
				$whereCourier ="AND c.TipoEntrega IN('C') AND c.CodCourier IN($filtroCourier) and c.Estatus IN(1,2,3)";
			}

		}


		$whereCanal = " and a.CodCanalVenta IN($CodCanalIN) ";
		if($filtroCanal !="")
		{
			$whereCanal = " and a.CodCanalVenta IN('$filtroCanal') ";
		}
		
		//20220705 se elimina restricción de fechas para todos los roles
		//20230707 se incluye el monto de pedido. 
		$sql ="select a.NumPed, a.CodCliente, b.Apenom , a.StatusEntrega , a.CiudadDelivery, 
		a.MetodoEnvio , a.Celular , a.Email, a.FechaPedido , a.MetodoPago, a.Cupon , 
		obtenerStatusEntrega(StatusEntrega) as 'DescStatusEntrega', obtenerStatusEntregaProxPaso(StatusEntrega) as 'DescStatusEntregaProxPaso' ,
		a.CodCanalVenta, d.NomCanalVenta , d.EsDigital , a.MontoPedido
		from pedido a inner join cliente b on (a.codcliente = b.codcliente) 
		inner join canalventa d on (a.CodCanalVenta = d.CodCanalVenta) 
		$innerPedidoEnvio
		where $whereStatus $whereCourier $whereCanal $order;";

		//echo $sql;

		

		$array = array (":DiasConsulta" => "");
		
		//echo $sql;
		//print_r($array);
		$result = $db->ejecutarobtener2($sql,$array, $funciono);
		return $result;	
		//echo "Funciono ($funciono)";
    }


//Obtener Listado Pedidos para GestionPedidos
//Utilizado desde adm_gestionar_pedidos_postventa.php
public function obtenerPedidosPostVenta($vista, $filtroStatusPost, $filtroCanal, $CodCanalIN,  &$funciono){
	$db = new BaseDatos();

	//$vista = 1 ordenado por pedido
	$order ="order by a.numped DESC";
	if ($vista ==2)
	{
		$order ="order by a.StatusPostVenta ASC, a.numped DESC";
	}

	$whereStatus =" and (a.StatusPostVenta IN(1,2) OR  ( a.StatusPostVenta IN(3) and a.FechaPedido >= CURDATE() - INTERVAL 62 DAY  )  )";
	if ($filtroStatusPost ==1 or $filtroStatusPost ==2)
	{
		$whereStatus =" and a.StatusPostVenta IN($filtroStatusPost)";
	}
	if ($filtroStatusPost ==3)
	{
		$whereStatus =" and ( a.StatusPostVenta IN($filtroStatusPost) and a.FechaPedido >= CURDATE() - INTERVAL 62 DAY ) ";
	}

	$whereCanal = " and a.CodCanalVenta IN($CodCanalIN) ";
	if($filtroCanal !="")
	{
		$whereCanal = " and a.CodCanalVenta IN('$filtroCanal') ";
	}


	//20230707 se incluye monto pedido.
	$sql ="select a.NumPed, a.CodCliente, b.Apenom , a.StatusPostVenta , a.ComentPostVenta, a.CiudadDelivery, a.MetodoEnvio , a.Celular , a.Email, a.FechaPedido , a.MetodoPago, a.Cupon , obtenerStatusPostVenta(StatusPostVenta) as 'DescStatusPostVenta', FecPostVenta, ComentPostVenta , FecPostVenta2, ComentPostVenta2 
	, a.CodCanalVenta, d.NomCanalVenta , d.EsDigital , a.MontoPedido
	from pedido a inner join cliente b on (a.codcliente = b.codcliente)
	inner join canalventa d on (a.CodCanalVenta = d.CodCanalVenta) 
	where a.StatusEntrega IN(7) $whereStatus $whereCanal 
	and a.FechaPedido >='2022-07-01' $order ;";

	//2022-07-01
	//echo $sql;

	

	$array = array (":DiasConsulta" => "");
	
	//echo $sql;
	//print_r($array);
	$result = $db->ejecutarobtener2($sql,$array, $funciono);
	return $result;	
	//echo "Funciono ($funciono)";
}


	




	//Obtener Pedidos para Gestionar
	public function obtenerPedidoParaGestionPedido($numPed ,$diasMax, &$funciono){
        $db = new BaseDatos();

		//20230928 Incluir campos adicionales por condición en la piel y cuentas de redes sociales
		$sql ="select a.NumPed, a.CodCliente, b.TipoDoc, b.NumDoc, b.Apenom , a.StatusEntrega , a.StatusPostVenta , a.CiudadDelivery, a.MetodoEnvio , a.Celular , a.MontoPedido, a.NotaPedido ,
		a.Email, a.FechaPedido , a.MetodoPago, a.Cupon , obtenerStatusEntrega(StatusEntrega) as 'DescStatusEntrega', obtenerStatusEntregaProxPaso(StatusEntrega) as 'DescStatusEntregaProxPaso' ,
		a.CiudadDelivery , a.DIreccionDelivery, MetodoEnvio, MontoEnvioPedido , FecPostVenta , ComentPostVenta , CodCanalVenta , 
		b.TipoCondPiel , ObtenerValorParamLista(1, b.TipoCondPiel) as 'DescTipoCondPiel', b.ComentCondPiel , b.CuentaFB, b.CuentaIG, b.CuentaTiktok  
		from pedido a inner join cliente b on (a.codcliente = b.codcliente)
		where a.NumPed=:pedido  order by a.numped DESC";
		//echo $sql; 
		$array = array (":pedido" => $numPed );
		
		//echo $sql;
		//print_r($array);
		$result = $db->ejecutarobtener2($sql,$array, $funciono);
		return $result;	
		//echo "Funciono ($funciono)";
    }

	//Actualizar Status PostVenta
	public function ActualizarPostVenta($NumPed, $fechaPost, $Comentario, &$funciono){
        $db = new BaseDatos();

        $sql ="UPDATE pedido SET StatusPostVenta = 2, FecPostVenta =:FechaPost,  ComentPostVenta =:Comentario  WHERE NumPed=:Numero;";

		$array = array (":Numero" => $NumPed , ":FechaPost" => $fechaPost , ":Comentario" => $Comentario  );
        $result = $db->consulta2($sql,$array, $funciono);
		return $result;	 
    }


	//Actualizar Status PostVenta2
	public function ActualizarPostVenta2($NumPed, $fechaPost, $Comentario, &$funciono){
        $db = new BaseDatos();

        $sql ="UPDATE pedido SET StatusPostVenta = 3, FecPostVenta2 =:FechaPost,  ComentPostVenta2 =:Comentario  WHERE NumPed=:Numero;";

		$array = array (":Numero" => $NumPed , ":FechaPost" => $fechaPost , ":Comentario" => $Comentario  );
        $result = $db->consulta2($sql,$array, $funciono);
		return $result;	 
    }


	//Actualizar Status
	public function ActualizarStatusEntrega($NumPed, $status, &$funciono){
        $db = new BaseDatos();

        $sql ="UPDATE pedido SET StatusEntrega = :estatus  WHERE NumPed=:Numero;";

		$array = array (":Numero" => $NumPed , ":estatus" => $status  );
        $result = $db->consulta2($sql,$array, $funciono);
		return $result;	 
    }


	//Actualizar Status a Entregado a CLiente. Obtener de procesar_pedido
	//Este procedimiento necesita al menos registro de pedido_pago con status 2
	//Este procedimiento necesita un pedido_envio con status 3
	public function ActualizarStatusEntregaCliente($NumPed, &$funciono){
        $db = new BaseDatos();

        $sql ="CALL UspActPedidoFechaEntrega (:numped);";

		$array = array (":numped" => $NumPed  );
        $result = $db->consulta2($sql,$array, $funciono);
		return $result;	 
    }

	//Actualizar Direccion
	public function ActualizarDireccion($NumPed, $direccion, &$funciono){
        $db = new BaseDatos();

        $sql ="UPDATE pedido SET DIreccionDelivery = :direc  WHERE NumPed=:Numero;";

		$array = array (":Numero" => $NumPed , ":direc" => $direccion  );
        $result = $db->consulta2($sql,$array, $funciono);
		return $result;	 
    }

	




}
