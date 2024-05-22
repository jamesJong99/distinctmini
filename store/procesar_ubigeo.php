<?php
session_start();

require_once("ClassesStore/BECliente.php");
require_once("ClassesStore/BEPedidoEnvio.php");
require_once("ClassesStore/BEUbigeo.php");
require_once("ClassesStore/DACliente.php");
require_once("ClassesStore/DAUbigeo.php");

//print_r($_POST);

$operacion = -1;
$CodRpta = 0;
$MensajeRpta = "";



if (isset($_POST['operacion'])) {
    $operacion = $_POST['operacion'];
}


//Mostrar ubigeo. 
//En caso no tenga un cliente logeado se muestra departamnto, provincia y distro.
//En caso  tenga un cliente logeado se muestra destinos frecuentes y opción de elegir departamento, provincia y distrito.
//codeTipoMensaje = 0 no tiene productos añadidos al carrito
//codeTipoMensaje = 1 significa que no hay un cliente logeado
//codeTipoMensaje = 2 significa que hay un cliente logeado
if ($operacion == 0) {

    $cantProductosCarrito = 0;
    $codeTipoMensaje = -1;
    $html = "";
    $inputHTMLHidden ="";

    if (isset($_SESSION['listadoProdCarrito'])) {
        //echo " </br> SESSION RECUPERADA ";
        $listadoProdCarrito = unserialize((base64_decode($_SESSION['listadoProdCarrito'])));
        $cantProductosCarrito = count($listadoProdCarrito);
    }

    if ($cantProductosCarrito == 0) {
        $codeTipoMensaje = 0;
    }
    //fin if($cantProductosCarrito ==0)

    if ($cantProductosCarrito > 0) {
        $BEClienteObtenido = new BECliente();
        $TieneClienteObtenido = 0;

        if (isset($_SESSION['clientecarritoobtenido'])) {
            $BEClienteObtenido = unserialize((base64_decode($_SESSION['clientecarritoobtenido'])));
            $TieneClienteObtenido = 1;
        }

        $inputHTMLHidden = "";

        //echo "Ojito TieneClienteObtenido ($TieneClienteObtenido)";

        //HTML SI NO TIENE CLIENTE OBTENIDO
        if ($TieneClienteObtenido == 0) {

            $html = $html . '<h3 class="text-[#8F6B60] text-left mt-4 mb-2" > Elije el distrito donde vives o trabajas</h3>';

            $html = $html . '
            <div class=" grid sm:grid-cols-2 grid-cols-1 gap-6 mt-4 mb-2">
						<div class="seleccionar text-[#8F6B60] text-left col-span-2 sm:col-span-1 sm:grid flex gap-2
                        ">
                            <h4 >Departamento * </h4>
							<div id="DivDpto">&nbsp</div>
                        </div>
                                  
                        <div class="seleccionar text-[#8F6B60] text-left col-span-2 sm:col-span-1 sm:grid flex gap-2
                        ">
                            <h4 >Provincia * </h4>
							<div id="DivProv">&nbsp</div>
                        </div>
                       
                        <div class="seleccionar text-[#8F6B60] text-left col-span-2 sm:col-span-1 sm:grid flex gap-2
                        ">
                            <h4 >Distrito *  </h4>
							<div id="DivDist">&nbsp</div>
                        </div>
                          

						<div class="seleccionar text-[#8F6B60] text-left col-span-2
                        ">
                            <div>
								<div id="DivTipoEntrega" ></div>
							</div>
                        </tdivr>

						<div class="seleccionar text-[#8F6B60] text-left col-span-2
                        ">
                            <div colspan="2">
								<div id="DivConsideracionesEntrega"  class="mt-2 text-[#8F6B60] text-left"></div>
							</div>
                        </div>
                        </div>
			';

            $codeTipoMensaje = 1;
        }
        //fin if($TieneClienteObtenido == 0) {




        //HTML SI  TIENE CLIENTE OBTENIDO
        if ($TieneClienteObtenido == 1) {

            $DACliente = new DACliente();
            $arrayDirecc =  array();

            $correlativoId = 0;

            $bolListaDireccionAnt = 0;
            //echo "Code Cliente (".$BEClienteObtenido->getCodCliente().")";

            $rptaBuscarDirecc = $DACliente->ObtenerUltimasDireccionsEntrega($BEClienteObtenido->getCodCliente(), $bolListaDireccionAnt);


            if ($bolListaDireccionAnt == 1) {


                while ($fila = $rptaBuscarDirecc->fetch()) {

                    $BEPedidoEnvio = new BEPedidoEnvio();

                    $direc = $fila["DireccionDelivery"];

                    if (strlen($direc) > 35) {
                        $direc = substr($direc, 0, 35);
                    }

                    $BEPedidoEnvio->setDireccionEnvio($direc);
                    $BEPedidoEnvio->setTipoEntrega($fila["TipoEntrega"]);
                    $BEPedidoEnvio->setIdTipoEntrega($fila["Id_TipoEntrega"]);
                    $BEPedidoEnvio->setEstatus($fila["EstatusTipoEntrega"]);

                    $BEPedidoEnvio->setDepartamento($fila["NomDpto"]);
                    $BEPedidoEnvio->setProvincia($fila["NomProv"]);
                    $BEPedidoEnvio->setDistrito($fila["NomDistrito"]);

                    $BEPedidoEnvio->setIdDepartamento($fila["Id_Dpto"]);
                    $BEPedidoEnvio->setIdProvincia($fila["Id_Provincia"]);
                    $BEPedidoEnvio->setIdDistrito($fila["Id_Distrito"]);

                    $BEPedidoEnvio->setNomTipoEntrega($fila["NomTipoEntrega"]);

                    //$BEUbigeo->setEstDist($fila["Est_Distrito"]);

                    //$correlativoId = $correlativoId +1;
                    $correlativoId = $BEPedidoEnvio->getIdTipoEntrega();
                    $arrayDirecc[$correlativoId] = $BEPedidoEnvio;
                }
            } else {
                $html = $html . '<div class="text-red-500 text-center" >';
                $html = $html .  '"Error en procedimiento de buscar direcciones de envío anteriores. "';
                $html = $html . '</div >';
            }

            //print_r($arrayDirecc);


            /*
            $html = $html.'<tr  >';
            $html = $html.'<th colspan="2">';
            $html = $html. "Count ".count($arrayDirecc);
            $html = $html. '</th>';
            $html = $html. '</tr  >';
            */


            //$html  = $html.'<input type="hidden" id="listaDpto" name="listaDpto" value="'.$BEPedidoEnvio->getIdDepartamento().'" >';
            //$html  = $html.'<input type="hidden" id="listaProv" name="listaProv" value="'.$BEPedidoEnvio->getIdProvincia().'" >';
            //$html  = $html.'<input type="hidden" id="listaDist" name="listaDist" value="'.$BEPedidoEnvio->getIdDistrito().'" >';

            //echo "Cantidad (".count($arrayDirecc).")";

            if (count($arrayDirecc) > 0) {

                $html = $html . '<div id="Direcciones">';
                $html = $html . '<h3  class="text-lg font-bold text-[#8F6B60] mb-2">';
                $html = $html . 'Entrega de producto';
                $html = $html . '</h3>';
                $html = $html . '<p class="text-sm text-[#8F6B60] mb-2">';
                $html = $html . 'Direcciones usadas anteriormente:';
                $html = $html . '</p>';



                $html  = $html . '<select id="listaDireccionesAnt" name="listaDireccionesAnt"  class=" border-2 border-[#8F6B60] rounded-md p-2 w-full bg-white text-[#8F6B60]">';

                $valorSelect = "";
                $valorSelectAnt = "";



                foreach ($arrayDirecc as $correlativo => $ProdElemento) {

                    $debeIncluir = 0;

                    $BEProductoMostrar = new BEPedidoEnvio();
                    $BEProductoMostrar = $ProdElemento;

                    $valorSelectAnt = $valorSelect;
                    $valorSelect = $BEProductoMostrar->getDistrito() . " - " . $BEProductoMostrar->getDireccionEnvio();

                    if ($BEProductoMostrar->getTipoEntrega() == "R") {
                        $valorSelect = $BEProductoMostrar->getNomTipoEntrega();
                    }

                    if ($valorSelect  != $valorSelectAnt) {
                        $debeIncluir = 1;
                    }

                    /*
                $BEPedidoEnvio->setNomTipoEntrega($fila["NomTipoEntrega"]);

                $BEPedidoEnvio->setIdTipoEntrega($fila["Id_TipoEntrega"]);
                $BEPedidoEnvio->setTipoEntrega($fila["TipoEntrega"]);

                            $BEPedidoEnvio->setDepartamento($fila["NomDpto"]);
                            $BEPedidoEnvio->setProvincia($fila["NomProv"]);
                            $BEPedidoEnvio->setDistrito($fila["NomDistrito"]);

                            $BEPedidoEnvio->setIdDepartamento($fila["Id_Dpto"]);
                            $BEPedidoEnvio->setIdProvincia($fila["Id_Provincia"]);
                            $BEPedidoEnvio->setIdDistrito($fila["Id_Distrito"]);
                */

                    if ($debeIncluir == 1) {
                        $html  = $html . "<option value='" . $correlativo . "'>" . $valorSelect . "</option>";
                        $inputHTMLHidden = $inputHTMLHidden . '<input type="hidden" name="DptoAntEleg' . $correlativo . '" id="DptoAntEleg' . $correlativo . '" value="' . $BEProductoMostrar->getIdDepartamento() . '">';
                        $inputHTMLHidden = $inputHTMLHidden . '<input type="hidden" name="ProvAntEleg' . $correlativo . '" id="ProvAntEleg' . $correlativo . '" value="' . $BEProductoMostrar->getIdProvincia() . '">';
                        $inputHTMLHidden = $inputHTMLHidden . '<input type="hidden" name="DistAntEleg' . $correlativo . '" id="DistAntEleg' . $correlativo . '" value="' . $BEProductoMostrar->getIdDistrito() . '">';

                        $inputHTMLHidden = $inputHTMLHidden . '<input type="hidden" name="NameDptoAntEleg' . $correlativo . '" id="NameDptoAntEleg' . $correlativo . '" value="' . $BEProductoMostrar->getDepartamento() . '">';
                        $inputHTMLHidden = $inputHTMLHidden . '<input type="hidden" name="NameProvAntEleg' . $correlativo . '" id="NameProvAntEleg' . $correlativo . '" value="' . $BEProductoMostrar->getProvincia() . '">';
                        $inputHTMLHidden = $inputHTMLHidden . '<input type="hidden" name="NameDistAntEleg' . $correlativo . '" id="NameDistAntEleg' . $correlativo . '" value="' . $BEProductoMostrar->getDistrito() . '">';

                        $inputHTMLHidden = $inputHTMLHidden . '<input type="hidden" name="DireccionAntEleg' . $correlativo . '" id="DireccionAntEleg' . $correlativo . '" value="' . $BEProductoMostrar->getDireccionEnvio() . '">';

                        $inputHTMLHidden = $inputHTMLHidden . '<input type="hidden" name="TipoEntAntEleg' . $correlativo . '" id="TipoEntAntEleg' . $correlativo . '" value="' . $BEProductoMostrar->getDireccionEnvio() . '">';
                    }
                }

                $html  = $html . '</select>';


                $html = $html . '<div  class="flex justify-center mt-4 gap-3 sm:flex-row flex-col ">';
                $html  = $html . '<input type ="button" onclick="elegirDireccionAnt()" value="Usar esta dirección" class="bg-[#EDDFDB] text-[#97847d] border-[#EDDFDB] hover:bg-[#C4B8B5] hover:text-white  hover:border-[#c4b8b5] p-2 rounded-md cursor-pointer">';

                $html  = $html . '<input type ="button" onclick="buscarNuevaDireccion()" value="Quiero enviar a Nueva Dirección" class="bg-[#EDDFDB] text-[#97847d] border-[#EDDFDB] hover:bg-[#C4B8B5] hover:text-white  hover:border-[#c4b8b5] p-2 rounded-md cursor-pointer">';

                $html = $html . '</div  >';

                $html = $html . '</div >';



                $codeTipoMensaje = 2;
            }
            //fin if(count($arrayDirecc)>0)



            if (count($arrayDirecc) == 0) {
                $html = $html . '<h3 class="text-[#8F6B60] text-left mt-4 mb-2"
                >Elije el distrito donde vives o trabajes</h3>';

                $html = $html . '
                <div class=" grid sm:grid-cols-2 grid-cols-1 gap-6 mt-4 mb-2">
						<div class="seleccionar text-[#8F6B60] text-left col-span-2 sm:col-span-1 sm:grid flex gap-2
                        ">
                            <h4 >Departamento * </h4>
							<div id="DivDpto">&nbsp</div>
                        </div>
                                  
                        <div class="seleccionar text-[#8F6B60] text-left col-span-2 sm:col-span-1 sm:grid flex gap-2
                        ">
                            <h4 >Provincia * </h4>
							<div id="DivProv">&nbsp</div>
                        </div>
                       
                        <div class="seleccionar text-[#8F6B60] text-left col-span-2 sm:col-span-1 sm:grid flex gap-2
                        ">
                            <h4 >Distrito *  </h4>
							<div id="DivDist">&nbsp</div>
                        </div>
                          

						<div class="seleccionar text-[#8F6B60] text-left col-span-2
                        ">
                            <div>
								<div id="DivTipoEntrega" ></div>
							</div>
                        </tdivr>

						<div class="seleccionar text-[#8F6B60] text-left col-span-2
                        ">
                            <div colspan="2">
								<div id="DivConsideracionesEntrega"  class="mt-2 text-[#8F6B60] text-left"></div>
							</div>
                        </div>
                        </div>
			';

                $codeTipoMensaje = 1;
            }
            //fin if(count($arrayDirecc)==0)


            $html = $html . '
                        <tr class="seleccionar">    
                            <td colspan="2">
								<div id="DivTipoEntrega" ></div>
							</td>
                        </tr>

						<tr class="seleccionar">
                            <td colspan="2">
								<div id="DivConsideracionesEntrega"  class="mt-2 text-[#8F6B60] text-left"></div>
							</td>
                        </tr>
                ';
        }
        //fin if($TieneClienteObtenido == 0) {



    }
    //fin if($cantProductosCarrito >0)

    echo $codeTipoMensaje . $html . $inputHTMLHidden;
}
//fin if ( $operacion ==0 ) { Mostrar Ubigeo





