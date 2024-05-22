<?php

require_once("basedatos.php");
require_once("BEProducto.php");
require_once("BEProductoDescripcion.php");

class DAProducto
{

	//Obtener 
	public function obtenerStockProdItem($codProd,  &$funciono)
	{

		$db = new BaseDatos();

		$sql = "CALL uspObtenerStockProdPorItem (:codProd);";
		$array = array(":codProd" => $codProd);

		//print_r($array);

		$result = $db->consulta2($sql, $array, $funciono);
		//print_r($result);
		return $result;
	}

	//Usado desde Store para buscar producto
	public function buscarProductoPorNombre($nomprod,  &$funciono)
	{

		$db = new BaseDatos();

		$sql = "CALL uspBuscarProdStore (:nomprod);";
		$array = array(":nomprod" => $nomprod);

		//print_r($array);

		$result = $db->consulta2($sql, $array, $funciono);
		//print_r($result);
		return $result;
	}


	//Usado desde Store obtener productos de la categoría de producto
	public function obtenerProductosCategoria($idCategoria, $tipo, $orden,  &$funciono)
	{

		$db = new BaseDatos();

		$limit = "";

		if ($tipo == 1) {
			$limit = "LIMIT 6";
		}

		$ordenSQL = "ORDER BY b.Orden ASC";
		if ($orden == 1) {
			$ordenSQL = "ORDER BY b.Orden ASC";
		}
		if ($orden == 2) {
			$ordenSQL = "ORDER BY PrecioActual ASC";
		}
		if ($orden == 3) {
			$ordenSQL = "ORDER BY PrecioActual DESC";
		}


		$sql = "SELECT a.CodProd, a.NomProd, a.SegundoNombre, a.Precio , obtenerPrecioProd(a.CodProd) as 'PrecioActual', 
		obtenerDctoProd(a.CodProd) as 'DctoActual',
		obtenerFotoPortada(a.CodProd) as 'RutaFoto' ,
		obtenerPrecioEtiquetaProd(a.CodProd,1) as 'PrecioActualDescripcion' ,
		obtenerPrecioEtiquetaProd(a.CodProd,2) as 'EtiquetaPrincipal' ,
		obtenerStockProd(a.CodProd) as 'EstadoStock'
		FROM categoriaprod_detalle b
		INNER JOIN producto a on (a.CodProd = b.CodProd)
		WHERE b.IdCategoria = :ideCategoria and a.MostrarWeb =1
		$ordenSQL
		$limit ;";

		//echo " </br></br>".$sql;

		//echo $sql;

		$array = array(":ideCategoria" => $idCategoria);

		//print_r($array);

		$result = $db->consulta2($sql, $array, $funciono);
		//print_r($result);
		return $result;
	}

	//obtener todos los productos
	public function obtenerProductos($tipo, $orden, &$funciono)
	{

		$db = new BaseDatos();

		$limit = "";

		if ($tipo == 1) {
			$limit = "LIMIT 6";
		}

		$ordenSQL = "ORDER BY a.CodProd ASC";
		if ($orden == 1) {
			$ordenSQL = "ORDER BY a.CodProd ASC";
		}
		if ($orden == 2) {
			$ordenSQL = "ORDER BY PrecioActual ASC";
		}
		if ($orden == 3) {
			$ordenSQL = "ORDER BY PrecioActual DESC";
		}

		$sql = "SELECT a.CodProd, a.NomProd, a.SegundoNombre, a.Precio , obtenerPrecioProd(a.CodProd) as 'PrecioActual', 
		obtenerDctoProd(a.CodProd) as 'DctoActual',
		obtenerFotoPortada(a.CodProd) as 'RutaFoto' ,
		obtenerPrecioEtiquetaProd(a.CodProd,1) as 'PrecioActualDescripcion' ,
		obtenerPrecioEtiquetaProd(a.CodProd,2) as 'EtiquetaPrincipal' ,
		obtenerStockProd(a.CodProd) as 'EstadoStock'
		FROM producto a
		WHERE a.MostrarWeb =1
		$ordenSQL
		$limit ;";

		//echo $sql;

		$array = array();

		//print_r($array);

		$result = $db->consulta2($sql, $array, $funciono);
		//print_r($result);
		return $result;
	}

