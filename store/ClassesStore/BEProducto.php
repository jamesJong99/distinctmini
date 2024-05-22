<?php
class BEProducto {    
    /*Atributos*/
    private $codProd;
    private $nomProd;
    private $segunoNombre;
    private $esPack;
    private $precio;
    private $dctoNormal;
    private $precioNormal;
    private $dctoCyber;
    private $precioCyber;
    private $mostrarWeb;
    private $rutaFotoPortada ="";

    //20230610 Manejo de Precio actual para cotizador para canal de venta
    //Tendra un listado de entidades del tipo BEPedidoItem
    public $listadoItem = array();

    //20230610 Manejo de Precio actual para cotizador para canal de venta
    private $precioActual;
    private $dctoActual;
    private $coditemvariante;
    private $nomgrupovariante;
    private $tipofotovideovariante;
    private $rutafotovariante;
    private $tieneVariante;
    private $cantidad;

    private $ahorro =0;


    public function getAhorro(){
        return $this->ahorro;
    }
    public function setAhorro($ahor){
        $this->ahorro = $ahor;
    }

    public function getCantidad(){
        return $this->cantidad;
    }
    public function setCantidad($cnt){
        $this->cantidad = $cnt;
    }


    public function getTieneVariante(){
        return $this->tieneVariante;
    }
    public function setTieneVariante($tieneVar){
        $this->tieneVariante = $tieneVar;
    }

    public function getTipoFotoVideoVariante(){
        return $this->tipofotovideovariante;
    }
    public function setTipoFotoVideoVariante($tipofotovideo){
        $this->tipofotovideovariante = $tipofotovideo;
    }
    public function getRutaFotoVariante(){
        return $this->rutafotovariante;
    }
    public function setRutaFotoVariante($path){
        $this->rutafotovariante = $path;
    }


    public function getCodItemVariante(){
        return $this->coditemvariante;
    }
    public function setCodItemVariante($code){
        $this->coditemvariante = $code;
    }
    public function getNomGrupoVariante(){
        return $this->nomgrupovariante;
    }
    public function setNomGrupoVariante($name){
        $this->nomgrupovariante = $name;
    }

    public function getPrecioActual(){
        return $this->precioActual;
    }
    public function setPrecioActual($precio){
        $this->precioActual = $precio;
    }
    public function getDctoActual(){
        return $this->dctoActual;
    }
    public function setDctoActual($dcto){
        $this->dctoActual = $dcto;
    }



    public function getRutaFotoPortada(){
        return $this->rutaFotoPortada;
    }
    public function setRutaFotoPortada($RutaPortada){
        $this->rutaFotoPortada = $RutaPortada;
    }

    public function getMostrarWeb(){
        return $this->mostrarWeb;
    }
    public function setMostrarWeb($MostrarEnWeb){
        $this->mostrarWeb = $MostrarEnWeb;
    }


    public function getPrecioCyber(){
        return $this->precioCyber;
    }
    public function setPrecioCyber($PreciosCyber){
        $this->precioCyber = $PreciosCyber;
    }

    public function getDctoCyber(){
        return $this->dctoCyber;
    }
    public function setDctoCyber($DescuentoCyber){
        $this->dctoCyber = $DescuentoCyber;
    }



    public function getPrecioNormal(){
        return $this->precioNormal;
    }
    public function setPrecioNormal($PreciosNormal){
        $this->precioNormal = $PreciosNormal;
    }

    public function getDctoNormal(){
        return $this->dctoNormal;
    }
    public function setDctoNormal($DescuentoNormal){
        $this->dctoNormal = $DescuentoNormal;
    }
    

    public function getPrecio(){
        return $this->precio;
    }
    public function setPrecio($Precios){
        $this->precio = $Precios;
    }

    public function getEsPack(){
        return $this->esPack;
    }
    public function setEsPack($esUnPack){
        $this->esPack = $esUnPack;
    }

    public function getSegundoNombre(){
        return $this->segunoNombre;
    }
    public function setSegundoNombre($segundoName){
        $this->segunoNombre = $segundoName;
    }

    public function getNomProd(){
        return $this->nomProd;
    }
    public function setNomProd($nombre){
        $this->nomProd = $nombre;
    }

    public function getCodProd(){
        return $this->codProd;
    }
    public function setCodProd($codigo){
        $this->codProd = $codigo;
    }

    //20240220 Obtener datos actuales
    private $descripcionStockProductos ="";
    private $descripcionPrecioActual ="";
    private $etiquetaPrincipal ="";
    private $stock =0;

    public function getStockProd(){
        return $this->stock;
    }
    public function setStockProd($cantStock){
        $this->stock = $cantStock;
    }


    public function getDescStockProd(){
        return $this->descripcionStockProductos;
    }
    public function setDescStockProd($stock){
        $this->descripcionStockProductos = $stock;
    }


    public function getDescPrecioActual(){
        return $this->descripcionPrecioActual;
    }
    public function setDescPrecioActual($descPrecio){
        $this->descripcionPrecioActual = $descPrecio;
    }

    public function getEtiquetaPrincipal(){
        return $this->etiquetaPrincipal;
    }
    public function setEtiquetaPrincipal($descEtiqueta){
        $this->etiquetaPrincipal = $descEtiqueta;
    }

    
}
?>
