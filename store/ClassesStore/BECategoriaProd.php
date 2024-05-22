<?php


class BECategoriaProd {    
    /*Atributos*/
    private $IdCategoria =0;
    private $NomCategoria ="";
    private $TipoFotoVideo ="";
    private $RutaFotoCategoria ="";
    private $DescripcionCategoria ="";

    

    public function getIdCategoria(){
        return $this->IdCategoria;
    }
    public function setIdCategoria($ide){
        $this->IdCategoria = $ide;
    }
    public function getNomCategoria(){
        return $this->NomCategoria;
    }
    public function setNomCategoria($nomb){
        $this->NomCategoria = $nomb;
    }

    public function getTipoFotoVideo(){
        return $this->TipoFotoVideo;
    }
    public function setTipoFotoVideo($tipe){
        $this->TipoFotoVideo = $tipe;
    }

    public function getRutaFotoCategoria(){
        return $this->RutaFotoCategoria;
    }
    public function setRutaFotoCategoria($ruta){
        $this->RutaFotoCategoria = $ruta;
    }

    public function getDescCategoria(){
        return $this->DescripcionCategoria;
    }
    public function setDescCategoria($desc){
        $this->DescripcionCategoria = $desc;
    }
    
    
}
?>