//Obtener departamentos
if ($operacion == 1) {

    //print_r($_POST);
    $iddptopredet = -1;
    $camposNecesarios = 1;

    if (isset($_POST['iddptopredet'])) {
        $iddptopredet = $_POST['iddptopredet'];
    }

    if ($camposNecesarios == 1) {

        $htmlInputText = "";
        $InputText = "";

        $DAUbigeo = new DAUbigeo();
        $BEUbigeo = new BEUbigeo();

        $bolLista = 0;
        $rptaLista = $DAUbigeo->listaDptoVigente($bolLista);
        $existeDatos = 0;

        if ($bolLista == -1) {
            $MensajeRpta = $MensajeRpta . "Error: Procedimiento de listado de dpto vigente ";
            $CodRpta = 2;
        }
        if ($bolLista == 1) {

            $MensajeRpta  = $MensajeRpta . '<select id="listaDpto" name="listaDpto" onchange="cambioDpto()" class=" border-2 border-[#8F6B60] rounded-md p-2 w-full bg-white text-[#8F6B60]">';

            $idProvPorElegir = 0;
            $idDistPorElegir = 0;

            while ($fila = $rptaLista->fetch()) {
                $BEUbigeo->setIdDpto($fila["Id_Dpto"]);
                $BEUbigeo->setNomDpto($fila["Nom_Dpto"]);
                //$BEUbigeo->setEstDpto($fila["Est_Dpto"]);

                $selected = "";
                if ($iddptopredet == $BEUbigeo->getIdDpto()) {
                    $selected = "selected";
                }

                $htmlfila  = "<option value='" . $BEUbigeo->getIdDpto() . "' " . $selected  . ">" . $BEUbigeo->getNomDpto() . "</option>";

                $MensajeRpta = $MensajeRpta . $htmlfila;

                $existeDatos = 1;

                /*Completa datos con id prov y id distrito por cada departamento */
                $idProvPorElegir = $fila["Id_ProvSelecDefaul"];
                $idDistPorElegir = $fila["Id_DistSelecDefaul"];

                $InputText = '<input type="hidden" name="ProvIDDefault' . $BEUbigeo->getIdDpto() . '" id="ProvIDDefault' . $BEUbigeo->getIdDpto() . '" value="' . $idProvPorElegir . '">';
                $htmlInputText  = $htmlInputText . $InputText;
                $InputText = '<input type="hidden" name="DistIDDefault' . $BEUbigeo->getIdDpto() . '" id="DistIDDefault' . $BEUbigeo->getIdDpto() . '" value="' . $idDistPorElegir . '">';
                $htmlInputText  = $htmlInputText . $InputText;
            }

            $MensajeRpta  = $MensajeRpta . '</select>';



            $CodRpta = 1;
        }


        $MensajeRpta  = $MensajeRpta . $htmlInputText;
    } else {
        $MensajeRpta = $MensajeRpta . "Datos incorrectos";
        $CodRpta = 2;
    }

    echo $MensajeRpta;
}
// fin if ( $operacion ==1 Obtener departamentos) {



