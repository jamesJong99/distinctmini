<?php
session_start();
require_once("ClassesStore/configuracionBD.php");
require_once("ClassesStore/Util.php");
require_once("ClassesStore/BEProducto.php");
require_once("ClassesStore/DAProducto.php");
require_once("ClassesStore/BECategoriaProd.php");
require_once("ClassesStore/DACategoriaProd.php");
require_once("ClassesStore/DASeo.php");
require_once("components/productoitemComponent.php");
require_once("header.php");
$header = new header();

$productoItem = new productoitem();

$urlCategoria ="";

//obtner numero de paginacion
$pagina = "";
if (isset($_GET["pagina"])) {
	$pagina = $_GET["pagina"];
}

if ($pagina == "") {
	$pagina = 1;
}

$slug = "";
if (isset($_GET["slug"])) {
	$slug = $_GET["slug"];
}
$seo = new DASeo();
$seo->obtenerSeoPorSlugYPagina($slug, "categoria.php");

if ($seo->getCodigo() == null) {
	echo "No se encontró una categoría con el slug proporcionado.";
	exit;
}

$uploadPath = RUTAFOTOPROD;
$prodSinFotoPortada = PRODSINFOTO;

$idCategoria = $seo->getCodigo();

if (isset($_POST["id"])) {
	$idCategoria = $_POST["id"];
}

$idOrdenar = 1;
if (isset($_POST["idOrdenar"])) {
	$idOrdenar = $_POST["idOrdenar"];
}

//echo "idOrdenar: ($idOrdenar)";

//echo $mensajeInicio;




//Obtener Categoría de Productos
$DACategoriaProd = new DACategoriaProd();
$BECategoriaProd = new BECategoriaProd();
$funcionoBuscarCategoria = 0;

$rptaObtenerCategoriaProd = $DACategoriaProd->obtenerCategoria($idCategoria, $funcionoBuscarCategoria);

if ($funcionoBuscarCategoria == -1) {
	echo "Error en procedimiento obtener datos de categoría";
}
if ($funcionoBuscarCategoria == 1) {

	while ($fila = $rptaObtenerCategoriaProd->fetch()) {
		$BECategoriaProd->setIdCategoria($idCategoria);
		$BECategoriaProd->setNomCategoria($fila["NomCategoria"]);
		$BECategoriaProd->setTipoFotoVideo($fila["TipoFotoVideo"]);
		$BECategoriaProd->setRutaFotoCategoria($fila["RutaFotoCategoria"]);
		$BECategoriaProd->setDescCategoria($fila["DescCategoria"]);
	}
}


//print_r($BECategoriaProd);



$DAProducto = new DAProducto();

//Obtener productos de lista 
$tipoBusqCategoriaFavoritos = 2;
$listaBEProductosCategoriaFavoritos = array();
$TieneProductoCategoriaFavoritos = 0;
$funcionoBusqCategoriaFavoritos = 0;

if ($idCategoria == 22) {
	$rptaCategoriaFavoritos = $DAProducto->obtenerProductos($tipoBusqCategoriaFavoritos, $idOrdenar, $funcionoBusqCategoriaFavoritos);
} else {

	$rptaCategoriaFavoritos = $DAProducto->obtenerProductosCategoria($idCategoria, $tipoBusqCategoriaFavoritos, $idOrdenar, $funcionoBusqCategoriaFavoritos);
}

