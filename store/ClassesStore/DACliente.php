<?php

require_once("basedatos.php");
require_once("BECliente.php");

class DACliente{
    

	//login
	public function login($email, $password , &$funciono){
			
		$db = new BaseDatos();
		//$sql = "SELECT a.codcliente FROM cliente a inner join clientepassword b on (a.CodCliente =b.CodCliente) WHERE a.email =:email and b.Password =:password";
		//$sql = "SELECT a.codcliente FROM cliente a WHERE a.email =:email";		
		$sql ="SELECT a.codcliente FROM cliente a inner join pedido b on (a.CodCliente = b.CodCliente) WHERE a.Email =:email or b.Email = :email group by a.CodCliente";

		//echo $sql;

		//$array = array (":email" => $email,":password" => $password );
		$array = array (":email" => $email);
		$result = $db->consulta2($sql,$array, $funciono);
		return $result;	      
	}

	//20230928 Buscar informacion de un cliente sin password
	public function buscarcliente_nombre_sinpassword($codcliente, &$funciono ){
        
		$db = new BaseDatos();
		$sql ="SELECT ApeNom , NumDoc,  CASE WHEN a.TipoDoc = 0 THEN 'SIN DOCUMENTO' WHEN a.TipoDoc = 1 THEN 'DNI' WHEN a.TipoDoc = 2 THEN 'OTRO' END as NomTipoDoc , PuntosActuales, RosasActuales, FecActPuntos ,
		ObtenerValorParamLista (1 , a.TipoCondPiel) as 'NomCondPiel' , a.ComentCondPiel , a. CuentaFB, a.CuentaIG, a.CuentaTiktok , a.FecNacimiento
		FROM cliente a where a.CodCliente=:codcliente";

		$array = array (":codcliente" => $codcliente);
		$result = $db->consulta2($sql,$array, $funciono);
		return $result;	
			
    }

