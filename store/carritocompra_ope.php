<?php
// Mostrar todos los errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (isset($_POST['operacion'])) {
    $operacion = $_POST['operacion'];
} else {
    echo "Error: No se ha definido la operación.";
    exit;
}

?>
<?php
session_start();

// facebookAds library
use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;

require_once ("ClassesStore/configuracionBD.php");
require_once ("ClassesStore/BEProducto.php");
require_once ("ClassesStore/BECuponImagen.php");
require_once ("ClassesStore/DACuponImagen.php");
require_once ("ClassesStore/BEPedidoItem.php");
require_once ("ClassesStore/BETipoEntrega.php");
require_once ("ClassesStore/DATipoEntrega.php");
require_once ("ClassesStore/BEPedido.php");
require_once ("./ClassesStore/Util.php");


$operacion = 0;
$cantCorrelativo = 0;

if (isset($_POST['operacion'])) {
    $operacion = $_POST['operacion'];
}



//Agregar producto. Viene desde producto.php
if ($operacion == 3) {
    //PASO 1 obtener datos de Post de elemento añadido si existe. Proceso viene adm_visitarproducto.php
    $anadirElemento = 0;

    $codprod = "";
    $canitemvariante = 0;
    $nomprod = "";
    $nomprodSegundo = "";
    $precioprodActual = 0;
    $cantidad = 0;
    $rutafotoportada = "";

    $preciobase = 0;
    $ahorro = 0;

    if (isset($_POST['canitemvariante'])) {
        $canitemvariante = $_POST['canitemvariante'];
    }

    if (isset($_POST['codprod'])) {
        $codprod = $_POST['codprod'];
    }

    if (isset($_POST['nomprod'])) {
        $nomprod = $_POST['nomprod'];
    }

    if (isset($_POST['nomprodSegundo'])) {
        $nomprodSegundo = $_POST['nomprodSegundo'];
    }

    if (isset($_POST['precioprodActual'])) {
        $precioprodActual = $_POST['precioprodActual'];
    }

    if (isset($_POST['cantidad'])) {
        $cantidad = $_POST['cantidad'];
    }

    if (isset($_POST['rutafotoportada'])) {
        $rutafotoportada = $_POST['rutafotoportada'];
    }

    if (isset($_POST['precioprodBase'])) {
        $preciobase = $_POST['precioprodBase'];
    }

    if (isset($_POST['ahorroprod'])) {
        $ahorro = $_POST['ahorroprod'];
    }
    // --------------------------------FacebookAds start-------------------------------------------------
    require 'vendor/autoload.php';
    require_once ("ClassesStore/FaceAdsInfo.php");

    $RECENT_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    $api = Api::init(null, null, ACCESS_TOKEN);
    $api->setLogger(new CurlLogger());

    // making contents

    if (isset($_SESSION['clientecarritoobtenido'])) {
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
        $contents = [];
        $content_name_arr = [];
        foreach ($productosArray as $key => $product) {
            $content_name_arr[] = $product['nombre'];
            $contents[] = (new Content())
                ->setProductId($product['codigo'])
                ->setContents($contents)
                ->setQuantity($product['cantidad'])
                ->setTitle($product['nombre'])
                ->setDeliveryCategory(DeliveryCategory::HOME_DELIVERY);
        }
        $custom_data = (new CustomData())
            ->setNumItems($cantidad)
            ->setCurrency('USD')
            ->setContentName($nomprod)
            ->setValue($ahorro);

        $event = (new Event())
            ->setEventTime(time())
            ->setEventName('AddToCart')
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
        $custom_data->setOrderId($BECliente->getCodCliente());
        $event->setCustomData($custom_data);

        $events = array();
        array_push($events, $event);

        $request = (new EventRequest(PIXEL_ID))
            ->setEvents($events);
        $response = $request->execute();
    }


    // --------------------------------FacebookAds end-------------------------------------------------


    //print_r($_POST);

    //echo "preciobase $preciobase";

    if ($codprod != "" and $canitemvariante != 0 and $precioprodActual != 0 and $cantidad != 0 and $preciobase != 0) {
        $anadirElemento = 1;
    }

    //echo "anadirElemento ($anadirElemento)";

    $BEProductoNuevo = new BEProducto();
    $cantCodItemProdNuevo = 0;
    //Estructurar arreglo en POST ha venido demasiado información y hay que estructurarlo en objetos
    if ($anadirElemento == 1) {


        $BEProductoNuevo->setCodProd($codprod);
        $BEProductoNuevo->setNomProd($nomprod);
        $BEProductoNuevo->setSegundoNombre($nomprodSegundo);
        $BEProductoNuevo->setPrecioActual($precioprodActual);

        $BEProductoNuevo->setPrecio($preciobase);
        $BEProductoNuevo->setAhorro($ahorro);

        $BEProductoNuevo->setCantidad($cantidad);
        $BEProductoNuevo->setRutaFotoPortada($rutafotoportada);

        $stockMinimoElección = -1;

        for ($i = 1; $i <= $canitemvariante; $i++) {

            $BEPedido_Item = new BEPedido_Item();

            //echo $i;
            $codItemVariante = "";
            $nomGrupoVariante = "";
            $tieneVariante = "";
            $codItemElegido = "";


            $llave = "CodItemVariante" . $i;
            if (isset($_POST[$llave])) {
                $codItemVariante = $_POST[$llave];
            }

            $llave = "NomGrupoVariante" . $i;
            if (isset($_POST[$llave])) {
                $nomGrupoVariante = $_POST[$llave];
            }

            $llave = "setTieneVariante" . $i;
            if (isset($_POST[$llave])) {
                $tieneVariante = $_POST[$llave];
            }

            $llave = "CodItemElegido" . $i;
            if (isset($_POST[$llave])) {
                $codItemElegido = $_POST[$llave];
            }





            $BEPedido_Item->setCodProd($codprod);
            $BEPedido_Item->setCodItemVariante($codItemVariante);
            $BEPedido_Item->setNomGrupoVariante($nomGrupoVariante);
            $BEPedido_Item->setTieneVariante($tieneVariante);
            $BEPedido_Item->setCodItemElegido($codItemElegido);

            $coditem = "";
            $nomitem = "";
            $namevariante = "";
            $stock = 0;

            $llave = "CodItem" . $i . "_" . $codItemElegido;
            if (isset($_POST[$llave])) {
                $coditem = $_POST[$llave];
            }

            $llave = "NomItem" . $i . "_" . $codItemElegido;
            if (isset($_POST[$llave])) {
                $nomitem = $_POST[$llave];
            }

            $llave = "nameVariante" . $i . "_" . $codItemElegido;
            if (isset($_POST[$llave])) {
                $namevariante = $_POST[$llave];
            }

            $llave = "StockItem" . $i . "_" . $codItemElegido;
            if (isset($_POST[$llave])) {
                $stock = $_POST[$llave];
            }

            $BEPedido_Item->setCodItem($coditem);
            $BEPedido_Item->setNomItem($nomitem);
            $BEPedido_Item->setNameVariante($namevariante);
            $BEPedido_Item->setStockItem($stock);

            //print_r($BEPedido_Item);

            $BEProductoNuevo->listadoItem[$codItemVariante] = $BEPedido_Item;
            $cantCodItemProdNuevo = $cantCodItemProdNuevo + 1;
            //echo $codItemVariante;

            if ($stockMinimoElección == -1) {
                $stockMinimoElección = $stock;
            } else {
                $stockMinimoElección = min($stockMinimoElección, $stock);
            }
        }
        //fin for ($i = 1; $i <= $canitemvariante; $i++) {

        //echo "stock ($stockMinimoElección)";

        $BEProductoNuevo->setStockProd($stockMinimoElección);

        //print_r($BEProductoNuevo);


    }
    // fin if($anadirElemento == 1)





    //PASO 2 Obtener listado de Session 
    $listadoProdCarrito = array();

    if (isset($_SESSION['listadoProdCarrito'])) {
        //echo " </br> SESSION RECUPERADA ";

        $listadoProdCarrito = unserialize((base64_decode($_SESSION['listadoProdCarrito'])));
        //unserialize( $_SESSION['listadoProdCarrito'] , 5 );

        //print_r($listadoProdCarrito);
        //echo " </br> SESSION RECUPERADA ";
    }


    $cantElementosActuales = count($listadoProdCarrito);





    //PASO 3 En caso exista un producto nuevo validar si se incluye o no.
    //Ojo no puede ser por CodProd considerar que puede tener variantes
    //Primera coincidencia CodProd, luego debe coincidir todos los CodItem
    $seRepiteProd = 0;
    $debeAgregar = 0;


    //echo "</br> cantElementosActuales ($cantElementosActuales) cantCodItemProdNuevo  ($cantCodItemProdNuevo) ";

    if ($anadirElemento == 1) {
        //En caso el array session no tenga elementos. Inicio con el código
        if ($cantElementosActuales == 0) {
            $debeAgregar = 1;
            //echo "</br> ARRAY VACIO. Marcado por agregar de Inicio";
        }

        if ($cantElementosActuales != 0) {
            //echo "</br> REVISAR";

            //Primer Filtro coincide CodProd
            foreach ($listadoProdCarrito as $corell => $ProdElemento) {
                $BEProductoComparar = new BEProducto();
                $BEProductoComparar = $ProdElemento;

                $codProd_Nuevo = $BEProductoNuevo->getCodProd();
                $codProd_Compara = $BEProductoComparar->getCodProd();

                //print_r($BEProductoComparar);

                //Primer Filtro coincide CodProd
                if ($codProd_Nuevo == $codProd_Compara) {
                    $seRepiteProd = 1;
                }
                //fin if($BEProductoNuevo->getCodProd() == $BEProductoComparar->getCodProd())

            }
            //fin foreach ($listaBEProductos as $ProdKey => $ProdElemento)

            //En caso no coincide ningún CodProd
            if ($seRepiteProd == 0) {
                $debeAgregar = 1;
                //echo "</br> ARRAY CON ELEMENTOS. Marcado por agregar porque CodProd no coincide";
            }

            //Segundo filtro solo si se repiteproducto
            if ($seRepiteProd == 1) {

                foreach ($listadoProdCarrito as $corell => $ProdElemento) {
                    $BEProductoComparar = new BEProducto();
                    $BEProductoComparar = $ProdElemento;

                    //Encontrar producto que coincide 
                    if ($BEProductoNuevo->getCodProd() == $BEProductoComparar->getCodProd()) {
                        $cantCoincidenciasItem = 0;

                        //Se revisa la coincidencias con item.
                        foreach ($BEProductoNuevo->listadoItem as $IdCodeItemVarNuevo => $ItemNuevo) {
                            foreach ($BEProductoComparar->listadoItem as $IdCodeItemVarComparar => $ItemComparar) {
                                $BEItem_Nuevo = new BEPedido_Item();
                                $BEItem_Comparar = new BEPedido_Item();
                                $BEItem_Nuevo = $ItemNuevo;
                                $BEItem_Comparar = $ItemComparar;

                                //echo "</br> Comparando CodItemNuevo (".$BEItem_Nuevo->getCodItem() .") == CodItemNuevoComparar (".$BEItem_Comparar->getCodItem() .") ";

                                if ($BEItem_Nuevo->getCodItem() == $BEItem_Comparar->getCodItem()) {
                                    $cantCoincidenciasItem = $cantCoincidenciasItem + 1;
                                }
                            }
                        }

                        //echo "</br></br></br> cantCodItemProdNuevo ($cantCodItemProdNuevo) == cantCoincidenciasItem ($cantCoincidenciasItem) ";

                        if ($cantCodItemProdNuevo != $cantCoincidenciasItem) {
                            $debeAgregar = 1;
                            //echo "</br> ARRAY CON ELEMENTOS. Marcado por agregar  CodProd existe. Variante Distinta";
                        }
                    }
                    //fin if($BEProductoNuevo->getCodProd() == $BEProductoComparar->getCodProd())

                }
                //fin foreach ($listaBEProductos as $ProdKey => $ProdElemento)


            }
        }
        //fin if($cantElementosActuales !=0)



    }
    //fin if($anadirElemento == 1)


    if ($debeAgregar == 1) {
        $nuevoCorrelativo = count($listadoProdCarrito);
        $nuevoCorrelativo = $nuevoCorrelativo + 1;
        $listadoProdCarrito[$nuevoCorrelativo] = $BEProductoNuevo;

        $reg_serlizer = base64_encode(serialize($listadoProdCarrito));

        $_SESSION['listadoProdCarrito'] = $reg_serlizer;
        //echo "</br> AGREGANDO ";
    }

    //print_r($listadoProdCarrito);

    header("Location:carritocompra.php");
}
//fin operacion 3





