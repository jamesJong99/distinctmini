<?php
session_start();

require_once("ClassesStore/BETipoEntrega.php");
require_once("ClassesStore/DATipoEntrega.php");
//print_r($_POST);

$operacion = "";
$CodRpta = 0;
$MensajeRpta = "";

if (isset($_POST['operacion'])) {
    $operacion = $_POST['operacion'];
}


//Obtener tipos de entrega por courier
if ($operacion == 1) {

    //print_r($_POST);
    $IdDistrito = -1;
    $IdProvincia = -1;
    $IdDepartamento = -1;

    $direccion = "";

    $camposNecesarios = 1;

    if (isset($_POST['IdDist'])) {
        $IdDistrito = $_POST['IdDist'];
    }
    if (isset($_POST['IdProv'])) {
        $IdProvincia = $_POST['IdProv'];
    }
    if (isset($_POST['IdDpto'])) {
        $IdDepartamento = $_POST['IdDpto'];
    }


    if ($IdDistrito != -1 and $IdProvincia != -1 and $IdDepartamento != -1) {
        $camposNecesarios = 1;
    }


    if ($camposNecesarios == 1) {


        $BETipoEntrega = new BETipoEntrega();
        $DATipoEntrega = new DATipoEntrega();

        $bolLista = 0;
        $rptaLista = $DATipoEntrega->obtenerListadoTipoEntrega($IdDepartamento, $IdProvincia, $IdDistrito, $bolLista);
        $existeDatos = 0;

        if ($bolLista == -1) {
            $MensajeRpta = $MensajeRpta . "Error: Procedimiento de tipo de entrega por ubigeo";
            $CodRpta = 2;
        }
        if ($bolLista == 1) {

            //$MensajeRpta  = $MensajeRpta.'<select id="listaTipoEntrega" name="listaTipoEntrega" >';
            $MensajeRpta  = $MensajeRpta . '<fieldset >';

            $MensajeRpta  = $MensajeRpta . '<legend>Elije la opción de entrega:</legend>';


            while ($fila = $rptaLista->fetch()) {
                $BETipoEntrega->setIdTipoEntregaUbic($fila["Id_TipoEntregaUbic"]);

                $BETipoEntrega->setIdTipoEntrega($fila["Id_TipoEntrega"]);
                $BETipoEntrega->setNomTipoEntrega($fila["NomTipoEntrega"]);

                $BETipoEntrega->setNomZona($fila["Nom_Zona"]);
                $BETipoEntrega->setNomCourier($fila["NomCourier"]);
                $BETipoEntrega->setCosto($fila["Costo"]);
                $BETipoEntrega->setTiempoEntrega($fila["TiempoEntrega"]);
                $BETipoEntrega->setConsideraciones($fila["Consideraciones"]);

                $labelEntrega = "";
                if ($BETipoEntrega->getNomZona() != "") {
                    $labelEntrega = "Zona: " . $BETipoEntrega->getNomZona() . " ";
                }

                $labelEntrega = $labelEntrega . "" . $BETipoEntrega->getNomTipoEntrega() . ": " . $BETipoEntrega->getTiempoEntrega() . " Costo S/ : " . $BETipoEntrega->getCosto();

                //$htmlfila  ="<option value='".$BETipoEntrega->getIdTipoEntrega()."'>".$BETipoEntrega->getNomTipoEntrega()."</option>";
                $htmlfila  = "<div><input type ='radio' onchange='eligioOpcionEntrega()'  id='opcEntr" . $BETipoEntrega->getIdTipoEntregaUbic() . "' name='opcEntr' value='" . $BETipoEntrega->getIdTipoEntregaUbic() . "'><label for='opcEntr" . $BETipoEntrega->getIdTipoEntregaUbic() . "'>" . $labelEntrega . "</label><input type ='hidden' id='consideraciones" . $BETipoEntrega->getIdTipoEntregaUbic() . "'  value='" . $BETipoEntrega->getConsideraciones() . "'  ><input type ='hidden' id='costoEntrega" . $BETipoEntrega->getIdTipoEntregaUbic() . "'  value='" . $BETipoEntrega->getCosto() . "'  ></div>";

                $MensajeRpta = $MensajeRpta . $htmlfila;

                $existeDatos = 1;
            }


            //$MensajeRpta  = $MensajeRpta.'</select>'; 
            $MensajeRpta  = $MensajeRpta . '<fieldset >';

            $MensajeRpta = $MensajeRpta . "<input type ='hidden' id='DireccionElegida'  name='DireccionElegida'  value='" . $direccion . "'>";
            $MensajeRpta = $MensajeRpta . "<input type ='hidden' id='EligioDireccionAnterior'  name='EligioDireccionAnterior'  value='0'>";

            $MensajeRpta = $MensajeRpta . "<input type ='hidden' id='listaDistEle'  name='listaDistEle'  value='" . $IdDistrito . "'>";
            $MensajeRpta = $MensajeRpta . "<input type ='hidden' id='listaProvEle'  name='listaProvEle'  value='" . $IdProvincia . "'>";
            $MensajeRpta = $MensajeRpta . "<input type ='hidden' id='listaDptoEle'  name='listaDptoEle'  value='" . $IdDepartamento . "'>";


            $CodRpta = 1;
        }
    } else {
        $MensajeRpta = $MensajeRpta . "Datos incorrectos";
        $CodRpta = 2;
    }

    echo $MensajeRpta;
}
// fin if ( $operacion ==1 Obtener departamentos) {




