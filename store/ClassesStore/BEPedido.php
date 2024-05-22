<?php
require_once("ClassesStore/BEPedidoItem.php");
require_once("ClassesStore/BECliente.php");

class BEPedido {    
    /*Atributos*/
    private $codCliente =0;
    private $nomCliente ="";
    private $tipoDoc =0;
    private $numDoc ="";
    private $numPed =0;
    private $fechaPedido;
    private $horaPedido;
    private $email;
    private $nota ="";
    
    
    private $metodoEnvio;
    private $celular;
    private $paisDelivery;
    private $ciudadDelivery;
    private $dptoDelivery;
    private $direccionDelivery;

    private $estadoPago;
    private $metodoPago;
    private $cupon;

    private $montoEnvioPedido;
    private $estadoEntrega;
    private $montoPedido;
    private $estatusRegistro ="PorRegistrar";

    //2022.10.10 Tendra un listado de entidades del tipo BEPedidoItemDet. Para la tabla PedidoItemDet
    public $listadoItemDetalle = array();

    //Tendra un listado de entidades del tipo BEPedidoItem
    public $listadoItem = array();
    //Tendra una entidad del tipo BECliente
    public $clienteRelac;
    //Tendra una entidad del tipo BEPedidoEnvio
    public $envioRelac;
    public $TieneEnvioRelac = 0;
    //Tendra un listado de entidades del tipo BEPedidoLog
    public $listadoLog = array();


    /*Campos para control de la aplicación. El campo estadoentrega y estadopago son variables que vienen de Wix */
    private $statusentrega ="";
    private $descStatusentrega;
    private $descStatusentregaProxPaso;
    private $descStatusPostVenta;
    private $statusPostVenta;
    private $fecPago;
    private $fecEntCliente;
    private $fecReEntCliente;


    private $fecPostVenta;
    private $comentarioPostVenta;
    private $fecPostVenta2;
    private $comentarioPostVenta2;

    /*20230626 Incluir cupón */
    private $montoProducto;
    private $montoDctoCupon;
    private $valorDctoCupon;

    public function getMontoProducto(){
        return $this->montoProducto;
    }
    public function setMontoProducto($montProd){
        $this->montoProducto = $montProd;
    }
    
    public function getMontoDctoCupon(){
        return $this->montoDctoCupon;
    }
    public function setMontoDctoCupon($mntDctCpn){
        $this->montoDctoCupon = $mntDctCpn;
    }

    public function getValorDctoCupon(){
        return $this->valorDctoCupon;
    }
    public function setValorDctoCupon($valor){
        $this->valorDctoCupon = $valor;
    }



    /*20230606 Añadir nuevas columnas */
    private $codcanalventa ="";
    private $nomcanalventa ="";
    private $esCanalDigital ="";
    private $codusuarioregistro="";
    private $sistemaregpedido ="";

    public function getSistemaRegPedido(){
        return $this->sistemaregpedido;
    }
    public function setSistemaRegPedido($sistemareg){
        $this->sistemaregpedido = $sistemareg;
    }
    
    public function getEsCanalDigital(){
        return $this->esCanalDigital;
    }
    public function setEsCanalDigital($esDigital){
        $this->esCanalDigital = $esDigital;
    }
    public function getNomCanalVenta(){
        return $this->nomcanalventa;
    }
    public function setNomCanalVenta($namecanal){
        $this->nomcanalventa = $namecanal;
    }
    public function getCodCanalVenta(){
        return $this->codcanalventa;
    }
    public function setCodCanalVenta($codcanal){
        $this->codcanalventa = $codcanal;
    }
    public function getCodUsuarioRegistro(){
        return $this->codusuarioregistro;
    }
    public function setCodUsuarioRegistro($codusuario){
        $this->codusuarioregistro = $codusuario;
    }


    /*20230927 Añadir columnas de condición en la piel y cuenta de redes sociales */
    private $codCondPiel = 0;
    private $nomCondPiel ="";
    private $comentCondPiel = "";
    private $cuentaIG = "";
    private $cuentaFB = "";
    private $cuentaTiktok = "";


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



    public function getFecPostVenta2(){
        return $this->fecPostVenta2;
    }
    public function setFecPostVenta2($fecha){
        $this->fecPostVenta2 = $fecha;
    }
    public function getComentarioPostVenta2(){
        return $this->comentarioPostVenta2;
    }
    public function setComentarioPostVenta2($comen){
        $this->comentarioPostVenta2 = $comen;
    }



    public function getFecPostVenta(){
        return $this->fecPostVenta;
    }
    public function setFecPostVenta($fecha){
        $this->fecPostVenta = $fecha;
    }

    
    public function getComentarioPostVenta(){
        return $this->comentarioPostVenta;
    }
    public function setComentarioPostVenta($comen){
        $this->comentarioPostVenta = $comen;
    }


    public function getDescStatusPostVenta(){
        return $this->descStatusPostVenta;
    }
    public function setDescStatusPostVenta($descrip){
        $this->descStatusPostVenta = $descrip;
    }


    public function getDescStatusEntregaProxPaso(){
        return $this->descStatusentregaProxPaso;
    }
    public function setDescStatusEntregaProxPaso($descrip){
        $this->descStatusentregaProxPaso = $descrip;
    }


    public function getDescStatusEntrega(){
        return $this->descStatusentrega;
    }
    public function setDescStatusEntrega($desc){
        $this->descStatusentrega = $desc;
    }

    
    public function getStatusEntrega(){
        return $this->statusentrega;
    }
    public function setStatusEntrega($estatus){
        $this->statusentrega = $estatus;
    }

