<?php
class BEPedidoItemDet {    
    /*Atributos*/
    private $numPed = 0;
    private $codItem ="";
    private $nomItem ="";
    private $cantidad =0;


    public function getNumPed(){
        return $this->numPed;
    }
    public function setNumPed($numeroped){
        $this->numPed = $numeroped;
    }


    public function getCodItem(){
        return $this->codItem;
    }
    public function setCodItem($codigoitem){
        $this->codItem = $codigoitem;
    }


    public function getNomItem(){
        return $this->nomItem;
    }
    public function setNomItem($nombreitem){
        $this->nomItem = $nombreitem;
    }


    public function getCantidad(){
        return $this->cantidad;
    }
    public function setCantidad($cant){
        $this->cantidad = $cant;
    }
  
    
}
?>