//Obtener provincias de un departamento
if ($operacion == 2) {

    //print_r($_POST);
    $iddpto = -1;
    $idprovpredet = -1;
    $camposNecesarios = -1;

    if (isset($_POST['iddpto'])) {
        $iddpto = $_POST['iddpto'];
    }

    if (isset($_POST['idprovpredet'])) {
        $idprovpredet = $_POST['idprovpredet'];
    }

    if ($iddpto >= 1) {
        $camposNecesarios = 1;
    }

    if ($camposNecesarios == 1) {

        $DAUbigeo = new DAUbigeo();
        $BEUbigeo = new BEUbigeo();

        $bolLista = 0;
        $rptaLista = $DAUbigeo->listaProvVigentexDpto($iddpto, $bolLista);
        $existeDatos = 0;

        if ($bolLista == -1) {
            $MensajeRpta = $MensajeRpta . "Error: Procedimiento de listado de prov vigente por dpto";
            $CodRpta = 2;
        }
        if ($bolLista == 1) {
            $MensajeRpta  = $MensajeRpta . '<select id="listaProv" name="listaProv" onchange="cambioProv()" class="border-2 border-[#8F6B60] rounded-md p-2 w-full bg-white text-[#8F6B60]">';

            while ($fila = $rptaLista->fetch()) {
                $BEUbigeo->setIdProv($fila["Id_Provincia"]);
                $BEUbigeo->setNomProv($fila["Nom_Provincia"]);
                //$BEUbigeo->setEstProv($fila["Est_Prov"]);

                $selected = "";
                if ($idprovpredet == $BEUbigeo->getIdProv()) {
                    $selected = "selected";
                }

                $htmlfila  = "<option $selected value='" . $BEUbigeo->getIdProv() . "'>" . $BEUbigeo->getNomProv() . "</option>";

                $MensajeRpta = $MensajeRpta . $htmlfila;

                $existeDatos = 1;
            }

            $MensajeRpta  = $MensajeRpta . '</select>';

            $CodRpta = 1;
        }
    } else {
        $MensajeRpta = $MensajeRpta . "Datos incorrectos";
        $CodRpta = 2;
    }

    echo $MensajeRpta;
}

