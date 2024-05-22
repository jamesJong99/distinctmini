<?php
class BECuponImagen {    
    /*Atributos*/
    private $codcupon;
    private $cuponpropio;
    private $link;
    private $nomEmpCupon;
    private $indicacionesCupon;
    private $nomimagen;

    /*20230625 Cambio de tabla Cupon*/
    private $idcupon = 0;
    private $tipodcto= 0;
    private $cantdcto = 0;
    private $tipoaplic = 0;
    private $valoraplicacion ="";
    private $montominimo = 0;
    private $cuponpublico = "";
    private $cuponnecesitacumplircond = "";

    public function getIdCupon(){
        return $this->idcupon;
    }
    public function setIdCupon($idecup){
        $this->idcupon = $idecup;
    }

    public function getTipoDcto(){
        return $this->tipodcto;
    }
    public function setTipoDcto($tipo){
        $this->tipodcto = $tipo;
    }

    public function getCantDcto(){
        return $this->cantdcto;
    }
    public function setCantDcto($cant){
        $this->cantdcto = $cant;
    }

    public function getTipoAplicacion(){
        return $this->tipoaplic;
    }
    public function setTipoAplicacion($tipoapli){
        $this->tipoaplic = $tipoapli;
    }

    public function getValorAplicacion(){
        return $this->valoraplicacion;
    }
    public function setValorAplicacion($valorapli){
        $this->valoraplicacion = $valorapli;
    }



    public function getMontoMinimo(){
        return $this->montominimo;
    }
    public function setMontoMinimo($minim){
        $this->montominimo = $minim;
    }

    public function getCuponPublico(){
        return $this->cuponpublico;
    }
    public function setCuponPublico($publico){
        $this->cuponpublico = $publico;
    }

    public function getCuponNecesitaCumplirCond(){
        return $this->cuponnecesitacumplircond;
    }
    public function setCuponNecesitaCumplirCond($necesita){
        $this->cuponnecesitacumplircond = $necesita;
    }
    
    
    /*metodos get and set*/
    public function getNomImagen(){
        return $this->nomimagen;
    }
    public function setNomImagen($nomimagen){
        $this->nomimagen = $nomimagen;
    }

    public function getIndicacionesCupon(){
        return $this->indicacionesCupon;
    }
    public function setIndicacionesCupon($indicacionesCupon){
        $this->indicacionesCupon = $indicacionesCupon;
    }

    public function getNomEmpCupon(){
        return $this->nomEmpCupon;
    }
    public function setNomEmpCupon($nomEmpCupon){
        $this->nomEmpCupon = $nomEmpCupon;
    }

    public function getLink(){
        return $this->link;
    }
    public function setLink($link){
        $this->link = $link;
    }

    public function getCuponPropio(){
        return $this->cuponpropio;
    }
    public function setCuponPropio($cuponpropio){
        $this->cuponpropio = $cuponpropio;
    }
  
    public function getCodCupon(){
        return $this->codcupon;
    }
    public function setCodCupon($codcupon){
        $this->codcupon = $codcupon;
    }
    
}
?>
