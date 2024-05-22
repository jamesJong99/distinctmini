<?php
session_start();

require_once ("ClassesStore/configuracionBD.php");
require_once ("ClassesStore/BETipoEntrega.php");
require_once ("ClassesStore/DATipoEntrega.php");
require_once ("ClassesStore/BEPedido.php");
require_once ("ClassesStore/BEProducto.php");
require_once ("ClassesStore/BEPedidoItem.php");
require_once ("ClassesStore/BECliente.php");
require_once ("ClassesStore/FaceAdsInfo.php");
require_once ("ClassesStore/DACliente.php");


require_once ("header.php");
$header = new header();

/*
if(isset($_SESSION['listadoProdCarrito'])) {
	$listadoProdCarrito = unserialize((base64_decode($_SESSION['listadoProdCarrito'])));
	print_r($listadoProdCarrito);
}
*/



//Recuperar la información de pedidos
$BEPedido = new BEPedido();
$BEPedido = unserialize((base64_decode($_SESSION['pedidodatos'])));

$montoTotal = $BEPedido->getMontoPedido();

$montoEnvio = $BEPedido->getMontoEnvioPed();
$montoCupon = $BEPedido->getAhorroCupon();
$productosArray = [];


//Mensajes

$CodRpta = 0;
$MensajeRpta = "";
$claseRpta = "";


if (isset($_REQUEST['CodRpta'])) {
	$CodRpta = $_REQUEST['CodRpta'];
}
if (isset($_REQUEST['MensajeRpta'])) {
	$MensajeRpta = $_REQUEST['MensajeRpta'];
}
if ($CodRpta == "1") {
	$claseRpta = "MensajeOk";
}
if ($CodRpta == "2" or $CodRpta == "3") {
	$claseRpta = "MensajeError";
}


//Obtener tipo de entrega
$BETipoEntrega = new BETipoEntrega();
$BETipoEntrega = unserialize((base64_decode($_SESSION['ubigeoElegido'])));

$DATipoEntrega = new DATipoEntrega();
//print_r($BETipoEntrega );

$bolCosto = 0;
$rptaCosto = $DATipoEntrega->obtenerTipoEntregaUbic_Ubigeo($BETipoEntrega->getIdTipoEntregaUbic(), $BETipoEntrega->getIdDpto(), $BETipoEntrega->getIdProv(), $BETipoEntrega->getIdDist(), $bolCosto);

if ($bolCosto == 1) {
	$cuponBuscadoNoEncontro = 1;

	while ($fila = $rptaCosto->fetch()) {


		$BETipoEntrega->setNomTipoEntrega($fila["NomTipoEntrega"]);
		$BETipoEntrega->setNameDpto($fila["NomDpto"]);
		$BETipoEntrega->setNameProv($fila["NomProv"]);
		$BETipoEntrega->setNameDist($fila["NomDist"]);

		$BETipoEntrega->setTiempoEntrega($fila["TiempoEntrega"]);
		$BETipoEntrega->setTipoEntrega($fila["TipoEntrega"]);


		//$BETipoEntrega->getIdTipoEntregaUbic($fila["Costo"]);
		$reg_serlizer = base64_encode(serialize($BETipoEntrega));
		$_SESSION['ubigeoElegido'] = $reg_serlizer;

	}
} else {
	echo "Error en procedimiento de obtener tipo entrega ubicación. ";
}


//print_r($BETipoEntrega);


//echo "montoTotal ($montoTotal). montoProductos ($montoProductos) montoDctoCupon ($montoDctoCupon) cuponCarritoEncontrado ($cuponCarritoEncontrado) ";

//20230626 Incluir cupon. En procesar_carritocompra se identifica si es cliente o no.
$operacion = 0;
$encontroCliente = 0;
$listadoProdCarrito = array();
$checkNoBrindaObtenido = "";
$BEClienteObtenido = new BECliente();

//PASO 1 Obtener listado de Session 
if (isset($_SESSION['clientecarritoobtenido'])) {
	$BEClienteObtenido = unserialize((base64_decode($_SESSION['clientecarritoobtenido'])));
	$operacion = 1;
} else {
	$operacion = 2;
	//$checkNoBrindaObtenido="NO";
}

