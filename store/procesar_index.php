<?php
session_start();
require_once("ClassesStore/DAProducto.php");
require_once("components/productoitemComponent.php");
//print_r($_POST);

$operacion = "";


if (isset($_POST['operacion'])) {
    $operacion = $_POST['operacion'];
}



    //Html de carrousel de novedades
    if ($operacion == 1) {

        $productoItem = new productoitem();

        //flechas
        $flechaleft = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
        </svg>';

        $flecharight = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
        </svg>';

        //Obtener productos de lista Novedades
        $idCategoriaNovedades = 2;
        $tipoBusqCategoriaNovedades = 1;
        $ordenCategoriaNovedades = 1;
        $listaBEProductosCategoriaNovedades = array();
        $TieneProductoCategoriaNovedades = 0;
        $funcionoBusqCategoriaNovedades = 0;

        $rptaCategoriaNovedades = $DAProducto->obtenerProductosCategoria($idCategoriaNovedades, $tipoBusqCategoriaNovedades, $ordenCategoriaNovedades, $funcionoBusqCategoriaNovedades);

        if ($funcionoBusqCategoriaNovedades == -1) {
            echo "Error en procedimiento obtener productos categoría 2";
        }
        if ($funcionoBusqCategoriaNovedades == 1) {

            while ($fila = $rptaCategoriaNovedades->fetch()) {
                $CodProd = $fila["CodProd"];
                $BEProducto = new BEProducto();
                $BEProducto->setCodProd($CodProd);
                $BEProducto->setNomProd($fila["NomProd"]);
                $BEProducto->setSegundoNombre($fila["SegundoNombre"]);


                $BEProducto->setPrecio($fila["Precio"]);
                $BEProducto->setRutaFotoPortada($fila["RutaFoto"]);

                $BEProducto->setPrecioActual($fila["PrecioActual"]);
                $BEProducto->setDctoActual($fila["DctoActual"]);


                $BEProducto->setDescStockProd($fila["EstadoStock"]);
                $BEProducto->setDescPrecioActual($fila["PrecioActualDescripcion"]);
                $BEProducto->setEtiquetaPrincipal($fila["EtiquetaPrincipal"]);

                $listaBEProductosCategoriaNovedades[$CodProd] = $BEProducto;
                $TieneProductoCategoriaNovedades = 1;
            }
        }

        ?>

        <?php if ($TieneProductoCategoriaNovedades == 1) : ?>
            <h2 class="font-sans text-center text-lg sm:text-2xl mb-1 mt-1 text-[#55423c] font-semibold uppercase">Novedades</h2>
            <div class="glide" id="categorianovedades">
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        <?php $productoItem->obtenerListaproducto($listaBEProductosCategoriaNovedades); ?>
                    </ul>
                    <!-- Flechas de navegación -->
                    <div data-glide-el="controls" class="glide__arrows">
                        <button class="glide__arrow glide__arrow--left rounded-full bg-white text-[#8F6B60] w-9 h-9 pl-2" data-glide-dir="<"><?php echo $flechaleft ?></button>
                        <button class="glide__arrow glide__arrow--right rounded-full bg-white text-[#8F6B60] w-9 h-9 pl-2" data-glide-dir=">"><?php echo $flecharight ?></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- //Seccion TieneProductoCategoriaPromociones -->


        <?php

        
    }
    //fin operacion 1


    //Html de carrousel de promociones
    if ($operacion == 2) {

        $productoItem = new productoitem();

        //flechas
        $flechaleft = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
        </svg>';

        $flecharight = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
        </svg>';

        //Obtener productos de lista Promociones
        $idCategoriaPromociones = 25;
        $tipoBusqCategoriaPromociones = 1;
        $ordenCategoriaPromociones = 1;
        $listaBEProductosCategoriaPromociones = array();
        $TieneProductoCategoriaPromociones = 0;
        $funcionoBusqCategoriaPromociones = 0;

        $rptaCategoriaPromociones = $DAProducto->obtenerProductosCategoria($idCategoriaPromociones, $tipoBusqCategoriaPromociones, $ordenCategoriaPromociones, $funcionoBusqCategoriaPromociones);

        if ($funcionoBusqCategoriaPromociones == -1) {
            echo "Error en procedimiento obtener productos categoría 3";
        }
        if ($funcionoBusqCategoriaPromociones == 1) {

            while ($fila = $rptaCategoriaPromociones->fetch()) {
                $CodProd = $fila["CodProd"];
                $BEProducto = new BEProducto();
                $BEProducto->setCodProd($CodProd);
                $BEProducto->setNomProd($fila["NomProd"]);
                $BEProducto->setSegundoNombre($fila["SegundoNombre"]);


                $BEProducto->setPrecio($fila["Precio"]);
                $BEProducto->setRutaFotoPortada($fila["RutaFoto"]);

                $BEProducto->setPrecioActual($fila["PrecioActual"]);
                $BEProducto->setDctoActual($fila["DctoActual"]);


                $BEProducto->setDescStockProd($fila["EstadoStock"]);
                $BEProducto->setDescPrecioActual($fila["PrecioActualDescripcion"]);
                $BEProducto->setEtiquetaPrincipal($fila["EtiquetaPrincipal"]);

                $listaBEProductosCategoriaPromociones[$CodProd] = $BEProducto;
                $TieneProductoCategoriaPromociones = 1;
            }
        }

        //Obtener productos de lista Promociones

        //echo "funcionoBusqCategoriaPromociones ($funcionoBusqCategoriaPromociones)";
        
        ?>

        <?php if ($TieneProductoCategoriaPromociones == 1) : ?>
            <h2 class="font-sans text-center text-lg sm:text-2xl mb-1 mt-1 text-[#55423c] font-semibold uppercase">Promociones</h2>
            <div class="glide" id="categoriapromociones">
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        <?php $productoItem->obtenerListaproducto($listaBEProductosCategoriaPromociones); ?>
                    </ul>
                    <!-- Flechas de navegación -->
                    <div data-glide-el="controls" class="glide__arrows">
                        <button class="glide__arrow glide__arrow--left rounded-full bg-white text-[#8F6B60] w-9 h-9 pl-2" data-glide-dir="<"><?php echo $flechaleft ?></button>
                        <button class="glide__arrow glide__arrow--right rounded-full bg-white text-[#8F6B60] w-9 h-9 pl-2" data-glide-dir=">"><?php echo $flecharight ?></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        


        <?php

        
    }
    //fin operacion 2
