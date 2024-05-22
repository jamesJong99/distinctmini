<?php
session_start();

require_once ("ClassesStore/configuracionBD.php");
require_once ("ClassesStore/Util.php");
require_once ("ClassesStore/BEReporte.php");
require_once ("ClassesStore/BEProducto.php");
require_once ("ClassesStore/BEProductoFoto.php");
require_once ("ClassesStore/BEPedido.php");
require_once ("ClassesStore/BEPedidoItem.php");
require_once ("ClassesStore/BEPedidoItemDet.php");
require_once ("ClassesStore/BEProductoDescripcion.php");
require_once ("ClassesStore/DAProducto.php");
require_once ("ClassesStore/DASeo.php");
require_once ("components/productoitemComponent.php");
require_once ("header.php");

$header = new header();

$uploadPath = RUTAFOTOPROD;
$prodSinFotoPortada = PRODSINFOTO;

//obtener producto
$productoItem = new productoitem();

//obtner slug
$slug = "";
if (isset($_GET["slug"])) {
    $slug = $_GET["slug"];
}

$seo = new DASeo();
$seo->obtenerSeoPorSlugYPagina($slug, "producto.php");

$codprod = "";
if (isset($_POST['CodProd'])) {
    $codprod = $_POST['CodProd'];
}

$codprod = $seo->getCodigo();
if ($codprod == null) {
    $codprod = $slug;
} else {
    $codprod = $seo->getCodigo();
}

//obtener SEO

$seo->obtenerSeoPorPaginaYCodigo("producto.php", $codprod);


//echo "codprod  $codprod ";

$nomprod = "";
$nomprodSegundo = "";
$precioActual = 0;
$precioBase = 0;
$ahorro = 0;
$rutafotoPortadaProd = "";

$encontroDatos = 0;
$DAProducto = new DAProducto();
$BEProducto = new BEProducto();
$BEProductoDescripcion = new BEProductoDescripcion();

$TieneProductos = 0;


if ($codprod != "") {

    $funcionoDatosProd = 0;


    $rptaDatos = $DAProducto->obtenerDatosParaCotizador($codprod, $funcionoDatosProd);
    if ($funcionoDatosProd == -1) {
        echo "Error en procedimiento obtener datos de producto ";
    }
    if ($funcionoDatosProd == 1) {

        while ($fila = $rptaDatos->fetch()) {
            $BEProducto->setCodProd($codprod);
            $BEProducto->setNomProd($fila["NomProd"]);
            $BEProducto->setSegundoNombre($fila["SegundoNombre"]);
            $BEProducto->setPrecio($fila["Precio"]);
            $BEProducto->setPrecioActual($fila["PrecioActual"]);
            $BEProducto->setDctoActual($fila["DctoActual"]);
            //$BEProducto->setRutaFotoPortada($fila["RutaFoto"]);


            $BEProducto->setDescPrecioActual($fila["PrecioActualDescripcion"]);
            $BEProducto->setEtiquetaPrincipal($fila["EtiquetaPrincipal"]);
            $BEProducto->setDescStockProd($fila["EstadoStock"]);


            $nomprod = $fila["NomProd"];
            $nomprodSegundo = $fila["SegundoNombre"];

            $precioActual = $fila["PrecioActual"];
            $precioBase = $fila["Precio"];
            $ahorro = $precioBase - $precioActual;

            $BEProducto->setAhorro($ahorro);


            if ($fila["RutaFoto"] != "") {
                $rutafotoPortadaProd = $uploadPath . $fila["RutaFoto"];
            } else {
                $rutafotoPortadaProd = $prodSinFotoPortada;
            }


            $BEProductoDescripcion->setCodProd($codprod);
            $BEProductoDescripcion->setResumen($fila["Resumen"]);
            $BEProductoDescripcion->setLoQueAmaras($fila["LoQueAmaras"]);
            $BEProductoDescripcion->setDescripcion($fila["Descripcion"]);
            $BEProductoDescripcion->setIngredientesEstrella($fila["IngredienteEstrella"]);
            $BEProductoDescripcion->setModoUso($fila["ModoUso"]);
            $BEProductoDescripcion->setIngredientesTotal($fila["Ingredientes"]);
            $BEProductoDescripcion->setPreguntasFrecuentes($fila["PreguntasFrecuentes"]);

            $TieneProductos = 1;
        }
    }
}
// fin if ($codprod !="") { 

// --------------------------------FacebookAds start-------------------------------------------------
require 'vendor/autoload.php';
require_once ("ClassesStore/FaceAdsInfo.php");


use FacebookAds\Api;
use FacebookAds\Logger\CurlLogger;
use FacebookAds\Object\ServerSide\ActionSource;
use FacebookAds\Object\ServerSide\CustomData;
use FacebookAds\Object\ServerSide\Event;
use FacebookAds\Object\ServerSide\EventRequest;
use FacebookAds\Object\ServerSide\UserData;