//print_r($BEClienteObtenido);



//Validar si tiene carrito de compra
if (isset($_SESSION['listadoProdCarrito'])) {
	$listadoProdCarrito = unserialize((base64_decode($_SESSION['listadoProdCarrito'])));


	// pasar items del carrito de compra a un array para luego pasarlos a mercado pago

	foreach ($listadoProdCarrito as $prod) {
		$productosArray[] = [
			"codigo" => $prod->getCodProd(),
			"nombre" => $prod->getNomProd(),
			"cantidad" => $prod->getCantidad(),
			"precio" => $prod->getPrecioActual()
		];
	}
}

/********************************************facebookAds setting start****************************************
 * 
 */
require 'vendor/autoload.php';
require_once ("ClassesStore/FaceAdsInfo.php");

use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\Content;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\DeliveryCategory;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;

$RECENT_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$api = Api::init(null, null, ACCESS_TOKEN);
$api->setLogger(new CurlLogger());


if (isset($_SESSION['clientecarritoobtenido'])) {
	// making contents
	$contents = [];
	$content_name_arr = [];
	foreach ($productosArray as $key => $product) {
		$content_name_arr[] = $product['nombre'];
		$contents[] = (new Content())
			->setProductId($product['codigo'])
			->setQuantity($product['cantidad'])
			->setTitle($product['nombre'])
			->setDeliveryCategory(DeliveryCategory::HOME_DELIVERY);
	}

	$custom_data = (new CustomData())
		->setContents($contents)
		->setNumItems(count($contents))
		->setCurrency('USD')
		->setContentName(implode(',', $content_name_arr))
		->setValue($montoTotal);

	$event = (new Event())
		->setEventTime(time())
		->setEventName('InitiateCheckout')
		->setEventSourceUrl($RECENT_URL)
		->setActionSource(ActionSource::WEBSITE) //Origen de acción
		->setEventId(microtime())
		->setDataProcessingOptions(['LDU'], 0, 0);
	// load user information
	$BECliente = new BECliente();
	$BECliente = unserialize((base64_decode($_SESSION['clientecarritoobtenido'])));
	$name = $BECliente->getApeNom();

	$name_arr = explode(' ', $name);
	$FaceAdsInfo = new FaceAdsInfo();
	$bolLista = 2;
	$location_data = $FaceAdsInfo->getLocationByClinent($BECliente->getCodCliente(), $bolLista);

	//20240517 CambioChristian
	$codProvMeta = 0;
	$distMeta = "";
	if ($bolLista == 1) {

		while ($fila = $location_data->fetch()) {
			$distMeta = $fila["NomDist"];
			//echo " distMeta ($distMeta)";
			$codProvMeta = $fila["Id_Provincia"];
		}
	}

	if ($codProvMeta == "1501") {
		$distMeta = "LIMA";
	}

	$user_data = (new UserData())
		->setEmail($BECliente->getEmail())
		->setLastName($name_arr[count($name_arr) - 1])
		->setFirstName($name)
		->setClientIpAddress($_SERVER['REMOTE_ADDR'])
		->setCity($distMeta)
		->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
		->setCountryCode('PE')
		->setPhone($BECliente->getCelular());
	$event->setUserData($user_data);
	$event->setCustomData($custom_data);


	$events = array();
	array_push($events, $event);

	$request = (new EventRequest(PIXEL_ID))
		->setEvents($events);
	$response = $request->execute();
}

// --------------------------------FacebookAds end-------------------------------------------------


//echo "acanga (".$distMeta.") ";

//print_r($productosArray);
//print_r($listadoProdCarrito);

if (count($listadoProdCarrito) == 0) {
	header("Location:procesar_carritocompra.php");
}

//echo "checkNoBrindaObtenido ($checkNoBrindaObtenido)";

$codClienteObtenido = -1;
$tipodocObtenido = -1;
$apenomObtenido = "";
$celularObtenido = "";
$emailObtenido = "";
$numDocObtenido = "";
$direccionObtenida = "";
$referenciaObtenida = "";

$disabled = "";
$bckDisabled = "";

$disablesDireccion = "";
$bckDirecDisabled = "";

