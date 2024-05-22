<?php
require_once("ClassesStore/DASeo.php");
// obtener url de cada codigo de categoria
$seo = new DASeo();

$categorias = array(
    array("id" => 15, "nombre" => "Cuidado Facial", "imagen" => "imagenes/facial.jpg", "alt" => "Cuidado Facial" , "background" => "#f2e9e4"),
    array("id" => 16, "nombre" => "Maquillaje", "imagen" => "imagenes/maquillaje.jpg", "alt" => "Maquillaje", "background" => "#F2F1EF"),
    array("id" => 23, "nombre" => "Capilar", "imagen" => "imagenes/capilar.jpg", "alt" => "Capilar", "background" => "#fffafa"),
    array("id" => 24, "nombre" => "Corporal", "imagen" => "imagenes/corporal.jpg", "alt" => "Corporal", "background" => "#F9EEE8")
);
?>



<section class="myDivCategorias opacity-0 transition duration-1000 max-w-screen-xl mx-auto text-[#97847d] p-2 m-auto my-10">
    
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-10 row-auto">
        <?php foreach ($categorias as $categoria) :
            $seo->obtenerSeoPorPaginaYCodigo("categoria.php", $categoria['id']);
        ?>
            <div class="w-full flex bg-[<?php echo $categoria['background']; ?>]">
                <a class="cursor-pointer no-underline w-full text-center " href=" categoria/<?php echo $seo->getSlug(); ?>">
                    
                    <img class="w-full" src="<?php echo $categoria['imagen']; ?>" alt="<?php echo $categoria['alt']; ?>" loading="lazy">

                    <div class="w-full flex text-center mt-10 mb-0">
                        <span class="w-full mb-3 text-[#97847d] text-center text-xl sm:text-2xl"><?php echo $categoria['nombre']; ?></span>
                    </div>
                    
                    <div class="w-full flex text-center mb-10 mt-0">
                                      
                        <span class="w-full text-center ">
                            <input type='button' class='px-8 py-0 border-2 border-[#8F6B60] text-[#8F6B60]  hover:bg-[#c4b8b5] hover:text-white hover:border-[#c4b8b5] transition-all' value ='Visitar'  >
                        </span>
                    </div>
                </a>
            </div>

        <?php endforeach; ?>
    </div>
</section>