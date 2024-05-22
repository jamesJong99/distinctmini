<?php
session_start();
require_once("ClassesStore/Util.php");
require_once("ClassesStore/DASeo.php");
require_once("components/productoitemComponent.php");
require_once("ClassesStore/Util.php");
require_once("header.php");
require_once("ClassesStore/DASeo.php");
require_once("ClassesStore/DAPaginas.php");
$pagina = new DAPaginas();
$seo = new DASeo();
$util = new Util();
$funciono = false;
$codParametro = 5; // Cambia esto por el código de parámetro que quieras usar
$resultado = $util->obtenerParametroLista($codParametro, $funciono);
$valor = "";

foreach ($resultado as $fila) {
    // Accede a los valores de la fila aquí
    $valor = $fila['Valor'];
}

$content = $valor;
$header = new header();

$tituloseo = "Políticas de devolución";
$descripcionSeo = "Políticas de devolución de productos de calidad en Distinct - Tienda Online";

//obtner slug
$slug = "";
if (isset($_GET["slug"])) {
    $slug = $_GET["slug"];
}
$seo->obtenerSeoPorSlugYPagina($slug, "paginas.php");
$codigo =  $seo->getCodigo();
$pagina->obtenerPaginapoCodigo($codigo);

$paginahtml = $pagina->getHtmlpage();

$header->headerSet($seo->getTituloSeo(), $seo->getDescripcionSeo());

?>
<div class="max-w-screen-lg mx-auto px-4 sm:px-6 lg:px-8 my-4">
    <div id="paginadetalle">
        <?php echo $paginahtml; ?>
    </div>
</div>
<style>
    #paginadetalle {
        font-family: "Poppins-Light";
    }

    #paginadetalle h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #97847d;
        text-align: center;
    }

    #paginadetalle h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #8f6b60;
    }

    #paginadetalle p {
        text-align: justify;
        margin-bottom:15px;
    }

    #paginadetalle h3 {
        font-size: 1rem;
        font-weight: 700;
        color: #97847d;
    }
    #paginadetalle ul {
        list-style-type: circle;
        font-size: 1rem;
    }
    #paginadetalle li {
        margin-left: 1rem;
    }
</style>

<?php require('footer.php'); ?>
</body>

</html>