$RECENT_URL = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

$api = Api::init(null, null, ACCESS_TOKEN);
$api->setLogger(new CurlLogger());

// It is recommended to send Client IP and User Agent for Conversions API Events.


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
    $custom_data = (new CustomData())
        ->setContents($contents)
        ->setNumItems(1)
        ->setCurrency('USD')
        ->setValue($ahorro)
        ->setContentName($nomprod);

    $event = (new Event())
        ->setEventTime(time())
        ->setEventName('ViewContent')
        ->setEventSourceUrl($RECENT_URL)
        ->setActionSource(ActionSource::WEBSITE) //Origen de acción
        ->setEventId(microtime())
        ->setDataProcessingOptions(['LDU'], 0, 0);
    $productosArray = [];
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
//print_r($BEProducto);
//print_r($BEProductoDescripcion);

//obtener fotos
$listaIDFotos = array();
$funcionoFotosProd = 0;
$encontroFotos = 0;
$rptaDatosFotos = $DAProducto->obtenerFotos($codprod, $funcionoFotosProd);

if ($funcionoFotosProd == -1) {
    echo "Error en procedimiento obtener fotos del producto";
}
if ($funcionoFotosProd == 1) {

    while ($fila = $rptaDatosFotos->fetch()) {
        $Idfotkey = $fila["IdFoto"];
        $UbicacionFoto = $uploadPath . $fila["RutaFoto"];
        $EsFotoPrincipal = $fila["FotoPrincipal"];

        $BEProductoFoto = new BEProductoFoto();
        $BEProductoFoto->setCodProd($codprod);
        $BEProductoFoto->setIdFoto($Idfotkey);
        $BEProductoFoto->setRutaFoto($UbicacionFoto);
        $BEProductoFoto->setFotoPrincipal($EsFotoPrincipal);

        $listaIDFotos[$Idfotkey] = $BEProductoFoto;
        $encontroFotos = 1;
    }
}


$timezone = -5; //(GMT -5:00) EST (U.S. & Canada)
$fechaHoy = gmdate("Y-m-d", time() + 3600 * ($timezone + date("I")));

$tieneItemVariante = 0;
$tieneVariante = 0;
$listaVariantes = array();
$CodItemVarianteIN = "";

$funcionoVariantes = 0;
$rptaVariante = $DAProducto->buscarSiTieneVariante($codprod, $fechaHoy, $funcionoVariantes);

if ($funcionoVariantes == -1) {
    echo "Error en procedimiento obtener fotos del producto";
}
if ($funcionoVariantes == 1) {

    while ($fila = $rptaVariante->fetch()) {
        $Id = $fila["CodItemVariante"];
        $CodItemVarianteIN = $CodItemVarianteIN . "'$Id',";

        $rutaFoto = $fila["RutaFotoVariante"];
        if ($rutaFoto != "") {
            $rutaFoto = $uploadPath . $rutaFoto;
        }

        $BEProductoVar = new BEProducto();
        $BEProductoVar->setCodProd($codprod);
        $BEProductoVar->setCodItemVariante($fila["CodItemVariante"]);
        $BEProductoVar->setTipoFotoVideoVariante($fila["TipoFotoVideo"]);
        $BEProductoVar->setRutaFotoVariante($rutaFoto);
        $BEProductoVar->setNomGrupoVariante($fila["NomGrupoVariante"]);
        $BEProductoVar->setTieneVariante($fila["TieneVariante"]);

        $listaVariantes[$Id] = $BEProductoVar;
        $tieneItemVariante = 1;

        if ($BEProductoVar->getTieneVariante() == "S") {
            $tieneVariante = 1;
        }
    }
    //fin while( $fila = $rptaVariante->fetch())

}
//fin if ($funcionoVariantes==1) {


if (strlen($CodItemVarianteIN) >= 10) {
    $CodItemVarianteIN = substr($CodItemVarianteIN, 0, strlen($CodItemVarianteIN) - 1);
    $contadoPedItem = 1;

    $funcionoDetVariante = 0;
    $rptaVarianteDet = $DAProducto->obtenerVariantesDet($CodItemVarianteIN, $funcionoDetVariante);


    while ($fila = $rptaVarianteDet->fetch()) {
        $Id = $fila["CodItemVariante"];

        $BEPedidoItem = new BEPedido_Item();
        $BEPedidoItem->setCodProd($codprod);
        $BEPedidoItem->setCodItemVariante($Id);
        $BEPedidoItem->setCodItem($fila["CodItem"]);
        $BEPedidoItem->setNameVariante($fila["NomVariante"]);
        $BEPedidoItem->setColorVariante($fila["ColorVariante"]);
        $BEPedidoItem->setOrdenVariante($fila["Orden"]);
        $BEPedidoItem->setNomItem($fila["NomItem"]);

        if (isset($listaVariantes[$Id])) {
            $BEProductoVar = new BEProducto();
            $BEProductoVar = $listaVariantes[$Id];
            $BEProductoVar->listadoItem[$contadoPedItem] = $BEPedidoItem;
        }

        $contadoPedItem++;
    }
}
//fin if(strlen($CodItemVarianteIN)>=10)