// fin if ( $operacion ==2 Obtener provincias de un departamento) {




//Obtener distritos de una provincia
if ($operacion == 3) {

    //print_r($_POST);
    $idprov = -1;
    $iddistpredet = -1;
    $camposNecesarios = -1;

    if (isset($_POST['idprov'])) {
        $idprov = $_POST['idprov'];
    }

    if (isset($_POST['iddistpredet'])) {
        $iddistpredet = $_POST['iddistpredet'];
    }

    if ($idprov >= 1) {
        $camposNecesarios = 1;
    }

    if ($camposNecesarios == 1) {

        $DAUbigeo = new DAUbigeo();
        $BEUbigeo = new BEUbigeo();

        $bolLista = 0;
        $rptaLista = $DAUbigeo->listaDistVigentexProv($idprov, $bolLista);
        $existeDatos = 0;

        if ($bolLista == -1) {
            $MensajeRpta = $MensajeRpta . "Error: Procedimiento de listado de prov vigente por dpto";
            $CodRpta = 2;
        }
        if ($bolLista == 1) {

            $MensajeRpta  = $MensajeRpta . '<select id="listaDist" name="listaDist" onchange="cambioDist()" class="border-2 border-[#8F6B60] rounded-md p-2 w-full bg-white text-[#8F6B60]">';

            while ($fila = $rptaLista->fetch()) {
                $BEUbigeo->setIdDist($fila["Id_Distrito"]);
                $BEUbigeo->setNomDist($fila["Nom_Distrito"]);
                //$BEUbigeo->setEstDist($fila["Est_Distrito"]);

                $selected = "";
                if ($iddistpredet == $BEUbigeo->getIdDist()) {
                    $selected = "selected";
                }

                $htmlfila  = "<option $selected value='" . $BEUbigeo->getIdDist() . "'>" . $BEUbigeo->getNomDist() . "</option>";

                $MensajeRpta = $MensajeRpta . $htmlfila;

                $existeDatos = 1;
            }

            $MensajeRpta  = $MensajeRpta . '</select>';

            $CodRpta = 1;
        }
    } else {
        $MensajeRpta = $MensajeRpta . "Datos incorrectos";
        $CodRpta = 2;
    }

    echo $MensajeRpta;
}
// fin if ( $operacion ==3 Obtener distritos de una provincia) {





