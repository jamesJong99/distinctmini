<?php
require_once(dirname(__FILE__) . "/../ClassesStore/Util.php");
require_once(dirname(__FILE__) . "/../ClassesStore/DASeo.php");

$seo = new DASeo();


$DAUtil = new UTIL();

// Función para obtener los banners
function obtenerBanners($DAUtil, $idParametro, &$listaBanners, &$TieneBanner)
{
    $funcionoBanner = 0;
    $rptaBanner = $DAUtil->obtenerParametroLista($idParametro, $funcionoBanner);

    if ($funcionoBanner == -1) {
        echo "Error en procedimiento obtener banner";
    } elseif ($funcionoBanner == 1) {
        while ($fila = $rptaBanner->fetch()) {
            $Codigo = $fila["CodValor"];
            $BEReporte = new BEReporte();
            $BEReporte->setKey($Codigo);
            $BEReporte->setElement1($fila["Valor"]);
            $BEReporte->setElement2($fila["Valor2"]);
            $listaBanners[$Codigo] = $BEReporte;
            $TieneBanner = 1;
        }
    }
}

// Obtener banners web
$idParamBannerWeb = 2;
$listaBannerWeb = [];
$TieneBannerWeb = 0;
obtenerBanners($DAUtil, $idParamBannerWeb, $listaBannerWeb, $TieneBannerWeb);


// Obtener banners celular
$idParamBannerCelular = 3;
$listaBannerCelular = [];
$TieneBannerCelular = 0;
obtenerBanners($DAUtil, $idParamBannerCelular, $listaBannerCelular, $TieneBannerCelular);

//flechas
$flechaleft = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
<path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0"/>
</svg>';

$flecharight = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
<path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708"/>
</svg>';
?>

<section class="myDiv transition duration-1000 max-w-screen-xl m-auto">
    <!-- HTML para los banners web -->
    <?php if ($TieneBannerWeb == 1) : ?>
        <div class="glide escritorioimagen" id="glide-web">
            <div class="glide__track" data-glide-el="track">
                <ul class="glide__slides">
                    <?php foreach ($listaBannerWeb as $Key => $BEReporte) : ?>
                        <?php
                        $rutaPortada = $BEReporte->getElement1();
                        $linkPortada = $BEReporte->getElement2();
                        if ($rutaPortada != "") {
                            $rutaPortada = $uploadPathBanner . $rutaPortada;
                        }

                        // Extraer el número de $linkPortada utilizando str_replace
                        //$codigo = str_replace(array('VisitarCategoria(', ')'), '', $linkPortada);
                        //echo "Codigo: ".$codigo;
                        
                        ?>
                        <li class="glide__slide">
                            <?php
                            // Obtener código de linkportada
                            //$codigoSlug = $seo->obtenerSeoPorPaginaYCodigo("categoria.php", $codigo);
                            //$slugportada = $seo->getSlug();

                            $slugportada = $linkPortada;

                            //echo "Slug: ".$slugportada;
                            
                            ?>
                            <a href=" <?php echo $slugportada; ?>
                    
                            ">
                                <img class=" w-full" src="<?php echo $rutaPortada; ?>" alt="">
                            </a>

                        </li>
                    <?php endforeach; ?>

                </ul>
            </div>
            <!-- Flechas de navegación -->
            <div data-glide-el="controls" class="glide__arrows">
                <button class="glide__arrow glide__arrow--left rounded-full bg-white text-[#8F6B60] w-9 h-9 pl-2" data-glide-dir="<"><?php echo $flechaleft ?></button>
                <button class="glide__arrow glide__arrow--right rounded-full bg-white text-[#8F6B60] w-9 h-9 pl-2" data-glide-dir=">"><?php echo $flecharight ?></button>
            </div>
        </div>
    <?php endif; ?>

    <!-- HTML para los banners celular -->
    <?php if ($TieneBannerCelular == 1) : ?>
        <div class="glide celularimagen" id="glide-celular">
            <div class="glide__track" data-glide-el="track">
                <ul class="glide__slides">
                <?php foreach ($listaBannerCelular as $Key => $BEReporte) : ?>
                        <?php
                        $rutaPortada = $BEReporte->getElement1();
                        $linkPortada = $BEReporte->getElement2();
                        if ($rutaPortada != "") {
                            $rutaPortada = $uploadPathBanner . $rutaPortada;
                        }

                        // Extraer el número de $linkPortada utilizando str_replace
                        //$codigo = str_replace(array('VisitarCategoria(', ')'), '', $linkPortada);
                        //echo "Codigo: ".$codigo;
                        
                        ?>
                        <li class="glide__slide">
                            <?php
                            // Obtener código de linkportada
                            //$codigoSlug = $seo->obtenerSeoPorPaginaYCodigo("categoria.php", $codigo);
                            //$slugportada = $seo->getSlug();

                            $slugportada = $linkPortada;

                            //echo "Slug: ".$slugportada;
                            
                            ?>
                            <a href=" <?php echo $slugportada; ?>
                    
                            ">
                                <img class=" w-full" src="<?php echo $rutaPortada; ?>" alt="">
                            </a>

                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <!-- Flechas de navegación -->
            <div class="glide__arrows" data-glide-el="controls">
                <button class="glide__arrow glide__arrow--left rounded-full bg-white text-[#8F6B60] w-9 h-9 pl-2" data-glide-dir="<"><?php echo $flechaleft ?></button>
                <button class="glide__arrow glide__arrow--right rounded-full bg-white text-[#8F6B60] w-9 h-9 pl-2" data-glide-dir=">"><?php echo $flecharight ?></button>
            </div>
        </div>
    <?php endif; ?>
</section>

<style>
    /* Estilo por defecto para dispositivos móviles */
    .celularimagen {
        display: block;
        /* Mostrar en dispositivos móviles */
    }

    /* Ocultar en dispositivos móviles */
    .escritorioimagen {
        display: block;
    }

    /* Estilo para pantallas de escritorio */
    @media (min-width: 769px) {
        .celularimagen {
            display: none;
            /* Ocultar en pantallas de escritorio */
        }

    }

    @media (max-width: 769px) {
        .escritorioimagen {
            display: none;
            /* Mostrar en pantallas de escritorio */
        }

    }
</style>
<!-- Incluye el script de Glide.js -->
<script src="https://cdn.jsdelivr.net/npm/@glidejs/glide/dist/glide.min.js"></script>

<!-- Configuración del carousel -->
<script>
    var glideWeb = new Glide('#glide-web', {
        type: 'carousel',
        perView: 1,
        focusAt: 'center',
        gap: 0,
        autoplay: 4000,
        animationDuration: 1800,
    });

    glideWeb.mount();

    var glideCelular = new Glide('#glide-celular', {
        type: 'carousel',
        perView: 1,
        focusAt: 'center',
        gap: 0,
        autoplay: 4000,
        animationDuration: 1800,
    });

    glideCelular.mount();
</script>