//Manejo de Stock
//Opciones: Agotado. Agotados todo 1 itemvariante o +. Ejemplo Pack Basic: agotado matte beige y matte light
//Opciones: ParcialmenteAgotado. Agotado 1 item de 1 itemvariante. Ejemplo Pack Basic. agotado matte beige
//Opciones: PocasUnd. De 1 a 3 unidades. Ejemplo Hay stock en todas las variantes. Pero al menos 1 de los item tiene de 1 a 3 unidades
//Opciones: Ok. Mas de 3 unidades en todas los item de todos los item variante

//echo "Stock: ".$BEProducto->getDescStockProd();

//Obtener detalle por item posibles
$listaItemVarianteStock = array();

if ($codprod != "") {

    $funcionoStock = 0;


    $rptaDatos = $DAProducto->obtenerStockProdItem($codprod, $funcionoStock);
    if ($funcionoDatosProd == -1) {
        echo "Error en procedimiento obtener datos de producto ";
    }
    if ($funcionoStock == 1) {

        while ($fila = $rptaDatos->fetch()) {


            $keyItemVarCodItem = $fila["CodItem"];

            $BEReporte = new BEReporte();
            $BEReporte->setKey($fila["CodItemVariante"]);
            $BEReporte->setElement1($fila["CodItem"]);
            $BEReporte->setElement2($fila["NomItem"]);

            $stockItem = $fila["Stock"];
            if ($stockItem > 5) {
                $stockItem = 5;
            }

            $BEReporte->setElement3($stockItem);

            //$BEProducto->setRutaFotoPortada($fila["RutaFoto"]);

            $listaItemVarianteStock[$keyItemVarCodItem] = $BEReporte;
        }
    }
}
// fin if ($codprod !="") { 

//print_r($listaItemVarianteStock);


//Obtener productos de carrito para validar si tiene stock necesario en la pagína de producto
//Ejemplo solo hay 1 turmeric y se elegio 1 tumeric. Ahora no debe permitir elegir 1 pack antiedad que tiene otro turmeric.
$listadoProdCarrito = array();

if (isset($_SESSION['listadoProdCarrito'])) {
    //echo " </br> SESSION RECUPERADA ";

    $listadoProdCarrito = unserialize((base64_decode($_SESSION['listadoProdCarrito'])));
    //unserialize( $_SESSION['listadoProdCarrito'] , 5 );

    //print_r($listadoProdCarrito);
    //echo " </br> SESSION RECUPERADA ";
}

//print_r( $listadoProdCarrito);

$listadoItemCarrito = array();

if (count($listadoProdCarrito) > 0) {


    foreach ($listadoProdCarrito as $ItemKey => $ItemElement) {
        $BEProductoList = new BEProducto();
        $BEProductoList = $ItemElement;

        $cantProdItem = $BEProductoList->getCantidad();


        foreach ($BEProductoList->listadoItem as $IdCodeItemVarComparar => $ItemComparar) {

            $BEItem_Comparar = new BEPedido_Item();
            $BEItem_Comparar = $ItemComparar;


            $codItem = $BEItem_Comparar->getCodItem();
            $canItem = $cantProdItem;
            //$nomItem= $BEProducto->getNomProd()." ".$BEItem_Comparar->getNomGrupoVariante()." ".$BEItem_Comparar->getNameVariante();
            $nomItem = $BEItem_Comparar->getNomItem();

            if (isset($listadoItem[$codItem])) {

                $BEPedidoItemDet = new BEPedidoItemDet();
                $BEPedidoItemDet = $listadoItemCarrito[$codItem];

                $cantAnterior = $BEPedidoItemDet->getCantidad();
                $cantActual = $cantAnterior + $canItem;

                $BEPedidoItemDet->setCantidad($cantActual);
                $listadoItemCarrito[$codItem] = $BEPedidoItemDet;
            } else {
                $BEPedidoItemDet = new BEPedidoItemDet();
                $BEPedidoItemDet->setCodItem($codItem);
                $BEPedidoItemDet->setNomItem($nomItem);
                $BEPedidoItemDet->setCantidad($canItem);

                $listadoItemCarrito[$codItem] = $BEPedidoItemDet;
            }
        }
        //fin de foreach

    }
}
//fin if(count($listadoProdCarrito)>0)