//Mostrar información de producto. Se obtiene por javascript desde carritocompra.php
if ($operacion == 4) {
    $listadoProdCarrito = array();

    if (isset($_SESSION['listadoProdCarrito'])) {
        //echo " </br> SESSION RECUPERADA ";

        $listadoProdCarrito = unserialize((base64_decode($_SESSION['listadoProdCarrito'])));
        //unserialize( $_SESSION['listadoProdCarrito'] , 5 );

        //print_r($listadoProdCarrito);
        //echo " </br> SESSION RECUPERADA ";
    }


    $cantProductosCarrito = count($listadoProdCarrito);


    if ($cantProductosCarrito == 0) { ?>

        <p class="text-red-500 text-center w-full">No se tiene productos en el carrito</p>

    <?php }

    if ($cantProductosCarrito != 0) {
        echo '<div class="tablaSinCambioColor">';

        foreach ($listadoProdCarrito as $correlativo => $ProdElemento) {
            $BEProductoMostrar = new BEProducto();
            $BEProductoMostrar = $ProdElemento;

            $cantProd = $BEProductoMostrar->getCantidad();
            $nomProd = $BEProductoMostrar->getNomProd();
            $precioProd = $BEProductoMostrar->getPrecioActual();
            $rutaFotoPortada = $BEProductoMostrar->getRutaFotoPortada();

            $stockMaximo = $BEProductoMostrar->getStockProd();

            $precioBase = $BEProductoMostrar->getPrecio();
            $ahorro = $BEProductoMostrar->getAhorro();

            $precioProdSubtotal = $precioProd * $cantProd;
            $ahorroProdSubtotal = $ahorro * $cantProd;

            $detalleVariante = "";

            $cantItemVariante = count($BEProductoMostrar->listadoItem);

            if ($cantItemVariante != 0) {
                foreach ($BEProductoMostrar->listadoItem as $IdCodeItemVarNuevo => $ItemNuevo) {
                    $BEPedido_Item = new BEPedido_Item();
                    $BEPedido_Item = $ItemNuevo;
                    if ($BEPedido_Item->getNameVariante() != "") {
                        $detalleVariante = $detalleVariante . " " . $BEPedido_Item->getNomGrupoVariante() . " - " . $BEPedido_Item->getNameVariante() . ". ";
                    }
                }
            }

            echo '<div class="pb-4 pt-2 border-b-2 border-[#8F6B60] p-1 flex gap-3">';
            echo "<div class='w-24 h-24'>";
            echo "<a class='fancybox' rel='group' href='$rutaFotoPortada'><img class='w-24 h-24' src='$rutaFotoPortada' alt='' /></a>";
            echo "</div>"; ?>
            <div class="w-full p-1  flex flex-col justify-between">

                <div class="flex justify-between items-center">
                    <p class="text-base font-medium text-[#8F6B60]"><?php echo $nomProd ?>             <?php echo $detalleVariante ?></p>
                    <a onclick="eliminarProducto(<?php echo $correlativo ?>);event.preventDefault();"
                        class="text-[#8F6B60] hover:shadow-2xl hover:cursor-pointer hover:text-white hover:bg-[#8F6B60] rounded-full p-1 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="currentColor" class="bi bi-x"
                            viewBox="0 0 16 16">
                            <path
                                d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                        </svg>
                    </a>
                </div>
                <div class="flex justify-between">
                    <div>
                        <div class="quantity">
                            <input class="text-[#8F6B60]" type="number" onchange="cambioCantidadProd(<?php echo $correlativo ?>)"
                                id="nuevaCant<?php echo $correlativo ?>" name="nuevaCant<?php echo $correlativo ?>"
                                value="<?php echo $cantProd ?>" min="0" max="<?php echo $stockMaximo ?>" />

                        </div>

                        <input type="hidden" id="stockMaximo<?php echo $correlativo ?>" name="stockMaximo<?php echo $correlativo ?>"
                            value="<?php echo $stockMaximo ?>" />
                        <input type="hidden" id="codeProde<?php echo $correlativo ?>" name="codeProde<?php echo $correlativo ?>"
                            value="<?php echo $BEProductoMostrar->getCodProd() ?>" />
                        <input type="hidden" id="preProd<?php echo $correlativo ?>" name="preProd<?php echo $correlativo ?>"
                            value="<?php echo $precioProd; ?>" />
                        <input type="hidden" id="ahoProd<?php echo $correlativo ?>" name="ahoProd<?php echo $correlativo ?>"
                            value="<?php echo $ahorro; ?>" />

                    </div>
                    <div class=" h-fit mt-auto text-right">
                        <p class="text-[#8F6B60]">

                            <span id="preProdHtml<?php echo $correlativo ?>" class="text-lg font-bold">S/
                                <?php echo $precioProdSubtotal ?></span>
                            <span id="ahoProdHtml<?php echo $correlativo ?>" class="block text-xs">
                                <?php echo $ahorroProdSubtotal > 0 ? 'Ahorras S/ ' . $ahorroProdSubtotal : '' ?>
                            </span>
                        </p>
                    </div>
                </div>
            </div>
            <?php
            echo '</div>';
        }

        echo '</div>';
        ?>
        <style>
            .quantity {
                position: relative;
            }

            input[type=number]::-webkit-inner-spin-button,
            input[type=number]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            input[type=number] {
                -moz-appearance: textfield;
            }

            .quantity input {
                width: 68px;
                height: 42px;
                line-height: 1.65;
                float: left;
                display: block;
                padding: 0;
                margin: 0;
                padding-left: 20px;
                border: 1px solid #8F6B60;
            }

            .quantity input:focus {
                outline: 0;
            }

            .quantity-nav {
                float: left;
                position: relative;
                height: 42px;
            }

            .quantity-button {
                position: relative;
                cursor: pointer;
                border-left: 1px solid #8F6B60;
                width: 20px;
                text-align: center;
                color: #8F6B60;
                font-size: 13px;
                font-family: "Trebuchet MS", Helvetica, sans-serif !important;
                line-height: 1.7;
                -webkit-transform: translateX(-100%);
                transform: translateX(-100%);
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                -o-user-select: none;
                user-select: none;
            }

            .quantity-button.quantity-up {
                position: absolute;
                height: 50%;
                top: 0;
                border-bottom: 1px solid #8F6B60;
                display: flex;
            }

            .quantity-button.quantity-up svg {
                margin: auto;
            }

            .quantity-button.quantity-down svg {
                margin: auto;
            }

            .quantity-button.quantity-down {
                position: absolute;
                bottom: -1px;
                height: 50%;
                display: flex;
            }
        </style>
        <script>
            var svgplus = '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-chevron-up" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M7.646 4.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1-.708.708L8 5.707l-5.646 5.647a.5.5 0 0 1-.708-.708z"/></svg>'
            var svgminus = '<svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" fill="currentColor" class="bi bi-chevron-down" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708"/></svg>'
            jQuery('<div class="quantity-nav"><div class="quantity-button quantity-up">' + svgplus + '</div><div class="quantity-button quantity-down">' + svgminus + '</div></div>').insertAfter('.quantity input');
            jQuery('.quantity').each(function () {
                var spinner = jQuery(this),
                    input = spinner.find('input[type="number"]'),
                    btnUp = spinner.find('.quantity-up'),
                    btnDown = spinner.find('.quantity-down'),
                    min = input.attr('min'),
                    max = input.attr('max');

                btnUp.click(function () {
                    var oldValue = parseFloat(input.val());
                    if (oldValue >= max) {
                        var newVal = oldValue;
                    } else {
                        var newVal = oldValue + 1;
                    }
                    spinner.find("input").val(newVal);
                    spinner.find("input").trigger("change");
                });

                btnDown.click(function () {
                    var oldValue = parseFloat(input.val());
                    if (oldValue <= min) {
                        var newVal = oldValue;
                    } else {
                        var newVal = oldValue - 1;
                    }
                    spinner.find("input").val(newVal);
                    spinner.find("input").trigger("change");
                });

            });
        </script>
        <?php
    }
}
//fin if($operacion ==4)




