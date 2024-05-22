<?php

require_once("basedatos.php");



class DAPaginas
{
    private $Codigo;
    private $Htmlpage;

    public function obtenerPaginapoCodigo($Codigo)
    {
        //paginas
        //codpage 	htmlpage 	
        $bd = new BaseDatos();
        $sql = "SELECT * FROM paginas WHERE codpage = :codpage";
        $parametros = array(':codpage' => $Codigo);
        $resultado = 0;
        $consulta = $bd->consulta2($sql, $parametros, $resultado);
        if ($resultado == 1) {
            $fila = $consulta->fetch(PDO::FETCH_ASSOC);
            if ($fila !== false) {
                $this->Codigo = $fila['codpage'];
                $this->Htmlpage = $fila['htmlpage'];
            } else {
                // Manejar el caso cuando no se encuentran resultados
                echo "No se encontraron resultados";
            }
        } else {
            echo "Error en la consulta";
        }
    }

    public function getHtmlpage()
    {
        return $this->Htmlpage;
    }
    public function getCodigo()
    {
        return $this->Codigo;
    }
}
