<?php

class Arma
{
    public $id;
    public $nombre;
    public $precio;
    public $foto;
    public $nacionalidad;

    public $stock;

   
    public function CrearArma()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO arma (nombre, precio, foto, nacionalidad, stock) VALUES (:nombre, :precio, :foto, :nacionalidad, :stock)"); 
        
        $consulta->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $this->precio, PDO::PARAM_INT);
        $consulta->bindValue(':foto', $this->foto );
        $consulta->bindValue(':nacionalidad', $this->nacionalidad, PDO::PARAM_STR);
        $consulta->bindValue(':stock', $this->stock, PDO::PARAM_INT);

        $consulta->execute();
         
        return $objAccesoDatos->obtenerUltimoId();
    }

    public static function ObtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, nombre, precio, foto, nacionalidad, stock FROM arma");
        $consulta->execute();
    
        $armas = array();
        while($fila = $consulta->fetch(PDO::FETCH_ASSOC)){
            $arma= new Arma();
    
            $arma->id = $fila['id'];
            $arma->nombre = $fila['nombre'];
            $arma->precio = $fila['precio'];
            $arma->foto = $fila['foto'];
            $arma->nacionalidad = $fila['nacionalidad'];
            $arma->stock = $fila['stock'];

            $armas[] = $arma; 
        }
    
        return $armas;
    }
    // public static function obtenerTodos()
    // {
    //     $objAccesoDatos = AccesoDatos::obtenerInstancia();
    //     $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM arma");
    //     $consulta->execute();
    //     return $consulta->fetchAll(PDO::FETCH_CLASS, 'Arma');
    // }

    public static function ObtenerArma($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM arma WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_STR);
        $consulta->execute();
        
        return $consulta->fetchObject('arma');
    }

    public static function ObtenerArmaPorNacionalidad($nacionalidad)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM arma WHERE nacionalidad = :nacionalidad");
        $consulta->bindValue(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
        $consulta->execute();
        
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function modificarArma($arma)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE arma SET nombre = :nombre, precio = :precio, nacionalidad = :nacionalidad, foto = :foto, stock = :stock WHERE id = :id");
        $consulta->bindValue(':nombre', $arma->nombre, PDO::PARAM_STR);
        $consulta->bindValue(':precio', $arma->precio, PDO::PARAM_INT);
        $consulta->bindValue(':nacionalidad', $arma->nacionalidad, PDO::PARAM_STR);
        $consulta->bindValue(':foto', $arma->foto, PDO::PARAM_STR);
        $consulta->bindValue(':stock', $arma->stock, PDO::PARAM_INT);
        $consulta->bindValue(':id', $arma->id, PDO::PARAM_INT);
        $consulta->execute();
    }
    

    public static function borrarArma($usuario)
    {
        $objAccesoDato = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDato->prepararConsulta("UPDATE arma SET fechaBaja = :fechaBaja WHERE id = :id");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':id', $usuario, PDO::PARAM_INT);
        $consulta->bindValue(':fechaBaja', date_format($fecha, 'Y-m-d H:i:s'));
        $consulta->execute();
    }
  

    public static function cargarLog($idUser, $idArma)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO logs (idUsuario, idArma, accion, fecha_accion) VALUES (:id_usuario, :id_arma, :accion, :fecha_accion)");
        $consulta->bindValue(':id_usuario', $idUser);
        $consulta->bindValue(':id_arma', $idArma);
        $consulta->bindValue(':accion', "Borrar");
        $fecha = new DateTime(date("d-m-Y"));
        $consulta->bindValue(':fecha_accion', date_format($fecha, 'Y-m-d'));
        $consulta->execute();
    }



    
}