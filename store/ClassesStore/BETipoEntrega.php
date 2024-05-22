<?php
class BETipoEntrega {    
    /*Atributos*/
    private $idTipoEntrega  = 0;
    private $nomTipoEntrega ="";
    private $codCourier ="";
    private $consideraciones ="";
    private $estatusTipoEntrega ="";


    private $idTipoEntregaUbic = 0;
    private $nomZona ="";
    private $nomCourier ="";
    private $costo = 0;
    private $tiempoEntrega ="";

    private $idDpto = 0;
    private $idProv = 0;
    private $idDist = 0;

    private $nameDpto = "";
    private $nameProv = "";
    private $nameDist = "";

    private $tipoEntrega ="";
    private $direccionElegida ="";
    private $eligioDireccionAnterior =0;

    public function getEligioDirecAnt(){
        return $this->eligioDireccionAnterior;
    }
    public function setEligioDirecAnt($elec){
        $this->eligioDireccionAnterior = $elec;
    }


    public function getDireccion(){
        return $this->direccionElegida;
    }
    public function setDireccion($direc){
        $this->direccionElegida = $direc;
    }


    public function getTipoEntrega(){
        return $this->tipoEntrega;
    }
    public function setTipoEntrega($tipoEnt){
        $this->tipoEntrega = $tipoEnt;
    }



    public function getNameDpto(){
        return $this->nameDpto;
    }
    public function setNameDpto($nam){
        $this->nameDpto = $nam;
    }

    public function getNameProv(){
        return $this->nameProv;
    }
    public function setNameProv($nam){
        $this->nameProv = $nam;
    }

    public function getNameDist(){
        return $this->nameDist;
    }
    public function setNameDist($nam){
        $this->nameDist = $nam;
    }



    public function getIdTipoEntregaUbic(){
        return $this->idTipoEntregaUbic;
    }
    public function setIdTipoEntregaUbic($id){
        $this->idTipoEntregaUbic = $id;
    }
    public function getNomZona(){
        return $this->nomZona;
    }
    public function setNomZona($id){
        $this->nomZona = $id;
    }


    public function getNomCourier(){
        return $this->nomCourier;
    }
    public function setNomCourier($id){
        $this->nomCourier = $id;
    }
    public function getCosto(){
        return $this->costo;
    }
    public function setCosto($id){
        $this->costo = $id;
    }


    public function getTiempoEntrega(){
        return $this->tiempoEntrega;
    }
    public function setTiempoEntrega($id){
        $this->tiempoEntrega = $id;
    }




    

    public function getIdTipoEntrega(){
        return $this->idTipoEntrega;
    }
    public function setIdTipoEntrega($id){
        $this->idTipoEntrega = $id;
    }

    public function getNomTipoEntrega(){
        return $this->nomTipoEntrega;
    }
    public function setNomTipoEntrega($name){
        $this->nomTipoEntrega = $name;
    }

    public function getCodCourier(){
        return $this->codCourier;
    }
    public function setCodCourier($codecourier){
        $this->codCourier = $codecourier;
    }

    public function getConsideraciones(){
        return $this->codCourier;
    }
    public function setConsideraciones($codecourier){
        $this->codCourier = $codecourier;
    }


    public function getIdDist(){
        return $this->idDist;
    }
    public function setIdDist($id){
        $this->idDist = $id;
    }

    public function getIdProv(){
        return $this->idProv;
    }
    public function setIdProv($id){
        $this->idProv = $id;
    }

    public function getIdDpto(){
        return $this->idDpto;
    }
    public function setIdDpto($id){
        $this->idDpto = $id;
    }

    
}
?>
