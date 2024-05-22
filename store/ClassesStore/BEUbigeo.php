<?php
class BEUbigeo {    
    /*Atributos*/
    private $idDpto = 0;
    private $nomDpto ="";
    private $estDpto ="";

    private $idProv = 0;
    private $nomProv ="";
    private $estProv ="";

    private $idDist = 0;
    private $nomDist ="";
    private $estDist ="";

    private $idProvSelectDefaul =0;
    private $idDistSelectDefaul =0;

    public function getIdProvSelectDefaul(){
        return $this->idProvSelectDefaul;
    }
    public function setIdProvSelectDefaul($id){
        $this->idProvSelectDefaul = $id;
    }
    public function getIdDistSelectDefaul(){
        return $this->idDistSelectDefaul;
    }
    public function setIdDistSelectDefaul($nom){
        $this->idDistSelectDefaul = $nom;
    }
    

    public function getIdDist(){
        return $this->idDist;
    }
    public function setIdDist($id){
        $this->idDist = $id;
    }
    public function getNomDist(){
        return $this->nomDist;
    }
    public function setNomDist($nom){
        $this->nomDist = $nom;
    }
    public function getEstDist(){
        return $this->estDist;
    }
    public function setEstDist($est){
        $this->estDist = $est;
    }


    public function getIdProv(){
        return $this->idProv;
    }
    public function setIdProv($id){
        $this->idProv = $id;
    }
    public function getNomProv(){
        return $this->nomProv;
    }
    public function setNomProv($nom){
        $this->nomProv = $nom;
    }
    public function getEstProv(){
        return $this->estProv;
    }
    public function setEstProv($est){
        $this->estProv = $est;
    }


    public function getIdDpto(){
        return $this->idDpto;
    }
    public function setIdDpto($id){
        $this->idDpto = $id;
    }
    public function getNomDpto(){
        return $this->nomDpto;
    }
    public function setNomDpto($nom){
        $this->nomDpto = $nom;
    }
    public function getEstDpto(){
        return $this->estDpto;
    }
    public function setEstDpto($est){
        $this->estDpto = $est;
    }




    
}
?>