	//BuscarCliente usado tanto para subir la trama de Wix en proceso_buscarcliente
	public function buscarClientePorFiltros($BECliente, &$funciono){
		
		$db = new BaseDatos();
		$whereDocCelEmail ="";
		$whereNom ="";
		$whereRedSocial ="";
		$where ="";

		$buscarNumDoc="X9999988";
		$buscarCelular="999999X99";
		$buscarEmail="NOEXISTO@NOP.COM";

		//20230928 Datos del Cliente
		$buscarCtaRedSocial="NoCuentaRedSocialNoNoNoNo";
		$buscarPedido=99999;

		$buscarNom0="NoExistoSinDNI";
		$buscarNom1="NoExistoSinDNI";
		$buscarNom2="NoExistoSinDNI";
		$buscarNom3="NoExistoSinDNI";
		$buscarNom4="NoExistoSinDNI";

		

		$indicadorBuscarNom0 =0;
		$indicadorBuscarNom1 =0;
		$indicadorBuscarNom2 =0;
		$indicadorBuscarNom3 =0;
		$indicadorBuscarNom4 =0;

		if($BECliente->getNumDoc() !="")
		{
			$buscarNumDoc=$BECliente->getNumDoc();
			$whereDocCelEmail = $whereDocCelEmail."OR b.NumDoc LIKE  '%$buscarNumDoc%' ";
		}

		if($BECliente->getCelular() !="")
		{
			$buscarCelular=$BECliente->getCelular();
			$whereDocCelEmail = $whereDocCelEmail."OR a.Celular LIKE '%$buscarCelular%' OR b.Celular LIKE '%$buscarCelular%' ";
		}

		if($BECliente->getEmail() !="")
		{
			$buscarEmail=$BECliente->getEmail();
			$whereDocCelEmail = $whereDocCelEmail."OR a.Email LIKE '%$buscarEmail%' OR b.Email LIKE '%$buscarEmail%' ";
		}

		//20230928 Datos del Cliente
		if($BECliente->getCtaIG() !="")
		{
			$buscarCtaRedSocial=$BECliente->getCtaIG();
			$whereDocCelEmail = $whereRedSocial."OR b.CuentaFB LIKE '%$buscarCtaRedSocial%' OR b.CuentaIG LIKE '%$buscarCtaRedSocial%' OR b.CuentaTiktok LIKE '%$buscarCtaRedSocial%'  ";
		}
		if($BECliente->getNumPedBuscar() !="")
		{
			$buscarPedido=$BECliente->getNumPedBuscar();
			$whereDocCelEmail = $whereRedSocial."OR a.NumPed = $buscarPedido ";
		}


		if($BECliente->getBuscarNom0() !="")
		{
			$buscarNom0 = $BECliente->getBuscarNom0();
			$whereNom = $whereNom."AND b.ApeNom LIKE '%$buscarNom0%' ";
		}

		if($BECliente->getBuscarNom1() !="")
		{
			$buscarNom1 = $BECliente->getBuscarNom1();
			$whereNom = $whereNom."AND b.ApeNom LIKE '%$buscarNom1%' ";
		}

		if($BECliente->getBuscarNom2() !="")
		{
			$buscarNom2 = $BECliente->getBuscarNom2();
			$whereNom = $whereNom."AND b.ApeNom LIKE '%$buscarNom2%' ";
		}

		if($BECliente->getBuscarNom3() !="")
		{
			$buscarNom3 = $BECliente->getBuscarNom3();
			$whereNom = $whereNom."AND b.ApeNom LIKE '%$buscarNom3%' ";
		}

		if($BECliente->getBuscarNom4() !="")
		{
			$buscarNom4 = $BECliente->getBuscarNom4();
			$whereNom = $whereNom."AND b.ApeNom LIKE '%$buscarNom4%' ";
		}

		//Configurar el bloque Where
		$temp1 ="";
		$temp2 ="";
		
		if($whereDocCelEmail !="" )
		{
			//echo "Acanga ";
			$temp1 = substr($whereDocCelEmail,2,strlen($whereDocCelEmail));
			//echo "</br> temp1  $temp1 ";

		}
		if($whereNom !="" )
		{
			$temp2 = substr($whereNom,3,strlen($whereNom));
		}

		if ($temp1 =="" and $temp2 =="")
		{
			$where = "";
		}

		if ($temp1 =="" and $temp2 !="")
		{
			$where = " and ( $temp2 ) ";
		}

		if ($temp1 !="" and $temp2 =="")
		{
			$where = " and ( $temp1 ) ";
		}
		if ($temp1 !="" and $temp2 !="")
		{
			$where = " and ( $temp1 ) and ( $temp2 ) ";
		}

		/*

		if($whereDocCelEmail !="" )
		{
			$temp1 = substr($whereDocCelEmail,2,strlen($whereDocCelEmail));
		}
		if($whereNom !="" )
		{
			$temp2 = substr($whereNom,3,strlen($whereNom));
		}
		if($whereRedSocial !="" )
		{
			$temp3 = substr($whereRedSocial,3,strlen($whereRedSocial));
		}

		if($whereDocCelEmail !="" and $whereNom !="" )
		{
			$where =$temp1." AND ($temp2)";
		}

		if($whereDocCelEmail !="" and $whereNom =="" )
		{
			$where =$temp1;
		}

		if($whereDocCelEmail =="" and $whereNom !="" )
		{
			$where = " ($temp2)";
		}

		if($whereDocCelEmail =="" and $whereNom =="" )
		{
			$where = "";
			$funciono =-1;
			return -1;
		}

		*/

		//echo "Llego a sql";

		//OR b.Apenom LIKE concat('%', vApenom, '%')

		$sql ="";

		
		//echo "</br> <b>whereDocCelEmail</b> ($whereDocCelEmail) ";

		//echo "</br> <b>temp1</b>  $temp1 ";

		//echo "</br> <b>WHERE</b> ($where) ";

		if($where != "")
		{
			$sql ="select b.CodCliente , b.ApeNom, b.NumDoc, b.Celular, b.Email , b.TipoDoc , b.PuntosActuales, b.RosasActuales, b.FecActPuntos
			from pedido a
			inner join cliente b on(a.CodCliente = b.Codcliente)
			where b.CodCliente >= 1000000 $where
			group by b.CodCliente;";

			//echo "</br> <b>SQL</b>  $sql </br>";
			$array = array ();
			$result = $db->ejecutarobtener2($sql,$array, $funciono);

		}
		
		//$sql = "CALL uspBuscarClienteFiltros(:NumeroDoc, :ApeNom, :Celular, :Email)";
		//$array = array (":NumeroDoc" => $BECliente->getNumDoc()  ,":ApeNom" => $BECliente->getApeNom() ,":Celular" => $BECliente->getCelular() ,":Email" => $BECliente->getEmail());
		
		//echo $sql;
		//print_r($array);
		
		return $result;	
		//echo "Funciono ($funciono)";
	}



	//BuscarCliente usado tanto para subir la trama de Wix en proc_cargarinfo
    public function buscarCliente($BECliente , &$funciono){
        
		$db = new BaseDatos();
		//$sql = "SELECT a.codcliente FROM cliente a WHERE a.email =:email";
		//$array = array (":email" => $email);

		$sql = "CALL uspBuscarCliente(:NumeroDoc, :ApeNom, :Celular, :Email)";
		$array = array (":NumeroDoc" => $BECliente->getNumDoc()  ,":ApeNom" => $BECliente->getApeNom() ,":Celular" => $BECliente->getCelular() ,":Email" => $BECliente->getEmail());
		
		//echo "</br>SQL: ".$sql;
		//echo "</br>NumeroDoc: ".$BECliente->getNumDoc();
		//echo "</br>ApeNom: ".$BECliente->getApeNom();
		//echo "</br>Celular:".$BECliente->getCelular();
		//echo "</br>Email:".$BECliente->getEmail();

		//print_r($array);

		$result = $db->consulta2($sql,$array, $funciono);
		return $result;	      
    }

    
	//Registrar un pedido 
	public function registrarCliente($BECliente,  &$funciono){
        $db = new BaseDatos();

		$entidad = new BECliente();
		$entidad = $BECliente;

        $sql ="CALL uspRegistrarCliente (:tipodoc,:numdoc,:apellidonom,:celular,:email);";
		$array = array (":tipodoc" => $entidad->getTipoDoc() ,":numdoc" => $entidad->getNumDoc()  ,":apellidonom" => $entidad->getApeNom()  ,":celular" => $entidad->getCelular()  ,":email" => $entidad->getEmail() );
		
		//echo $sql;
		//print_r($array);
		$result = $db->ejecutarobtener2($sql,$array, $funciono);
		return $result;	
		//echo "Funciono ($funciono)";
    }

