<?php
session_start();

require_once("ClassesStore/BECliente.php");
require_once("ClassesStore/DACliente.php");
require_once("ClassesStore/BECuponImagen.php");
require_once("ClassesStore/DACuponImagen.php");

//print_r($_POST);

$operacion = "";


if (isset($_POST['operacion'])) {
    $operacion = $_POST['operacion'];
}



//Mostrar datos de cliente. Invocado por ajax desde carritocompra.php
if ($operacion == 1) {

    $cantProductosCarrito = 0;
    if (isset($_SESSION['listadoProdCarrito'])) {
        //echo " </br> SESSION RECUPERADA ";
        $listadoProdCarrito = unserialize((base64_decode($_SESSION['listadoProdCarrito'])));
        $cantProductosCarrito = count($listadoProdCarrito);
    }

    if ($cantProductosCarrito >= 0) {

        //RECUPERACIÓN DE SESSION DE clientecarritoobtenido. Luego buscar si tiene cupones
        $BEClienteObtenido = new BECliente();
        $TieneClienteObtenido = 0;
        $tienecuponClienteObtenido = 0;
        $listadocuponesClienteObtenido = array();


        $txtRosasPuntos = "";
        $CantRosas = 0;
        $CantPuntos = 0;
        $FecActualizacion = "";

        if (isset($_SESSION['clientecarritoobtenido'])) {
            $BEClienteObtenido = unserialize((base64_decode($_SESSION['clientecarritoobtenido'])));
            $TieneClienteObtenido = 1;
            //print_r($BEClienteObtenido);


            $CantRosas = $BEClienteObtenido->getRosasActuales();
            $CantPuntos = $BEClienteObtenido->getPuntosActuales();
            $FecActualizacion = $BEClienteObtenido->getFecActualizoPuntos();

            $txtRosasPuntos = "Rosas Actuales: $CantRosas </br> Puntos Actuales: $CantPuntos </br> Fecha Actualizacion: $FecActualizacion";

            $funciono3 = 0;

            $DACupon = new DACuponImagen();
            //20230711 se envia parametro X para considerar  b.CuponPublico in('S','R')
            $rpta3 = $DACupon->buscarcupones($BEClienteObtenido->getCodCliente(), 'X', $funciono3);

            if ($funciono3 == -1) {
                //header("Location:login.php");	
                echo "Error en procedimiento buscar cupones de clientes";
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

            //print_r($listadocuponesClienteObtenido);


        }
        //fin if(isset($_SESSION['clientecarritoobtenido'])) {


        //Mostrar HTML
        //En caso no tiene un cliente obtenido, entonces le permite busco uno
        if ($TieneClienteObtenido == 0) { ?>
            <div class="logincontent">
                <h3 class=" text-left text-[#8F6B60] mb-4 font-bold">
                    ¿Eres Cliente Distinct? Logeate y obtén tus cupones disponibles
                </h3>
                <div>
                    <div id="DivClientesMsjError"></div>
                    <div class=" grid grid-cols-7 items-center justify-between gap-1 mb-4 border-b-2 border-[#8F6B60] p-4 rounded-md login_cliente">
                        <label for=" numdocbuscar" class="
                     text-[#8F6B60] text-left col-span-2">
                            Número de documento:
                        </label>
                        <input type="text" id="numdocbuscar" name="numdocbuscar" value="" placeholder="12345678" class="col-span-3 max-w-32 text-center border border-[#8F6B60] rounded-md p-2  focus:outline-none focus:ring-2 focus:ring-[#8F6B60] focus:border-transparent">
                        <button class="col-span-2 bg-[#EDDFDB] text-[#97847d] border-[#EDDFDB] hover:bg-[#C4B8B5] hover:text-white  hover:border-[#c4b8b5]  px-5 py-2 rounded-md ml-2 focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-opacity-50
                    " onclick="buscarCliente();event.preventDefault();">Login   </button>
                    </div>
                </div>
            </div>
        <?php
        }
        //fin if ($TieneClienteObtenido == 0)

        //En caso tiene un cliente obtenido, entonces muestra los datos y permite elimniar el cliente
        if ($TieneClienteObtenido == 1) {
            //flechas
            $flechaleft = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
<path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
</svg>';

            $flecharight = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
<path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
</svg>';
        ?>
            <div class="logueado">
                <h3 class="text-center text-[#8F6B60] mb-4 font-bold">
                    Hola <?php echo $BEClienteObtenido->getApeNom(); ?>
                    <?php
                    if ($tienecuponClienteObtenido == 1) {
                    ?>, te muestro tus cupones
                </h3>
                <div class="glide" id="cuponescar">
                    
                    <div class="glide__track" data-glide-el="track">
                        <ul class="glide__slides">
                            <?php
                            foreach ($listadocuponesClienteObtenido as $BECupon) {
                                //print_r($BECupon);
                                $nomarchcupon = $BECupon->getNomImagen();

                                $nomarchcupon = "../cupones/" . $nomarchcupon;
                                //$linkcupon= $BECupon->getLink();
                                //$cuponindicaciones= $BECupon->getIndicacionesCupon();


                                //echo "<div><img class='cuponimagen' src='../cupones/$nomarchcupon' alt='' /></div>";
                            ?>
                                <li class="glide__slide">
                                    <a class='fancybox' rel='group' href='<?php echo $nomarchcupon; ?>'><img class='cuponimagen w-full' src='<?php echo $nomarchcupon; ?>' alt='' /></a>
                                </li>
                            <?php
                            }
                            ?>
                        </ul>
                        <!-- Flechas de navegación -->
                        <div data-glide-el="controls" class="glide__arrows">
                            <button class="glide__arrow glide__arrow--left rounded-full bg-white text-[#8F6B60] w-9 h-9 pl-2" data-glide-dir="<"><?php echo $flechaleft ?></button>
                            <button class="glide__arrow glide__arrow--right rounded-full bg-white text-[#8F6B60] w-9 h-9 pl-2" data-glide-dir=">"><?php echo $flecharight ?></button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                var glidefavoritos = new Glide('#cuponescar', {
                    type: 'carousel',
                    perView: 1,
                    focusAt: 'center',
                    autoplay: 6000,
                    animationDuration: 800,
                    breakpoints: {
                        800: {
                            perView: 2
                        },
                        480: {
                            perView: 2
                        }
                    }
                })

                glidefavoritos.mount()
            </script>
            <script>
                $(document).ready(function() {
                    $(".fancybox").fancybox();
                });
            </script>
        <?php
                    } else {
        ?> </h3> <?php
                    }
                    //fin if ($tienecuponClienteObtenido==1)
                    echo "<button class='mt-4 block m-auto text-center bg-[#EDDFDB] text-[#97847d] border-[#EDDFDB] hover:bg-[#C4B8B5] hover:text-white  hover:border-[#c4b8b5] px-5 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-600 focus:ring-opacity-50' onclick='eliminarCliente();event.preventDefault();' >Cerrar sessión</button>";
                }
                //fin if ($TieneClienteObtenido == 1)

            }
            //fin if($cantProductosCarrito >0) {
        }
        //fin operacion 1




        //Mostrar datos de cliente. Invocado por ajax desde carritocompra.php
        if ($operacion == 2) {

            $noEncontroClienteBuscado = 0;
            $numdocclientebuscar = "";

            $encontroCliente = 0;

            if (isset($_POST['numdocclientebuscar'])) {
                $numdocclientebuscar = $_POST['numdocclientebuscar'];
            }

            $BECliente = new BECliente();
            $BECliente->setNumDoc($numdocclientebuscar);

            $DACliente = new DACliente();

            $bolBuscarCliente = 0;
            $mensaje = "";


            if (strlen($numdocclientebuscar) >= 8) {


                $rptaBuscarCliente = $DACliente->buscarClientePorFiltros($BECliente, $bolBuscarCliente);



                if ($bolBuscarCliente == 1) {
                    $noEncontroClienteBuscado = 1;

                    //echo "Despues de buscar $numdocclientebuscar bolBuscarCliente ($bolBuscarCliente)";


                    while ($fila = $rptaBuscarCliente->fetch()) {
                        //echo " Llege aquí";

                        /*
				$codClienteObtenido = $fila["CodCliente"];
				$tipodocObtenido = $fila["TipoDoc"];
				$numDocObtenido = $fila["NumDoc"];
				$apenomObtenido = $fila["ApeNom"];
				$celularObtenido = $fila["Celular"];
				$emailObtenido = $fila["Email"];
				*/
                        $BEClienteGuardar = new BECliente();
                        $BEClienteGuardar->setTieneCodCliente(1);
                        $BEClienteGuardar->setCodCliente($fila["CodCliente"]);
                        $BEClienteGuardar->setTipoDoc($fila["TipoDoc"]);
                        $BEClienteGuardar->setNumDoc($fila["NumDoc"]);
                        $BEClienteGuardar->setApeNom($fila["ApeNom"]);
                        $BEClienteGuardar->setCelular($fila["Celular"]);
                        $BEClienteGuardar->setEmail($fila["Email"]);

                        //2023-07-13 Guardar Rosas, Puntos y fecha de actualizacións
                        $BEClienteGuardar->setPuntosActuales($fila["PuntosActuales"]);
                        $BEClienteGuardar->setRosasActuales($fila["RosasActuales"]);
                        $BEClienteGuardar->setFecActualizoPuntos($fila["FecActPuntos"]);

                        //print_r($BEClienteGuardar);

                        $reg_serlizer = base64_encode(serialize($BEClienteGuardar));
                        $_SESSION['clientecarritoobtenido'] = $reg_serlizer;

                        $encontroCliente = 1;
                        $noEncontroClienteBuscado = 0;
                    }

                    if ($encontroCliente == 0) {
                        $mensaje = $mensaje . "No se encontró al cliente";
                    }
                } else {
                    $mensaje = $mensaje . "Error en procedimiento de buscar cliente. ";
                }
            } else {
                $mensaje = $mensaje . "Debe ingresar al menos 8 caracterés. ";
            }

            if ($mensaje != "") {
                $mensaje = '<div style="color:red">' . $mensaje . '</div>';
            }

            echo $mensaje;
        }
        //fin operacion 2





        //Eliminar Datos del Cliente. Invocado por ajax desde carritocompra.php
        if ($operacion == 3) {
            $mensaje = "";

            unset($_SESSION['clientecarritoobtenido']);
            unset($_SESSION['cuponCarrito']);

            echo $mensaje;
        }
//fin operacion 3
