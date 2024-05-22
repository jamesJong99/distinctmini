<?php
class BEProductoFoto {    
    /*Atributos*/
    private $idFoto;
    private $codProd;
    private $ordenFoto;
    private $rutaFoto;
    private $fotoPrincipal;

    public function getFotoPrincipal(){
        return $this->fotoPrincipal;
    }
    public function setFotoPrincipal($principal){
        $this->fotoPrincipal = $principal;
    }

    public function getRutaFoto(){
        return $this->rutaFoto;
    }
    public function setRutaFoto($photo){
        $this->rutaFoto = $photo;
    }

    public function getOrdenFoto(){
        return $this->ordenFoto;
    }
    public function setOrdenFoto($order){
        $this->ordenFoto = $order;
    }

    public function getCodProd(){
        return $this->codProd;
    }
    public function setCodProd($codigo){
        $this->codProd = $codigo;
    }
    
    public function getIdFoto(){
        return $this->idFoto;
    }
    public function setIdFoto($id){
        $this->idFoto = $id;
    }
    
}
?>