//Elegir tipo de entrega anterior. Mostrar Tipo de entrega
if ($operacion == 2) {

    //print_r($_POST);
    $IdDistrito = -1;
    $IdProvincia = -1;
    $IdDepartamento = -1;

    $NameDistrito = "";
    $NameProvincia = "";
    $NameDepartamento = "";

    $direccion = "";

    $IdTipoEntrega = -1;

    $camposNecesarios = 1;

    if (isset($_POST['IdDist'])) {
        $IdDistrito = $_POST['IdDist'];
    }
    if (isset($_POST['IdProv'])) {
        $IdProvincia = $_POST['IdProv'];
    }
    if (isset($_POST['IdDpto'])) {
        $IdDepartamento = $_POST['IdDpto'];
    }


    if (isset($_POST['NameDist'])) {
        $NameDistrito = $_POST['NameDist'];
    }
    if (isset($_POST['NameProv'])) {
        $NameProvincia = $_POST['NameProv'];
    }
    if (isset($_POST['NameDpto'])) {
        $NameDepartamento = $_POST['NameDpto'];
    }


    if (isset($_POST['IdTipoEntrega'])) {
        $IdTipoEntrega = $_POST['IdTipoEntrega'];
    }

    if (isset($_POST['valueDireccionAntes'])) {
        $direccion = $_POST['valueDireccionAntes'];
    }


    if ($IdDistrito != -1 and $IdProvincia != -1 and $IdDepartamento != -1 and $IdTipoEntrega != -1 and $NameDistrito != "" and $NameProvincia != "" and $NameDepartamento != "") {
        $camposNecesarios = 1;
    }


    if ($camposNecesarios == 1) {


        $BETipoEntrega = new BETipoEntrega();
        $DATipoEntrega = new DATipoEntrega();

        $bolLista = 0;
        $rptaLista = $DATipoEntrega->obtenerTipoEntrega($IdDepartamento, $IdProvincia, $IdDistrito, $bolLista);
        $existeDatos = 0;

        if ($bolLista == -1) {
            $MensajeRpta = $MensajeRpta . "Error: Procedimiento obtener tipo de entrega";
            $CodRpta = 2;
        }
        if ($bolLista == 1) {

            //$MensajeRpta  = $MensajeRpta.'<select id="listaTipoEntrega" name="listaTipoEntrega" >';
            $MensajeRpta  = $MensajeRpta . '<div class="w-full">';

            $MensajeRpta  = $MensajeRpta . '<p class=" text-center text-[#8F6B60] text-lg font-bold my-3">Destino: ' . $NameDepartamento . ' - ' . $NameProvincia . ' - ' . $NameDistrito . '</p>';
            $MensajeRpta  = $MensajeRpta . '<p class="  text-[#8F6B60] text-lg font-light mb-4">Elije la opción de entrega:</p>';
            $MensajeRpta  = $MensajeRpta . '<fieldset class="flex flex-col gap-2 text-[#8F6B60]
            ">';

            $CodeRadioElegir = -1;

            while ($fila = $rptaLista->fetch()) {
                $BETipoEntrega->setIdTipoEntregaUbic($fila["Id_TipoEntregaUbic"]);

                $BETipoEntrega->setIdTipoEntrega($fila["Id_TipoEntrega"]);
                $BETipoEntrega->setNomTipoEntrega($fila["NomTipoEntrega"]);

                $BETipoEntrega->setNomZona($fila["Nom_Zona"]);
                $BETipoEntrega->setNomCourier($fila["NomCourier"]);
                $BETipoEntrega->setCosto($fila["Costo"]);
                $BETipoEntrega->setTiempoEntrega($fila["TiempoEntrega"]);
                $BETipoEntrega->setConsideraciones($fila["Consideraciones"]);

                $labelEntrega = "";
                if ($BETipoEntrega->getNomZona() != "") {
                    $labelEntrega = "Zona: " . $BETipoEntrega->getNomZona() . " ";
                }

                $labelEntrega = $labelEntrega . "" . $BETipoEntrega->getNomTipoEntrega() . ": " . $BETipoEntrega->getTiempoEntrega() . " Costo S/ : " . $BETipoEntrega->getCosto();


                if ($BETipoEntrega->getIdTipoEntrega() == $IdTipoEntrega) {
                    $CodeRadioElegir = $BETipoEntrega->getIdTipoEntregaUbic();
                }

                //$htmlfila  ="<option value='".$BETipoEntrega->getIdTipoEntrega()."'>".$BETipoEntrega->getNomTipoEntrega()."</option>";
                $htmlfila  = "<div class='flex gap-3 cursor-pointer'><input type ='radio' onchange='eligioOpcionEntrega()'  id='opcEntr" . $BETipoEntrega->getIdTipoEntregaUbic() . "' name='opcEntr'  value='" . $BETipoEntrega->getIdTipoEntregaUbic() . "'><label for='opcEntr" . $BETipoEntrega->getIdTipoEntregaUbic() . "'>" . $labelEntrega . "</label><input type ='hidden' id='consideraciones" . $BETipoEntrega->getIdTipoEntregaUbic() . "'  value='" . $BETipoEntrega->getConsideraciones() . "'  ><input type ='hidden' id='costoEntrega" . $BETipoEntrega->getIdTipoEntregaUbic() . "'  value='" . $BETipoEntrega->getCosto() . "'  ></div>";

                $MensajeRpta = $MensajeRpta . $htmlfila;

                $existeDatos = 1;
            }

            $MensajeRpta = $MensajeRpta . "<input type ='hidden' id='radioPorElegir'  name='radioPorElegir'  value='" . $CodeRadioElegir . "'>";
            $MensajeRpta = $MensajeRpta . "<input type ='hidden' id='DireccionElegida'  name='DireccionElegida'  value='" . $direccion . "'>";
            $MensajeRpta = $MensajeRpta . "<input type ='hidden' id='EligioDireccionAnterior'  name='EligioDireccionAnterior'  value='1'>";


            $MensajeRpta = $MensajeRpta . "<input type ='hidden' id='listaDistEle'  name='listaDistEle'  value='" . $IdDistrito . "'>";
            $MensajeRpta = $MensajeRpta . "<input type ='hidden' id='listaProvEle'  name='listaProvEle'  value='" . $IdProvincia . "'>";
            $MensajeRpta = $MensajeRpta . "<input type ='hidden' id='listaDptoEle'  name='listaDptoEle'  value='" . $IdDepartamento . "'>";


            //$MensajeRpta  = $MensajeRpta.'</select>'; 
            $MensajeRpta  = $MensajeRpta . '<fieldset >';
            $MensajeRpta  = $MensajeRpta . '</div>';

            $CodRpta = 1;
        }
    } else {
        $MensajeRpta = $MensajeRpta . "Datos incorrectos";
        $CodRpta = 2;
    }

    echo $MensajeRpta;
}
// fin if ( $operacion ==2 Obtener departamentos) {





