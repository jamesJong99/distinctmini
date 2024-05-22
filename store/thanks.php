<?php
session_start();

/********************************************facebookAds library load start****************************************
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

// ---------------------------------facebookAds library load end-----------------------------------

require_once ("ClassesStore/configuracionBD.php");
require_once ("ClassesStore/DATipoEntrega.php");
require_once ("ClassesStore/BEProducto.php");
require_once ("ClassesStore/BECliente.php");
require_once ("ClassesStore/BEPedido.php");
require_once ("ClassesStore/BEPedidoItem.php");
require_once ("ClassesStore/BEPedidoItemDet.php");
require_once ("ClassesStore/BEUbigeo.php");
require_once ("ClassesStore/BETipoEntrega.php");
require_once ("ClassesStore/DACliente.php");
require_once ("ClassesStore/DAPedido.php");
require_once ("ClassesStore/DAPedidoItem.php");
require_once ("ClassesStore/DAPedidoItemDet.php");
require_once ("ClassesStore/DAPedidoLog.php");
require_once ("ClassesStore/DAUbigeo.php");
require_once ("header.php");

$header = new header();

$CodRpta = "";
$MensajeRpta = "";
$claseRpta = "";

//Usuario Sistema
$codeUsuario = 999;

//print_r($_POST);

//PASO 1 Obtener listado de Session 
$listadoProdCarrito = array();
$cantElementosActuales = 0;

$ClienteRecuperoSession = 0;
$BEClienteObtenido = new BECliente();

$tieneDatosMontos = 0;
$BEPedidoMonto = new BEPedido();

$tieneUbigeoGuardado = 0;
$BETipoEntregaGuardado = new BETipoEntrega();
$tipoEntregaGuardado = "";


$tieneCuponGuardado = 0;
$nombreCuponGuardado = "";

if (isset($_SESSION['listadoProdCarrito'])) {
    $listadoProdCarrito = unserialize((base64_decode($_SESSION['listadoProdCarrito'])));
}
$cantElementosActuales = count($listadoProdCarrito);

//print_r($listadoProdCarrito);

if (isset($_SESSION['clientecarritoobtenido'])) {
    $BEClienteObtenido = unserialize((base64_decode($_SESSION['clientecarritoobtenido'])));
    $ClienteRecuperoSession = 1;
}

if (isset($_SESSION['pedidodatos'])) {
    $BEPedidoMonto = unserialize((base64_decode($_SESSION['pedidodatos'])));
    $tieneDatosMontos = 1;
}

if (isset($_SESSION['ubigeoElegido'])) {
    $BETipoEntregaGuardado = unserialize((base64_decode($_SESSION['ubigeoElegido'])));
    $tieneUbigeoGuardado = 1;
    $tipoEntregaGuardado = $BETipoEntregaGuardado->getTipoEntrega();
}

if (isset($_SESSION['cuponCarrito'])) {
    $nombreCuponGuardado = $_SESSION['cuponCarrito'];
    $tieneCuponGuardado = 1;
}

//echo "<br> cantElementosActuales($cantElementosActuales) tieneDatosMontos($tieneDatosMontos) tieneUbigeoGuardado($tieneUbigeoGuardado)   ";

if ($cantElementosActuales == 0 or $tieneDatosMontos == 0 or $tieneUbigeoGuardado == 0) {
    $CodRpta = 2;
    $MensajeRpta = "No se tiene datos necesarios";
    //echo "Linea 40 $MensajeRpta";
    header("Location:carritocompra.php?CodRpta=$CodRpta&MensajeRpta=$MensajeRpta");
}

//Paso 2 Recuperar datos de Session
//$nobrindadatos ="";
$tipodocciente = 0;
$numdoccliente = 0;
$apenomcliente = "";
$celularcliente = "";
$emailcliente = "";
$direccioncliente = "";
$referenciacliente = "";
$notapedido = "";

$codcliente = "";

//20230626 Cambios sin cupon
$montoTotal = 0;
$montoProductos = 0;
$montoDctoCupon = 0;
$valorDctoCupon = 0;
$cuponCarritoEncontrado = "";

$montoEnvio = 0;
$montoAhorro = 0;

$montoTotal = $BEPedidoMonto->getMontoPedido();
$montoProductos = $BEPedidoMonto->getMontoProducto();
$montoDctoCupon = $BEPedidoMonto->getMontoDctoCupon();
$valorDctoCupon = $BEPedidoMonto->getValorDctoCupon();

$montoEnvio = $BEPedidoMonto->getMontoEnvioPed();
$montoAhorro = $BEPedidoMonto->getAhorroPedido();

/*20240425 */
$montoAhorroProductos = $BEPedidoMonto->getAhorroProductos();
$montoAhorroEnvio = $BEPedidoMonto->getAhorroEnvioGratis();
$montoAhorroCupon = $BEPedidoMonto->getAhorroCupon();
$txtEnvioGratis = $BEPedidoMonto->getTieneEnvioGratis();

$textoAhorroTotal = "";
if ($montoAhorroCupon > 0) {
    $textoAhorroTotal = $textoAhorroTotal . "</br>- Dcto por Cupón S/ " . $montoAhorroCupon;
}
if ($montoAhorroEnvio > 0) {
    $textoAhorroTotal = $textoAhorroTotal . "</br>- Ahorro por envío S/ " . $montoAhorroEnvio;
}
if ($montoAhorroProductos > 0) {
    $textoAhorroTotal = $textoAhorroTotal . "</br>- Ahorro por dcto en productos S/ " . $montoAhorroProductos;
}

$cuponCarritoEncontrado = $nombreCuponGuardado;

/*
if(isset($_POST['cuponCarritoEncontrado'])) {
    $cuponCarritoEncontrado = $_POST['cuponCarritoEncontrado'];
}
*/
//

$BEPedido = new BEPedido();
$BEPedido = unserialize((base64_decode($_SESSION['pedidodatos'])));

$montoTotal = $BEPedido->getMontoPedido();

$montoEnvio = $BEPedido->getMontoEnvioPed();
$montoCupon = $BEPedido->getAhorroCupon();


// facebook
$access_token = 'EAANRZCtYZCu8QBO8rIxYN2Lei0NWqZCaFoRZBTZCXkWQWbhKBPggNVZAvXwthwknPhWjDIvr27Y6GlGNAHL8xdwOrQZCZCykZBLryqoiBPofQM5E7VaesXM55IsTGLBHZB2O4OlD5JbLS4V7pO4XtsFEMmoRcjReUXuOE0b8re4LuZBJ7T9OSeibZBNsqmxUa4d2yzZCyCgZDZD';
$pixel_id = '1127127705148709';
$RECENT_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// load user information
$BECliente = new BECliente();