	//obtener datos del producto
	public function obtenerDatosParaCotizador($CodProd, &$funciono)
	{

		//echo "COd Prod evaluar ($CodProd)";

		$db = new BaseDatos();
		$sql = "SELECT a.CodProd, a.NomProd, a.SegundoNombre, a.EsPack, a.Precio,
		obtenerPrecioProd(a.CodProd) as 'PrecioActual', obtenerDctoProd(a.CodProd) as 'DctoActual',
		Resumen, LoQueAmaras, Descripcion, IngredienteEstrella,ModoUso, Ingredientes, PreguntasFrecuentes ,
		obtenerFotoPortada(a.CodProd) as RutaFoto ,
		obtenerPrecioEtiquetaProd(a.CodProd,1) as 'PrecioActualDescripcion' ,
		obtenerPrecioEtiquetaProd(a.CodProd,2) as 'EtiquetaPrincipal' ,
		obtenerStockProd(a.CodProd) as 'EstadoStock'
		FROM producto a
		INNER JOIN producto_descripcion b on (a.CodProd = b.CodProd)
		WHERE a.CodProd=:codigo;";
		$array = array(":codigo" => $CodProd);
		$result = $db->consulta2($sql, $array, $funciono);
		return $result;
	}


	//obtener fotos
	public function obtenerFotos($CodProd, &$funciono)
	{

		$db = new BaseDatos();
		$sql = "SELECT IdFoto , CodProd , OrdenFoto , RutaFoto , FotoPrincipal FROM producto_foto WHERE CodProd=:codigo ORDER BY OrdenFoto ASC;";
		$array = array(":codigo" => $CodProd);
		$result = $db->consulta2($sql, $array, $funciono);
		return $result;
	}


	//Obtener la información de Item Variante de un producto y sus variantes existentes.
	public function buscarSiTieneVariante($codprod, $fechahoy, &$funciono)
	{

		$db = new BaseDatos();
		//$sql = "SELECT a.codcliente FROM cliente a WHERE a.email =:email";
		//$array = array (":email" => $email);

		//SELECT a.CodProd , a.NomProd , b.CodItemVariante , b.FechaInicio, b.FechaFin , c.TipoFotoVideo, c.RutaFotoVariante
		$sql = "SELECT a.NomProd, c.CodItemVariante, c.TipoFotoVideo, c.RutaFotoVariante , c.NomGrupoVariante , c.TieneVariante
		FROM producto a 
		INNER JOIN producto_item b on (a.CodProd = b.CodProd)
		INNER JOIN item_variante c on (b.CodItemVariante = c.CodItemVariante)
		WHERE a.CodProd = :codeprod
		AND b.FechaInicio <= :fecHoy
		AND b.FechaFin >= :fecHoy";

		$array = array(":codeprod" => $codprod, ":fecHoy" => $fechahoy);

		//print_r($array);

		$result = $db->consulta2($sql, $array, $funciono);
		//print_r($result);
		return $result;
	}


	//Obtener la información de item_variante_det y de sus item relacionados
	public function obtenerVariantesDet($CodItemVarianteIN, &$funciono)
	{

		$db = new BaseDatos();

		$sql = "SELECT a.CodItemVariante , a.CodItem, a.NomVariante, a.ColorVariante , a.Orden , b.NomItem
		FROM item_variante_detalle a 
		INNER JOIN item b on (a.CodItem = b.CodItem)
		WHERE a.CodItemVariante IN($CodItemVarianteIN)
		ORDER BY a.CodItemVariante ASC , a.Orden ASC;";

		//echo $sql;

		$array = array();

		//print_r($array);

		$result = $db->consulta2($sql, $array, $funciono);
		//print_r($result);
		return $result;
	}


	//obtener listado de productos
	public function buscarProductoRelacionado($CodProdIN, &$funciono)
	{


		$db = new BaseDatos();
		$sql = "SELECT b.CodProd, b.NomProd, b.SegundoNombre, b.EsPack, b.Precio,
		obtenerPrecioProd(b.CodProd) as 'PrecioActual', obtenerDctoProd(b.CodProd) as 'DctoActual',
		obtenerFotoPortada(b.CodProd) as RutaFoto ,
		obtenerPrecioEtiquetaProd(b.CodProd,1) as 'PrecioActualDescripcion' ,
		obtenerPrecioEtiquetaProd(b.CodProd,2) as 'EtiquetaPrincipal' ,
		obtenerStockProd(b.CodProd) as 'EstadoStock'
		FROM (
			SELECT b.CodProdRelac
			FROM producto a
			INNER JOIN producto_relacionado b on (a.CodProd = b.CodProd)
			WHERE a.CodProd IN (:codeProd) and a.MostrarWeb ='1'
			GROUP BY b.CodProdRelac
		) x 
		INNER JOIN producto b on (x.CodProdRelac = b.CodProd)
		WHERE b.CodProd NOT IN (:codeProd)
		ORDER BY b.NomProd ASC;";


		//echo $sql;
		//echo "  mostrarWeb ($mostrarWeb)";
		$array = array(":codeProd" => $CodProdIN);
		$result = $db->consulta2($sql, $array, $funciono);
		return $result;
	}
}
