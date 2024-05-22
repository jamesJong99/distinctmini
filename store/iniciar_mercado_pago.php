<?php
session_start();

// integrar mercado pago
require_once 'vendor/autoload.php';


require_once ("ClassesStore/BECliente.php");
require_once 'ClassesStore/configuracionBD.php';

MercadoPago\SDK::setAccessToken(ACCESS_TOKEN_MP);

$preference = new MercadoPago\Preference();
$preference->auto_return = 'approved';


// definir urls de retorno

$url_thanks = BASE_URL_STORE . "thanks.php?" .
  "tipodocciente=" . $_POST['tipodocciente'] . '&' .
  "numdoccliente=" . $_POST['numdoccliente'] . '&' .
  "apenomcliente=" . $_POST['apenomcliente'] . '&' .
  "celularcliente=" . $_POST['celularcliente'] . '&' .
  "emailcliente=" . $_POST['emailcliente'] . '&' .
  "direccion=" . $_POST['direccion'] . '&' .
  "referencia=" . $_POST['referencia'] . '&' .
  "nota=" . $_POST['nota'] . '&' .
  "tipo_pago=" . $_POST['tipo_pago'];

$url_error = BASE_URL_STORE . "carritocompra.php";


$preference->back_urls = array(
  "success" => $url_thanks,
  "failure" => $url_error,
  "pending" => $url_thanks
);



// excluir metodos de pago
$preference->payment_methods = [
  "excluded_payment_types" => [
    ["id" => "bank_transfer"],
    ["id" => "ticket"]
  ],
  "installments" => 1,
  "default_installments" => 1
];



// pasar datos del cliente
$payer = new MercadoPago\Payer();
$payer->name = $_POST['apenomcliente'];
$payer->email = $_POST['emailcliente'];

$identification_type = '';
if ($_POST['tipodocciente'] == 1)
  $identification_type = 'DNI';
elseif ($_POST['tipodocciente'] == 2)
  $identification_type = 'Otro';
else
  $identification_type = 'C.E';

$payer->identification = [
  "type" => $identification_type,
  "number" => $_POST['numdoccliente']
];

$preference->payer = $payer;

$montoTotal = 0;

// pasar productos a mercado pago
$items = [];
$i = 0;

foreach ($_POST['productosArray'] as $prod) {
  $items[$i] = new MercadoPago\Item();
  $items[$i]->id = $prod['codigo'];
  $items[$i]->title = $prod['nombre'];
  $items[$i]->quantity = $prod['cantidad'];
  $items[$i]->unit_price = $prod['precio'];
  $montoTotal += $prod['precio'];
  ++$i;
}



// incluir costo de envio si existe
if ($_POST['montoEnvio'] != 0) {
  $items[$i] = new MercadoPago\Item();
  $items[$i]->title = "Envío";
  $items[$i]->quantity = 1;
  $items[$i]->unit_price = $_POST['montoEnvio'];
  $montoTotal += $_POST['montoEnvio'];
  ++$i;
}



// aplicar cupon de descuento si existe
if ($_POST['montoCupon'] != 0) {
  $items[$i] = new MercadoPago\Item();
  $items[$i]->title = "Cupón de descuento";
  $items[$i]->quantity = 1;
  $items[$i]->unit_price = ($_POST['montoCupon'] * -1);
  $montoTotal += $items[$i]->unit_price;
}



$preference->items = $items;
/********************************************facebookAds setting start****************************************
 * 
 */
require_once ("ClassesStore/FaceAdsInfo.php");
require_once ("ClassesStore/configuracionBD.php");

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

if (isset($_SESSION['clientecarritoobtenido'])) {
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
  $contents = [];
  $content_name_arr = [];
  foreach ($_POST['productosArray'] as $key => $product) {
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
    ->setEventName('AddPaymentInfo')
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
$preference->save();


// retornar link de pago
$link = (ENVIRONMENT == 'PROD') ? $preference->init_point : $preference->sandbox_init_point;

echo json_encode([
  'status' => 'OK',
  'link' => $link
]);

?>