    public function getStatusPostVenta(){
        return $this->statusPostVenta;
    }
    public function setStatusPostVenta($estatus){
        $this->statusPostVenta = $estatus;
    }

    public function getFecPago(){
        return $this->fecPago;
    }
    public function setFecPago($fecha){
        $this->fecPago = $fecha;
    }

    public function getFecEntCliente(){
        return $this->fecEntCliente;
    }
    public function setFecEntCliente($fecha){
        $this->fecEntCliente = $fecha;
    }

    public function getFecReEntCliente(){
        return $this->fecReEntCliente;
    }
    public function setFecReEntCliente($fecha){
        $this->fecReEntCliente = $fecha;
    }


    

    public function getDptoDelivery(){
        return $this->dptoDelivery;
    }
    public function setDptoDelivery($dpto){
        $this->dptoDelivery = $dpto;
    }

    public function getEstatusRegistro(){
        return $this->estatusRegistro;
    }
    public function setEstatusRegistro($estatus){
        $this->estatusRegistro = $estatus;
    }


    public function getMontoPedido(){
        return $this->montoPedido;
    }
    public function setMontoPedido($monto){
        $this->montoPedido = $monto;
    }

    public function getEstadoEntrega(){
        return $this->estadoEntrega;
    }
    public function setEstadoEntrega($estEntrega){
        $this->estadoEntrega = $estEntrega;
    }

    public function getMontoEnvioPed(){
        return $this->montoEnvioPedido;
    }
    public function setMontoEnvioPed($mtoEnvioPed){
        $this->montoEnvioPedido = $mtoEnvioPed;
    }

    public function getCupon(){
        return $this->cupon;
    }
    public function setCupon($copun){
        $this->cupon = $copun;
    }

    public function getMetodoPago(){
        return $this->metodoPago;
    }
    public function setMetodoPago($metPago){
        $this->metodoPago = $metPago;
    }

    public function getEstadoPago(){
        return $this->estadoPago;
    }
    public function setEstadoPago($estPago){
        $this->estadoPago = $estPago;
    }

    public function getDireccionDelivery(){
        return $this->direccionDelivery;
    }
    public function setDireccionDelivery($direccion){
        $this->direccionDelivery = $direccion;
    }

    public function getCiudadDelivery(){
        return $this->ciudadDelivery;
    }
    public function setCiudadDelivery($ciudad){
        $this->ciudadDelivery = $ciudad;
    }

    public function getPaisDelivery(){
        return $this->paisDelivery;
    }
    public function setPaisDelivery($pais){
        $this->paisDelivery = $pais;
    }

    public function getCelular(){
        return $this->celular;
    }
    public function setCelular($celu){
        $this->celular = $celu;
    }

    public function getMetodoEnvio(){
        return $this->metodoEnvio;
    }
    public function setMetodoEnvio($metEnvio){
        $this->metodoEnvio = $metEnvio;
    }

    

    public function getNota(){
        return $this->nota;
    }
    public function setNota($note){
        $this->nota = $note;
    }

    public function getEmail(){
        return $this->email;
    }
    public function setEmail($mail){
        $this->email = $mail;
    }

    public function getHoraPedido(){
        return $this->horaPedido;
    }
    public function setHoraPedido($horPedido){
        $this->horaPedido = $horPedido;
    }

    public function getFechaPedido(){
        return $this->fechaPedido;
    }
    public function setFechaPedido($fecpedido){
        $this->fechaPedido = $fecpedido;
    }

    public function getNumPed(){
        return $this->numPed;
    }
    public function setNumPed($numeropedido){
        $this->numPed = $numeropedido;
    }



    public function getTipoDoc(){
        return $this->tipoDoc;
    }
    public function setTipoDoc($tipx){
        $this->tipoDoc = $tipx;
    }


    public function getNumDoc(){
        return $this->numDoc;
    }
    public function setNumDoc($numero){
        $this->numDoc = $numero;
    }


    public function getNomCliente(){
        return $this->nomCliente;
    }
    public function setNomCliente($nombreCliente){
        $this->nomCliente = $nombreCliente;
    }

    public function getCodCliente(){
        return $this->codCliente;
    }
    public function setCodCliente($codigocliente){
        $this->codCliente = $codigocliente;
    }


    /*20240302 Ahorro Total*/
    private $ahorroTotal = 0;

    public function getAhorroPedido(){
        return $this->ahorroTotal;
    }

    public function setAhorroPedido($ahorro){
        $this->ahorroTotal = $ahorro;
    }

    /*2024425 Ahorro detallado*/
    private $ahorroProductos = 0;
    private $ahorroEnvioGratis = 0;
    private $ahorroCupon = 0;
    private $tieneEnvioGratis ="";

    public function getAhorroProductos(){
        return $this->ahorroProductos;
    }

    public function setAhorroProductos($ahorro){
        $this->ahorroProductos = $ahorro;
    }

    public function getAhorroEnvioGratis(){
        return $this->ahorroEnvioGratis;
    }

    public function setAhorroEnvioGratis($ahorro){
        $this->ahorroEnvioGratis = $ahorro;
    }

    public function getAhorroCupon(){
        return $this->ahorroCupon;
    }

    public function setAhorroCupon($ahorro){
        $this->ahorroCupon = $ahorro;
    }

    public function getTieneEnvioGratis(){
        return $this->tieneEnvioGratis;
    }

    public function setTieneEnvioGratis($tiene){
        $this->tieneEnvioGratis = $tiene;
    }


}
?>
