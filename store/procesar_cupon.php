<?php
session_start();

require_once("ClassesStore/BEProducto.php");
require_once("ClassesStore/BECliente.php");
require_once("ClassesStore/DACliente.php");
require_once("ClassesStore/BECuponImagen.php");
require_once("ClassesStore/DACuponImagen.php");

//print_r($_POST);

$operacion = "";


if (isset($_POST['operacion'])) {
	$operacion = $_POST['operacion'];
}



//Mostrar html para solicitar ingresar el cupón o si ya eligio un cupón mostrarlo
//Invocado por ajax desde carritocompra.php
if ($operacion == 1) {

	//Ver si tiene productos en el carrito
	$cantProductosCarrito = 0;
	if (isset($_SESSION['listadoProdCarrito'])) {
		//echo " </br> SESSION RECUPERADA ";
		$listadoProdCarrito = unserialize((base64_decode($_SESSION['listadoProdCarrito'])));
		$cantProductosCarrito = count($listadoProdCarrito);
	}

	if ($cantProductosCarrito > 0) {
		//Recuperar Cupon Si Existe
		//cuponBuscadoAplica
		$BECuponEncontrado = new BECuponImagen();
		$cuponCarritoEncontrado = "";

		$cuponBuscadoAplica = 0;

		if (isset($_SESSION['cuponCarrito'])) {
			$cuponCarritoEncontrado = $_SESSION['cuponCarrito'];
			$cuponBuscadoAplica = 1;

			//echo "</br> Session Recuperada de Cupón";

			$DACuponImagen = new DACuponImagen();
			$bolBuscarCuponExiste = 0;
			$rptaBuscarCupon = $DACuponImagen->buscarCuponPorCodigo($cuponCarritoEncontrado, $bolBuscarCuponExiste);

			if ($bolBuscarCuponExiste == 1) {
				$cuponBuscadoNoEncontro = 1;

				while ($fila = $rptaBuscarCupon->fetch()) {

					$BECuponEncontrado->setIdCupon($fila["IdCupon"]);
					$BECuponEncontrado->setCodCupon($fila["CodigoCupon"]);
					$BECuponEncontrado->setTipoDcto($fila["TipoDcto"]);
					$BECuponEncontrado->setCantDcto($fila["CantidadDcto"]);
					$BECuponEncontrado->setTipoAplicacion($fila["TipoAplicacion"]);
					$BECuponEncontrado->setValorAplicacion($fila["ValorAplicacion"]);
					$BECuponEncontrado->setMontoMinimo($fila["MontoMinimo"]);
					$BECuponEncontrado->setCuponPublico($fila["CuponPublico"]);
					$BECuponEncontrado->setCuponNecesitaCumplirCond($fila["CuponNecesitaCumplirCond"]);

					//print_r($BECuponBuscado);

					$cuponBuscadoExiste = 1;
					$cuponBuscadoNoEncontro = 0;
				}
			} else {
				echo "Error en procedimiento de buscar cupon. ";
			}
		}

?>
		<h3 class="text-left text-[#8F6B60] mb-4 font-bold text-lg">
			¿Quieres aplicar un cupón?
		</h3>
		<div class="w-full">
			<div class="MensajeError text-red-500 text-sm text-center">
				<div id="DivCuponMsjError"></div>
			</div>
		</div>
		<?php
		//En caso no aplica se permite incluir un cupón
		if ($cuponBuscadoAplica == 0) {
		?>

			<div class="w-full">
				<div class="flex items-center justify-between gap-4 mb-4 border-b-2 border-[#8F6B60] p-4 rounded-md">
					<label for="codcuponbuscar" class="text-[#8F6B60] text-left">
						Cupón a Aplicar
					</label>
					<input type="text" id="codcuponbuscar" name="codcuponbuscar" value="" placeholder="CUPON123" class="max-w-32 text-center border border-[#8F6B60] rounded-md p-2  focus:outline-none focus:ring-2 focus:ring-[#8F6B60] focus:border-transparent">
					<button class="bg-[#EDDFDB] text-[#97847d] border-[#EDDFDB] hover:bg-[#C4B8B5] hover:text-white  hover:border-[#c4b8b5] px-5 py-2 rounded-md ml-2 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-opacity-50" onclick="AplicaCupon();event.preventDefault();">Aplicar</button>
				</div>
			</div>
			
<?php
		}


		if ($cuponBuscadoAplica == 1) {
?>

			<div class="w-full">
				<div class="flex items-center justify-between gap-4 mb-4 border-b-2 border-[#8F6B60] p-4 rounded-md">
					<div class="text-[#8F6B60] text-left">
						Cupón aplicado
					</div>
					<div class="max-w-32 text-center border border-[#8F6B60] rounded-md p-2">
						<?php echo $cuponCarritoEncontrado; ?>
					</div>
					<button class="bg-[#EDDFDB] text-[#97847d] border-[#EDDFDB] hover:bg-[#C4B8B5] hover:text-white  hover:border-[#c4b8b5] px-5 py-2 rounded-md ml-2 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-opacity-50" onclick="EliminarCupon();event.preventDefault();">Eliminar Cupón</button>
				</div>
			</div>
			
<?php
		}
	}
	//fin if($cantProductosCarrito >0)




}
//fin operacion 1






