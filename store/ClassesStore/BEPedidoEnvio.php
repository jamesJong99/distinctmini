<?php

class BEPedidoEnvio
{
    /*Atributos*/

    private $idEnvio;
    private $numPed;
    private $tipoEntrega;
    private $codCourier;
    private $nomCourier;
    private $trackingCourier;

    private $fecEntCourier;
    private $fecEntCliente;
    private $estatus;
    private $comentario;

    private $iddepartamento;
    private $idprovincia;
    private $iddistrito;
    private $idtipoentrega;
    private $nomtipoentrega = "";

    private $departamento;
    private $provincia;
    private $distrito;
    private $coordenadas;

    private $costoEnvio;
    private $seguimiento;

    //20220817 Campo para obtener las muestras o regalitos enviados por cada pedido
    private $muestra;

    //20240209 Campo para guardar la dirección. Usada en Store. 
    //Carrito de compra al obtener las direcciones de entrega anteriores del cliente
    private $direccionEnvio = "";





    public function getNomTipoEntrega()
    {
        return $this->nomtipoentrega;
    }
    public function setNomTipoEntrega($name)
    {
        $this->nomtipoentrega = $name;
    }


    public function getIdTipoEntrega()
    {
        return $this->idtipoentrega;
    }
    public function setIdTipoEntrega($idtipoentrega)
    {
        $this->idtipoentrega = $idtipoentrega;
    }

    public function getMuestra()
    {
        return $this->muestra;
    }
    public function setMuestra($regalito)
    {
        $this->muestra = $regalito;
    }

    public function getCostoEnvio()
    {
        return $this->costoEnvio;
    }
    public function setCostoEnvio($costo)
    {
        $this->costoEnvio = $costo;
    }


    public function getSeguimiento()
    {
        return $this->seguimiento;
    }
    public function setSeguimiento($seguim)
    {
        $this->seguimiento = $seguim;
    }


    public function getIdDepartamento()
    {
        return $this->iddepartamento;
    }
    public function setIdDepartamento($dpto)
    {
        $this->iddepartamento = $dpto;
    }

    public function getIdProvincia()
    {
        return $this->idprovincia;
    }
    public function setIdProvincia($prov)
    {
        $this->idprovincia = $prov;
    }

    public function getIdDistrito()
    {
        return $this->iddistrito;
    }
    public function setIdDistrito($dist)
    {
        $this->iddistrito = $dist;
    }





    public function getDepartamento()
    {
        return $this->departamento;
    }
    public function setDepartamento($dpto)
    {
        $this->departamento = $dpto;
    }

    public function getProvincia()
    {
        return $this->provincia;
    }
    public function setProvincia($prov)
    {
        $this->provincia = $prov;
    }

    public function getDistrito()
    {
        return $this->distrito;
    }
    public function setDistrito($dist)
    {
        $this->distrito = $dist;
    }

    public function getCoordenadas()
    {
        return $this->coordenadas;
    }
    public function setCoordenadas($coord)
    {
        $this->coordenadas = $coord;
    }


    public function getIdEnvio()
    {
        return $this->idEnvio;
    }
    public function setIdEnvio($id)
    {
        $this->idEnvio = $id;
    }

    public function getNumPed()
    {
        return $this->numPed;
    }
    public function setNumPed($ped)
    {
        $this->numPed = $ped;
    }

    public function getTipoEntrega()
    {
        return $this->tipoEntrega;
    }
    public function setTipoEntrega($tip)
    {
        $this->tipoEntrega = $tip;
    }

    public function getCodCourier()
    {
        return $this->codCourier;
    }
    public function setCodCourier($cod)
    {
        $this->codCourier = $cod;
    }


    public function getNomCourier()
    {
        return $this->nomCourier;
    }
    public function setNomCourier($nom)
    {
        $this->nomCourier = $nom;
    }


    public function getTrackingCourier()
    {
        return $this->trackingCourier;
    }
    public function setTrackingCourier($track)
    {
        $this->trackingCourier = $track;
    }


    public function getFecEntCourier()
    {
        return $this->fecEntCourier;
    }
    public function setFecEntCourier($fec)
    {
        $this->fecEntCourier = $fec;
    }


    public function getFecEntCliente()
    {
        return $this->fecEntCliente;
    }
    public function setFecEntCliente($fec)
    {
        $this->fecEntCliente = $fec;
    }


    public function getEstatus()
    {
        return $this->estatus;
    }
    public function setEstatus($stat)
    {
        $this->estatus = $stat;
    }


    public function getComentario()
    {
        return $this->comentario;
    }
    public function setComentario($comen)
    {
        $this->comentario = $comen;
    }

    public function getDireccionEnvio()
    {
        return $this->direccionEnvio;
    }
    public function setDireccionEnvio($name)
    {
        $this->direccionEnvio = $name;
    }
}