//$operacion ==3 Elegir tipo de entrega anterior.
if ($operacion == 3) {

    $IdDistrito = -1;
    $IdProvincia = -1;
    $IdDepartamento = -1;

    $direccionElegida = "";
    $IdTipoEntregaUbic = -1;
    $EligioDireccionAnterior = -1;

    $idTipoEntrega =-1;

    $datosNecesario = 0;

    if (isset($_POST['IdDist'])) {
        $IdDistrito = $_POST['IdDist'];
    }
    if (isset($_POST['IdProv'])) {
        $IdProvincia = $_POST['IdProv'];
    }
    if (isset($_POST['IdDpto'])) {
        $IdDepartamento = $_POST['IdDpto'];
    }

    if (isset($_POST['codeOpcionEntregaUbic'])) {
        $IdTipoEntregaUbic = $_POST['codeOpcionEntregaUbic'];
    }

    if (isset($_POST['DireccionElegida'])) {
        $direccionElegida = $_POST['DireccionElegida'];
    }
    if (isset($_POST['EligioDireccionAnterior'])) {
        $EligioDireccionAnterior = $_POST['EligioDireccionAnterior'];
    }



    if ($IdDistrito != -1 and $IdProvincia != -1 and $IdDepartamento != -1 and $IdTipoEntregaUbic != -1 and $EligioDireccionAnterior != -1 ) {
        $datosNecesario = 1;
    }


    if ($datosNecesario == 1) {
        $BETipoEntrega = new BETipoEntrega();
        $BETipoEntrega->setIdTipoEntregaUbic($IdTipoEntregaUbic);
        $BETipoEntrega->setIdDpto($IdDepartamento);
        $BETipoEntrega->setIdProv($IdProvincia);
        $BETipoEntrega->setIdDist($IdDistrito);
        $BETipoEntrega->setDireccion($direccionElegida);
        $BETipoEntrega->setEligioDirecAnt($EligioDireccionAnterior);

        //print_r($BETipoEntrega);

        $reg_serlizer = base64_encode(serialize($BETipoEntrega));
        $_SESSION['ubigeoElegido'] = $reg_serlizer;

        //Obtener sus consideraciones

       
    }
    //fin if($datosNecesario ==1)


}
// fin if ( $operacion ==3 Elegir tipo de entrega anterior.