//Buscar cupón si aplica
//Invocado por ajax desde carritocompra.php AplicaCupon
if ($operacion == 2) {

	//Obtener monto de Productos
	$montoProductos = 0;
	$cantProductosCarrito = 0;
	if (isset($_SESSION['listadoProdCarrito'])) {
		//echo " </br> SESSION RECUPERADA ";
		$listadoProdCarrito = unserialize((base64_decode($_SESSION['listadoProdCarrito'])));
		$cantProductosCarrito = count($listadoProdCarrito);
	}

	if ($cantProductosCarrito > 0) {
		foreach ($listadoProdCarrito as $correlativo => $ProdElemento) {
			$BEProductoMostrar = new BEProducto();
			$BEProductoMostrar = $ProdElemento;

			$cantProd = $BEProductoMostrar->getCantidad();
			$precioProd = $BEProductoMostrar->getPrecioActual();

			$montoProductos = $montoProductos + ($cantProd  * $precioProd);
		}
	}
	//fin if($cantProductosCarrito >0)


	//PASO ALTERNO CALCULAR CUPON
	//Paso A. Evaluar si cupón existe
	//Paso B. Evaluar si aplica para el cliente (identificado o no identificado)
	//Paso C. Obtener información del cupón
	//Paso D. Calcular el descuento

	//Evaluar si aplica el cliente
	$cuponBuscadoExiste = 0;
	$cuponBuscadoNoEncontro = -1;
	$cuponBuscadoNoTieneLongMinima = 0;
	$cuponBuscadoAplica = 0;
	$cuponBuscadoGeneraDcto = 0;

	$cantItemAplicaDcto = 0;

	$codigocuponbuscar = "";

	$mensajeError = "";

	$TieneClienteObtenido = 0;

	//Ver si cliente tiene cupón
	$tienecuponClienteObtenido = 0;
	$listadocuponesClienteObtenido = array();

	if (isset($_SESSION['clientecarritoobtenido'])) {
		$BEClienteObtenido = unserialize((base64_decode($_SESSION['clientecarritoobtenido'])));
		$TieneClienteObtenido = 1;

		$DACupon = new DACuponImagen();
		//20230711 se envia parametro X para considerar  b.CuponPublico in('S','R')
		$rpta3 = $DACupon->buscarcupones($BEClienteObtenido->getCodCliente(), 'X', $funciono3);

		if ($funciono3 == -1) {
			//header("Location:login.php");	
			//echo "Error en procedimiento buscar cupones de clientes";
			$mensajeError = $mensajeError . "Error en procedimiento buscar cupones de clientes.";
		}
		if ($funciono3 == 1) {

			while ($fila = $rpta3->fetch()) {
				$BECuponelement = new BECuponImagen();
				$tienecuponClienteObtenido  = 1;

				//20230625 Actualización de tabla de Cupones y columnas
				$idCupon = $fila["IdCupon"];
				//echo "<br>".$fila["NomImagen"];

				$BECuponelement->setIDCupon($idCupon);
				$BECuponelement->setCodCupon($fila["CodigoCupon"]);
				$BECuponelement->setNomImagen($fila["NomImagen"]);
				$BECuponelement->setLink($fila["Link"]);
				$BECuponelement->setIndicacionesCupon($fila["IndicacionCupon"]);



				$listadocuponesClienteObtenido[$idCupon] = $BECuponelement;
			}
		}
	}
	// fin if(isset($_SESSION['clientecarritoobtenido'])) {


	$BECuponBuscado = new BECuponImagen();

	if (isset($_POST['codigocuponbuscar'])) {
		$codigocuponbuscar = strtoupper($_POST['codigocuponbuscar']);
	}

	//echo "Ojito codigocuponbuscar ($codigocuponbuscar)";

	if (strlen($codigocuponbuscar) >= 3) {
		$DACuponImagen = new DACuponImagen();
		$bolBuscarCuponExiste = 0;
		$rptaBuscarCupon = $DACuponImagen->buscarCuponPorCodigo($codigocuponbuscar, $bolBuscarCuponExiste);

		if ($bolBuscarCuponExiste == 1) {
			$cuponBuscadoNoEncontro = 1;

			while ($fila = $rptaBuscarCupon->fetch()) {

				$BECuponBuscado->setIdCupon($fila["IdCupon"]);
				$BECuponBuscado->setCodCupon($fila["CodigoCupon"]);
				$BECuponBuscado->setTipoDcto($fila["TipoDcto"]);
				$BECuponBuscado->setCantDcto($fila["CantidadDcto"]);
				$BECuponBuscado->setTipoAplicacion($fila["TipoAplicacion"]);
				$BECuponBuscado->setValorAplicacion($fila["ValorAplicacion"]);
				$BECuponBuscado->setMontoMinimo($fila["MontoMinimo"]);
				$BECuponBuscado->setCuponPublico($fila["CuponPublico"]);
				$BECuponBuscado->setCuponNecesitaCumplirCond($fila["CuponNecesitaCumplirCond"]);

				//print_r($BECuponBuscado);

				$cuponBuscadoExiste = 1;
				$cuponBuscadoNoEncontro = 0;
			}
		} else {
			//echo "Error en procedimiento de buscar cupon. ";
			$mensajeError = $mensajeError . "Error en procedimiento de buscar cupon.";
		}


		if ($cuponBuscadoExiste == 1) {
			//echo " NecesitaCumplirCond (".$BECuponBuscado->getCuponNecesitaCumplirCond().") ";
			//echo " montoProductos ($montoProductos) ";
			//echo " montoMinimoCupon (".$BECuponBuscado->getMontoMinimo().") ";
			//echo " TieneClienteObtenido (".$TieneClienteObtenido.") ";

			/*
			if($montoProductos < $BECuponBuscado->getMontoMinimo() )
			{
				$mensajeError =$mensajeError."Cupón tiene monto mínimo: S/ ".$BECuponBuscado->getMontoMinimo()."</br>";
			}

			//$mensajeError =$mensajeError."Necesita condiciones ".$BECuponBuscado->getCuponNecesitaCumplirCond();

			if($BECuponBuscado->getCuponNecesitaCumplirCond() =="S" and $TieneClienteObtenido == 0 )
			{
				$mensajeError =$mensajeError."Cupón no aplica </br>";
			}
			*/

			//En caso sea un cupón que no necesita aplicar condiciones especiales. Solo se necesita validar el monto mínimo
			if ($BECuponBuscado->getCuponNecesitaCumplirCond() == "N" and $montoProductos >= $BECuponBuscado->getMontoMinimo()) {
				$cuponBuscadoAplica = 1;
			}

			//En caso sea un cupón que SI necesita aplicar condiciones especiales. Primero validar el monto mínimo
			//Luego validar que tenga un cliente elegido.
			//Finalmente si tiene ese cupón
			if ($BECuponBuscado->getCuponNecesitaCumplirCond() == "S" and $montoProductos >= $BECuponBuscado->getMontoMinimo() and $TieneClienteObtenido == 1) {

				//echo " Por aquí ";
				//echo " tienecuponClienteObtenido (".$tienecuponClienteObtenido.") ";

				//Revisaremos si el cupón es parte de sus cupones
				if ($tienecuponClienteObtenido == 1) {
					foreach ($listadocuponesClienteObtenido as $BECupon) {
						//print_r($BECupon);
						$codecupon = $BECupon->getCodCupon();

						//echo " codecupon ($codecupon)  codigocuponbuscar($codigocuponbuscar)";

						if ($codecupon == $codigocuponbuscar) {
							$cuponBuscadoAplica = 1;
						}
					}
				}
			}

			//Manejar el caso si no aplica cupón 
			if ($cuponBuscadoAplica == 0) {
				$mensajeError = $mensajeError . "No se cumple condiciones para aplicar cupón </br>";
			}
		} else {
			$mensajeError = $mensajeError . "Cupón no existe o no está vigente.";
		}
		//fin if($cuponBuscadoExiste == 1)

		//echo " </br> cuponBuscadoAplica(".$cuponBuscadoAplica.") ";

		if ($cuponBuscadoAplica == 1) {
			$_SESSION['cuponCarrito'] = $codigocuponbuscar;
		}
	} else {
		//$cuponBuscadoNoTieneLongMinima =1;
		$mensajeError = $mensajeError . "Cupón debe tener al menos 3 carácteres.";
	}
	//fin if( strlen($codigocuponbuscar) >=3  ) {

	if ($mensajeError != "") {
		$mensajeError = '<div style="color:red">' . $mensajeError . '</div>';
	}

	echo $mensajeError;
}
//fin operacion 2



