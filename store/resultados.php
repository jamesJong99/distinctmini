<?php
session_start();
require_once("ClassesStore/configuracionBD.php");
require_once("ClassesStore/BEProducto.php");
require_once("ClassesStore/DAProducto.php");
require_once("components/productoitemComponent.php");
require_once("header.php");

$header = new header();

//llamar productos
$productoItem = new productoitem();

//obtener numero de paginacion
$pagina = "";
if (isset($_GET["pagina"])) {
	$pagina = $_GET["pagina"];
}

if ($pagina == "") {
	$pagina = 1;
}

$uploadPath = RUTAFOTOPROD;
$prodSinFotoPortada = PRODSINFOTO;

$postBuscar = "";

if (isset($_GET["q"])) {
	$postBuscar = $_GET["q"];
}


$listaBEProductos = array();
$TieneProducto = 0;
$DAProducto = new DAProducto();
$funcionoBusq = 0;

if ($postBuscar != "") {
	$postBuscar = substr($postBuscar, 0, 50);
	$rptaListado = $DAProducto->buscarProductoPorNombre($postBuscar, $funcionoBusq);

	if ($funcionoBusq == -1) {
		echo "Error en procedimiento buscar productos";
	}
	if ($funcionoBusq == 1) {

		while ($fila = $rptaListado->fetch()) {
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

			$listaBEProductos[$CodProd] = $BEProducto;
			$TieneProducto = 1;
		}
	}
}

//echo "TieneProducto ($TieneProducto); ";
//print_r($listaBEProductos);
//echo $postBuscar;

$title = "Resultados de búsqueda " . $postBuscar . " - Distinct Store";
$descripcion = "Resultados de búsqueda de " . $postBuscar;

$header->headerSet($title, $descripcion);
?>


<section class="max-w-screen-xl m-auto my-5">

	<h1 class="font-sans text-center text-lg sm:text-2xl mb-5 mt-5 text-[#97847d] font-semibold">Resúltado de búsqueda: <?php echo $postBuscar; ?> </h1>

	<?php


	?>
	<?php if ($TieneProducto == 1) { ?>
		<div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
			<?php $productoItem->obtenerListaproductoPaginacion($listaBEProductos, $pagina); ?>
		</div>
		<!-- paginacion -->
		<?php
		$paginacion = $productoItem->obtenerPaginacion();
		$elementosPorPagina = $paginacion;
		$inicio = ($pagina - 1) * $elementosPorPagina;
		$fin = $pagina * $elementosPorPagina;
		$contador = 0;
		$numeroPaginas = 0;
		$numeroPaginas = count($listaBEProductos) / $elementosPorPagina;
		$numeroPaginas = ceil($numeroPaginas);
		$paginaAnterior = $pagina - 1;
		$paginaSiguiente = $pagina + 1;
		$urlCategoria =  BASE_URL_STORE . 'buscar/pagina';
		$q = $_GET['q'];
		?>
		<div class="flex justify-center gap-3 my-6">
			<?php
			if ($pagina > 1) {
				echo '<a href="' . $urlCategoria . '/' . $paginaAnterior . '?q=' . urlencode($q) . '" class="text-[#97847d] hover:text-[#ae978f]">Anterior</a>';
			}
			?>
			<?php
			for ($i = 1; $i <= $numeroPaginas; $i++) {
				if ($i == $pagina) {
					echo '<a href="' . $urlCategoria . '/' . $i . '?q=' . urlencode($q) . '" class="text-[#97847d] hover:text-[#ae978f] font-bold">' . $i . '</a>';
				} else {
					echo '<a href="' . $urlCategoria . '/' . $i . '?q=' . urlencode($q) . '" class="text-[#97847d] hover:text-[#ae978f]">' . $i . '</a>';
				}
			}
			?>
			<?php
			if ($pagina < $numeroPaginas) {
				echo '<a href="' . $urlCategoria . '/' . $paginaSiguiente . '?q=' . urlencode($q) . '" class="text-[#97847d] hover:text-[#ae978f]">Siguiente</a>';
			}
			?>
		</div>
	<?php } //fin if ($tienePedidos==1) { 
	else {
		echo "<p class='MensajeError' > No se encontraron resultados.</p>";
	}

	?>
</section>

<?php
require('footer.php');
?>


<script type="text/javascript" charset="utf-8">
	//Fancy Lightbox
	$(document).ready(function() {
		$(".fancybox").fancybox();
	});
</script>


</body>

</html>