//echo "Uso dirección antigua:".$BETipoEntrega->getEligioDirecAnt() ;

//Buscar ciente por número de documento
if ($operacion == 1) {

	$codClienteObtenido = $BEClienteObtenido->getCodCliente();
	$tipodocObtenido = $BEClienteObtenido->getTipoDoc();
	$numDocObtenido = $BEClienteObtenido->getNumDoc();
	$apenomObtenido = $BEClienteObtenido->getApeNom();
	$celularObtenido = $BEClienteObtenido->getCelular();
	$emailObtenido = $BEClienteObtenido->getEmail();

	$direccionObtenida = $BETipoEntrega->getDireccion();

	$encontroCliente = 1;
	$disabled = "disabled";
	$bckDisabled = "bg-neutral-200";

	if ($BETipoEntrega->getEligioDirecAnt() == 1) {
		$disablesDireccion = "disabled";
		$bckDirecDisabled = "bg-neutral-200";
	}
}
//fin if($operacion == 1) {


//$listadoProdCarrito[1] = $BEProductoNuevo;
$cantElementosActuales = count($listadoProdCarrito);
//print_r($BEProductoNuevo);

$title = "Distinct - Tienda Online";
$descripcion = "Tienda online de productos de calidad";

$header->headerSet($title, $descripcion);

//print_r($BETipoEntrega);


?>


<style>
	.tipo_pago_opcion {
		display: block;
	}

	.tipo_pago_img {
		display: inline;
		float: right;
		margin-top: -12px;
	}

	.tipo_pago_content {
		display: none;
	}

	#tipo_pago_manual_content p {
		font-size: 13px !important;
	}

	#loading {
		animation: girar 1.5s infinite linear;
		display: none;
	}

	@keyframes girar {
		from {
			transform: rotate(0deg);
		}

		to {
			transform: rotate(360deg);
		}
	}

	@media(max-width: 450px) {
		.tipo_pago_img {
			display: none;
		}
	}
</style>



<section class="p-2 max-w-md m-auto my-0">

	<div class="flex gap-3 justify-center items-center my-4">
		<?php
		$linkRegresar = '"carritocompra.php"';
		?>
		<button
			class="bg-[#EDDFDB] text-[#97847d] border-[#EDDFDB] hover:bg-[#C4B8B5] hover:text-white  hover:border-[#c4b8b5] w-auto m-auto mb-3 block text-center py-1 px-2 rounded-md transition-colors"
			onclick=<?php echo "window.location.href=" . $linkRegresar; ?>>
			Regresar al carrito
		</button>

		<?php $linkCompra = "index.php"; ?>
		<button
			class="bg-[#EDDFDB] text-[#97847d] border-[#EDDFDB] hover:bg-[#C4B8B5] hover:text-white  hover:border-[#c4b8b5] w-auto m-auto mb-3 block text-center py-1 px-2 rounded-md transition-colors"
			onclick="<?php echo "window.location.href='" . $linkCompra . "'" ?>">
			Seguir comprando
		</button>
	</div>
