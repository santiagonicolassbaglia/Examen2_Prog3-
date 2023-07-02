<?php
 
class Usuario  
{
    public $id;
    public $usuario;
    public $clave;
    public $mail;
    public $tipo;
   



    public function crearUsuario()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("INSERT INTO usuarios(usuario,clave,mail,tipo) VALUES  (:usuario, :clave, :mail, :tipo)");  
        
        $consulta->bindValue(':usuario', $this->usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $this->clave, PDO::PARAM_STR);
        $consulta->bindValue(':mail', $this->mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
    

        $consulta->execute();
         
        return $objAccesoDatos->obtenerUltimoId();
    }
 
    public static function ObtenerTodos()
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id,usuario,clave,mail,tipo FROM usuarios");
        $consulta->execute();
    
        $usuarios = array();
        while($fila = $consulta->fetch(PDO::FETCH_ASSOC)){
            $usuario= new Usuario();
    
            $usuario->id = $fila['id'];
            $usuario->usuario = $fila['usuario'];
            $usuario->clave = $fila['clave'];
            $usuario->mail = $fila['mail'];
            $usuario->tipo = $fila['tipo'];

           

            $usuarios[] = $usuario; 
        }
    
        return $usuarios;
    }
    

    public static function obtenerUsuario($usuario)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT id, usuario, clave, tipo, mail FROM usuarios WHERE usuario = :usuario");
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->execute();

        return $consulta->fetchObject('Usuario');
    }

    public static function obtenerUsuarioPorId($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("SELECT * FROM usuarios WHERE id = :id");
        $consulta->bindValue(':id', $id );
        $consulta->execute();

        return $consulta->fetchObject();
    }

    public static function modificarUno($id, $usuario, $clave, $mail, $tipo)
    { 
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("UPDATE usuarios SET usuario = :usuario, clave = :clave, mail = :mail, tipo = :tipo WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->bindValue(':usuario', $usuario, PDO::PARAM_STR);
        $consulta->bindValue(':clave', $clave, PDO::PARAM_STR);
        $consulta->bindValue(':mail', $mail, PDO::PARAM_STR);
        $consulta->bindValue(':tipo', $tipo, PDO::PARAM_STR);
        $consulta->execute();
 
        return true;
    }
    public static function borrarUsuario($id)
    {
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $consulta = $objAccesoDatos->prepararConsulta("DELETE FROM usuarios WHERE id = :id");
        $consulta->bindValue(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
    }
    
    
     
 
}
?>