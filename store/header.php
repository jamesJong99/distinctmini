<?php require_once('components/headerComponents/headComponent.php');

class header extends headComponent
{


    public function headerSet($titlePage, $descripcionPage)
    {
        $header = new  headComponent();
        $title = "Distinct - Tienda Online";
        $descripcion = "Tienda online de productos de calidad";
        if ($titlePage != "") {
            $title = $titlePage;
        }
        if ($descripcionPage != "") {
            $descripcion = $descripcionPage;
        }
        $header->head($title, $descripcion);
?>

        <body>
            <?php require_once('components/headerComponents/headerComponent.php'); ?>
        
<?php
    }
}

?>