</section>
<section class="max-w-screen-xl m-auto my-5">
	<h1 class="font-bold text-xl text-center">
		Finalizar Compra
	</h1>
	<div class="p-2 max-w-md m-auto">

		<?php
		if ($CodRpta == "1" or $CodRpta == "2" or $CodRpta == "3") {
			echo "<div class='$claseRpta'>$MensajeRpta</div>";
		}
		?>

		<div>
			<h3 class="text-left font-light text-lg text-zinc-700">Datos del Cliente</h3>

			<form id="formulario" name="formulario" method="post" class="text-[#8F6B60]">
				<div class="flex gap-3 my-2 ">
					<div>
						<label for="TipoDoc" class="mb-1 text-center ">
							Tipo de Documento*
						</label>
						<?php
						$tipo1 = "";
						$tipo2 = "";
						$tipo3 = "";

						//echo $tipodocObtenido;
						
						if ($tipodocObtenido != -1) {
							if ($tipodocObtenido == 1) {
								$tipo1 = "selected";
							}
							if ($tipodocObtenido == 2) {
								$tipo2 = "<option selected value='2'>Otro Tipo</option>";
							}
							if ($tipodocObtenido == 3) {
								$tipo3 = "selected";
							}
						}

						?>
						<select <?php echo $disabled; ?> id="TipoDoc" name="TipoDoc"
							class="border-[#8F6B60] border-2 rounded-md py-1 px-2 w-full h-8 <?php echo $$bckDisabled; ?> ">
							<option value="0">Elegir opción</option>
							<option <?php echo $tipo1; ?> value="1">DNI</option>
							<?php echo $tipo2; ?>
							<option <?php echo $tipo3; ?> value="3">Carnet de extranjeria</option>
						</select>
					</div>
					<div>
						<label for="NumDoc" class="mb-1 text-center">
							Num Documento Identidad*
						</label>
						<input <?php echo $disabled; ?> type="text"
							class="border-[#8F6B60] border-2 rounded-md py-1 px-2 w-full h-8 <?php echo $$bckDisabled; ?>"
							id="NumDoc" name="NumDoc" value="<?php echo $numDocObtenido; ?>">
					</div>
				</div>
				<div class="my-2">
					<label for="ApeNom" class="mb-1 text-center">
						Nombres y apellidos*
					</label>
					<input <?php echo $disabled; ?> type="text"
						class="border-[#8F6B60] border-2 rounded-md py-1 px-2 w-full h-8 <?php echo $$bckDisabled; ?>"
						id="ApeNom" name="ApeNom" value="<?php echo $apenomObtenido; ?>">

				</div>
				<div class="my-2">
					<label for="Celular" class="mb-1 text-center">
						Celular* (Contacto para coordinar entrega)
					</label>
					<input type="text" class="border-[#8F6B60] border-2 rounded-md py-1 px-2 w-full h-8" id="Celular"
						name="Celular" value="<?php echo $celularObtenido; ?>">
				</div>
				<div class="my-2">
					<label for="Email" class="mb-1 text-center">
						Email* (Llegará un email con el pedido generado)
					</label>
					<input type="text" class="border-[#8F6B60] border-2 rounded-md py-1 px-2 w-full h-8" id="Email"
						name="Email" value="<?php echo $emailObtenido; ?>">
				</div>
				<?php
				if ($BETipoEntrega->getTipoEntrega() == "C") {
					?>
					<div class="my-2">
						<label for="" class="mb-1 text-center">
							Destino
						</label>
						<p class="text-base">
							<?php echo $BETipoEntrega->getNameDpto() . " - " . $BETipoEntrega->getNameProv() . " - " . $BETipoEntrega->getNameDist(); ?>
						</p>
					</div>
					<?php
				}
				?>
				<div class="my-2">
					<label for="" class="mb-1 text-center">
						Tipo de entrega
					</label>
					<p class="text-base">
						<?php echo $BETipoEntrega->getNomTipoEntrega() . " ." . $BETipoEntrega->getTiempoEntrega(); ?>
					</p>
					<input type="hidden" class="numero" id="tipoEntrega" name="tipoEntrega"
						value="<?php echo $BETipoEntrega->getTipoEntrega(); ?>"
						class="border-[#8F6B60] border-2 rounded-md py-1 px-2 w-full h-8">
					<input type="hidden" class="numero" id="tieneCliente" name="tieneCliente"
						value="<?php echo $encontroCliente; ?>"
						class="border-[#8F6B60] border-2 rounded-md py-1 px-2 w-full h-8">
					<input type="hidden" class="numero" id="usoDireccionAnterior" name="usoDireccionAnterior"
						value="<?php echo $BETipoEntrega->getEligioDirecAnt(); ?>"
						class="border-[#8F6B60] border-2 rounded-md py-1 px-2 w-full h-8">
				</div>

				<?php
				if ($BETipoEntrega->getTipoEntrega() == "C") {
					?>
					<div class="my-2">
						<label for="Direccion">
							Dirección de envío (*)
						</label>
						<input <?php echo $disablesDireccion; ?> type="text"
							class="border-[#8F6B60] border-2 rounded-md py-1 px-2 w-full h-8 <?php echo $bckDirecDisabled; ?>"
							id="Direccion" name="Direccion" value="<?php echo $direccionObtenida; ?>">
					</div>
					<div class="my-2">
						<label for="Referencia">
							Referencia de envío (*)
						</label>
						<input <?php echo $disablesDireccion; ?> type="text"
							class="border-[#8F6B60] border-2 rounded-md py-1 px-2 w-full h-8 <?php echo $bckDirecDisabled; ?>"
							id="Referencia" name="Referencia" value="<?php echo $referenciaObtenida; ?>">
					</div>
					<?php
				}
				?>

				<?php
				if ($BETipoEntrega->getTipoEntrega() == "R") {
					?>
					<div class="my-2">
						<label for="Direccion">
							Dirección de recojo
						</label>
						<p class="text-base">Avenida Angamos Este 1559</p>

					</div>
					<?php
				}
				?>

				<div class="my-2">
					<label for="Nota" class="mb-1 text-center">
						Nota
					</label>
					<input type="text" class="border-[#8F6B60] border-2 rounded-md py-1 px-2 w-full h-8" id="Nota"
						name="Nota" value="">
				</div>

				<div class="my-2">
					<div class="mensaje" id="Mensaje"></div>
				</div>
				<div class="my-2 text-center">
					<b> Monto Total: S/ <?php echo $montoTotal; ?> </b>
				</div>



				<!-- controles opciones de pago -->
				<div class="mt-10">

					<h3 class="text-left font-light text-lg text-zinc-700">¿Como desea pagar?</h3>

					<label class="border border-gray-400 bg-gray-50 p-5 mt-5 cursor-pointer tipo_pago_opcion"
						for="tipo_pago_mp">
						<input type="radio" class="cursor-pointer" name="tipo_pago" id="tipo_pago_mp"
							value="MercadoPago">
						<span class="mb-1 cursor-pointer">Pago con tarjeta de crédito o débito</span>
						<img src="<?php echo BASE_URL ?>imagenes/mercadoPago.svg" class="tipo_pago_img">
					</label>

					<label class="border border-gray-400 bg-gray-50 p-5 mt-3 cursor-pointer tipo_pago_opcion"
						for="tipo_pago_manual">
						<input type="radio" class="cursor-pointer" name="tipo_pago" id="tipo_pago_manual"
							value="Manual">
						<span class="mb-1 cursor-pointer">Pago por YAPE, PLIN y transferencia</span>
						<img src="<?php echo BASE_URL ?>imagenes/pago_manual.svg" class="tipo_pago_img">
					</label>

				</div>



				<!-- opciones pago -->
				<div class="mt-10 mb-10 text-gray-500">


					<!-- mercado pago -->
					<div class="bg-gray-100 tipo_pago_content p-8 text-center" id="tipo_pago_mp_content">

						<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
							stroke="currentColor" class="w-6 h-6" style="display: inline;">
							<path stroke-linecap="round" stroke-linejoin="round"
								d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
						</svg>

						<span>Una vez que hagas clic para continuar, se te redireccionará a MercadoPago</span>

					</div>


					<!-- pago manual -->
					<div class="bg-gray-100 tipo_pago_content p-8" id="tipo_pago_manual_content">

						<p class="text-base text-gray-500">
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
								stroke="currentColor" class="w-6 h-6" style="display: inline;">
								<path stroke-linecap="round" stroke-linejoin="round"
									d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
							</svg>

							Para realizar la compra por favor sigue los 3 pasos siguientes:
						</p>

						<p class="text-base text-gray-500 mt-5">
							<b>Paso 1. Realiza una transferencia bancaria a BBVA, BCP, Yape o Lukita</b>
						</p>

						<p class="text-base text-gray-500 mt-5">
							<b>YAPE</b><br>
							Celular: 997931997. Titular: Kyc Panda Eirl
						</p>

						<p class="text-base text-gray-500 mt-5">
							<b>PLIN</b><br>
							Celular: 997931997. Titular: Katerine Sanchez
						</p>

						<p class="text-base text-gray-500 mt-5">
							<b>CUENTA BBVA - SOLES</b><br>
							Nro Cuenta: 0011-0508-0200334959 <br>
							Cuenta interbancaria (CCI): <br> 011-508-000200334959-90 <br>
							Titular: KYC Panda EIRL <br>
							Si realizas un deposito a cuenta y te encuentras en provincia incluir S/ 9 por comisión
							bancaria.
						</p>

						<p class="text-base text-gray-500 mt-5">
							<b>CUENTA BCP- SOLES</b><br>
							Nro Cuenta: 194-70567233-0-31 <br>
							Cuenta interbancaria (CCI): 00219417056723303196<br>
							Titular: Kyc Panda Eirl<br>
							Si realizas un deposito a cuenta y te encuentras en provincia incluir S/ 9 por comisión
							bancaria.
						</p>

						<p class="text-base text-gray-500 mt-5">
							<b>CUENTA INTERBANK- SOLES</b><br>
							Nro Cuenta: 898-3185098771 <br>
							Cuenta interbancaria (CCI): 003-898-013185098771-43<br>
							Titular: Katerine Sanchez Jurado<br>
							Si realizas un deposito a cuenta y te encuentras en provincia incluir S/ 9 por comisión
							bancaria.
						</p>

						<p class="text-base text-gray-500 mt-5">
							<b>Paso 2. Envíanos el boucher de pago o screenshot</b><br>
							Por whatsapp: 986145878 o <br>
							Por correo: distinct.venta@gmail.com
						</p>


						<p class="text-base text-gray-500 mt-5">
							<b>Paso 3. Te confirmamos la fecha de entrega</b><br>
							Validamos el pago y te brindamos la fecha de entrega.<br>
							Lima metropolitana: De 1 a 3 días laborales<br>
							Otros destinos Perú: De 3 a 7 días laborales.
						</p>

					</div>
					<!--/ pago manual -->


				</div>
				<!--/ opciones pago -->



				<div class="my-2">
					<div class="flex items-center gap-2">
						<input type="checkbox" name="terminos" id="terminos"
							class="border-[#8F6B60] border-2 rounded-full py-1 px-2 h-8" required checked>
						<label for="terminos">Acepto los <a class="border-b-2 border-[#8F6B60]"
								href="<?php echo BASE_URL_STORE . 'politicas-devolucion'; ?>" target="_blank">términos y
								condiciones</a></label>

					</div>
					<div class="flex items-start gap-2 mt-2">
						<input type="checkbox" name="recibirmail" id="recibirmail"
							class="border-[#8F6B60] border-2 rounded-full py-1 px-2 h-8" checked>
						<label for="recibirmail">Quiero recibir información de ofertas e informes por e-mail y/o
							celular</label>
					</div>
				</div>
				<div class="my-4 text-center
					">
					<a onclick='validarDatos();event.preventDefault();' id="btnGenerarPedido" name="btnGenerarPedido"
						class="bg-[#EDDFDB] text-[#97847d] border-[#EDDFDB] hover:bg-[#C4B8B5] hover:text-white  hover:border-[#c4b8b5] p-2 rounded-md cursor-pointer border-2 transition-all text-center">
						<button>

							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
								stroke="currentColor" class="w-6 h-6" id="loading">
								<path stroke-linecap="round" stroke-linejoin="round"
									d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
							</svg>

							Generar Pedido</button>
					</a>

				</div>


			</form>

		</div>

		<div>&nbsp;</div>
	</div>