if ($funcionoBusqCategoriaFavoritos == -1) {
	echo "Error en procedimiento obtener productos categoría";
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

//print_r($listaBEProductosCategoriaFavoritos);


$header->headerSet($seo->getTituloSeo(), $seo->getDescripcionSeo());
?>
<section class="max-w-screen-xl m-auto">
	
<?php
	//Se muestra el banner con excepción de la categoría de todos los productos
	if ($idCategoria != 10) { ?>

			<div class="bg-[#D0B0B3] py-1">
				
			<img class="w-full h-auto object-contain max-h-32" src="<?php echo BASE_URL_STORE ?>
			imagenes/Categorias/<?php echo $BECategoriaProd->getRutaFotoCategoria() ?>" alt="Foto de Categoría">

			</div>
			
			
		
	<?php
	}
	?>
	
		

	<?php

if ($BECategoriaProd->getDescCategoria() != "" and $TieneProductoCategoriaFavoritos == 1) {
?>

	<p class="mt-5 text-left text-base">
		<?php

		echo $BECategoriaProd->getDescCategoria();

		?>
	</p>

	<?php

	} //fin if ($tienePedidos==1) { 

?>
</section>
<?php

if ($TieneProductoCategoriaFavoritos == 1) { ?>
	<section class="max-w-screen-xl m-auto mt-2">
		<input type="hidden" name="idCategoria" id="idCategoria" value="<?php echo $idCategoria; ?>">
		<?php

		$select1 = "";
		$select2 = "";
		$select3 = "";

		if ($idOrdenar == 1) {
			$select1 = 'selected';
		}
		if ($idOrdenar == 2) {
			$select2 = 'selected';
		}
		if ($idOrdenar == 3) {
			$select3 = 'selected';
		}

		//echo "idOrdenar ($idOrdenar) select1 ($select1) select2 ($select2) select3 ($select3)";
		?>
		<select onchange="ordenar()" name="orden" id="orden" class="mb-5 h-auto p-4 rounded-md border-2 bg-transparent text-black focus:ring-2  focus:ring-inset focus:ring-[#97847d] sm:text-sm ml-auto block">
			<?php
			echo '<option value="1" ' . $select1 . '>Prioridad</option>';
			echo '<option value="2" ' . $select2 . '>De menor a mayor precio</option>';
			echo '<option value="3" ' . $select3 . '>De mayor a menor precio</option>';

			echo '</select >'; ?>
	</section>

	<section class="max-w-screen-xl m-auto ">

		<div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
			<?php $productoItem->obtenerListaproductoPaginacion($listaBEProductosCategoriaFavoritos, $pagina); ?>
		</div>
		<!-- paginacion -->
		<?php
		$paginacion = $productoItem->obtenerPaginacion();
		$elementosPorPagina = $paginacion;
		$inicio = ($pagina - 1) * $elementosPorPagina;
		$fin = $pagina * $elementosPorPagina;
		$contador = 0;
		$numeroPaginas = 0;
		$numeroPaginas = count($listaBEProductosCategoriaFavoritos) / $elementosPorPagina;
		$numeroPaginas = ceil($numeroPaginas);
		$paginaAnterior = $pagina - 1;
		$paginaSiguiente = $pagina + 1;
		$urlCategoria =  BASE_URL_STORE . 'categoria/' . $slug;
		?>
		<div class="flex justify-center gap-3 my-6">
			<?php
			if ($pagina > 1) {
				echo '<a href="' . $urlCategoria . '/pagina/' . $paginaAnterior . '" class="text-[#97847d] hover:text-[#ae978f]">Anterior</a>';
			}
			?>
			<?php
			for ($i = 1; $i <= $numeroPaginas; $i++) {
				if ($i == $pagina) {
					echo '<a href="' . $urlCategoria . '/pagina/' . $i . '" class="text-[#97847d] hover:text-[#ae978f] font-bold">' . $i . '</a>';
				} else {
					echo '<a href="' . $urlCategoria . '/pagina/' . $i . '" class="text-[#97847d] hover:text-[#ae978f]">' . $i . '</a>';
				}
			}
			?>
			<?php
			if ($pagina < $numeroPaginas) {
				echo '<a href="' . $urlCategoria . '/pagina/' . $paginaSiguiente . '" class="text-[#97847d] hover:text-[#ae978f]">Siguiente</a>';
			}
			?>
	</section>
<?php
} else {
	echo "<p class='max-w-screen-xl m-auto MensajeError' > No se encontraron resultados.</p>";
}
?>

<?php
require('footer.php');
?>



<script type="text/javascript" charset="utf-8">
	function ordenar() {
		var idOrdenar = document.getElementById("orden").value;
		var idCategoria = document.getElementById("idCategoria").value;


		$.redirect("<?php echo $urlCategoria ?>", {
			"id": idCategoria,
			"idOrdenar": idOrdenar
		}, "POST");


	}
</script>


<script type="text/javascript" charset="utf-8">
	//Fancy Lightbox
	$(document).ready(function() {
		$(".fancybox").fancybox();
	});
</script>


</body>

</html>