$api = Api::init(null, null, $access_token);
$api->setLogger(new CurlLogger());

// It is recommended to send Client IP and User Agent for Conversions API Events.




$clienteNuevoTieneDatosNecesarios = 0;
$clienteNuevoTieneDatosDireccion = 0;
$msjClienteNuevo = "";
if (isset($_GET['tipodocciente'])) {
    $tipodocciente = $_GET['tipodocciente'];
}
if (isset($_GET['numdoccliente'])) {
    $numdoccliente = $_GET['numdoccliente'];
}
if (isset($_GET['apenomcliente'])) {
    $apenomcliente = $_GET['apenomcliente'];
}
if (isset($_GET['celularcliente'])) {
    $celularcliente = $_GET['celularcliente'];
}
if (isset($_GET['emailcliente'])) {
    $emailcliente = $_GET['emailcliente'];
}
if (isset($_GET['direccion'])) {
    $direccioncliente = $_GET['direccion'];
    if (strlen($direccioncliente) > 150) {
        $direccioncliente = substr($direccioncliente, 0, 150);
    }
}
if (isset($_GET['referencia'])) {
    $referenciacliente = $_GET['referencia'];
    if (strlen($referenciacliente) > 150) {
        $referenciacliente = substr($referenciacliente, 0, 150);
    }
}

if (isset($_GET['nota'])) {
    $notapedido = $_GET['nota'];
    if (strlen($notapedido) > 400) {
        $notapedido = substr($notapedido, 0, 400);
    }
}
/********************************************facebookAds setting start****************************************
 * 
 */

//Recuperar la información de pedidos
$BEPedido = new BEPedido();
$BEPedido = unserialize((base64_decode($_SESSION['pedidodatos'])));

$montoTotal = $BEPedido->getMontoPedido();

$montoEnvio = $BEPedido->getMontoEnvioPed();
$montoCupon = $BEPedido->getAhorroCupon();

$RECENT_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$api = Api::init(null, null, ACCESS_TOKEN);
$api->setLogger(new CurlLogger());


//Validar si tiene carrito de compra
$productosArray = [];
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
// making contents

if (isset($_SESSION['clientecarritoobtenido'])) {
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
        ->setContentName(implode(',', $content_name_arr))
        ->setCurrency('USD')
        ->setValue($montoTotal);
    $event = (new Event())
        ->setEventTime(time())
        ->setEventName('Purchase')
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
        ->setEmail($emailcliente)
        ->setLastName($name_arr[count($name_arr) - 1])
        ->setFirstName($name)
        ->setClientIpAddress($_SERVER['REMOTE_ADDR'])
        ->setCity($distMeta)
        ->setClientUserAgent($_SERVER['HTTP_USER_AGENT'])
        ->setCountryCode('PE')
        ->setPhone($celularcliente);
    $event->setUserData($user_data);
    $custom_data->setOrderId($BECliente->getCodCliente());
    $event->setCustomData($custom_data);


    $events = array();
    array_push($events, $event);

    $request = (new EventRequest(PIXEL_ID))
        ->setEvents($events);
    $response = $request->execute();
}

// --------------------------------FacebookAds end-------------------------------------------------
if ($ClienteRecuperoSession == 0) {

    //echo "Ojito notapedido Parte1 (".$notapedido.") ";



    //echo "<br>Info1 tipoEntregaGuardado($tipoEntregaGuardado) codcliente($codcliente) tipodocciente ($tipodocciente) numdoccliente ($numdoccliente)  apenomcliente ($apenomcliente) celularcliente ($celularcliente) emailcliente ($emailcliente) direccion ($direccioncliente) referencia ($referenciacliente) ";


    if (strlen($tipodocciente) >= 1 and strlen($numdoccliente) >= 8 and strlen($apenomcliente) >= 5 and strlen($celularcliente) == 9 and strlen($emailcliente) >= 3) {
        $clienteNuevoTieneDatosNecesarios = 1;
    } else {
        $msjClienteNuevo = $msjClienteNuevo . "<br>Cliente nuevo no tiene datos necesarios";
    }

    if ($tipoEntregaGuardado == "R") {
        $clienteNuevoTieneDatosDireccion = 1;
    }

    if ($tipoEntregaGuardado == "C" and strlen($direccioncliente) >= 3) {
        $clienteNuevoTieneDatosDireccion = 1;
    } else {
        $msjClienteNuevo = $msjClienteNuevo . "<br>Dirección de cliente nuevo no tiene datos necesarios";
    }
}

if ($ClienteRecuperoSession == 1) {
    $codcliente = $BEClienteObtenido->getCodCliente();

    $tipodocciente = $BEClienteObtenido->getTipoDoc();
    $numdoccliente = $BEClienteObtenido->getNumDoc();
    $apenomcliente = $BEClienteObtenido->getApeNom();

    //Se obtiene de POST
    //$celularcliente = $BEClienteObtenido->getCelular();
    //$emailcliente = $BEClienteObtenido->getEmail();
    if (isset($_GET['celularcliente'])) {
        $celularcliente = $_GET['celularcliente'];
    }
    if (isset($_GET['emailcliente'])) {
        $emailcliente = $_GET['emailcliente'];
    }


    if (isset($_GET['direccion'])) {
        $direccioncliente = $_GET['direccion'];
        if (strlen($direccioncliente) > 150) {
            $direccioncliente = substr($direccioncliente, 0, 150);
        }
    }
    if (isset($_GET['referencia'])) {
        $referenciacliente = $_GET['referencia'];
        if (strlen($referenciacliente) > 150) {
            $referenciacliente = substr($referenciacliente, 0, 150);
        }
    }




    if (isset($_GET['nota'])) {
        $notapedido = $_GET['nota'];
        if (strlen($notapedido) > 400) {
            $notapedido = substr($notapedido, 0, 400);
        }
    }


    //echo "<br>Info2 tipoEntregaGuardado($tipoEntregaGuardado) codcliente($codcliente) tipodocciente ($tipodocciente) numdoccliente ($numdoccliente)  apenomcliente ($apenomcliente) celularcliente ($celularcliente) emailcliente ($emailcliente) direccion ($direccioncliente) referencia ($referenciacliente) ";


}

/*
OJITO $BETipoEntrega->getEligioDirecAnt();
OJITO $BETipoEntrega->getTipoEntrega()
*/


$puedeProcesar = 0;

//echo "<br> ClienteRecuperoSession($ClienteRecuperoSession) clienteNuevoTieneDatosNecesarios ($clienteNuevoTieneDatosNecesarios) clienteNuevoTieneDatosDireccion ($clienteNuevoTieneDatosDireccion)  ";


