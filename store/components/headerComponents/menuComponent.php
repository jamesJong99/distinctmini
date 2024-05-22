<?php
// Mostrar todos los errores de PHP
error_reporting(E_ALL);

// Mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require_once("ClassesStore/DASeo.php");
// obtener url de cada codigo de categoria
$seo = new DASeo();
// Definir una matriz de categorías con el formato [id => nombre]
$categorias = array(
    22 => 'Todo',
    25 => 'Promociones',
    15 => 'Cuidado Facial',
    16 => 'Maquillaje',
    23 => 'Cuidado Capilar',
    24 => 'Cuidado Corporal'
);

// Mostrar los enlaces utilizando un bucle foreach
?>

<div class="max-w-screen-xl m-auto" id="nav">
    <nav class="flex sm:gap-5 sm:justify-between sm:flex-row flex-col bg-white z-50 left-0 right-0 p-4 relative">
        
        <div class="sm:contents hidden sm:flex flex-col sm:flex-row left-0 right-0 bg-white z-50 absolute sm:relative top-full shadow-2xl sm:shadow-none" id="navLinks">
            <?php
            // Iterar sobre la matriz de categorías con un bucle foreach
            foreach ($categorias as $id => $nombre) {
                // Generar el enlace con el ID de categoría y el nombre
                $seo->obtenerSeoPorPaginaYCodigo("categoria.php", $id);
            ?>
                <a class="hover:cursor-pointer hover:animate-pulse no-underline hover:underline transition-all text-[#97847d] px-2 py-3 sm:px-2 sm:py-4" href="<?php echo BASE_URL_STORE . 'categoria/' . $seo->getSlug(); ?>"><?php echo $nombre; ?></a>
            <?php
            }
            ?>
        </div>
    </nav>
</div>

<script>
    // Función para mostrar u ocultar la navegación en dispositivos móviles
    function toggleMobileMenu() {
        var navLinks = document.getElementById('navLinks');
        navLinks.classList.toggle('open');
    }

    // Agregar evento de clic al botón del menú móvil
    document.getElementById('mobileMenuButton').addEventListener('click', function() {
        toggleMobileMenu();
    });
</script>

<style>
    /* Estilos para mostrar y ocultar el menú en dispositivos móviles */
    @media screen and (max-width: 640px) {
        #navLinks {
            display: none;
            transition: all 0.3s ease;
        }

        #navLinks.open {
            display: flex !important;
            flex-direction: column;
            animation: fadeIn 0.3s ease;
        }
    }


    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }
</style>