</section>


<script>
	//ok
	function validarDatos() {

		var productosArray = <?php echo json_encode($productosArray) ?>;
		var montoEnvio = <?php echo json_encode($montoEnvio) ?>;
		var montoCupon = <?php echo json_encode($montoCupon) ?>;


		//alert('inicio')

		var tieneCliente = 0;
		var tipoEntrega = "C";

		tieneCliente = document.getElementById("tieneCliente").value;
		usoDirecAnt = document.getElementById("usoDireccionAnterior").value;
		tipoEntrega = document.getElementById("tipoEntrega").value;


		var tipodocciente = document.getElementById("TipoDoc").value;
		var numdoccliente = document.getElementById("NumDoc").value;
		var apenomcliente = document.getElementById("ApeNom").value;
		var celularcliente = document.getElementById("Celular").value;
		var emailcliente = document.getElementById("Email").value;

		var recibirmail = document.getElementById("recibirmail").value;
		var terminos = document.getElementById("terminos").value;

		var direccion = "";
		var referencia = "";
		var nota = "";

		if (tipoEntrega == "C") {
			direccion = document.getElementById("Direccion").value;
			referencia = document.getElementById("Referencia").value;
		}

		nota = document.getElementById("Nota").value;

		var mensaje = '';
		var cumple = true;

		//alert('tieneCliente: '+tieneCliente);
		//alert('usoDirecAnt: '+usoDirecAnt);
		//alert('tipoEntrega: '+tipoEntrega);

		if (tieneCliente == "0") {
			//validación de tipdoc cliente
			if (tipodocciente == "1" || tipodocciente == "3") {
				cumple = true;
			} else {
				mensaje = 'Selecciona un tipo de documento correcto  \n' + mensaje;
				cumple = false;
			}

			//validación de numdoc cliente
			if (validarobligatorio(numdoccliente)) {
				if (validarnumero(numdoccliente) && validarLongitud(numdoccliente, 8, 10)) {
					cumple = true;
				} else {
					mensaje = 'Ingrese un número de documento válido de 8 a 10 caracteres  \n' + mensaje;
					cumple = false;
				}
			} else {
				mensaje = 'Ingrese un número de documento válido de 8 a 10 caracteres  \n' + mensaje;
				cumple = false;
			}

			//Validar apellidos y nombres
			if (validarobligatorio(apenomcliente)) {
				if (validarLongitud(apenomcliente, 5, 199)) {
					cumple = true;
				} else {
					mensaje = 'Ingrese los apellidos y nombre  de 5 a 199 caracteres  \n' + mensaje;
					cumple = false;
				}
			} else {
				mensaje = 'Ingrese los apellidos y nombre  de 5 a 199 caracteres  \n' + mensaje;
				cumple = false;
			}


			//validación de email cliente
			if (validarobligatorio(emailcliente)) {
				if (validar(emailcliente, 2)) {
					tieneEmail = true;
				} else {
					mensaje = 'Ingrese un email valido \n' + mensaje;
					cumple = false;
				}

			} else {
				mensaje = 'Ingrese un email valido \n' + mensaje;
				cumple = false;
			}


			//alert('Msj 5');

			//validación de celular cliente
			if (validarobligatorio(celularcliente)) {
				if (validar(celularcliente, 3)) {
					tieneCelular = true;
				} else {
					mensaje = 'Ingrese un celular valido \n' + mensaje;
					cumple = false;
				}

			} else {
				mensaje = 'Ingrese un celular valido \n' + mensaje;
				cumple = false;
			}

			if (tipoEntrega == "C") {
				//validación de direccion
				if (validarobligatorio(direccion)) {
					if (validarLongitud(direccion, 5, 150)) {
						tieneCelular = true;
					} else {
						mensaje = 'Ingrese una dirección valida  \n' + mensaje;
						cumple = false;
					}

				} else {
					mensaje = 'Ingrese una dirección valida \n' + mensaje;
					cumple = false;
				}


			}
			//fin if(tipoEntrega =="C")



		}
		//fin if(tieneCliente =="0")

		if (tieneCliente == "1" && usoDirecAnt == "0" && tipoEntrega == "C") {


			//validación de email cliente
			if (validarobligatorio(emailcliente)) {
				if (validar(emailcliente, 2)) {
					tieneEmail = true;
				} else {
					mensaje = 'Ingrese un email valido \n' + mensaje;
					cumple = false;
				}

			} else {
				mensaje = 'Ingrese un email valido \n' + mensaje;
				cumple = false;
			}


			//alert('Msj 5');

			//validación de celular cliente
			if (validarobligatorio(celularcliente)) {
				if (validar(celularcliente, 3)) {
					tieneCelular = true;
				} else {
					mensaje = 'Ingrese un celular valido \n' + mensaje;
					cumple = false;
				}

			} else {
				mensaje = 'Ingrese un celular valido \n' + mensaje;
				cumple = false;
			}

			//validación de direccion
			if (validarobligatorio(direccion)) {
				if (validarLongitud(direccion, 5, 150)) {
					tieneCelular = true;
				} else {
					mensaje = 'Ingrese una dirección valida  \n' + mensaje;
					cumple = false;
				}

			} else {
				mensaje = 'Ingrese una dirección valida \n' + mensaje;
				cumple = false;
			}




		}
		//fin if(tieneCliente =="0")
		// script para hacer required los input con id terminos y recibirmail al hacer click button con id btnGenerarPedido
		if (!document.getElementById('terminos').checked) {
			alert('Debe aceptar los términos y condiciones');
			return false;
		}

		//alert('termine de validar');
		//alert(cumple);
		//alert(cumple);

		//alert(nota);

		if (cumple) {
			//document.formulario.submit();	


			if ($('#tipo_pago_mp').prop('checked')) // si el metodo de pago es MP
			{
				var data = {
					productosArray: productosArray,
					montoEnvio: montoEnvio,
					montoCupon: montoCupon,
					tipodocciente: tipodocciente,
					numdoccliente: numdoccliente,
					apenomcliente: apenomcliente,
					celularcliente: celularcliente,
					emailcliente: emailcliente,
					direccion: direccion,
					referencia: referencia,
					nota: nota,
					tipo_pago: 'mercado_pago'
				};

				// setting product



				$.ajax({
					data: data,
					url: 'iniciar_mercado_pago.php',
					type: 'POST',

					beforeSend: function () {
						$('#loading').css('display', 'inline-block')
					},

					success: function (response) {
						try {
							$('#loading').css('display', 'none')

							console.log(response)

							response = JSON.parse(response);

							if (response.status != undefined && response.status == 'OK') {
								location.href = response.link;
							}
							else
								alert('Ha ocurrido un error, por favor intente de nuevo')

						} catch (error) {
							console.log(error)
							alert('Ha ocurrido un error, por favor intente de nuevo')
						}

					},

					error: function (error) {
						console.log(error)
						alert('Ha ocurrido un error, por favor intente de nuevo')
					}
				});


			}
			else // si es manual
			{
				$.redirect("thanks.php", {
					"tipodocciente": tipodocciente,
					"numdoccliente": numdoccliente,
					"apenomcliente": apenomcliente,
					"celularcliente": celularcliente,
					"emailcliente": emailcliente,
					"direccion": direccion,
					"referencia": referencia,
					"nota": nota,
					"tipo_pago": 'manual'
				},
					"GET");
			}


		} else {
			alert(mensaje);
		}


	}



	// seleccionar metodo de pago
	$('.tipo_pago_opcion input').click(function (e) {
		if ($('.tipo_pago_opcion input.active').length > 0) // desactivar opcion previa
		{
			let opcionPrevia = $('.tipo_pago_opcion input.active')

			$(opcionPrevia).removeClass('active')

			$(opcionPrevia).parent().removeClass('border-blue-400')
			$(opcionPrevia).parent().removeClass('bg-blue-100')

			$('#' + opcionPrevia.attr('id') + '_content').hide(100)
		}


		// activar nueva opcion
		let opcion = $(this)
		opcion.addClass('active')

		opcion.parent().addClass('border-blue-400')
		opcion.parent().addClass('bg-blue-100')

		$('#' + opcion.attr('id') + '_content').show(100)
	})


	// activar por defecto mercado pago
	$('#tipo_pago_mp').click()

</script>


<style>
	.whatsapp-icon {
		position: fixed;
		bottom: 20px;
		right: 20px;
		width: 60px;
		height: 60px;
		background-color: #25D366;
		border-radius: 50%;
		text-align: center;
		line-height: 60px;
		color: #fff;
		font-size: 30px;
		z-index: 1000;
		/* Asegura que el ícono esté sobre otros elementos */
		cursor: pointer;
	}
</style>

<a href="https://wa.link/tc9xfn" target="_blank"><img class="whatsapp-icon"
		src="<?php echo BASE_URL_STORE ?>imagenes/wsp.png" alt="LogoWsp"></a>

<?php
require ('footer.php');
?>

<script>
	$(document).ready(function () {
		$('#btnGenerarPedido').click(function () {
			$('#terminos').prop('required', true);
		});
	});
</script>


</body>

</html>