//Obtener distritos de una provincia
if ($operacion == 4) {

    $html = '
            <div class="seleccionar">
                <div colspan="2">
                    <div  class="m-auto w-fit max-w-full">
                        <input type ="button" onclick="quieroEnviarOpcionUsadaAntes()" value="Quiero enviar a dirección usada antes"
                        class="bg-[#EDDFDB] text-[#97847d] border-[#EDDFDB] hover:bg-[#C4B8B5] hover:text-white  hover:border-[#c4b8b5] p-2 rounded-md cursor-pointer text-center truncate max-w-full"
                        >
                    </div  >
                </div>
            </div>
            ';

    $html = $html . '
    <div class=" grid sm:grid-cols-2 grid-cols-1 gap-6 mt-4 mb-2">
						<div class="seleccionar text-[#8F6B60] text-left col-span-2 sm:col-span-1 sm:grid flex gap-2
                        ">
                            <div >Departamento * </div>
							<div id="DivDpto">&nbsp</div>
                        </div>
                                  
                        <div class="seleccionar text-[#8F6B60] text-left col-span-2 sm:col-span-1 sm:grid flex gap-2
                        ">
                            <div >Provincia * </div>
							<div id="DivProv">&nbsp</div>
                        </div>
                       
                        <div class="seleccionar text-[#8F6B60] text-left col-span-2 sm:col-span-1 sm:grid flex gap-2
                        ">
                            <div >Distrito *  </div>
							<div id="DivDist">&nbsp</div>
                        </div>
                          

						<div class="seleccionar text-[#8F6B60] text-left col-span-2
                        ">
                            <div colspan="2">
								<div id="DivTipoEntrega" ></div>
							</div>
                        </div>

						<div class="seleccionar text-[#8F6B60] text-left col-span-2
                        ">
                            <div colspan="2">
								<div id="DivConsideracionesEntrega"  class="mt-2 text-[#8F6B60] text-left"></div>
							</div>
                        </div>
                        <div>

			';




    echo $html;
}
// fin if ( $operacion ==4 Elegir Nueva Dirección para Cliente Logeado) {