//Cambio Cantidad Prod actualizar en session . Se obtiene por javascript desde carritocompra.php
if ($operacion == 5) {
    $codeProde = "";
    $nuevaCantidad = -1;
    $camposNecesarios = 0;
    $actualizoSession = 0;

    if ($_POST['codeProde']) {
        $codeProde = $_POST['codeProde'];
    }

    if ($_POST['nuevaCantidad']) {
        $nuevaCantidad = $_POST['nuevaCantidad'];
    }

    //echo "CodeProde $codeProde nuevaCantidad $nuevaCantidad";


    $listadoProdCarrito = array();

    if (isset($_SESSION['listadoProdCarrito'])) {
        $listadoProdCarrito = unserialize((base64_decode($_SESSION['listadoProdCarrito'])));
    }

    if ($codeProde != "" and $nuevaCantidad != -1 and count($listadoProdCarrito) > 0) {
        if (is_numeric($nuevaCantidad)) {
            if (ctype_digit($nuevaCantidad)) {
                $camposNecesarios = 1;
            }
        }
    }

    //echo "camposNecesarios $camposNecesarios";

    $cambioArreglo = 0;

    if ($camposNecesarios == 1) {
        foreach ($listadoProdCarrito as $corell => $ProdElemento) {
            $BEProductoComparar = new BEProducto();
            $BEProductoComparar = $ProdElemento;

            if ($BEProductoComparar->getCodProd() == $codeProde) {
                $BEProductoComparar->setCantidad($nuevaCantidad);
                $listadoProdCarrito[$corell] = $BEProductoComparar;
                $cambioArreglo = 1;
                //echo " Cambiado ";
            }
        }
        //fin foreach ($listadoProdCarrito as $corell => $ProdElemento)
    }
    //fin if($camposNecesarios ==1)

    //unset($_SESSION['listadoProdCarrito']);
    //print_r($listadoProdCarrito);

    if ($cambioArreglo == 1) {
        $reg_serlizer = base64_encode(serialize($listadoProdCarrito));
        $_SESSION['listadoProdCarrito'] = $reg_serlizer;
        $actualizoSession = 1;

        //2023-07-11. Eliminar el cupón para incluirlo de nuevo y tenga que aplicar los filtros de monto
        //unset($_SESSION['cuponCarrito']);
    }

    echo $actualizoSession;
}
//fin if($operacion ==5)