//Eliminar Cupón
//Invocado por ajax desde carritocompra.php EliminarCupón
if ($operacion == 3) {
	//unset($_SESSION['clientecarritoobtenido']);
	unset($_SESSION['cuponCarrito']);
}




//ReevaluarCupón. Si ya no aplica se quita de Session
//Invocado por ajax desde carritocompra.php 
if ($operacion == 4) {

	//Obtener monto de Productos
	$montoProductos = 0;
	$cantProductosCarrito = 0;
	if (isset($_SESSION['listadoProdCarrito'])) {
		//echo " </br> SESSION RECUPERADA ";
		$listadoProdCarrito = unserialize((base64_decode($_SESSION['listadoProdCarrito'])));
		$cantProductosCarrito = count($listadoProdCarrito);
	}

	if ($cantProductosCarrito > 0) {
		foreach ($listadoProdCarrito as $correlativo => $ProdElemento) {
			$BEProductoMostrar = new BEProducto();
			$BEProductoMostrar = $ProdElemento;

			$cantProd = $BEProductoMostrar->getCantidad();
			$precioProd = $BEProductoMostrar->getPrecioActual();

			$montoProductos = $montoProductos + ($cantProd  * $precioProd);
		}
	}
	//fin if($cantProductosCarrito >0)


	//PASO ALTERNO CALCULAR CUPON
	//Paso A. Evaluar si cupón existe
	//Paso B. Evaluar si aplica para el cliente (identificado o no identificado)
	//Paso C. Obtener información del cupón
	//Paso D. Calcular el descuento

	//Evaluar si aplica el cliente
	$cuponBuscadoExiste = 0;
	$cuponBuscadoNoEncontro = -1;
	$cuponBuscadoNoTieneLongMinima = 0;
	$cuponBuscadoAplica = 0;
	$cuponBuscadoGeneraDcto = 0;

	$cantItemAplicaDcto = 0;

	$codigocuponbuscar = "";

	$mensajeError = "";


	$BECuponBuscado = new BECuponImagen();

	if (isset($_SESSION['cuponCarrito'])) {
		$codigocuponbuscar = $_SESSION['cuponCarrito'];
	}

	//echo "codigocuponbuscar ($codigocuponbuscar)";

	if ($codigocuponbuscar != "") {
		//echo "ACANGA1 ";
		//Ver si cliente tiene cupón
		$tienecuponClienteObtenido = 0;
		$listadocuponesClienteObtenido = array();

		if (isset($_SESSION['clientecarritoobtenido'])) {
			$BEClienteObtenido = unserialize((base64_decode($_SESSION['clientecarritoobtenido'])));
			$TieneClienteObtenido = 1;

			$DACupon = new DACuponImagen();
			//20230711 se envia parametro X para considerar  b.CuponPublico in('S','R')
			$rpta3 = $DACupon->buscarcupones($BEClienteObtenido->getCodCliente(), 'X', $funciono3);

			if ($funciono3 == -1) {
				//header("Location:login.php");	
				//echo "Error en procedimiento buscar cupones de clientes";
				$mensajeError = $mensajeError . "Error en procedimiento buscar cupones de clientes.";
			}
			if ($funciono3 == 1) {

				while ($fila = $rpta3->fetch()) {
					$BECuponelement = new BECuponImagen();
					$tienecuponClienteObtenido  = 1;

					//20230625 Actualización de tabla de Cupones y columnas
					$idCupon = $fila["IdCupon"];
					//echo "<br>".$fila["NomImagen"];

					$BECuponelement->setIDCupon($idCupon);
					$BECuponelement->setCodCupon($fila["CodigoCupon"]);
					$BECuponelement->setNomImagen($fila["NomImagen"]);
					$BECuponelement->setLink($fila["Link"]);
					$BECuponelement->setIndicacionesCupon($fila["IndicacionCupon"]);



					$listadocuponesClienteObtenido[$idCupon] = $BECuponelement;
				}
			}
		}
		// fin if(isset($_SESSION['clientecarritoobtenido'])) {


		//echo "ACANGA2 ";

		//echo "Ojito codigocuponbuscar ($codigocuponbuscar)";

		$DACuponImagen = new DACuponImagen();
		$bolBuscarCuponExiste = 0;
		$rptaBuscarCupon = $DACuponImagen->buscarCuponPorCodigo($codigocuponbuscar, $bolBuscarCuponExiste);

		//echo "bolBuscarCuponExiste ($bolBuscarCuponExiste)";

		if ($bolBuscarCuponExiste == 1) {
			$cuponBuscadoNoEncontro = 1;

			while ($fila = $rptaBuscarCupon->fetch()) {

				$BECuponBuscado->setIdCupon($fila["IdCupon"]);
				$BECuponBuscado->setCodCupon($fila["CodigoCupon"]);
				$BECuponBuscado->setTipoDcto($fila["TipoDcto"]);
				$BECuponBuscado->setCantDcto($fila["CantidadDcto"]);
				$BECuponBuscado->setTipoAplicacion($fila["TipoAplicacion"]);
				$BECuponBuscado->setValorAplicacion($fila["ValorAplicacion"]);
				$BECuponBuscado->setMontoMinimo($fila["MontoMinimo"]);
				$BECuponBuscado->setCuponPublico($fila["CuponPublico"]);
				$BECuponBuscado->setCuponNecesitaCumplirCond($fila["CuponNecesitaCumplirCond"]);

				//print_r($BECuponBuscado);

				echo " ACanga4 " . $BECuponBuscado->getMontoMinimo();

				$cuponBuscadoExiste = 1;
				$cuponBuscadoNoEncontro = 0;
			}
		} else {
			//echo "Error en procedimiento de buscar cupon. ";
			$mensajeError = $mensajeError . "Error en procedimiento de buscar cupon.";
		}
	}
	//fin if($codigocuponbuscar!="")





	if ($cuponBuscadoExiste == 1) {
		//echo " Acanga 5 ".$BECuponBuscado->getCuponNecesitaCumplirCond();
		//echo " NecesitaCumplirCond (".$BECuponBuscado->getCuponNecesitaCumplirCond().") ";
		//echo " montoProductos ($montoProductos) ";
		//echo " montoMinimoCupon (".$BECuponBuscado->getMontoMinimo().") ";
		//echo " TieneClienteObtenido (".$TieneClienteObtenido.") ";

		//En caso sea un cupón que no necesita aplicar condiciones especiales. Solo se necesita validar el monto mínimo
		if ($BECuponBuscado->getCuponNecesitaCumplirCond() == "N" and $montoProductos >= $BECuponBuscado->getMontoMinimo()) {
			$cuponBuscadoAplica = 1;
		}

		//En caso sea un cupón que SI necesita aplicar condiciones especiales. Primero validar el monto mínimo
		//Luego validar que tenga un cliente elegido.
		//Finalmente si tiene ese cupón
		if ($BECuponBuscado->getCuponNecesitaCumplirCond() == "S" and $montoProductos >= $BECuponBuscado->getMontoMinimo() and $TieneClienteObtenido == 1) {

			//echo " Por aquí ";
			//echo " tienecuponClienteObtenido (".$tienecuponClienteObtenido.") ";

			//Revisaremos si el cupón es parte de sus cupones
			if ($tienecuponClienteObtenido == 1) {
				foreach ($listadocuponesClienteObtenido as $BECupon) {
					//print_r($BECupon);
					$codecupon = $BECupon->getCodCupon();

					//echo " codecupon ($codecupon)  codigocuponbuscar($codigocuponbuscar)";

					if ($codecupon == $codigocuponbuscar) {
						$cuponBuscadoAplica = 1;
					}
				}
			}

			//echo " Acanga 6 ".$cuponBuscadoAplica;


		}
	} else {
		//$mensajeError =$mensajeError."Cupón no existe o no está vigente.";
	}
	//fin if($cuponBuscadoExiste == 1)

	//echo " </br> cuponBuscadoAplica(".$cuponBuscadoAplica.") ";



	/*
		if($cuponBuscadoAplica == 1)
		{
			$_SESSION['cuponCarrito'] = $codigocuponbuscar;
		}
		*/

	if ($mensajeError != "") {
		$mensajeError = '<div style="color:red">' . $mensajeError . '</div>';
	}



	//echo " Acanga 7 ".$cuponBuscadoAplica;

	//En caso no aplica el cupón actual se elimina de sessión.
	if ($cuponBuscadoAplica == 0) {
		unset($_SESSION['cuponCarrito']);
	}


	echo $mensajeError;
}
//fin operacion 4
