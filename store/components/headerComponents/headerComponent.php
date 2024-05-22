<header class="max-w-screen-xl m-auto">
    <div class="grid w-full items-center gap-3 mt-3 grid-cols-7">

        <a href="<?php echo BASE_URL_STORE ?>" class="sm:col-span-3 col-span-7 text-center"><img class="w-36  sm:ml-0 m-auto" src="<?php echo BASE_URL ?>imagenes/logo.png" alt="Logo Distinct"></a>
        <div class="sm:hidden col-span-1 pl-5">
            <!-- Icono de hamburguesa para dispositivos móviles -->
            <button id="mobileMenuButton" class="text-gray-600 focus:outline-none focus:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                </svg>
            </button>
        </div>
        <?php require('searchComponent.php'); ?>
        <div class="flex gap-4 w-auto p-1 col-span-2 sm:col-span-1 mr-2 sm:mr-0">
            <a class="ml-auto" href="<?php echo BASE_URL_STORE ?>carritocompra.php"><img class="hover:drop-shadow-lg h-8 object-contain" src="<?php echo BASE_URL_STORE ?>imagenes/carrito.png" alt="Logo Distinct"></a>
            <a href="<?php echo BASE_URL_STORE ?>login_cliente.php"><img class="hover:drop-shadow-lg h-8 object-contain" src="<?php echo BASE_URL_STORE ?>imagenes/login.png" alt="Logo Distinct"></a>
        </div>
    </div>
    <?php
    require('menuComponent.php');
    ?>
</header>