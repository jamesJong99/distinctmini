<?php
require_once "configuracionBD.php";
class BaseDatos {
    public $conexion;
    protected $db;

	 public function cadenaConexion()
    {
        $cadena ='mysql:host='.HOST.';dbname='.DBNAME;
		return $cadena;
    }
	
	public function consulta2($sql,$array, &$resultado)
    {
		try {
			$cadena = $this->cadenaConexion();
			//echo "</br> basedatos.php Cadena conexión $cadena";
			$dbh = new PDO($this->cadenaConexion(), USER, PASS);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$dbh->query("SET NAMES 'utf8'");
			
			$sth = $dbh->prepare($sql);
			$sth->execute($array);
			
			$resultado= $sth->setFetchMode(PDO::FETCH_ASSOC);
			$dbh = null;
			
			$resultado=1;
			return $sth;
			
		} catch (PDOException $e) {
			//throw new pdoException($e);
			$mensajeexcepcion = $e->getMessage();
			echo "</br> basedatos.php Error  $mensajeexcepcion";
			$resultado=-1;
		}	
    }

	public function ejecutarobtener2($sql,$array, &$resultado)
    {
		try {
			$dbh = new PDO($this->cadenaConexion(), USER, PASS);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$dbh->query("SET NAMES 'utf8'");
			
			$sth = $dbh->prepare($sql);
			$sth->execute($array);
			
			$resultado= $sth->setFetchMode(PDO::FETCH_ASSOC);
			$dbh = null;
			
			$resultado=1;
			return $sth;
			
		} catch (PDOException $e) {
			//throw new pdoException($e);
			$resultado=-1;
		}	
    }

	
	public function ejecutar2($sql,$array, &$resultado)
    {
		try {
			$dbh = new PDO($this->cadenaConexion(), USER, PASS);
			$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			$dbh->query("SET NAMES 'utf8'");
			
			$sth = $dbh->prepare($sql);
			$sth->execute($array);
			
			//$resultado= $sth->setFetchMode(PDO::FETCH_ASSOC);
			$dbh = null;
			
			$resultado=1;
			return $sth;
			
		} catch (PDOException $e) {
			throw new pdoException($e);
			$resultado=-1;
		}
		
		
    }

	
	
	
	public function test($sql,$array)
    {
		//$cadena ='mysql:host=localhost;dbname=Distinct';
		//$cadena ='mysql:host='.HOST.';dbname='.DBNAME;
		
		$dbh = new PDO($this->cadenaConexion(), USER, PASS);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		
		//print_r($array);
		

		//echo $sql;
		//$sql ="SELECT codcliente FROM cliente a where a.Email=:email and a.Celular =:celular";
		//echo $sql."<br>";
		//$email ="christian779@gmail.com";
		//$celular ="980735802";
		
		$sth = $dbh->prepare($sql);
		
		//$sth->bindParam(":email", $email);
		//$sth->bindParam(":celular", $celular);
		//$sth->bindParam(':email', $email);
		//$sth->bindParam(':celular', $celular);
		$sth->execute($array);
		
		$resultado= $sth->setFetchMode(PDO::FETCH_ASSOC);
		/*
		while( $fila = $sth->fetch())
        {
            echo "ACANGA ";
			echo $fila["celular"];
        }
		*/
		
		$dbh = null;
        
        return $sth;
    }
	

    public function conectar()
    {
        
        $this->conexion = mysqLi_connect(HOST, USER, PASS);
        if($this->conexion->connect_error )
        {
            die("Connection failed: " . $this->conexion->connect_error);
        }
        $this->db = mysqli_select_db($this->conexion,DBNAME) or DIE("Lo sentimos, no se ha podido conectar con la base datos: " . DBNAME);;
        return true;
    }

    public function desconectar()
    {
        if ($this->conexion) {
            mysqli_close($this->conexion);
        }
    }
	
	
	
	 public function consulta($sql)
    {
        $this->conectar();
        $query = mysqli_query($this->conexion,$sql);
        $this->desconectar();
        
        return $query;
    }
    
    public function ejecutar($sql)
    {
        $ejecuto=false;
        
        $this->conectar();
        if (mysqli_query($this->conexion, $sql)) {
            $ejecuto =true;
        } else {
            die( "Error: " . $sql . "<br>" . mysqli_error($this->conexion));
        }
        $this->desconectar();
        
        return $ejecuto;
    }
	
}