<?php

class VentaArma
{
    public $id;
    public $idUsuario;
    public $idArma;
    public $cantidad;
    public $precio;
    public $foto;
    public $fecha;

    public function crearVentaArma()
    { 
       $arma= Arma::ObtenerArma($this->idArma);
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO ventaarmas (idUsuario, idArma, precio, cantidad,fecha) VALUES (:idUsuario, :idArma, :precio, :cantidad ,:fecha)");
        $consulta->bindValue(':idUsuario', $this->idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':idArma', $this->idArma, PDO::PARAM_INT);
        $consulta->bindValue(':cantidad', $this->cantidad, PDO::PARAM_INT);
  
        $consulta->bindValue(':precio', $this->cantidad * $arma->precio);
        $consulta->bindValue(':fecha', $this->fecha);

        $consulta->execute();

        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function obtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ventaarmas");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'VentaArmas');
    }

    public static function obtenerTodosNacionalidad($nacionalidad)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ventaArmas INNER JOIN arma ON ventaArmas.idArma = arma.id WHERE arma.nacionalidad = :nacionalidad AND ventaArma.fecha < '2023-11-13' AND ventaArma.fecha > '2023-11-16'");
        $consulta->bindValue(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, 'VentaArmas');
    }

    public static function obtenerTodosNacionalidadYFechas($nacionalidad, $primerFecha, $segundaFecha)
    { $query ="SELECT * FROM ventaArmas 
        inner join arma on idArma= arma.id
         where arma.nacionalidad= :nacionalidad 
         and ventaarmas.fecha 
         between :primerFecha and :segundaFecha";
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta($query);
        $consulta->bindValue(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
        $consulta->bindValue(':primerFecha', $primerFecha );
        $consulta->bindValue(':segundaFecha', $segundaFecha );
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


   
     
    
    // public static function obtenerTodosNombre($nombre)
    // {
    //     $objAccesoDatos = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDatos->prepararConsulta("SELECT usuarios.id, usuarios.mail, usuarios.tipo FROM usuarios JOIN ventaarmas ON usuarios.id = ventaarmas.idUsuario JOIN arma  ON arma.id = ventaarmas.idArma WHERE arma.nombre = :nombre");
    //     $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
    //     $consulta->execute();
    //     return $consulta->fetchAll(PDO::FETCH_CLASS, 'Usuario');
    // }

     //traer todos los usuarios que compraron un arma en particular
    public static function obtenerTodosNombre($nombre)
    {
        $query ="SELECT usuarios.id, usuarios.mail, usuarios.tipo FROM usuarios 
        JOIN ventaarmas ON usuarios.id = ventaarmas.idUsuario 
        JOIN arma  ON arma.id = ventaarmas.idArma 
        WHERE arma.nombre = :nombre";
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta($query);
        $consulta->bindValue(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerVentaArma($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ventaarmas WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchObject('VentaArma');
    }

    public static function modificarVentaArma($ventaarma)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ventaarmas SET cantidad = :cantidad WHERE id = :id");
        $consulta->bindValue(':cantidad', $ventaarma->cantidad, PDO::PARAM_INT);
        $consulta->bindValue(':id', $ventaarma->id, PDO::PARAM_INT);
        $consulta->execute();
    }
    
    public static function borrarVentaArma($id)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE ventaarmas SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("Y-m-d"));
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', $fecha->format('Y-m-d'));
        $consulta->execute();
    }

    public function __toString()
    {
        return "$this->id, $this->idUsuario, $this->idArma, $this->cantidad, $this->precio, $this->fecha";
    }

    public static function ExportarPDFVentas($path)
{  
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM ventaarmas WHERE fecha > DATE_SUB(NOW(), INTERVAL 1 MONTH) ORDER BY fecha DESC");
    $consulta->execute();
    $ventas = $consulta->fetchAll(PDO::FETCH_CLASS, 'VentaArma');

    $file = fopen($path, "w");
    foreach ($ventas as $venta) {
        fwrite($file, json_encode($venta)); // Escribir cada venta en el archivo
    }
    fclose($file);

    return $path;
}

public static function ExportarcsvLogs($path)
{  
    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM logs");
    $consulta->execute();
    $ventas = $consulta->fetchAll(PDO::FETCH_ASSOC, 'Logs');

    $file = fopen($path, "w");
    foreach ($ventas as $venta) {
        fwrite($file, json_encode($venta)); // Escribir cada venta en el archivo
    }
    fclose($file);

    return $path;
}



public static function ExportarLogsCSV2($path)
{$objAccesoDatos = AccesoDatos::obtenerInstancia();
     
    $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM logs");
    $consulta->execute();
    $logs = $consulta->fetchAll(PDO::FETCH_ASSOC, 'logs');
    $file = fopen($path, "w");
    foreach ($logs as $log) {
        fwrite($file, json_encode($log));  
    }


    fclose($file);  
    return $path;     
}

    
}
?>