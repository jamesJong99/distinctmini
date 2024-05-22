<?php
require_once('ClassesStore/DASeo.php');
$seo = new DASeo();

$categorias = array(
	array("id" => 1, "nombre" => "Productos Favoritos"),
	array("id" => 2, "nombre" => "Novedades"),
	array("id" => 25, "nombre" => "Promociones"),
	array("id" => 15, "nombre" => "Cuidado Facial"),
	array("id" => 16, "nombre" => "Maquillaje"),
	array("id" => 23, "nombre" => "Cuidado Capilar"),
	array("id" => 24, "nombre" => "Cuidado Corporal")
);
?>

<footer class="bg-[#f1ece8] rounded-t-2xl pt-16 pb-8">
	<div class="grid grid-cols-1 sm:grid-cols-4 max-w-screen-xl m-auto px-4 gap-1">
		<div class="sm:pt-1 pt-4">
			<h3 class='text-base font-bold text-[#5e4c46] font-sans'>CATEGORÍAS</h3>
			<nav>
				<?php foreach ($categorias as $categoria) :
					$seo->obtenerSeoPorPaginaYCodigo("categoria.php", $categoria['id']);
				?>
					<a href="<?php echo BASE_URL_STORE . 'categoria/' . $seo->getSlug(); ?>" class="cursor-pointer no-underline block font-extralight text-[#5e4c46] font-sans"><?php echo $categoria['nombre']; ?></a>
				<?php endforeach; ?>
			</nav>
		</div>
		<div class="sm:pt-1 pt-4">
			<h3 class='text-base font-bold text-[#5e4c46] font-sans'>CONTÁCTANOS</h3>
			<div class="no-underline flex flex-col  text-slate-800">
				<span class="font-extralight text-[#5e4c46] font-sans ">Lunes a Sábado de 9 a 8 pm </span>
				<span class="font-extralight text-[#5e4c46] font-sans">Recojo en Surquillo. Av Angamos este 1559 de lunes a sábado </span>
				<span class="font-extralight text-[#5e4c46] font-sans">Contacto web: 986145878 </span>
				<span class="font-extralight text-[#5e4c46] font-sans">Whatsapp ventas: 974319328 </span>
				<span class="font-extralight text-[#5e4c46] font-sans">Email: distinct.venta@gmail.com </span>
			</div>
		</div>

		<div class="sm:pt-1 pt-4">
			<h3 class='text-base font-bold text-[#5e4c46] font-sans'>POLÍTICAS</h3>
			<div class=" ">

				<nav>
					<p> <a class="cursor-pointer no-underline block font-extralight text-[#5e4c46] font-sans" href="<?php echo BASE_URL_STORE . 'politicas-devolucion'; ?>">Política de devolución</a> </p>
				</nav>
			</div>
		</div>

		<div class="sm:pt-1 pt-4 flex sm:block">
			<h3 class='text-base font-bold text-[#5e4c46] font-sans w-[28rem] sm:w-full '>COMPRAS 100% SEGURAS</h3>
			<div class="grid grid-cols-6 items-center gap-2">
				<img src="<?php echo BASE_URL_STORE ?>imagenes/yape.png" alt="Yape">
				<img src="<?php echo BASE_URL_STORE ?>imagenes/plin.png" alt="Yape">
				<img src="<?php echo BASE_URL_STORE ?>imagenes/bcp.png" alt="Yape">
				<img src="<?php echo BASE_URL_STORE ?>imagenes/bbva.png" alt="Yape">
				<img src="<?php echo BASE_URL_STORE ?>imagenes/visa.png" alt="Yape">
				<img src="<?php echo BASE_URL_STORE ?>imagenes/mastercard.png" alt="Yape">
			</div>
		</div>

	</div>

	<div class='max-w-screen-xl m-auto sm:pt-1 pt-4 mt-5'>
		<div class=' flex w-fit m-auto gap-2'>
			<div class=' w-11 h-11'>
				<a class='w-11 h-11 object-cover hover:shadow-2xl hover:animate-pulse' href='https://www.instagram.com/distinct.bio/' target='_blank'><img class='' src="<?php echo BASE_URL ?>imagenes/RedIG.png" alt="Distinct Instagram"></a>
			</div>

			<div class='w-11 h-11'>
				<a class='w-11 h-11 object-cover hover:shadow-2xl hover:animate-pulse' href='https://www.facebook.com/Distinct.bio' target='_blank'><img src="<?php echo BASE_URL ?>imagenes/RedFB.png" alt="Distinct Facebook"></a>
			</div>

			<div class='w-11 h-11'>
				<a class='w-11 h-11 object-cover hover:shadow-2xl hover:animate-pulse' href='https://api.whatsapp.com/send?phone=51986145878&text=Hola%20Distinct.' target='_blank'><img src="<?php echo BASE_URL ?>imagenes/RedWSP.png" alt="Distinct Wsp"></a>
			</div>

		</div><!-- .piepagina -->
	</div><!-- .piepagina_general -->


	<!-- Include Glide JS -->
	<script src="https://cdn.jsdelivr.net/npm/@glidejs/glide"></script>
</footer>

</body>

</html>