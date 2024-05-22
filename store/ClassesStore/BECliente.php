<?php
class BECliente {    
    /*Atributos*/
    private $tieneCodCliente =0;
    private $codCliente;
    private $apeNom;
    private $tipoDoc =1;
    private $nomTipoDoc ="";
    private $numDoc;
    private $email;
    private $celular;

    /*20240122 Fecha de Cumpleaños */
    private $fecnacimiento ="";

    public function getFecNacimiento(){
        return $this->fecnacimiento;
    }
    public function setFecNacimiento($fec){
        $this->fecnacimiento = $fec;
    }


    //Para busqueda
    private $buscarnom0;
    private $buscarnom1;
    private $buscarnom2;
    private $buscarnom3;
    private $buscarnom4;

    /*20230927 Añadir columnas de condición en la piel y cuenta de redes sociales */
    private $codCondPiel = 0;
    private $nomCondPiel ="";
    private $comentCondPiel = "";
    private $cuentaIG = "";
    private $cuentaFB = "";
    private $cuentaTiktok = "";
    private $numpedBuscar ="";

    public function getCodCondPiel(){
        return $this->codCondPiel;
    }
    public function setCodCondPiel($codCond){
        $this->codCondPiel = $codCond;
    }

    public function getNomCondPiel(){
        return $this->nomCondPiel;
    }
    public function setNomCondPiel($nomCond){
        $this->nomCondPiel = $nomCond;
    }



    public function getComentCondPiel(){
        return $this->comentCondPiel;
    }
    public function setComentCondPiel($comenCond){
        $this->comentCondPiel = $comenCond;
    }

    public function getCtaIG(){
        return $this->cuentaIG;
    }
    public function setCtaIG($ctaIG){
        $this->cuentaIG = $ctaIG;
    }

    public function getCtaFB(){
        return $this->cuentaFB;
    }
    public function setCtaFB($ctaFb){
        $this->cuentaFB = $ctaFb;
    }

    public function getCtaTiktok(){
        return $this->cuentaTiktok;
    }
    public function setCtaTiktok($ctaTik){
        $this->cuentaTiktok = $ctaTik;
    }

    public function getNumPedBuscar(){
        return $this->numpedBuscar;
    }
    public function setNumPedBuscar($num){
        $this->numpedBuscar = $num;
    }
    

    //2023-07-11 Incluir fidelización con Rosas y Puntos
    private $rosasActuales;
    private $puntosActuales;
    private $fecActPuntos;


    public function getRosasActuales(){
        return $this->rosasActuales;
    }
    public function setRosasActuales($rosasAct){
        $this->rosasActuales = $rosasAct;
    }

    public function getPuntosActuales(){
        return $this->puntosActuales;
    }
    public function setPuntosActuales($puntosact){
        $this->puntosActuales = $puntosact;
    }

    public function getFecActualizoPuntos(){
        return $this->fecActPuntos;
    }
    public function setFecActualizoPuntos($fec){
        $this->fecActPuntos = $fec;
    }


    public function getBuscarNom0(){
        return $this->buscarnom0;
    }
    public function setBuscarNom0($nom){
        $this->buscarnom0 = $nom;
    }
    
    public function getBuscarNom1(){
        return $this->buscarnom1;
    }
    public function setBuscarNom1($nom){
        $this->buscarnom1 = $nom;
    }

    public function getBuscarNom2(){
        return $this->buscarnom2;
    }
    public function setBuscarNom2($nom){
        $this->buscarnom2 = $nom;
    }

    public function getBuscarNom3(){
        return $this->buscarnom3;
    }
    public function setBuscarNom3($nom){
        $this->buscarnom3 = $nom;
    }

    public function getBuscarNom4(){
        return $this->buscarnom4;
    }
    public function setBuscarNom4($nom){
        $this->buscarnom4 = $nom;
    }


    
    public function getNomTipoDocumento(){
        return $this->nomTipoDoc;
    }
    public function setNomTipoDocumento($nomTip){
        $this->nomTipoDoc = $nomTip;
    }

    public function getTieneCodCliente(){
        return $this->tieneCodCliente;
    }
    public function setTieneCodCliente($tienecod){
        $this->tieneCodCliente = $tienecod;
    }

    public function getApeNom(){
        return $this->apeNom;
    }
    public function setApeNom($apellidonom){
        $this->apeNom = $apellidonom;
    }

    public function getEmail(){
        return $this->email;
    }
    public function setEmail($mail){
        $this->email = $mail;
    }

    public function getCelular(){
        return $this->celular;
    }
    public function setCelular($celu){
        $this->celular = $celu;
    }

    public function getNumDoc(){
        return $this->numDoc;
    }
    public function setNumDoc($numerodocumento){
        $this->numDoc = $numerodocumento;
    }
    
    public function getTipoDoc(){
        return $this->tipoDoc;
    }
    public function setTipoDoc($tipodocumento){
        $this->tipoDoc = $tipodocumento;
    }

    public function getCodCliente(){
        return $this->codCliente;
    }
    public function setCodCliente($codigocliente){
        $this->codCliente = $codigocliente;
    }

    
}
?>
