<?php

require_once("basedatos.php");


class Util
{

	//Usado desde Store para buscar Parametro
	public function buscarParametro($codParametro,  &$funciono)
	{

		$db = new BaseDatos();

		$sql = "SELECT DescParametro, ValorParametro FROM parametro WHERE CodParametro =:codigoParam;";
		$array = array(":codigoParam" => $codParametro);

		//print_r($array);

		$result = $db->consulta2($sql, $array, $funciono);
		//print_r($result);
		return $result;
	}

	//Usado desde Store para buscar Parametro
	public function obtenerParametroLista($codParametro,  &$funciono)
	{

		$db = new BaseDatos();

		$sql = "SELECT CodValor, Valor, Valor2 FROM parametro_lista WHERE CodLista =:codigoParam AND Estatus ='V' ORDER BY Orden ASC;";
		$array = array(":codigoParam" => $codParametro);

		//print_r($array);

		$result = $db->consulta2($sql, $array, $funciono);
		//print_r($result);
		return $result;
	}
}
