<?php
require_once("ClassesStore/DAProducto.php");
require_once("ClassesStore/DASeo.php");
require_once("ClassesStore/BEProducto.php");
require_once("ClassesStore/Util.php");

$uploadPath = RUTAFOTOPROD;
$prodSinFotoPortada = PRODSINFOTO;

$seo = new DASeo();
$DAProducto = new DAProducto();
$BEProducto = new BEProducto();

class productoitem extends DAProducto
{
    private $uploadPath;
    private $prodSinFotoPortada;

    public function __construct()
    {
        $this->uploadPath = RUTAFOTOPROD;
        $this->prodSinFotoPortada = PRODSINFOTO;
    }
    public function obtenerListaproducto($listaProductos)
    {
        foreach ($listaProductos as $ProdKey => $ProdElemento) {
            $BEProducto = new BEProducto();
            $BEProducto = $ProdElemento;

            $rutaPortada = $BEProducto->getRutaFotoPortada();

            if ($rutaPortada != "") {
                $rutaPortada = $this->uploadPath . $rutaPortada; // Aquí es donde se cambió
            } else {
                $rutaPortada = $this->prodSinFotoPortada;
            }
            if ($BEProducto->getDctoActual() == 0) {
                $dctoActual100 = "0";
            } else {
                $dctoActual100 = $BEProducto->getDctoActual() * 100;
                $dctoActual100 = $dctoActual100 . "%";
            }

            $seo = new DASeo();
            $seo->obtenerSeoPorPaginaYCodigo("producto.php", $ProdKey);
            $seoSlug = $seo->getSlug();
            if ($seoSlug == "") {
                $seoSlug = $ProdKey;
            } else {
                $seoSlug = $seo->getSlug();
            }
            $urlproducto = BASE_URL_STORE . "producto/" . $seoSlug; ?>

            <div class="bg-white select-none p-2">
                <div class="relative">
                    <a href="<?php echo $urlproducto; ?>" class="cursor-pointer">
                        <img class='w-full max-h-48 sm:max-h-60 h-auto object-cover min-h-52' src='<?php echo $rutaPortada; ?>' alt='<?php echo $BEProducto->getEtiquetaPrincipal(); ?> imagen <?php echo $BEProducto->getNomProd(); ?>' loading="lazy" />
                    </a>
                    
                    <?php 
                    $colorFondo ="#deb1b6";
                    $colorLetra ="#ffffff";


                    if($BEProducto->getEtiquetaPrincipal()!="")
                    {

                    ?>                    
                        <span class="absolute top-0 right-0 bg-[<?php echo $colorFondo?>] text-[<?php echo $colorLetra?>] px-2 py-1 m-1 mr-0 text-xs">
                            <?php
                            echo $BEProducto->getEtiquetaPrincipal(); ?>
                        </span>
                    <?php 
                    }
                    ?>
                        

                </div>
                <div class="mt-2">
                    <h3 class="mb-2 w-full text-center sm:text-left  break-words whitespace-normal text-sm">
                        <?php echo $BEProducto->getNomProd(); ?>
                        <span class="text-xs">
                            <?php
                            if ($BEProducto->getSegundoNombre() != "") {
                                //segundo nombre
                                echo '<br>' . $BEProducto->getSegundoNombre();
                            }
                            ?>
                        </span>
                    </h3>

                    <span></span>
                    <div class="flex sm:flex-row  flex-col justify-between items-center ">
                        <p><?php echo $BEProducto->getDescPrecioActual(); ?></p>
                        <a href="<?php echo $urlproducto; ?>" class="w-full text-center mt-2 mb-3 sm:mt-0 sm:mb-0 text-xs hover:bg-[#c4b8b5] hover:border-[#c4b8b5] hover:text-white  sm:text-[0.70rem] sm:w-auto text-[#97847d] cursor-pointer  transition-all border-[#97847d] border-2 rounded-lg px-1 py-2">Añadir al Carrito</a>
                    </div>

                </div>
                <style>
                    .underlineprecio {
                        font-size: 12px;
                        color: #97847d;
                    }
                </style>
            </div>
            <?php }
    }