//print_r($listadoItem);

//print_r($listadoItemCarrito["ITEM0046"]);




//Productos relacionados

$listaBEProductosRelac = array();
$TieneProductosRelac = 0;

if ($TieneProductos == 1) {
    $funcionoRelac = 0;

    $rptaListado = $DAProducto->buscarProductoRelacionado($codprod, $funcionoRelac);

    if ($funcionoRelac == -1) {
        echo "Error en procedimiento listado de productos relacionados";
    }
    if ($funcionoRelac == 1) {

        while ($fila = $rptaListado->fetch()) {
            $CodProdRelac = $fila["CodProd"];
            $BEProductoRelac = new BEProducto();

            $BEProductoRelac->setCodProd($codprod);
            $BEProductoRelac->setNomProd($fila["NomProd"]);
            $BEProductoRelac->setSegundoNombre($fila["SegundoNombre"]);
            $BEProductoRelac->setPrecio($fila["Precio"]);
            $BEProductoRelac->setPrecioActual($fila["PrecioActual"]);
            $BEProductoRelac->setDctoActual($fila["DctoActual"]);
            $BEProductoRelac->setRutaFotoPortada($fila["RutaFoto"]);

            $BEProductoRelac->setDescPrecioActual($fila["PrecioActualDescripcion"]);
            $BEProductoRelac->setEtiquetaPrincipal($fila["EtiquetaPrincipal"]);
            $BEProductoRelac->setDescStockProd($fila["EstadoStock"]);

            $listaBEProductosRelac[$CodProdRelac] = $BEProductoRelac;
            $TieneProductosRelac = 1;
        }
    }
}

//print_r($listaBEProductosRelac);

//print_r($listaVariantes);

$header->headerSet($seo->getTituloSeo(), $seo->getDescripcionSeo());
?>

<style>
    #infoprood p {

        font-family: arial;
        font-size: 0.9rem;
        color: #4b4b4b;


    }



    #infoprood2 p {

        font-family: arial;
        font-size: 0.9rem;
        color: #4b4b4b;

    }

    #infoprood2 ul {}
</style>

<?php
$percent = number_format($BEProducto->getDctoActual() * 100, 0, ",", ".") . "%";
$off = "";
if ($BEProducto->getDctoActual() != 0) {
    $off = "$percent dcto";
}
?>

