<?php
session_start();
require_once("ClassesStore/Util.php");
require_once("ClassesStore/BEReporte.php");
require_once("ClassesStore/BEProducto.php");
require_once("ClassesStore/DAProducto.php");
require_once("ClassesStore/DASeo.php");
require_once("components/productoitemComponent.php");
require_once("header.php");

$header = new header();
$productoItem = new productoitem();

$uploadPath = RUTAFOTOPROD;
$prodSinFotoPortada = PRODSINFOTO;
$uploadPathBanner = RUTAFOTOBANNER;


$codParametroMensaje = 6;
$mensajeInicio = "";
$tieneMensajeInicio = 0;
$DAUtil = new UTIL();

//obtener SEO
$seo = new DASeo();
$seo->obtenerSeoPorPagina("index.php");

$funcionoBusq = 0;
$rptaListado =  $DAUtil->buscarParametro($codParametroMensaje, $funcionoBusq);

if ($funcionoBusq == -1) {
	echo "Error en procedimiento buscar parametro";
}
if ($funcionoBusq == 1) {

	while ($fila = $rptaListado->fetch()) {
		$mensajeInicio = $fila["ValorParametro"];
		$tieneMensajeInicio = 1;
	}
}
$DAProducto = new DAProducto();

//Obtener productos de lista Categoria
$idCategoriaFavoritos = 1;
$tipoBusqCategoriaFavoritos = 1;
$ordenCategoriaFavorios = 1;
$listaBEProductosCategoriaFavoritos = array();
$TieneProductoCategoriaFavoritos = 0;
$funcionoBusqCategoriaFavoritos = 0;

$rptaCategoriaFavoritos = $DAProducto->obtenerProductosCategoria($idCategoriaFavoritos, $tipoBusqCategoriaFavoritos, $ordenCategoriaFavorios, $funcionoBusqCategoriaFavoritos);

if ($funcionoBusqCategoriaFavoritos == -1) {
	echo "Error en procedimiento obtener productos categoría 1";
}
if ($funcionoBusqCategoriaFavoritos == 1) {

	while ($fila = $rptaCategoriaFavoritos->fetch()) {
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

		$listaBEProductosCategoriaFavoritos[$CodProd] = $BEProducto;
		$TieneProductoCategoriaFavoritos = 1;
	}
}




//print_r($listaBEProductosCategoriaPromociones);

$header->headerSet($seo->getTituloSeo(), $seo->getDescripcionSeo());

if ($tieneMensajeInicio == 1) {

	echo '<div class="bg-[#d89ba1] text-center py-2">';
	echo '<span class="text-white font-bold">' . $mensajeInicio . '</span>';
	echo '</div>';
} //fin if ($tienePedidos==1) { 

?>
<?php
require('./components/bannerIndexComponent.php');
?>

<!-- //Seccion TieneProductoCategoriaFavoritos -->
<section class="myDiv transition duration-1000 max-w-screen-xl m-auto my-10">

	<?php if ($TieneProductoCategoriaFavoritos == 1) :
	?>
		<h2 class="font-sans text-center text-lg sm:text-2xl mb-1 mt-1 text-[#55423c] font-semibold uppercase">Tus Favoritos</h2>
		<div class="glide" id="categoriafavoritos">
			<div class="glide__track" data-glide-el="track">
				<ul class="glide__slides">
					<?php $productoItem->obtenerListaproducto($listaBEProductosCategoriaFavoritos); ?>
				</ul>
				<!-- Flechas de navegación -->
				<div data-glide-el="controls" class="glide__arrows">
					<button class="glide__arrow glide__arrow--left rounded-full bg-white text-[#8F6B60] w-9 h-9 pl-2" data-glide-dir="<"><?php echo $flechaleft ?></button>
					<button class="glide__arrow glide__arrow--right rounded-full bg-white text-[#8F6B60] w-9 h-9 pl-2" data-glide-dir=">"><?php echo $flecharight ?></button>
				</div>
			</div>
		</div>
	<?php endif; ?>

</section>


<section id="novedades" class="myDiv transition duration-1000 max-w-screen-xl m-auto my-10">
</section>


<section id="promociones" class="myDiv transition duration-1000 max-w-screen-xl m-auto my-10">
</section>


<?php
require('./components/categoriasItemsComponent.php');
?>

<?php
require('footer.php');
?>

<script>
	var glidefavoritos = new Glide('#categoriafavoritos', {
		type: 'carousel',
		perView: 5,
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
	window.addEventListener('load', function() {

		setTimeout(function() {
			//alert('alerta novedades');
			cargarNovedades();
		}, 500);

		setTimeout(function() {
			//alert('alerta promociones');
			cargarPromociones();
		}, 1000);

		setTimeout(function() {
			//alert('alerta promociones');
			mostrarCategorias();
		}, 1500);

	});

	function cargarNovedades() {

	
		var parametros = {
			"operacion": 1
		};

		$.ajax({
			data: parametros,
			url: 'procesar_index.php',
			type: 'post',
			beforeSend: function() {
				//mostrar_mensaje("Procesando, espere por favor...");
				//alert('3');
			},
			success: function(response) {
				//alert(response);
				//mostrar_mensaje(response);
				$("#novedades").html(response);

				setTimeout(function(){ // just being safe

					//alert('a');
					var glidenovedades = new Glide('#categorianovedades', {
						type: 'carousel',
						perView: 5,
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
					

					//alert('b');
					glidenovedades.mount()
					//alert('c');

               },100);

			}
		});

		
	}

	function cargarPromociones() {

	//alert(1);
	
	var parametros = {
		"operacion": 2
	};

	$.ajax({
		data: parametros,
		url: 'procesar_index.php',
		type: 'post',
		beforeSend: function() {
			//mostrar_mensaje("Procesando, espere por favor...");
			//alert('3');
		},
		success: function(response) {
			//alert(response);
			
			//mostrar_mensaje(response);
			$("#promociones").html(response);

			setTimeout(function(){ // just being safe

				//alert('a');
				var glidenovedades = new Glide('#categoriapromociones', {
					type: 'carousel',
					perView: 5,
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
				

				//alert('b');
				glidenovedades.mount()
				//alert('c');

		},100);

		}
	});


	}



	function mostrarCategorias() {

		window.addEventListener('scroll', function() {
			const divs = document.querySelectorAll('.myDivCategorias');
			const windowHeight = window.innerHeight;

			divs.forEach(div => {
				const divTop = div.getBoundingClientRect().top;

				if (divTop < windowHeight) {
					div.classList.add('opacity-100');
				} else {
					div.classList.remove('opacity-100');
				}
			});
		});


	}

</script>
</body>

</html>