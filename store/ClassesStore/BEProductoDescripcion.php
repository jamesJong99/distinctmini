<?php
class BEProductoDescripcion {    
    /*Atributos*/
    private $codProd;
    private $resumen;
    private $loQueAmaras;
    private $descripcion;
    private $ingredientesEstrella;
    private $modoUso;
    private $ingredientes;
    private $preguntasFrecuentes;
    

    public function getPreguntasFrecuentes(){
        return $this->preguntasFrecuentes;
    }
    public function setPreguntasFrecuentes($preg){
        $this->preguntasFrecuentes = $preg;
    }

    public function getIngredientesTotal(){
        return $this->ingredientes;
    }
    public function setIngredientesTotal($ing){
        $this->ingredientes = $ing;
    }

    public function getModoUso(){
        return $this->modoUso;
    }
    public function setModoUso($modo){
        $this->modoUso = $modo;
    }

    public function getIngredientesEstrella(){
        return $this->ingredientesEstrella;
    }
    public function setIngredientesEstrella($ingEstrella){
        $this->ingredientesEstrella = $ingEstrella;
    }

    public function getDescripcion(){
        return $this->descripcion;
    }
    public function setDescripcion($desc){
        $this->descripcion = $desc;
    }

    public function getLoQueAmaras(){
        return $this->loQueAmaras;
    }
    public function setLoQueAmaras($amaras){
        $this->loQueAmaras = $amaras;
    }

    public function getResumen(){
        return $this->resumen;
    }
    public function setResumen($resum){
        $this->resumen = $resum;
    }

    public function getCodProd(){
        return $this->codProd;
    }
    public function setCodProd($codigo){
        $this->codProd = $codigo;
    }
    
}
?>
