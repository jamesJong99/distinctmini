<?php

class BEPedidoLog {    
    /*Atributos*/

    private $idPedidolog;
    private $numPed;
    private $codAccion;
    private $descripcion;
    private $codUsuario;
    private $nomUsuario;
    private $fecHora;
   
    public function getIdPedidoLog(){
        return $this->idPedidolog;
    }
    public function setIdPedidoLog($id){
        $this->idPedidolog = $id;
    }

    public function getNumPed(){
        return $this->numPed;
    }
    public function setNumPed($num){
        $this->numPed = $num;
    }

    public function getCodAccion(){
        return $this->codAccion;
    }
    public function setCodAccion($cod){
        $this->codAccion = $cod;
    }

    public function getDescripcion(){
        return $this->descripcion;
    }
    public function setDescripcion($desc){
        $this->descripcion = $desc;
    }

    public function getCodUsuario(){
        return $this->codUsuario;
    }
    public function setCodUsuario($codusu){
        $this->codUsuario = $codusu;
    }

    public function getNomUsuario(){
        return $this->nomUsuario;
    }
    public function setNomUsuario($nomusu){
        $this->nomUsuario = $nomusu;
    }

    public function getFecHora(){
        return $this->fecHora;
    }
    public function setFecHora($fec){
        $this->fecHora = $fec;
    }


}
?>