<section class="max-w-screen-xl m-auto my-10 gap-4">
    <div class="grid sm:grid-cols-2 grid-cols-1 gap-5 mb-4">
        <div class="p-2 ">
            <?php
            if ($encontroFotos == 1) {
                //flechas
                $flechaleft = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
<path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
</svg>';

                $flecharight = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
<path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
</svg>';
                ?>
                <div class="glide" id="imagen_producto">
                    <div class="glide__track" data-glide-el="track">
                        <ul class="glide__slides">
                            <?php
                            foreach ($listaIDFotos as $Id => $BEElementoFoto) {

                                $BEProductoFoto = new BEProductoFoto();
                                $BEProductoFoto = $BEElementoFoto;

                                $ubicacionFoto = $BEProductoFoto->getRutaFoto();

                                echo '<li class="glide__slide">';
                                //echo '<div ><img src="'.$ubicacionFoto.'" alt="Distinct" style="max-width:300px"  ></div>';
                                echo "<a class='fancybox ' rel='group' href='$ubicacionFoto'><img class='w-full sm:max-h-[30rem] max-h-72 object-contain' src='$ubicacionFoto' alt='' /></a>";
                                echo "</li> ";
                            }
                            //Fin Foreach
                            ?>
                        </ul>
                    </div>
                    <div data-glide-el="controls" class="glide__arrows">
                        <button class="glide__arrow glide__arrow--left rounded-full bg-white text-[#8F6B60] w-9 h-9 pl-2"
                            data-glide-dir="<"><?php echo $flechaleft ?></button>
                        <button class="glide__arrow glide__arrow--right rounded-full bg-white text-[#8F6B60] w-9 h-9 pl-2"
                            data-glide-dir=">"><?php echo $flecharight ?></button>
                    </div>
                    <div class="slider__bullets glide__bullets" data-glide-el="controls[nav]">
                        <?php
                        $cont = 0;
                        foreach ($listaIDFotos as $Id => $BEElementoFoto) {
                            echo '<button class="slider__bullet glide__bullet border-[#8F6B60] border-[1px]" data-glide-dir="=' . $cont . '"></button>';
                            $cont = $cont + 1;
                        }
                        ?>
                    </div>

                </div>
                <?php
            }
            //fin if ($encontroFotos==1) {
            
            ?>
        </div>


        <div id="infoprood" class="p-4 ">
            <h2 class="sm:text-2xl text-xl font-bold text-center sm:text-left mb-5">
                <?php echo $BEProducto->getNomProd($codprod); ?>
            </h2>
            <?php if ($BEProducto->getSegundoNombre($codprod) == "") {
            } else {
                ?>
                <h2 class="sm:text-lg text-sm font-semibold mb-3">
                    <?php echo $BEProducto->getSegundoNombre($codprod); ?>
                </h2>
                <?php
            }
            ?>

            <div class="p-1 flex items-center gap-2 w-fit m-auto sm:w-auto">

                <span class="text-lg font-bold">
                    S/ <?php echo $BEProducto->getPrecioActual(); ?>
                    <?php
                    if ($BEProducto->getDctoActual() != 0) { ?>
                        <span class="line-through text-[#8F6B60] font-light text-xs">
                            S/ <?php echo $BEProducto->getPrecio(); ?>
                        </span>
                        <?php
                    }
                    ?>
                </span>

                <?php
                if ($BEProducto->getDctoActual() != 0) { ?>
                    <span class="px-2 py-1 text-xs border-2 border-[#f5e1e3] text-[#8F6B60] rounded-md">
                        <?php echo " " . $off; ?>
                    </span>
                    <span class="px-2 py-1 text-xs border-2 border-[#f5e1e3] text-[#8F6B60] bg-[#f5e1e3] rounded-md">
                        Ahorro S/
                        <?php
                        echo $BEProducto->getAhorro();
                        ?>
                    </span>
                    <?php
                }
                ?>
            </div>



            <div class="my-3 font-light text-base leading-6 text-justify text-slate-600">
                <?php echo $BEProductoDescripcion->getResumen(); ?>
            </div>




            <div>
                <form id="formulario" name="formulario" method="post"
                    action="<?php echo BASE_URL_STORE ?>carritocompra_ope.php">

                    <?php

                    $CorrelativoItemVariante = 0;
                    $agotadoProd = 0;
                    $nombreProdAgotado = "";

                    //echo "tieneVariante ($tieneVariante)";
                    //Caso 1.
                    if ($tieneItemVariante == 1) {
                        foreach ($listaVariantes as $IdCodItemVar => $ProdElemento) {
                            $CorrelativoItemVariante = $CorrelativoItemVariante + 1;

                            $BEProductoVar = new BEProducto();
                            $BEProductoVar = $ProdElemento;
                            $listadoItem = $BEProductoVar->listadoItem;

                            //print_r($listadoItem);
                    
                            if ($BEProductoVar->getTieneVariante() == "N") {
                                $cantVariantes = 0;
                                $cantVariantesAgotadas = 0;


                                //Solo tendrá un elemento
                                foreach ($listadoItem as $Correlativo => $ItemElemento) {
                                    $BEPedidoItem = new BEPedido_Item();
                                    $BEPedidoItem = $ItemElemento;



                                    //echo '<option value="'.$keyVariante.'">'.$nameVariante.'</option>';
                                    //$CorrelativoItemVariante
                                    echo '<input type ="hidden" id="CodItemVariante' . $CorrelativoItemVariante . '" name="CodItemVariante' . $CorrelativoItemVariante . '" value="' . $IdCodItemVar . '" >';
                                    echo '<input type ="hidden" id="NomGrupoVariante' . $CorrelativoItemVariante . '" name="NomGrupoVariante' . $CorrelativoItemVariante . '" value="' . $BEProductoVar->getNomGrupoVariante() . '" >';
                                    echo '<input type ="hidden" id="setTieneVariante' . $CorrelativoItemVariante . '" name="setTieneVariante' . $CorrelativoItemVariante . '" value="' . $BEProductoVar->getTieneVariante() . '" >';
                                    echo '<input type ="hidden" id="CodItemElegido' . $CorrelativoItemVariante . '" name="CodItemElegido' . $CorrelativoItemVariante . '" value="' . $BEPedidoItem->getCodItem() . '" >';

                                    $idItem = $CorrelativoItemVariante . "_" . $BEPedidoItem->getCodItem();

                                    echo '<input type ="hidden" id="nameVariante' . $idItem . '" name="nameVariante' . $idItem . '" value="' . $BEPedidoItem->getNameVariante() . '" >';
                                    echo '<input type ="hidden" id="CodItem' . $idItem . '" name="CodItem' . $idItem . '" value="' . $BEPedidoItem->getCodItem() . '" >';
                                    echo '<input type ="hidden" id="NomItem' . $idItem . '" name="NomItem' . $idItem . '" value="' . $BEPedidoItem->getNomItem() . '" >';

                                    $BEReporte = new BEReporte();
                                    $BEReporte = $listaItemVarianteStock[$BEPedidoItem->getCodItem()];

                                    $stockItemCod = $BEReporte->getElement3();

                                    echo '<input type ="hidden" id="StockItem' . $idItem . '" name="StockItem' . $idItem . '" value="' . $stockItemCod . '" >';

                                    //echo "Codigo (".$BEPedidoItem->getCodItem().") Stock ($stockItemCod)";
                    
                                    //echo "</br> stockItemCod: $stockItemCod ";
                    
                                    if (isset($listadoItemCarrito[$BEPedidoItem->getCodItem()])) {
                                        $cantStockCarrito = 0;
                                        $BEPedidoItemDetCarrito = new BEPedidoItemDet();
                                        $BEPedidoItemDetCarrito = $listadoItemCarrito[$BEPedidoItem->getCodItem()];

                                        $cantStockCarrito = $BEPedidoItemDetCarrito->getCantidad();
                                        $stockItemCod = $stockItemCod - $cantStockCarrito;
                                    }

                                    //echo "stockItemCod: $stockItemCod    ITEM: ".$BEPedidoItem->getCodItem()." OJITO $cantStockCarrito (cantStockCarrito)";
                    

                                    $cantVariantes = $cantVariantes + 1;

                                    if ($stockItemCod == 0) {
                                        $cantVariantesAgotadas = $cantVariantesAgotadas + 1;
                                    }
                                }
                                //fin foreach
                    
                                if ($cantVariantes == $cantVariantesAgotadas) {
                                    $agotadoProd = 1;
                                    $nombreProdAgotado = $nombreProdAgotado . "," . $BEProductoVar->getNomGrupoVariante();
                                }
                            }
                            //fin de if
                    
                            //Solo se detalla si tiene item variante
                            if ($BEProductoVar->getTieneVariante() == "S") {

                                $cantVariantes = 0;
                                $cantVariantesAgotadas = 0;

                                $nomTono = $BEProductoVar->getNomGrupoVariante();
                                $rutaFotoVariante = $BEProductoVar->getRutaFotoVariante(); ?>

                                <?php


                                echo '<input type ="hidden" id="CodItemVariante' . $CorrelativoItemVariante . '" name="CodItemVariante' . $CorrelativoItemVariante . '" value="' . $IdCodItemVar . '" >';
                                echo '<input type ="hidden" id="NomGrupoVariante' . $CorrelativoItemVariante . '" name="NomGrupoVariante' . $CorrelativoItemVariante . '" value="' . $BEProductoVar->getNomGrupoVariante() . '" >';
                                echo '<input type ="hidden" id="setTieneVariante' . $CorrelativoItemVariante . '" name="setTieneVariante' . $CorrelativoItemVariante . '" value="' . $BEProductoVar->getTieneVariante() . '" >'; ?>

                                <div class="flex gap-3 my-4 items-center">
                                    <h3> Elegir tono de <?php echo $nomTono ?></h3>
                                    <?php
                                    foreach ($listadoItem as $Correlativo => $ItemElemento) {
                                        $BEPedidoItem = $ItemElemento;
                                        $colorVariante = $BEPedidoItem->getColorVariante();
                                    }

                                    // Variable para asignar un nombre único al grupo de radios
                                    $groupName = 'CodItemElegido' . $CorrelativoItemVariante;

                                    if ($colorVariante != "") {
                                        ?>
                                        <div class="flex gap-2" id="radioGroup' . $CorrelativoItemVariante . '">
                                            <?php
                                            foreach ($listadoItem as $Correlativo => $ItemElemento) {
                                                $BEPedidoItem = $ItemElemento;
                                                $keyVariante = $BEPedidoItem->getCodItem();
                                                $nameVariante = $BEPedidoItem->getNameVariante();
                                                $colorVariante = $BEPedidoItem->getColorVariante();

                                                //Validar Stock de opción
                                                $BEReporte = $listaItemVarianteStock[$BEPedidoItem->getCodItem()];
                                                $stockItemCod = $BEReporte->getElement3();

                                                if (isset($listadoItemCarrito[$BEPedidoItem->getCodItem()])) {
                                                    $BEPedidoItemDetCarrito = $listadoItemCarrito[$BEPedidoItem->getCodItem()];
                                                    $cantStockCarrito = $BEPedidoItemDetCarrito->getCantidad();
                                                    $stockItemCod -= $cantStockCarrito;
                                                }

                                                if ($stockItemCod != 0) {
                                                    // Genera un radio button con un nombre único
                            
                                                    ?>
                                                    <div class="flex items-center gap-2">
                                                        <label class="relative text-center flex items-center flex-col-reverse gap-2">
                                                            <input type="radio" class="appearance-none hidden" name="<?php echo $groupName ?>"
                                                                value="<?php echo $keyVariante ?>">
                                                            <span
                                                                class="block circleinput h-4 p-2 w-4 bg-[<?php echo $colorVariante ?>] border-white border-2 rounded-full cursor-pointer"></span>
                                                            </span>
                                                            <span class="hovertitle cursor-pointer bg-black text-white transition-all text-xs">
                                                                <?php echo $nameVariante ?>
                                                            </span>
                                                        </label>

                                                    </div>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            <style>
                                                input[type="radio"]:checked+.circleinput {
                                                    border: 2px solid #8F6B60;
                                                }

                                                .hovertitle {
                                                    display: none;
                                                }

                                                input[type="radio"]:hover+.circleinput+.hovertitle {
                                                    display: block;
                                                    position: absolute;
                                                    top: -170%;
                                                    background-color: #000;
                                                    color: #fff;
                                                    padding: 5px;
                                                    border-radius: 5px;
                                                    z-index: 100;
                                                }

                                                @media screen and (max-width: 640px) {
                                                    input[type="radio"]:checked+.circleinput+.hovertitle {
                                                        display: block;
                                                        position: absolute;
                                                        top: -170%;
                                                        background-color: #000;
                                                        color: #fff;
                                                        padding: 5px;
                                                        border-radius: 5px;
                                                        z-index: 100;
                                                    }
                                                }


                                                .hovertitle::before {
                                                    content: "";
                                                    position: absolute;
                                                    top: 100%;
                                                    left: 50%;
                                                    margin-left: -5px;
                                                    border-width: 5px;
                                                    border-style: solid;
                                                    border-color: #000 transparent transparent transparent;
                                                }
                                            </style>



                                        </div>
                                        <?php

                                    }
                                    //fin de if
                                    else {
                                        ?>
                                        <select name="<?php echo $groupName ?>" < id="color"
                                            class="border border-[#8F6B60] rounded-md p-2 bg-white">
                                            <?php
                                            foreach ($listadoItem as $Correlativo => $ItemElemento) {
                                                $BEPedidoItem = $ItemElemento;
                                                $keyVariante = $BEPedidoItem->getCodItem();
                                                $nameVariante = $BEPedidoItem->getNameVariante();
                                                $colorVariante = $BEPedidoItem->getColorVariante();

                                                //Validar Stock de opción
                                                $BEReporte = $listaItemVarianteStock[$BEPedidoItem->getCodItem()];
                                                $stockItemCod = $BEReporte->getElement3();

                                                if (isset($listadoItemCarrito[$BEPedidoItem->getCodItem()])) {
                                                    $BEPedidoItemDetCarrito = $listadoItemCarrito[$BEPedidoItem->getCodItem()];
                                                    $cantStockCarrito = $BEPedidoItemDetCarrito->getCantidad();
                                                    $stockItemCod -= $cantStockCarrito;
                                                }

                                                if ($stockItemCod != 0) {
                                                    // Genera un radio button con un nombre único
                            
                                                    ?>
                                                    <option name="<?php echo $groupName ?>" value="<?php echo $keyVariante ?>">
                                                        <?php echo $nameVariante ?>
                                                    </option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>

                                        <?php
                                    }
                                    //fin de else
                                    ?>
                                </div>
                                <?php

                                foreach ($listadoItem as $Correlativo => $ItemElemento) {
                                    $BEPedidoItem = new BEPedido_Item();
                                    $BEPedidoItem = $ItemElemento;

                                    $idItem = $CorrelativoItemVariante . "_" . $BEPedidoItem->getCodItem();

                                    echo '<input type ="hidden" id="nameVariante' . $idItem . '" name="nameVariante' . $idItem . '" value="' . $BEPedidoItem->getNameVariante() . '" >';
                                    echo '<input type ="hidden" id="CodItem' . $idItem . '" name="CodItem' . $idItem . '" value="' . $BEPedidoItem->getCodItem() . '" >';
                                    echo '<input type ="hidden" id="NomItem' . $idItem . '" name="NomItem' . $idItem . '" value="' . $BEPedidoItem->getNomItem() . '" >';

                                    $BEReporte = new BEReporte();
                                    $BEReporte = $listaItemVarianteStock[$BEPedidoItem->getCodItem()];

                                    $stockItemCod = $BEReporte->getElement3();

                                    echo '<input type ="hidden" id="StockItem' . $idItem . '" name="StockItem' . $idItem . '" value="' . $stockItemCod . '" >';



                                    //echo "</br> stockItemCod: $stockItemCod ";
                    
                                    if (isset($listadoItemCarrito[$BEPedidoItem->getCodItem()])) {
                                        $cantStockCarrito = 0;
                                        $BEPedidoItemDetCarrito = new BEPedidoItemDet();
                                        $BEPedidoItemDetCarrito = $listadoItemCarrito[$BEPedidoItem->getCodItem()];

                                        $cantStockCarrito = $BEPedidoItemDetCarrito->getCantidad();
                                        $stockItemCod = $stockItemCod - $cantStockCarrito;
                                    }


                                    //echo "stockItemCod: $stockItemCod    ITEM: ".$BEPedidoItem->getCodItem()." OJITO $cantStockCarrito (cantStockCarrito)";
                    

                                    $cantVariantes = $cantVariantes + 1;

                                    if ($stockItemCod == 0) {
                                        $cantVariantesAgotadas = $cantVariantesAgotadas + 1;
                                    }
                                }

                                if ($cantVariantes == $cantVariantesAgotadas) {
                                    $agotadoProd = 1;
                                    $nombreProdAgotado = $nombreProdAgotado . "," . $BEProductoVar->getNomGrupoVariante();
                                } ?>

                            <?php }
                            //fin de if
                        }

                        if ($agotadoProd == 0) {
                            echo "<input type='submit' class='block mx-auto sm:mr-auto sm:ml-0 my-4 text-center px-4 py-2 cursor-pointer border-2 border-[#8F6B60] text-[#8F6B60] rounded-md hover:bg-[#c4b8b5] hover:text-white hover:border-[#c4b8b5] transition-all' value ='Añadir al carrito'  >";
                        }

                        if ($agotadoProd == 1) {
                            $nombreProdAgotado = substr($nombreProdAgotado, 1, strlen($nombreProdAgotado));
                            ?>
                            <span class="text-red-500 text-left">Producto Agotado: <?php echo $nombreProdAgotado ?> </span>
                            <?php
                        }
                        echo '<input type ="hidden" id="operacion" name="operacion" value="3" >';
                        echo '<input type ="hidden" id="canitemvariante" name="canitemvariante" value="' . $CorrelativoItemVariante . '" >';
                        echo '<input type ="hidden" id="codprod" name="codprod" value="' . $codprod . '" >';
                        echo '<input type ="hidden" id="nomprod" name="nomprod" value="' . $nomprod . '" >';
                        echo '<input type ="hidden" id="nomprodSegundo" name="nomprodSegundo" value="' . $nomprodSegundo . '" >';
                        echo '<input type ="hidden" id="precioprodActual" name="precioprodActual" value="' . $precioActual . '" >';
                        echo '<input type ="hidden" id="cantidad" name="cantidad" value="1" >';
                        echo '<input type ="hidden" id="rutafotoportada" name="rutafotoportada" value="' . $rutafotoPortadaProd . '" >';
                        echo '<input type ="hidden" id="precioprodBase" name="precioprodBase" value="' . $precioBase . '" >';
                        echo '<input type ="hidden" id="ahorroprod" name="ahorroprod" value="' . $ahorro . '" >';
                    } ?>

            </div>
            </form>
        </div>
    </div>
    </div>
</section>

<?php require ('components/productoComponents/detallesComponent.php'); ?>

<section class="max-w-screen-xl m-auto my-20">
    <?php if ($TieneProductosRelac == 1): ?>
        <h2 class="font-sans text-center text-lg sm:text-2xl mb-5 mt-5 text-[#97847d] font-semibold">Productos Relacionados
        </h2>
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <?php $productoItem->obtenerListaproducto($listaBEProductosRelac); ?>
        </div>
    <?php else:
    //echo "<p class='MensajeError' > No se encontraron resultados.</p>";
endif; ?>
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
<script>
    document.querySelector('input[type="submit"]').addEventListener('click', function (event) {
        const radioGroups = [...new Set(Array.from(document.querySelectorAll('input[type="radio"]')).map(radio => radio.name))];
        let allSelected = true;

        radioGroups.forEach(group => {
            const radios = document.querySelectorAll(`input[type="radio"][name="${group}"]`);
            let selected = false;

            radios.forEach(radio => {
                if (radio.checked) {
                    selected = true;
                }
            });

            if (!selected) {
                allSelected = false;
            }
        });

        if (!allSelected) {
            event.preventDefault();
            alert('Por favor, selecciona un color en cada grupo.');
        }
    });
</script>
<script>
    var imagen_producto = new Glide('.glide', {
        type: 'carousel',
        startAt: 0,
        perView: 1,
        focusAt: 'center',
        gap: 0,
        autoplay: 5000,
        hoverpause: true,
        animationDuration: 800,
        animationTimingFunc: 'ease-in-out',
        breakpoints: {
            1024: {
                perView: 1
            },
            600: {
                perView: 1
            }
        }
    });

    imagen_producto.mount();
</script>
<script>
    $(document).ready(function () {
        $(".fancybox").fancybox();
    });
</script>
</body>

</html>