//Eliminar un correlativo
//Significar generar un nuevo arreglo sin considerar a ese correlativo
if ($operacion == 2) {
    $idEliminar = -1;

    if (isset($_POST['numCorrelDelete'])) {
        $idEliminar = $_POST['numCorrelDelete'];
    }


    $listadoProdCarrito = array();
    $listadoProdCarritoNEW = array();
    $cambioArreglo = 0;

    if (isset($_SESSION['listadoProdCarrito'])) {
        //echo "</br>Recupero session";
        $listadoProdCarrito = unserialize((base64_decode($_SESSION['listadoProdCarrito'])));
    }

    $nuevoCorrelativo = 0;

    foreach ($listadoProdCarrito as $corell => $ProdElemento) {
        $BEProductoComparar = new BEProducto();
        $BEProductoComparar = $ProdElemento;

        if ($corell != $idEliminar) {
            $nuevoCorrelativo = $nuevoCorrelativo + 1;
            $listadoProdCarritoNEW[$nuevoCorrelativo] = $BEProductoComparar;
        }
    }

    //print_r($listadoProdCarritoNEW);

    $reg_serlizer = base64_encode(serialize($listadoProdCarritoNEW));
    $_SESSION['listadoProdCarrito'] = $reg_serlizer;

    //2023-07-11. Eliminar el cupón para incluirlo de nuevo y tenga que aplicar los filtros de monto
    //unset($_SESSION['cuponCarrito']);

}
//fin operacion 2