    public function obtenerListaproductoPaginacion($listaProductos, $pagina)
    {
        $util = new Util();
        $funciono = false;
        $codParametro = 4; // Cambia esto por el código de parámetro que quieras usar
        $resultado = $util->obtenerParametroLista($codParametro, $funciono);
        $valor = "";

        foreach ($resultado as $fila) {
            // Accede a los valores de la fila aquí
            $valor = $fila['Valor'];
        }

        $numeropaginacion = $valor;
        $elementosPorPagina = $numeropaginacion;
        $inicio = ($pagina - 1) * $elementosPorPagina;
        $fin = $pagina * $elementosPorPagina;
        $contador = 0;
        foreach ($listaProductos as $ProdKey => $ProdElemento) {
            if ($contador >= $inicio && $contador < $fin) {
                $BEProducto = new BEProducto();
                $BEProducto = $ProdElemento;

                $rutaPortada = $BEProducto->getRutaFotoPortada();

                if ($rutaPortada != "") {
                    $rutaPortada = $this->uploadPath . $rutaPortada; // Aquí es donde se cambió
                } else {
                    $rutaPortada = $this->prodSinFotoPortada;
                }
                if ($BEProducto->getDctoActual() == 0) {
                    $dctoActual100 = "0";
                } else {
                    $dctoActual100 = $BEProducto->getDctoActual() * 100;
                    $dctoActual100 = $dctoActual100 . "%";
                }

                $seo = new DASeo();
                $seo->obtenerSeoPorPaginaYCodigo("producto.php", $ProdKey);
                $seoSlug = $seo->getSlug();
                if ($seoSlug == "") {
                    $seoSlug = $ProdKey;
                } else {
                    $seoSlug = $seo->getSlug();
                }
                $urlproducto = BASE_URL_STORE . "producto/" . $seoSlug;
            ?>

                <div class="bg-white select-none p-2">
                    <div class="relative">
                        <a href="<?php echo $urlproducto; ?>" class="cursor-pointer">
                            <img class='w-full max-h-48 sm:max-h-60 h-auto object-cover min-h-52' src='<?php echo $rutaPortada; ?>' alt='<?php echo $BEProducto->getEtiquetaPrincipal(); ?> imagen <?php echo $BEProducto->getNomProd(); ?>' loading="lazy" />
                        </a>

                        <?php 
                    $colorFondo ="#deb1b6";
                    $colorLetra ="#ffffff";

                    if($BEProducto->getEtiquetaPrincipal()!="")
                    {

                    ?>                    
                        <span class="absolute top-0 right-0 bg-[<?php echo $colorFondo?>] text-[<?php echo $colorLetra?>] px-2 py-1 m-1 mr-0 text-xs">
                            <?php
                            echo $BEProducto->getEtiquetaPrincipal(); ?>
                        </span>
                    <?php 
                    }
                    ?>


                    </div>
                    <div class="mt-2">
                        <h3 class="mb-2 w-full text-center sm:text-left  break-words whitespace-normal text-sm">
                            <?php echo $BEProducto->getNomProd(); ?>
                            <span class="text-xs">
                                <?php
                                if ($BEProducto->getSegundoNombre() != "") {
                                    //segundo nombre
                                    echo '<br>' . $BEProducto->getSegundoNombre();
                                }
                                ?>
                            </span>
                        </h3>
                        
                        <span></span>
                        <div class="flex sm:flex-row  flex-col justify-between items-center ">
                            <p><?php echo $BEProducto->getDescPrecioActual(); ?></p>
                            <a href="<?php echo $urlproducto; ?>" class="w-full text-center mt-2 mb-3 sm:mt-0 sm:mb-0 text-xs hover:bg-[#c4b8b5] hover:border-[#c4b8b5] hover:text-white  sm:text-[0.70rem] sm:w-auto text-[#97847d] cursor-pointer  transition-all border-[#97847d] border-2 rounded-lg px-1 py-2">Añadir al Carrito</a>
                        </div>

                    </div>
                    
                    <style>
                        .underlineprecio {
                            font-size: 12px;
                            color: #97847d;
                        }
                    </style>
                </div>
<?php }
            $contador++;
        }
    }

    //obtener paginacion
    public function obtenerPaginacion()
    {
        $util = new Util();
        $funciono = false;
        $codParametro = 4; // Cambia esto por el código de parámetro que quieras usar
        $resultado = $util->obtenerParametroLista($codParametro, $funciono);
        $valor = "";

        foreach ($resultado as $fila) {
            // Accede a los valores de la fila aquí
            $valor = $fila['Valor'];
        }

        $numeropaginacion = $valor;

        return $numeropaginacion;
    }
}
