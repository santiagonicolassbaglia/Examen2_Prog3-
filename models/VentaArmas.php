<?php



class VentaArmas
{
    public $id;
    public $idUsuario;
    public $idArma;
    public $precio;
    public $cantidad;
    public $fecha;
 
    public $estado;
 



    public function Crear()
    {

        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ventaArmas (idUsuario,idArma,precio,cantidad,fecha ) VALUES (:idUsuario, :idArma, :precio, :cantidad, :fecha)");
  
        

        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':idArma', $this->idArma, PDO::PARAM_INT);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':fecha', $this->fecha, PDO::PARAM_STR);
 
        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

 


    public static function ObtenerTodos()
    {
        
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT idUsuario,idArma,precio,cantidad,fecha   FROM ventaArmas");
        $consulta->execute();

        return $consulta->fetchAll(PDO::FETCH_CLASS, 'ventaArmas');
    }

    public static function ObtenerPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT  idUsuario,idArma,precio,cantidad,fecha FROM ventaArmas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);


        $consulta->execute();

        return $consulta->fetchObject('ventaArmas');
    }

    public static function Modificar($id, $usuario, $clave, $mail, $tipo,$fecha)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ventaArmas SET idUsuario = :idUsuario, idArma = :idArma, precio = :precio, cantidad = :cantidad , fecha = :fecha  WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':idUsuario', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':idArma', $clave, PDO::PARAM_INT);
        $consulta->bindValue(':precio', $mail, PDO::PARAM_STR);
        $consulta->bindValue(':cantidad', $tipo, PDO::PARAM_STR);
        $consulta->bindValue(':fecha', $fecha, PDO::PARAM_STR);

        $consulta->execute();
    }
    
    public static function Borrar($id)
    {

        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ventaArmas SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }

    public static function ObtenerVentasEEUU()
{
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ventaArmas WHERE fecha >= :fechaInicio AND fecha <= :fechaFin");
    
    $fechaInicio = '2023-11-13';
    $fechaFin = '2023-11-16';
    
    $consulta->bindValue(':fechaInicio', $fechaInicio, PDO::PARAM_STR);
    $consulta->bindValue(':fechaFin', $fechaFin, PDO::PARAM_STR);
    
    $consulta->execute();
    
    return $consulta->fetchAll(PDO::FETCH_CLASS, 'ventaArmas');
}

 
}