	//Actualizar Nombre usado desde procesar_pedidoenvio
	public function ActualizarNombre($CodCliente, $nombre, &$funciono){
        $db = new BaseDatos();

        $sql ="UPDATE cliente SET ApeNom = :nombre WHERE CodCliente =:codigo;";

		$array = array (":codigo" => $CodCliente , ":nombre" => $nombre  );
        $db->ejecutar2($sql,$array, $funciono);
    }

	//Actualizar Nombre usado desde procesar_pedidoenvio
	public function ActualizarCelularEmail($CodCliente, $celular, $email, &$funciono){
        $db = new BaseDatos();

        $sql ="UPDATE cliente SET Celular = :celu , Email = :mail WHERE CodCliente =:codigo;";

		$array = array (":codigo" => $CodCliente , ":celu" => $celular , ":mail" => $email  );
        $db->ejecutar2($sql,$array, $funciono);
    }

	//20230712 Registrar eventos de puntos del cliente
	public function registrarEventoPuntoCliente($idCod, $fecha, &$funciono){
        $db = new BaseDatos();

        $sql ="CALL uspRegistrarEventoPuntosRosas (:code,:fec);";

		$array = array (":code" => $idCod , ":fec" => $fecha  );
        $db->ejecutar2($sql,$array, $funciono);
    }

	//20230926 listado de condiciones en la piel
	public function listadoCondicionesPiel(&$funciono ){
        
		$db = new BaseDatos();
		//$sql ="SELECT ApeNom , NumDoc,  CASE WHEN a.TipoDoc = 0 THEN 'SIN DOCUMENTO' WHEN a.TipoDoc = 1 THEN 'DNI' WHEN a.TipoDoc = 2 THEN 'OTRO' END as NomTipoDoc , PuntosActuales, RosasActuales, FecActPuntos FROM cliente a where a.CodCliente=:codcliente";

		$sql ="SELECT CodValor , Valor FROM `parametro_lista` WHERE CodLista = 1 and Estatus ='V' Order By Orden ASC";
		$array = array ();
		$result = $db->consulta2($sql,$array, $funciono);
		return $result;	
			
    }

	//20230928 Actualizar DatosCondcionPiel y Cuenta Cliente
	public function ActualizarCondClienteCtaRedes($CodCliente, $codCond , $comCond, $ctaIG , $ctaTk , $ctaFB  , &$funciono){
        $db = new BaseDatos();

        $sql ="UPDATE cliente SET 
		TipoCondPiel = :tipo ,
		ComentCondPiel = :comen ,
		CuentaIG = :ig ,
		CuentaTiktok = :tk ,
		CuentaFB = :fb 
		WHERE CodCliente =:codigo;";

		$array = array (":codigo" => $CodCliente , ":tipo" => $codCond , ":comen" => $comCond , ":ig" => $ctaIG , ":tk" => $ctaTk , ":fb" => $ctaFB  );
        $db->ejecutar2($sql,$array, $funciono);
    }

	//20230928 Actualizar DatosCondcionPiel y Cuenta Cliente
	public function ObtenerUltimasDireccionsEntrega($CodCliente, &$funciono){
        $db = new BaseDatos();

        $sql ="select DireccionDelivery, TipoEntrega,  Id_TipoEntrega, NomTipoEntrega, EstatusTipoEntrega,
		Id_Dpto, obtenerDepartamento(Id_Dpto,0) as 'NomDpto' , 
        Id_Provincia, obtenerProvincia(Id_Provincia,0) as 'NomProv',
        Id_Distrito , obtenerDistrito(Id_Distrito,0) as 'NomDistrito'
		from (
			SELECT Distinct a.DireccionDelivery , 
			b.TipoEntrega , b.Id_TipoEntrega , c.NomTipoEntrega , c.EstatusTipoEntrega , b.Id_Dpto, b.Id_Provincia, b.Id_Distrito
			FROM pedido a 
			INNER JOIN pedido_envio b on(a.NumPed = b.NumPed)
			INNER JOIN tipoentrega c on(b.Id_TipoEntrega = c.Id_TipoEntrega)
			WHERE a.CodCliente =:codigo and b.TipoEntrega != 14 and c.EstatusTipoEntrega ='V'
			ORDER BY a.NumPed Desc
		) X Limit 3;";

		$array = array (":codigo" => $CodCliente );
        $result = $db->consulta2($sql,$array, $funciono);
		return $result;	
    }

}
