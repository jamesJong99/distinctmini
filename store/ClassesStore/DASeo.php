
<?php

require_once("basedatos.php");



class DASeo
{
    private $Pagina;
    private $Codigo;
    private $Slug;
    private $TituloSeo;
    private $DescripcionSeo;

    public function obtenerSeoPorPaginaYCodigo($Pagina, $Codigo)
    {
        $bd = new BaseDatos();
        $sql = "SELECT * FROM seo WHERE Pagina = :pagina AND Codigo = :codigo";
        $parametros = array(':pagina' => $Pagina, ':codigo' => $Codigo);
        $resultado = 0;
        $consulta = $bd->consulta2($sql, $parametros, $resultado);
        if ($resultado == 1) {
            $fila = $consulta->fetch(PDO::FETCH_ASSOC);
            if ($fila !== false) {
                $this->Pagina = $fila['Pagina'];
                $this->Codigo = $fila['Codigo'];
                $this->Slug = $fila['Slug'];
                $this->TituloSeo = $fila['TituloSeo'];
                $this->DescripcionSeo = $fila['DescripcionSeo'];
            } else {
                // Manejar el caso cuando no se encuentran resultados
            }
        } else {
            // Manejar error
        }
    }
    //obtener pagina
    public function obtenerSeoPorPagina($Pagina)
    {
        $bd = new BaseDatos();
        $sql = "SELECT * FROM seo WHERE Pagina = :pagina";
        $parametros = array(':pagina' => $Pagina);
        $resultado = 0;
        $consulta = $bd->consulta2($sql, $parametros, $resultado);
        if ($resultado == 1) {
            $fila = $consulta->fetch(PDO::FETCH_ASSOC);
            if ($fila !== false) {
                $this->Pagina = $fila['Pagina'];
                $this->Codigo = $fila['Codigo'];
                $this->Slug = $fila['Slug'];
                $this->TituloSeo = $fila['TituloSeo'];
                $this->DescripcionSeo = $fila['DescripcionSeo'];
            } else {
                // Manejar el caso cuando no se encuentran resultados
            }
        } else {
            // Manejar error
        }
    }
    //obtener slug y pagina
    public function obtenerSeoPorSlugYPagina($Slug, $Pagina)
    {
        $bd = new BaseDatos();
        $sql = "SELECT * FROM seo WHERE Slug = :slug AND Pagina = :pagina";
        $parametros = array(':slug' => $Slug, ':pagina' => $Pagina);
        $resultado = 0;
        $consulta = $bd->consulta2($sql, $parametros, $resultado);
        if ($resultado == 1) {
            $fila = $consulta->fetch(PDO::FETCH_ASSOC);
            if ($fila !== false) {
                $this->Pagina = $fila['Pagina'];
                $this->Codigo = $fila['Codigo'];
                $this->Slug = $fila['Slug'];
                $this->TituloSeo = $fila['TituloSeo'];
                $this->DescripcionSeo = $fila['DescripcionSeo'];
            } else {
                // Manejar el caso cuando no se encuentran resultados
            }
        } else {
            // Manejar error
        }
    }

    public function getPagina()
    {
        return $this->Pagina;
    }

    public function getCodigo()
    {
        return $this->Codigo;
    }

    public function getSlug()
    {
        return $this->Slug;
    }

    public function getTituloSeo()
    {
        return $this->TituloSeo;
    }

    public function getDescripcionSeo()
    {
        return $this->DescripcionSeo;
    }
}