//$operacion ==4 Elegir tipo de entrega anterior.
if ($operacion == 4) {

    unset($_SESSION['ubigeoElegido']);
}
// fin if ( $operacion ==3 Elegir tipo de entrega anterior.




//$operacion ==4 Elegir tipo de entrega anterior.
//Solo usado si Cliente logeado elegio opción Recojo en Surquillo 
//javascript eligioRecojoSurquilloClienteLogeado
if ($operacion == 5) {

    $IdDistrito = -1;
    $IdProvincia = -1;
    $IdDepartamento = -1;

    $direccionElegida = "";
    $IdTipoEntregaUbic = -1;
    $EligioDireccionAnterior = -1;

    $idTipoEntrega =-1;

    $datosNecesario = 0;

    if (isset($_POST['IdDist'])) {
        $IdDistrito = $_POST['IdDist'];
    }
    if (isset($_POST['IdProv'])) {
        $IdProvincia = $_POST['IdProv'];
    }
    if (isset($_POST['IdDpto'])) {
        $IdDepartamento = $_POST['IdDpto'];
    }

    if (isset($_POST['codeOpcionEntregaUbic'])) {
        $IdTipoEntregaUbic = $_POST['codeOpcionEntregaUbic'];
    }

    if (isset($_POST['DireccionElegida'])) {
        $direccionElegida = $_POST['DireccionElegida'];
    }
    if (isset($_POST['EligioDireccionAnterior'])) {
        $EligioDireccionAnterior = $_POST['EligioDireccionAnterior'];
    }

    if (isset($_POST['idCorrelativo'])) {
        $idTipoEntrega = $_POST['idCorrelativo'];
    }

    if ($IdDistrito != -1 and $IdProvincia != -1 and $IdDepartamento != -1 and $IdTipoEntregaUbic != -1 and $EligioDireccionAnterior != -1 and $idTipoEntrega != -1) {
        $datosNecesario = 1;
    }

 

    if ($datosNecesario == 1) {
        $BETipoEntrega = new BETipoEntrega();
        $BETipoEntrega->setIdTipoEntregaUbic($IdTipoEntregaUbic);
        $BETipoEntrega->setIdDpto($IdDepartamento);
        $BETipoEntrega->setIdProv($IdProvincia);
        $BETipoEntrega->setIdDist($IdDistrito);
        $BETipoEntrega->setDireccion($direccionElegida);
        $BETipoEntrega->setEligioDirecAnt($EligioDireccionAnterior);

        //print_r($BETipoEntrega);

        $reg_serlizer = base64_encode(serialize($BETipoEntrega));
        $_SESSION['ubigeoElegido'] = $reg_serlizer;

        //echo "Puso la session";

        //print_r($BETipoEntrega);
        //print_r($_SESSION['ubigeoElegido']);

        //Obtener sus consideraciones

        //fin if($idTipoEntrega==10)

        $DATipoEntrega = new DATipoEntrega();
        $bolLista = 0;
        $rptaLista = $DATipoEntrega->obtenerConsideracionTipoEntrega($idTipoEntrega, $bolLista);
        $existeDatos = 0;

        if ($bolLista == -1) {
            $MensajeRpta = $MensajeRpta . "Error: Procedimiento Obtener Consideraciones";
            $CodRpta = 2;
        }
        if ($bolLista == 1) {

           

            while ($fila = $rptaLista->fetch()) {

                //echo "Lo obtuvo ";
                echo "<p class='font-bold mb-2'>Consideraciones de entrega</p>";
                echo $fila["Consideraciones"];
                
            }

        }


    }
    //fin if($datosNecesario ==1)


}
// fin if ( $operacion ==3 Elegir tipo de entrega anterior.


