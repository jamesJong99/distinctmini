<?php
class BEPedido_Item {    
    /*Atributos*/
    private $numPed = 0;
    private $codProd ="";
    private $nomProd;
    private $variante;
    private $sku ="";
    private $cantidad = 0;
    private $precioProd =0;

    //20230610 Datos para cotizador 
    private $coditemvariante;
    private $coditem;
    private $nomitem;
    private $namevariante;
    private $colorvariante;
    private $ordenvariante;
    private $nomgrupovariante;
    private $tieneVariante;
    private $codItemElegido;

    //20240221 Stock
    private $stock;

    //20240306 Ahorro
    private $ahorro;

    public function getAhorro(){
        return $this->ahorro;
    }
    public function setAhorro($mntAhorr){
        $this->ahorro = $mntAhorr;
    }


    public function getStockItem(){
        return $this->stock;
    }
    public function setStockItem($code){
        $this->stock = $code;
    }



    public function getCodItemElegido(){
        return $this->codItemElegido;
    }
    public function setCodItemElegido($code){
        $this->codItemElegido = $code;
    }


    public function getCodItemVariante(){
        return $this->coditemvariante;
    }
    public function setCodItemVariante($code){
        $this->coditemvariante = $code;
    }

    public function getCodItem(){
        return $this->coditem;
    }
    public function setCodItem($code){
        $this->coditem = $code;
    }

    public function getNomItem(){
        return $this->nomitem;
    }
    public function setNomItem($name){
        $this->nomitem = $name;
    }

    public function getNameVariante(){
        return $this->namevariante;
    }
    public function setNameVariante($namevar){
        $this->namevariante = $namevar;
    }

    public function getColorVariante(){
        return $this->colorvariante;
    }
    public function setColorVariante($color){
        $this->colorvariante = $color;
    }

    public function getOrdenVariante(){
        return $this->ordenvariante;
    }
    public function setOrdenVariante($ord){
        $this->ordenvariante = $ord;
    }

    public function getNomGrupoVariante(){
        return $this->nomgrupovariante;
    }
    public function setNomGrupoVariante($name){
        $this->nomgrupovariante = $name;
    }

    public function getTieneVariante(){
        return $this->tieneVariante;
    }
    public function setTieneVariante($tieneVar){
        $this->tieneVariante = $tieneVar;
    }

    




    public function getPrecio(){
        return $this->precioProd;
    }
    public function setPrecio($precio){
        $this->precioProd = $precio;
    }

    public function getCantidad(){
        return $this->cantidad;
    }
    public function setCantidad($cant){
        $this->cantidad = $cant;
    }

    public function getSku(){
        return $this->sku;
    }
    public function setSku($codsku){
        $this->sku = $codsku;
    }

    public function getVariante(){
        return $this->variante;
    }
    public function setVariante($varian){
        $this->variante = $varian;
    }

    public function getNomProd(){
        return $this->nomProd;
    }
    public function setNomProd($nombreprod){
        $this->nomProd = $nombreprod;
    }

    public function getCodProd(){
        return $this->codProd;
    }
    public function setCodProd($codigoprod){
        $this->codProd = $codigoprod;
    }

    public function getNumPed(){
        return $this->numPed;
    }
    public function setNumPed($numeropedido){
        $this->numPed = $numeropedido;
    }

    
}
?>