//Calcular 
if ($operacion == 6) {

    //echo "ACANGA";

    //Pasos 1 Calcular el monto del carrito

    //Obtener monto de Productos
    $montoProductos = 0;
    $montoDctoCupon = 0;
    $cantProductosCarrito = 0;

    $montoAhorroProductos = 0;
    $montoAhorroEnvio = 0;
    $montoAhorroTotal = 0;

    if (isset($_SESSION['listadoProdCarrito'])) {
        //echo " </br> SESSION RECUPERADA ";
        $listadoProdCarrito = unserialize((base64_decode($_SESSION['listadoProdCarrito'])));
        $cantProductosCarrito = count($listadoProdCarrito);
    }

    //print_r($listadoProdCarrito);

    if ($cantProductosCarrito > 0) {
        foreach ($listadoProdCarrito as $correlativo => $ProdElemento) {
            $BEProductoMostrar = new BEProducto();
            $BEProductoMostrar = $ProdElemento;

            $cantProd = $BEProductoMostrar->getCantidad();
            $precioProd = $BEProductoMostrar->getPrecioActual();
            $montoAhorroProductos = $montoAhorroProductos + ($cantProd * $BEProductoMostrar->getAhorro());

            $montoProductos = $montoProductos + ($cantProd * $precioProd);
        }
    }

    //echo " Acanga Monto montoProductos ($montoProductos ) ";

    if ($montoProductos != 0) {
        //echo " Acanga Monto montoProductos ($montoProductos ) ";

        //Paso 2 Calcular monto de cupón si existe

        //Recuperar Cupon Si Existe
        //cuponBuscadoAplica
        $cuponBuscadoAplica = 0;
        $cantItemAplicaDcto = 0;
        $BECuponEncontrado = new BECuponImagen();
        $cuponCarritoEncontrado = "";
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
        //fin if(isset($_SESSION['cuponCarrito'])) {

        //echo " Acanga antes cupón aplica ";

        if ($cuponBuscadoAplica == 1) {
            //echo " NecesitaCumplirCond (".$BECuponBuscado->getCuponNecesitaCumplirCond().") ";

            $valorDctoCupon = $BECuponEncontrado->getCantDcto();

            //NECESITA CUMPLIR CONDICIONES = NO. SE CALCULA DIRECTAMENTE EL MONTO DE DCTO
            if ($BECuponEncontrado->getCuponNecesitaCumplirCond() == "N") {
                //Tipo de aplicación 1 es todos los items.
                if ($BECuponEncontrado->getTipoAplicacion() == 1) {
                    //Tipo de dcto en porcentaje
                    if ($BECuponEncontrado->getTipoDcto() == 1) {
                        $montoDctoCupon = round($montoProductos * $BECuponEncontrado->getCantDcto(), 2);
                        $cuponBuscadoGeneraDcto = 1;
                    }

                    //Tipo de dcto en monto
                    if ($BECuponEncontrado->getTipoDcto() == 2) {
                        $montoDctoCupon = $BECuponEncontrado->getCantDcto();
                        $cuponBuscadoGeneraDcto = 1;
                    }
                }
            }
            //fin if( $BECuponBuscado->getCuponNecesitaCumplirCond() =="N")

            //NECESITA CUMPLIR CONDICIONES = SI.
            if ($BECuponEncontrado->getCuponNecesitaCumplirCond() == "S") {
                //Tipo de aplicación 1 es todos los items. 
                //Por el momento tenemos tipo de Aplicación 1 con tipo Dcto 1. Pero se considera regla con tipo Dcto 2 
                if ($BECuponEncontrado->getTipoAplicacion() == 1) {
                    //Tipo de dcto en porcentaje
                    if ($BECuponEncontrado->getTipoDcto() == 1) {
                        $montoDctoCupon = round($montoProductos * $BECuponEncontrado->getCantDcto(), 2);
                        $cuponBuscadoGeneraDcto = 1;
                    }

                    //Tipo de dcto en monto
                    if ($BECuponEncontrado->getTipoDcto() == 2) {
                        $montoDctoCupon = $BECuponEncontrado->getCantDcto();
                        $cuponBuscadoGeneraDcto = 1;
                    }
                }
                //fin if( $BECuponBuscado->getTipoAplicacion() ==1)


                //Tipo de aplicación 3 es a descuento a un Item especifico 
                //Por el momento tenemos tipo de Aplicación 3 con tipo Dcto 2.  
                //Tipo de aplicación 2 es a descuento a un ItemVariante especifico 
                //Por el momento tenemos tipo de Aplicación 2 con tipo Dcto 2.  
                if ($BECuponEncontrado->getTipoAplicacion() == 3 or $BECuponEncontrado->getTipoAplicacion() == 2) {

                    //Tipo de dcto en monto
                    if ($BECuponEncontrado->getTipoDcto() == 2) {

                        //Necesito recorrer el arreglo para identificar cuantos item cumplen la condición
                        if (count($listadoProdCarrito) > 0) {
                            foreach ($listadoProdCarrito as $correlativo => $ProdElemento) {
                                $BEProductoMostrar = new BEProducto();
                                $BEProductoMostrar = $ProdElemento;

                                $cantItem = $BEProductoMostrar->getCantidad();

                                foreach ($BEProductoMostrar->listadoItem as $IdCodeItemVarNuevo => $ItemNuevo) {
                                    $BEPedido_Item = new BEPedido_Item();
                                    $BEPedido_Item = $ItemNuevo;

                                    //Para TipoAplicacion3 la validacion es por CodItem
                                    if ($BECuponEncontrado->getTipoAplicacion() == 3 and $BEPedido_Item->getCodItem() == $BECuponEncontrado->getValorAplicacion()) {
                                        $cantItemAplicaDcto = $cantItemAplicaDcto + $cantItem;
                                    }

                                    //Para TipoAplicacion2 la validacion es por CodItemVariante
                                    if ($BECuponEncontrado->getTipoAplicacion() == 2 and $BEPedido_Item->getCodItemVariante() == $BECuponEncontrado->getValorAplicacion()) {
                                        $cantItemAplicaDcto = $cantItemAplicaDcto + $cantItem;
                                    }
                                }
                            }
                        }
                    }
                    //fin if( $BECuponBuscado->getTipoDcto() ==2)

                    $montoDctoCupon = $cantItemAplicaDcto * $BECuponEncontrado->getCantDcto();
                }
                //fin if( $BECuponBuscado->getTipoAplicacion() ==1)




            }
            //fin if( $BECuponBuscado->getCuponNecesitaCumplirCond() =="N")


        }
        //fin if($cuponBuscadoAplica == 1)

        $costoEntrega = 0;
        $tieneUbigeo = 0;
        //echo " Acanga Buscando session ";

        //print_r($_SESSION['ubigeoElegido']);
        //PASO 3 Calcular monto de entrega
        if (isset($_SESSION['ubigeoElegido'])) {
            $tieneUbigeo = 1;

            //echo " </br> SESSION RECUPERADA ";
            $BETipoEntrega = new BETipoEntrega();
            $BETipoEntrega = unserialize((base64_decode($_SESSION['ubigeoElegido'])));

            $DATipoEntrega = new DATipoEntrega();

            //print_r($BETipoEntrega);
            $bolCosto = 0;
            $rptaCosto = $DATipoEntrega->obtenerTipoEntregaUbic($BETipoEntrega->getIdTipoEntregaUbic(), $bolCosto);

            if ($bolCosto == 1) {
                $cuponBuscadoNoEncontro = 1;

                while ($fila = $rptaCosto->fetch()) {

                    $costoEntrega = $fila["Costo"];
                    //$BETipoEntrega->getIdTipoEntregaUbic($fila["Costo"]);

                }
            } else {
                echo "Error en procedimiento de obtener tipo entrega ubicación. ";
            }
        }

        //PASO 4 Evaluar envio gratis
        $codParametroEnvio = 7;
        $montoEnvioGratis = 0;
        $tieneParamEnvioGratis = 0;
        $tieneEnvioGratis = 0;
        $DAUtil = new UTIL();

        $funcionoParam = 0;
        $rptaListado = $DAUtil->buscarParametro($codParametroEnvio, $funcionoParam);

        if ($funcionoParam == -1) {
            echo "Error en procedimiento buscar parametro";
        }
        if ($funcionoParam == 1) {

            while ($fila = $rptaListado->fetch()) {
                $montoEnvioGratis = $fila["ValorParametro"];
                $tieneParamEnvioGratis = 1;
            }
        }


        //echo "tieneParamEnvioGratis ($tieneParamEnvioGratis) montoProductos ($montoProductos) montoEnvioGratis ($montoEnvioGratis) costoEntrega ($costoEntrega)";

        if ($tieneParamEnvioGratis == 1 and $montoProductos >= $montoEnvioGratis and $costoEntrega > 0) {
            $montoAhorroEnvio = $costoEntrega;
            $costoEntrega = 0;
            $tieneEnvioGratis = 1;
        }

        $txtEnvioGratis = "";
        if ($tieneEnvioGratis == 1) {
            $txtEnvioGratis = "Envio gratis";
        }




        //Mostrar Montos
        $permiteFinalizar = 0;
        $montoTotal = 0;

        $montoAhorroTotal = $montoAhorroEnvio + $montoAhorroProductos + $montoDctoCupon;
        $textoAhorroTotal = "";

        if ($montoDctoCupon > 0) {
            $textoAhorroTotal = $textoAhorroTotal . "</br>- Dcto por Cupón S/ " . $montoDctoCupon;
        }
        if ($montoAhorroEnvio > 0) {
            $textoAhorroTotal = $textoAhorroTotal . "</br>- Ahorro por envío S/ " . $montoAhorroEnvio;
        }
        if ($montoAhorroProductos > 0) {
            $textoAhorroTotal = $textoAhorroTotal . "</br>- Ahorro por dcto en productos S/ " . $montoAhorroProductos;
        }


        $montoTotal = $montoProductos - $montoDctoCupon + $costoEntrega;

        //echo "Ojito $costoEntrega";
        ?>
        <div class="mt-2 border-t-2 border-[#8F6B60] p-4">
            <legend class=" flex justify-between items-center text-2xl font-bold text-[#8F6B60] w-full">
                <h3>Monto total</h3>
                <span>
                    S/<?php echo $montoTotal ?>
                </span>
            </legend>
            <div class="flex justify-between items-center mt-2">
                <p class="text-[#8F6B60]">Monto de Productos:</p>
                <p class="text-[#8F6B60]">S/ <?php echo $montoProductos ?></p>
            </div>
            <div class="flex justify-between items-center mt-2">
                <p class="text-[#8F6B60]">Monto Envío:</p>
                <p class="text-[#8F6B60]">S/ <?php echo $costoEntrega ?>         <?php echo $txtEnvioGratis ?></p>
            </div>
            <div class="flex justify-between items-center mt-2">
                <p class="text-[#8F6B60]">Ahorro total:</p>
                <p class="text-[#8F6B60]">S/ <?php echo $montoAhorroTotal ?></p>
            </div>
            <p class=" text-[#8F6B60] mt-2 text-sm">
                <?php echo $textoAhorroTotal ?>
            </p>

        </div>

        <!-- // echo "<legend> <b>Monto total S/ $montoTotal </b> </legend>";

        // echo "</br> Monto de Productos: S/ $montoProductos ";
        // //echo "</br> Dcto total: S/ $montoAhorroTotal  ";
        // echo "</br> Monto Envío: S/ $costoEntrega $txtEnvioGratis";
        // echo "</br> Ahorro total: <b>  S/ $montoAhorroTotal  </b>";
        // echo $textoAhorroTotal;

        // echo "</br> ";
        // echo "</br> "; -->

        <?php

        //echo "TIENE UBIGEO ($tieneUbigeo )";
        if ($tieneUbigeo == 1) {
            $BEPedido = new BEPedido();

            $BEPedido->setMontoPedido($montoTotal);
            $BEPedido->setMontoProducto($montoProductos);
            $BEPedido->setAhorroPedido($montoAhorroTotal);
            $BEPedido->setMontoEnvioPed($costoEntrega);

            $BEPedido->setCupon($cuponCarritoEncontrado);
            $BEPedido->setValorDctoCupon($BECuponEncontrado->getCantDcto());

            $BEPedido->setAhorroProductos($montoAhorroProductos);
            $BEPedido->setAhorroEnvioGratis($montoAhorroEnvio);
            $BEPedido->setAhorroCupon($montoDctoCupon);
            $BEPedido->setTieneEnvioGratis($txtEnvioGratis);

            $reg_serlizer = base64_encode(serialize($BEPedido));
            $_SESSION['pedidodatos'] = $reg_serlizer;
            ?>
            <div class="w-fit m-auto">
                <?php
                echo '<input type="button" onclick="finalizarCarrito()"  value="Finalizar Compra" class="bg-[#EDDFDB] text-[#97847d] border-[#EDDFDB] p-2 rounded-md cursor-pointer hover:bg-[#C4B8B5] hover:text-white  hover:border-[#c4b8b5] border-2 transition-all text-center" />';
                ?>
            </div>
            <?php
        } else {
            echo '<div class="w-fit m-auto">';
            echo '<input type="button" onclick="faltaElegirTipoEntrega()"  value="Finalizar Compra" class="bg-[#EDDFDB] text-[#97847d] border-[#EDDFDB] p-2 rounded-md cursor-pointer hover:bg-[#C4B8B5] hover:text-white  hover:border-[#c4b8b5] border-2 transition-all text-center" />';
            echo '</div>';
        }
    }
    //fin if($montoProductos !=0)




}
//fin operacion 3

?>