//3 casos distintos
//Caso 1 cliente identificado ClienteRecuperaSession = 1 con nueva o antigua dirección. No es relevante esta en el objeto Ubigeo Guardado
//Caso 2 cliente nuevo que tenga datos completos para generar pedido y con la dirección necesaria.
if ($ClienteRecuperoSession == 1 or ($ClienteRecuperoSession == 0 and $clienteNuevoTieneDatosNecesarios == 1 and $clienteNuevoTieneDatosDireccion = 1)) {
    $puedeProcesar = 1;
}


if ($puedeProcesar == 0) {
    //echo "Aqui";
    $CodRpta = 2;
    $MensajeRpta = "Información necesaria está incompleta.";

    //echo "Linea 116 $MensajeRpta";
    header("Location:carritocompra.php?CodRpta=$CodRpta&MensajeRpta=$MensajeRpta");
}
//fin if($nobrindadatos =="NO" )

//echo "<br> puedeProcesar: $puedeProcesar";
$title = "Gracias";
$description = "Gracias por su compra";

$header->headerSet($title, $description);
?>


<section class="contenedor_info">
    <div>
        <div>
            <?php


            //Paso 3 Generar Codigo de Cliente en caso no tenga
            $DACliente = new DACliente();
            $BEPedido = new BEPedido();

            //echo "<br> Revisar codcliente($codcliente)";
            

            if ($ClienteRecuperoSession == 1) {

                $codcliente = $BEClienteObtenido->getCodCliente();
                //$celularcliente = $BEClienteObtenido->getCelular();
                //$emailcliente  = $BEClienteObtenido->getEmail();
            
                $BEPedido->setCodCliente($codcliente);
                //echo "<br> Cliente Logeado. Puso cliente en BEPedido: ($codcliente)";
            



                //Actualizar Email y celular
                //actualizar nombre del cliente
                $bolActualizarDatoCliente = 0;
                $errorUpdateCliente = "";
                $DACliente->ActualizarCelularEmail($codcliente, $celularcliente, $emailcliente, $bolActualizarDatoCliente);

                //echo " Datos Update($codcliente, $celularcliente, $emailcliente) ";
            
                if ($bolActualizarDatoCliente == -1) {
                    $errorUpdateCliente = $errorUpdateCliente . "Error: Procedimiento de actualizar nombre del cliente";

                }

                //echo " Error update (".$errorUpdateCliente.")";
            
            }


            $ClienteNoLogeadoUbicoClienteporNumDoc = -1;

            /* 20240420 Ubicar a un cliente por DNI en caso no se haya logeado */
            //En caso ubica a un codígo de cliente se asigna el codigo de cliente a la entidad cliente y pedido.
            //Tambíen se actualiaz 
            if ($ClienteRecuperoSession == 0) {
                $BECliente2 = new BECliente();
                $BECliente2->setNumDoc($numdoccliente);

                //echo " Buscando cliente ".$numdoccliente." ";
            
                $rptaBuscarCliente = $DACliente->buscarCliente($BECliente2, $bolBuscarCliente);


                if ($bolBuscarCliente == -1) {

                    $ClienteNoLogeadoUbicoClienteporNumDoc = 0;
                }
                if ($bolBuscarCliente == 1) {

                    while ($fila = $rptaBuscarCliente->fetch()) {
                        //echo " Encontro cliente ".$fila["CodCliente"]." ";
            
                        $ClienteNoLogeadoUbicoClienteporNumDoc = 1;
                        //$BECliente->setCodCliente($fila["CodCliente"]);
                        $BEPedido->setCodCliente($fila["CodCliente"]);

                        //Actualizar Email y celular
                        $bolActualizarDatoCliente = 0;
                        $errorUpdateCliente = "";
                        $DACliente->ActualizarCelularEmail(($fila["CodCliente"]), $celularcliente, $emailcliente, $bolActualizarDatoCliente);

                        //echo " Datos Update($codcliente, $celularcliente, $emailcliente) ";
            
                        if ($bolActualizarDatoCliente == -1) {
                            $errorUpdateCliente = $errorUpdateCliente . "Error: Procedimiento de actualizar nombre del cliente";

                        }

                        //echo " Error update (".$errorUpdateCliente.")";
            
                    }
                }


            }
            //fin if($ClienteRecuperoSession==0  )
            
            //echo "<br> codcliente($codcliente) nobrindadatos ($nobrindadatos) tipodocciente ($tipodocciente) numdoccliente ($numdoccliente)  apenomcliente ($apenomcliente) celularcliente ($celularcliente) emailcliente ($emailcliente) ";
            
            if ($ClienteRecuperoSession == 0 && $ClienteNoLogeadoUbicoClienteporNumDoc != 1) {


                $BECliente = new BECliente();
                $BECliente->setTipoDoc($tipodocciente);
                $BECliente->setNumDoc($numdoccliente);
                $BECliente->setApeNom($apenomcliente);
                $BECliente->setCelular($celularcliente);
                $BECliente->setEmail($emailcliente);

                $bolRegistrarCliente = 0;
                $rptaRegistrarCliente = $DACliente->registrarCliente($BECliente, $bolRegistrarCliente);

                if ($bolRegistrarCliente == -1) {

                    echo "<p class='mensaje'>";
                    echo "Error: Procedimiento Registrar Cliente";
                    echo "</p>";
                }

                if ($bolRegistrarCliente == 1) {

                    while ($fila = $rptaRegistrarCliente->fetch()) {
                        $nuevoCodCliente = $fila["vNuevoCodCliente"];
                        $BECliente->setCodCliente($nuevoCodCliente);
                        $BEPedido->setCodCliente($nuevoCodCliente);
                        $codcliente = $nuevoCodCliente;
                        //echo "<br> Cod Cliente generado: $nuevoCodCliente ";
                    }
                }
            }
            //FIN if($codcliente =="")
            
            //print_r($BEPedido);
            
            //Antes de generar pedido evaluar si pertenece a un código de usuario
            $claveRegistro = $notapedido;
            $longClaveRegistro = strlen($claveRegistro);
            if ($longClaveRegistro >= 2) {
                $claveRegistro = substr($claveRegistro, 0, 2);

                //echo " Nota 2 $claveRegistro";
            
                $DAPedido = new DAPedido();
                $bolBuscarClave = 0;
                $rptaClaveRegistro = $DAPedido->obtenerCanalUsuarioxClaveRegistro($claveRegistro, $bolBuscarClave);

                if ($bolBuscarClave == -1) {

                    echo "<p class='mensaje'>";
                    echo "Error: Procedimiento Buscar Clave Registro";
                    echo "</p>";
                }
                if ($bolBuscarClave == 1) {

                    while ($fila = $rptaClaveRegistro->fetch()) {

                        //$codigoCanal = $fila["CodCanalVenta"];
                        $codeUsuario = $fila["CodUsuario"];

                        //echo " Clave ubicado: ";
                        //echo " CodCanal ".$codigoCanal;
                        //echo " CodUsuario ".$codigoUsuarioRegistro;
            
                    }
                }

            }


            //Paso 4 generar pedido
            //La entidad BEPedido Ya tiene codcliente asignado
            
            //Ojito número de pedido se debe generar
            //$BEPedido->setNumPed( $sheet->getCell($arrayInfoNecesaria["Núm. de pedido"].$row)->getValue() );
            
            $timezone = -5; //(GMT -5:00) EST (U.S. & Canada)
            $fechaHoy = gmdate("Y-m-d", time() + 3600 * ($timezone + date("I")));


            //Cuidado con Fecha de Pedido tendrá la fecha en formato final
            $BEPedido->setPaisDelivery("PER");
            $BEPedido->setDptoDelivery("Lima");
            $BEPedido->setEmail($emailcliente);
            $BEPedido->setCelular($celularcliente);
            $BEPedido->setMetodoPago("offline");
            $BEPedido->setFechaPedido($fechaHoy);
            $BEPedido->setEstadoPago("paid");
            $BEPedido->setEstadoEntrega("fulfilled");
            $BEPedido->setCodCanalVenta("C001");
            $BEPedido->setCodUsuarioRegistro($codeUsuario);
            $BEPedido->setSistemaRegPedido("SIA");

            //OJITO TENGO DPTO, PROV Y DISTRITO JIJI
            
            if ($tipoEntregaGuardado == "R") {
                $BEPedido->setCiudadDelivery("Lima. Lima. Surquillo");
                $BEPedido->setDireccionDelivery("Recojo en Surquillo");
                $BEPedido->setMetodoEnvio("Recojo en Surquillo");
                $BEPedido->setNota("");
            }

            if ($tipoEntregaGuardado == "C") {
                //$BETipoEntregaGuardado
                $dptoProvDist = $BETipoEntregaGuardado->getNameDpto() . " - " . $BETipoEntregaGuardado->getNameProv() . " - " . $BETipoEntregaGuardado->getNameDist();
                $tiempoEntrega = $BETipoEntregaGuardado->getNomTipoEntrega() . " - " . $BETipoEntregaGuardado->getTiempoEntrega();
                $direccionRegistrar = $direccioncliente;

                if ($referenciacliente != "") {
                    $direccionRegistrar = $direccionRegistrar . " Ref:" . $referenciacliente;
                    $direccioncliente = $direccionRegistrar;
                }

                $BEPedido->setCiudadDelivery($dptoProvDist);
                $BEPedido->setDireccionDelivery($direccionRegistrar);
                $BEPedido->setMetodoEnvio($tiempoEntrega);

            }



            //20240306 Cotización con cupon
            
            $BEPedido->setMontoPedido($montoTotal);
            $BEPedido->setMontoProducto($montoProductos);
            $BEPedido->setMontoDctoCupon($montoDctoCupon);
            $BEPedido->setCupon($cuponCarritoEncontrado);
            $BEPedido->setMontoEnvioPed($montoEnvio);
            $BEPedido->setAhorroPedido($montoAhorro);
            $BEPedido->setNota($notapedido);

            //echo "Ojito notapedido Parte2 (".$notapedido.") ";
            
            //echo " 1 ValorDcto Cupon ($valorDctoCupon) ";
            $BEPedido->setValorDctoCupon($valorDctoCupon);
            //echo " 2 GetValorDctoCupon (".$BEPedido->getValorDctoCupon().") ";
            

            $nuevoNumPedido = 0;
            $DAPedido = new DAPedido();
            $bolObtenerNumPed = 0;
            $rptaObtenerNumPed = $DAPedido->obtenerCorrelativoPedido($bolObtenerNumPed);

            if ($bolObtenerNumPed == -1) {

                echo "<p class='mensaje'>";
                echo "Error: Procedimiento Obtener correlativo de pedido";
                echo "</p>";
            }

            if ($bolObtenerNumPed == 1) {

                while ($fila = $rptaObtenerNumPed->fetch()) {
                    $nuevoNumPedido = $fila["NuevoCodPed"];
                    //echo "<br> Pedido obtenido (ojo aún no generado): $nuevoNumPedido ";
                }
            }

            $BEPedido->setNumPed($nuevoNumPedido);
            //print_r($BEPedido);
            
            $pedidoRegistrado = 0;
            $bolRegistraPedido = 0;
            $rptaRegistraPedido = $DAPedido->registrarPedidoSIA($BEPedido, $bolRegistraPedido);

            if ($bolRegistraPedido == -1) {

                echo "<p class='mensaje'>";
                echo "Error: Procedimiento Registrar Pedido desde SIA";
                echo "</p>";
            } else {
                //echo " <br> Pedido registrado ";
                $pedidoRegistrado = 1;
            }



            //Registro de Pedido_Item. Continuar si se registro Pedido
            $pedidosItemRegistrados = 0;
            if ($pedidoRegistrado == 1) {
                $DAPedidoItem = new DAPedidoItem();

                //print_r($listadoProdCarrito);
            
                foreach ($listadoProdCarrito as $ItemKey => $ItemElement) {
                    $BEProducto = new BEProducto();
                    $BEProducto = $ItemElement;

                    $BEPedidoItem = new BEPedido_Item();

                    $tempcodprod = $BEProducto->getCodProd();
                    $tempnomprod = $BEProducto->getNomProd();
                    $tempcant = $BEProducto->getCantidad();
                    $tempprecioactual = $BEProducto->getPrecioActual();
                    $tempahorroprod = $BEProducto->getAhorro();

                    //Del listadoItem tener un foreach utilizar nomgrupovariante y namevariante
                    $tempvariante = "";

                    foreach ($BEProducto->listadoItem as $IdCodeItemVarComparar => $ItemComparar) {
                        $BEItem_Comparar = new BEPedido_Item();
                        $BEItem_Comparar = $ItemComparar;

                        //echo "</br> Comparando CodItemNuevo (".$BEItem_Nuevo->getCodItem() .") == CodItemNuevoComparar (".$BEItem_Comparar->getCodItem() .") ";
            
                        if ($BEItem_Comparar->getNomGrupoVariante() != "" or $BEItem_Comparar->getNameVariante() != "") {
                            $tempvariante = $tempvariante . "" . $BEItem_Comparar->getNomGrupoVariante() . ":" . $BEItem_Comparar->getNameVariante() . " ";
                        }
                    }

                    if (strlen($tempvariante) >= 2) {
                        $tempvariante = substr($tempvariante, 0, strlen($tempvariante) - 1);
                    }


                    $bolRegistrarPedidoItem = 0;
                    $BEPedidoItem->setNumPed($nuevoNumPedido);
                    $BEPedidoItem->setCodProd($tempcodprod);
                    $BEPedidoItem->setNomProd($tempnomprod);
                    $BEPedidoItem->setVariante($tempvariante);
                    $BEPedidoItem->setSku("");
                    $BEPedidoItem->setCantidad($tempcant);
                    $BEPedidoItem->setPrecio($tempprecioactual);
                    $BEPedidoItem->setAhorro($tempahorroprod);

                    //print_r($BEPedidoItem);
            
                    $DAPedidoItem->registrarPedidoItem($BEPedidoItem, $bolRegistrarPedidoItem);

                    //echo "<br> Func ($bolRegistrarPedidoItem)";
            

                    if ($bolRegistrarPedidoItem == -1) {
                        echo "<p class='mensaje'>";
                        echo "Error: Procedimiento Registrar Pedido Item";
                        echo "</p>";
                    } else {
                        $pedidosItemRegistrados = $pedidosItemRegistrados + 1;
                    }

                    //echo "<br> pedidosItemRegistrados ($pedidosItemRegistrados)";
            

                }
            }
            //if($pedidoRegistrado == 1)
            
            if ($pedidoRegistrado == 0) {
                echo "<p class='mensaje'>";
                echo "Error: Pedido no fue generado.";
                echo "</p>";
            }



            //Registro de Pedido_Item_Det
            $pedidosItemRegistrados = 0;
            $pedidosItemDetRegistrados = 0;
            if ($pedidoRegistrado == 1) {

                //Antes de registrar la información se necesita contar con un arreglo a nivel de item y que tenga cantidad.
                //Es posible que en un producto 1 como el pack pretty tenga una bb cream latte y en el producto 2 tenga otra bb cream latte.
                //En estos casos se debe acumular la cantidad y agrupar por Cod Item
            
                $listadoItem = array();

                foreach ($listadoProdCarrito as $ItemKey => $ItemElement) {
                    $BEProducto = new BEProducto();
                    $BEProducto = $ItemElement;

                    $cantProdItem = $BEProducto->getCantidad();


                    foreach ($BEProducto->listadoItem as $IdCodeItemVarComparar => $ItemComparar) {

                        $BEItem_Comparar = new BEPedido_Item();
                        $BEItem_Comparar = $ItemComparar;


                        $codItem = $BEItem_Comparar->getCodItem();
                        $canItem = $cantProdItem;
                        //$nomItem= $BEProducto->getNomProd()." ".$BEItem_Comparar->getNomGrupoVariante()." ".$BEItem_Comparar->getNameVariante();
                        $nomItem = $BEItem_Comparar->getNomItem();

                        if (isset($listadoItem[$codItem])) {

                            $BEPedidoItemDet = new BEPedidoItemDet();
                            $BEPedidoItemDet = $listadoItem[$codItem];

                            $cantAnterior = $BEPedidoItemDet->getCantidad();
                            $cantActual = $cantAnterior + $canItem;

                            $BEPedidoItemDet->setCantidad($cantActual);
                            $listadoItem[$codItem] = $BEPedidoItemDet;
                        } else {
                            $BEPedidoItemDet = new BEPedidoItemDet();
                            $BEPedidoItemDet->setNumPed($nuevoNumPedido);
                            $BEPedidoItemDet->setCodItem($codItem);
                            $BEPedidoItemDet->setNomItem($nomItem);
                            $BEPedidoItemDet->setCantidad($canItem);

                            $listadoItem[$codItem] = $BEPedidoItemDet;
                        }
                    }
                    //fin de foreach
            
                }
                //fin de foreach
            
                //print_r($listadoItem);
            
                $DAPedidoItemDet = new DAPedidoItemDet();
                foreach ($listadoItem as $CodItemKey => $ItemElement) {
                    $BEItemDet = new BEPedidoItemDet();
                    $BEItemDet = $ItemElement;

                    //echo "</br> Item:".$BEPedidoItemDet->getCodItem();
            
                    $bolRegistrarPedidoItemDet = 0;
                    $DAPedidoItemDet->registrarPedidoItemDetalle($BEItemDet, $bolRegistrarPedidoItemDet);

                    //echo "</br> Func ($bolRegistrarPedidoItem) ";
            
                    if ($bolRegistrarPedidoItemDet == -1) {
                        echo "<p class='mensaje'>";
                        echo "Error: Procedimiento Registrar Pedido Item Det";
                        echo "</p>";
                    } else {
                        $pedidosItemDetRegistrados = $pedidosItemDetRegistrados + 1;
                    }
                }
                //fin de foreach
            
                //echo "</br> pedidosItemRegistrados ($pedidosItemRegistrados) pedidosItemDetRegistrados  ($pedidosItemDetRegistrados )";
            
                //print_r($listadoItem);
            



                //Finalmente la validación. Ademas actualiza algunos campos necesarios
                $DAPedido = new DAPedido();


                //$registroCantidadItem = count($BEProducto->listadoItem);
                $registroCantidadItem = count($listadoProdCarrito);
                $registroCantidadItemDet = count($listadoItem);

                //echo "</br> registroCantidadItem ($registroCantidadItem) registroCantidadItemDet ($registroCantidadItemDet) ";
            
                $bolValidarPedido = 0;
                $validoPedido = -1;

                $rptaValidarPedido = $DAPedido->uspValidarPedidoGenSia($nuevoNumPedido, $registroCantidadItem, $registroCantidadItemDet, $bolValidarPedido);

                if ($bolValidarPedido == -1) {

                    echo "<p class='mensaje'>";
                    echo "Error: Procedimiento Validar Cliente";
                    echo "</p>";
                }
                if ($bolValidarPedido == 1) {

                    while ($fila = $rptaValidarPedido->fetch()) {
                        //print_r($fila);
                        $validoPedido = $fila["vError"];
                    }
                }

                if ($validoPedido == -1) {
                    echo "<p class='mensaje'>";
                    echo " Validación: No se obtuvo rpta del procedimiento validar pedido";
                    echo "</p>";
                }
                if ($validoPedido == 1) {
                    echo "<p class='mensaje'>";
                    echo " Validación: Error en registro de pedido. Se elimino toda la información del pedido.";
                    echo "</p>";
                }
                if ($validoPedido == 2) {
                    echo "<p class='mensaje'>";
                    echo " Validación: Error en registro de pedido item. Se elimino toda la información del pedido.";
                    echo "</p>";
                }
                if ($validoPedido == 3) {


                    echo "<p class='mensaje'>";
                    echo " Validación: Error en registro de pedido item det. Se elimino toda la información del pedido.";
                    echo "</p>";
                }

                $imgTodoOK = ' <img src="../imagenes/iconoOk.png" alt="Distinct" width="15px"  >';
                if ($validoPedido == 0) {
                    // enviar correo El email se debe enviar al cliente e ir con copia a distinct.venta@gmail.com y distinct.sales1@outlook.com (guardar los email en tabla parametro_lista)
                    // Definir constantes para las direcciones de correo electrónico
            
                    // Definir constantes para las direcciones de correo electrónico
                    define('EMAIL_FROM', 'no-reply@distinct.pe');
                    define('EMAIL_CC', 'distinct.venta@gmail.com, distinct.sales1@outlook.com');

                    // Definir las variables necesarias para enviar el correo
                    $to = $emailcliente;
                    $subject = "Distinct. Pedido Generado: $nuevoNumPedido";
                    $message = $message = "<!DOCTYPE html>

                    <html lang='es' xmlns='https://www.w3.org/1999/xhtml' xmlns:o='urn:schemas-microsoft-com:office:office'>
                    
                    <head>
                    
                        <meta charset='UTF-8'>
                    
                        <meta name='viewport' content='width=device-width,initial-scale=1'>
                    
                        <meta name='x-apple-disable-message-reformatting'>
                    
                        <title></title>
                    
                    
                    
                    </head>
                    
                    <body>
                        <table width='100%' cellpadding='0' cellspacing='0'
                            style='max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif; color: #8C8081;'>
                            <tr>
                                <td style='text-align: center; padding: 20px 0;'>
                                    <img style='max-width: 150px;' src='https://www.distinct.pe/imagenes/logo.png' >
                                </td>
                            </tr>
                            <tr>
                                <td style='background-color: #d1bec0; text-align: center; padding: 20px 0;'>
                                    <h2 style='font-size: 24px; color:#ffffff; font-weight: bold; margin-bottom: 0px;'>Pedido
                                        " . $nuevoNumPedido . "
                                    </h2>
                                </td>
                            </tr>
                            <tr>
                            <td style='text-align: center; padding: 20px 0;'>
                                <p style='font-size: 16px; font-weight: 300; margin-bottom: 20px;'>Te enviamos un email con la
                                    informacion del pedido y formas de pago.</p>
                            </td>
                            </tr>
                            <tr>
                                <td style='padding: 20px;'>
                                    <table width='100%' cellpadding='0' cellspacing='0'>
                                        <tr>
                                            <td style='text-align: center; font-size: 16px; font-weight: bold;'>Total a pagar</td>
                                            <td style='text-align: center; font-size: 16px; font-weight: bold;'>S/.
                                                " . $montoTotal . " <span
                                                    style='display: block; font-size: 12px; font-weight: 300;'>Ahorras S/.
                                                    " . $montoAhorro . "
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style='border-top: 2px solid #d1bec0; padding: 20px 0;'>
                                    <h3 style='text-align: center; font-size: 20px; font-weight: bold; margin-bottom: 20px;'>Formas de pago
                                    </h3>
                                    <p style='font-weight: 300; margin-bottom: 10px;'>Enviar Boucher a distinct.venta@gmail.com o WSP
                                        986145878</p>
                                    <p style='font-weight: 300; margin-bottom: 10px;'><b>Yape:</b> Celular: 997931997 Titular: Kyc Panda Eirl</p>
                                    <p style='font-weight: 300; margin-bottom: 10px;'><b>Plin:</b> Celular: 997931997 Titular: Katerine Sanchez</p>
                                    <p style='font-weight: 300; margin-bottom: 10px;'><b>BBVA:</b> <br>- Cuenta: 0011-0508-0200334959<br>- CCI:
                                        011-508-000200334959-09<br>- Titular: Kyc Panda Eirl</p>
                                    <p style='font-weight: 300; margin-bottom: 10px;'><b>BCP:</b><br>- Cuenta: 194-70567233-0-31<br>- CCI:
                                        002-19417056723303196<br>- Titular: Kyc Panda Eirl</p>
                                    <p style='font-weight: 300; margin-bottom: 10px;'><b>Interbank:</b><br>- Cuenta: 898-3185098771<br>- CCI:
                                        003-898-013185098771-43<br>- Titular: Katerine Sanchez Jurado</p>
                                    <p style='font-weight: 300; margin-bottom: 10px;'><b>Link de pago (Visa o Mastercard):</b><br>- Escribenos al
                                        WSP 986145878 y te enviamos el link</p>
                                </td>
                            </tr>
                            <tr>
                                <td style='border-top: 2px solid #97847d; padding: 20px 0;'>
                                    <h3 style='text-align: center; font-size: 20px; font-weight: bold; margin-bottom: 20px;'>Detalle de
                                        pedido</h3>
                                    <h4 style='font-size: 18px; font-weight: bold; margin-bottom: 10px;'>Cliente</h4>
                                    <p style='font-weight: 300; margin-bottom: 20px;'>DNI:
                                        " . $numdoccliente . ".
                                        " . $apenomcliente . "
                                    </p>
                                    <h4 style='font-size: 18px; font-weight: bold; margin-bottom: 10px;'>Productos</h4>";
                    foreach ($listadoProdCarrito as $ItemKey => $ItemElement) {
                        $BEProducto = new BEProducto();
                        $BEProducto = $ItemElement;

                        $cantProd = $BEProducto->getCantidad();
                        $precioProd = $BEProducto->getPrecioActual();
                        $ahorro = $BEProducto->getAhorro();

                        $precioProdSubtotal = $precioProd * $cantProd;
                        $ahorroProdSubtotal = $ahorro * $cantProd;

                        $message .= "<table width='100%' cellpadding='0' cellspacing='0' style='margin-bottom: 20px;'>
                                        <tr>
                                            <td width='33%' style='padding: 10px;'>";

                        $rutaPortada = $BEProducto->getRutaFotoPortada();
                        if ($rutaPortada != '') {
                            $rutaPortada = $rutaPortada;
                        } else {
                            $rutaPortada = $prodSinFotoPortada;
                        }

                        $message .= "<img src='" . $rutaPortada . "'
                                                    alt='" . $BEProducto->getEtiquetaPrincipal() . "' style='max-width: 100%;'>
                                            </td>
                                            <td width='33%' style='padding: 10px;'>
                                                <p style='font-weight: bold; font-size: 16px; margin-bottom: 5px;'>
                                                    " . $BEProducto->getNomProd() . "
                                                </p>
                                                <p style='font-weight: 300; font-size: 14px;'>Cantidad:
                                                    " . $BEProducto->getCantidad() . "
                                                </p>
                                            </td>";

                        $message .= "       <td width='33%' style='padding: 10px;'>
                                                <p style='font-weight: bold; font-size: 16px; margin-bottom: 5px;'>
                                                S/. " . $precioProdSubtotal . "";

                        if ($ahorroProdSubtotal > 0) {
                            $message .= "               <span style='display: block; font-size: 12px; font-weight: 300;'>Ahorras S/.
                                                        " . $ahorroProdSubtotal . "
                                                        </span> ";
                        }

                        $message .= "           </p>
                                            </td>
                                        </tr>
                                    </table>
                                    ";
                    }


                    $message .= "</td>
                            </tr>
                            <tr>
                                <td style='border-top: 2px solid #97847d; padding: 20px 0;'>
                                    <h3 style='font-size: 20px; font-weight: bold; margin-bottom: 10px;'>Forma de entrega</h3>
                                    <p style='font-weight: 300; margin-bottom: 5px;'>Destino:
                                        " . $BETipoEntregaGuardado->getNameDpto() . " -
                                        " . $BETipoEntregaGuardado->getNameProv() . " -
                                        " . $BETipoEntregaGuardado->getNameDist() . "
                                    </p>
                                    <p style='font-weight: 300; margin-bottom: 5px;'>Tipo de entrega:
                                        " . $BETipoEntregaGuardado->getNomTipoEntrega() . ".
                                        " . $BETipoEntregaGuardado->getTiempoEntrega() . " 
                                    </p>
                                    ";

                    if ($tipoEntregaGuardado == "C") {

                        $message .= "<p style='font-weight: 300; margin-bottom: 10px;'>Dirección:
                                        " . $direccioncliente . "
                                    </p>";
                    }

                    if ($tipoEntregaGuardado == "R") {

                        $message .= "<p style='font-weight: 300; margin-bottom: 10px;'>Dirección de recojo:Avenida Angamos Este 1559
                                    </p>";
                    }

                    if ($notapedido != "") {

                        $message .= "<p style='font-weight: 300; margin-bottom: 10px;'>Nota de pedido:
                                        " . $notapedido . "
                                    </p>";
                    }



                    $message .= "</td>
                            </tr>
                            <tr>
                                <td style='border-top: 2px solid #97847d; padding: 20px;'>
                                    <table width='100%' cellpadding='0' cellspacing='0'>
                                        <tr>
                                            <td style='font-weight: bold; font-size: 16px;'>Monto a pagar</td>
                                            <td style='font-weight: bold; font-size: 16px; text-align: right;'>S/.
                                                " . $montoTotal . "
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight: 300; font-size: 14px;'>Sub total de productos</td>
                                            <td style='font-weight: 300; font-size: 14px; text-align: right;'>S/.
                                                " . $montoProductos . "
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight: 300; font-size: 14px;'>Envio</td>
                                            <td style='font-weight: 300; font-size: 14px; text-align: right;'>S/.
                                                " . $montoEnvio . " " . $txtEnvioGratis . "
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style='font-weight: 300; font-size: 14px;'>Ahorro Total</td>
                                            <td style='font-weight: 300; font-size: 14px; text-align: right;'>S/.
                                                " . $montoAhorro . "
                                            </td>
                                        </tr>
                                    </table>
                                    <p style='font-size: 12px; font-weight: 300; margin-top: 10px;'>$textoAhorroTotal
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </body>
                    
                    </html>";
                    $headers = "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
                    $headers .= "From: " . EMAIL_FROM . "\r\n";
                    $headers .= "CC: " . EMAIL_CC . ", $emailcliente\r\n";


                    /*
                    echo $message;
                    echo "</br>";
                    echo "</br>";
                    echo "</br>";
                    */

                    // Enviar el correo
                    if (mail($to, $subject, $message, $headers)) {
                        // Mostrar el número de pedido si se envía correctamente
                        //echo "<h2>Pedido $nuevoNumPedido</h2>";
                        ?>
                        <!-- Aquí va el HTML de la tabla y el contenido -->

                        <?php
                    } else {
                        // Manejar errores si el correo no se envía correctamente
                        $error = error_get_last();
                        if ($error !== null && isset($error['message'])) {
                            echo "Error al enviar correo: " . $error['message'];
                        } else {
                            echo "Error desconocido al enviar correo.";
                        }
                    }
                    ?>



                    <div class="max-w-lg m-auto">
                        <div>
                            <h2 class="text-center text-2xl font-bold text-[#97847d] mt-8 mb-4">
                                Pedido <?php echo $nuevoNumPedido; ?>
                            </h2>
                            <p class="text-center text-base  font-light text-[#97847d] mb-4">
                                Te enviamos un email con la informacion del pedido y formas de pago.
                            </p>
                            <div class="flex justify-between">
                                <h2 class="text-center text-lg font-bold text-[#97847d] mt-8 mb-4">
                                    Total a pagar
                                </h2>
                                <p class="text-center text-lg font-bold text-[#97847d] mt-8 mb-4">
                                    S/.<?php echo $montoTotal; ?>
                                    <span class="text-[#97847d] font-light text-sm block">
                                        Ahorras S/.<?php echo $montoAhorro; ?>
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="font-light text-[#97847d] border-t-2 border-[#97847d] mt-4">
                            <h3 class="text-xl font-bold text-[#97847d] mt-8 mb-4 text-center">
                                Formas de pago
                            </h3>


                            <p class="text-base">Enviar Boucher a distinct.venta@gmail.com o WSP 986145878</p>
                            <br>
                            <p class="text-base">Yape: Celular: 997931997 Titular: Kyc Panda Eirl </p>
                            <br>
                            <p class="text-base">Plin: Celular: 997931997 Titular: Katerine Sanchez </p>

                            <br>
                            <p class="text-base">
                                BBVA <br>
                                - Cuenta: 0011-0508-0200334959 <br>
                                - CCI: 011-508-000200334959-09 <br>
                                - Titular: Kyc Panda Eirl <br>
                            </p>

                            <br>
                            <p class="text-base">
                                BCP <br>
                                - Cuenta: 194-70567233-0-31 <br>
                                - CCI: 002-19417056723303196 <br>
                                - Titular: Kyc Panda Eirl <br>
                            </p>

                            <br>
                            <p class="text-base">
                                Interbank <br>
                                - Cuenta: 898-3185098771 <br>
                                - CCI: 003-898-013185098771-43 <br>
                                - Titular: Katerine Sanchez Jurado <br>
                            </p>

                            <br>
                            <p class="text-base">
                                Link de pago (Visa o Mastercard) <br>
                                - Escribenos al WSP 986145878 y te enviamos el link <br>
                            </p>

                        </div>
                        <div class="font-light text-[#97847d] border-t-2 border-[#97847d] mt-4">
                            <h3 class="text-xl font-bold text-[#97847d] mt-8 mb-4 text-center">
                                Detalle de pedido
                            </h3>
                            <h4 class="text-lg font-bold text-[#97847d] mt-8 mb-4">
                                Cliente
                            </h4>
                            <p class="text-base">
                                DNI: <?php echo $numdoccliente; ?>. <?php echo $apenomcliente; ?>
                            </p>

                            <h4 class="text-lg font-bold text-[#97847d] mt-8 mb-4">
                                Productos
                            </h4>
                            <?php foreach ($listadoProdCarrito as $ItemKey => $ItemElement) {
                                $BEProducto = new BEProducto();
                                $BEProducto = $ItemElement;

                                $cantProd = $BEProducto->getCantidad();
                                $precioProd = $BEProducto->getPrecioActual();
                                $ahorro = $BEProducto->getAhorro();

                                $precioProdSubtotal = $precioProd * $cantProd;
                                $ahorroProdSubtotal = $ahorro * $cantProd;

                                ?>
                                <div class="flex gap-4 mt-2">
                                    <div class="w-1/3">
                                        <?php $rutaPortada = $BEProducto->getRutaFotoPortada();
                                        if ($rutaPortada != "") {
                                            $rutaPortada = $rutaPortada;
                                        } else {
                                            $rutaPortada = $prodSinFotoPortada;
                                        }
                                        ?>
                                        <img src="<?php echo $rutaPortada; ?>"
                                            alt="<?php echo $BEProducto->getEtiquetaPrincipal(); ?>" />
                                    </div>
                                    <div class="w-1/2">
                                        <p class="text-[#97847d] font-bold text-base">
                                            <?php echo $BEProducto->getNomProd(); ?>
                                        </p>
                                        <p class="text-[#97847d] font-light text-base">
                                            Cantidad: <?php echo $BEProducto->getCantidad(); ?>
                                        </p>
                                    </div>
                                    <div class="w-1/5">
                                        <p class="text-[#97847d] font-bold text-base">
                                            S/.<?php echo $precioProdSubtotal; ?>
                                            <span class="text-[#97847d] font-light text-sm">
                                                <?php echo $ahorroProdSubtotal > 0 ? 'Ahorras S/ ' . $ahorroProdSubtotal : '' ?>
                                            </span>

                                        </p>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="font-light text-[#97847d] border-t-2 border-[#97847d] mt-4">
                            <h3 class="text-xl font-bold text-[#97847d] mt-8 mb-4">
                                Forma de entrega
                            </h3>
                            <p class="text-base">
                                Destino: <?php echo $BETipoEntregaGuardado->getNameDpto(); ?> -
                                <?php echo $BETipoEntregaGuardado->getNameProv(); ?> -
                                <?php echo $BETipoEntregaGuardado->getNameDist(); ?>
                            </p>



                            <?php
                            if ($tipoEntregaGuardado == "C") {
                                ?>

                                <p class="text-base">
                                    Forma de entrega: <?php echo $BETipoEntregaGuardado->getNomTipoEntrega(); ?> -
                                    <?php echo $BETipoEntregaGuardado->getTiempoEntrega(); ?>
                                </p>

                                <p class="text-base">
                                    Dirección: <?php echo $direccioncliente; ?>
                                </p>

                                <?php
                            }
                            //fin if ($tipoEntregaGuardado == "C") {
                            ?>


                            <?php
                            if ($tipoEntregaGuardado == "R") {
                                ?>

                                <p class="text-base">
                                    Forma de entrega: <?php echo $BETipoEntregaGuardado->getNomTipoEntrega(); ?> -
                                    <?php echo $BETipoEntregaGuardado->getTiempoEntrega(); ?>
                                </p>

                                <p class="text-base">
                                    Dirección de recojo: Avenida Angamos Este 1559
                                </p>

                                <?php
                            }
                            //fin if ($tipoEntregaGuardado == "R") {
                            ?>


                            <?php
                            if ($notapedido != "") {
                                ?>

                                <p class="text-base">
                                    Nota de pedido: <?php echo $notapedido; ?>
                                </p>

                                <?php
                            }
                            //fin if if ($notapedido != "") {
                            ?>


                        </div>
                        <div class="font-normal text-[#97847d] border-t-2 border-[#97847d] mt-4">
                            <div class="flex justify-between">
                                <h4 class="text-lg font-bold text-[#97847d] mt-8 mb-4">
                                    Monto a pagar
                                </h4>
                                <p class="text-lg font-bold text-[#97847d] mt-8 mb-4">
                                    S/.<?php echo $montoTotal; ?>
                                </p>
                            </div>
                            <div class="flex justify-between">
                                <h4 class="text-base">
                                    Sub total de productos
                                </h4>
                                <p class="text-base">
                                    S/.<?php echo $montoProductos; ?>
                                </p>

                            </div>
                            <div class="flex justify-between">
                                <h4 class="text-base">
                                    Envio
                                </h4>
                                <p class="text-base">
                                    S/.<?php echo $montoEnvio; ?>         <?php echo $txtEnvioGratis; ?>
                                </p>
                            </div>
                            <div class="flex justify-between">
                                <h4 class="text-base">
                                    Ahorro Total
                                </h4>
                                <p class="text-base">
                                    S/.<?php echo $montoAhorro; ?>
                                </p>
                            </div>
                            <p class="text-[#97847d] font-light text-sm">
                                <?php echo $textoAhorroTotal; ?>
                            </p>
                        </div>
                    </div>

                    <?php
                }
                //Solo falta generar el log
                $DAPedidoLog = new DAPedidoLog();
                $DAPedidoLog->Log($nuevoNumPedido, 1, "1. Generar Pedido SIA", $codeUsuario);
            }
            //fin if($pedidoRegistrado == 1)
            


            //Eliminar Session
            

            unset($_SESSION['listadoProdCarrito']);
            unset($_SESSION['clientecarritoobtenido']);
            unset($_SESSION['pedidodatos']);
            unset($_SESSION['ubigeoElegido']);
            unset($_SESSION['cuponCarrito']);



            ?>


        </div>
    </div>
</section>


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


</body>

</html>