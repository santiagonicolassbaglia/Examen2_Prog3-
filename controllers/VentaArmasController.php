<?php

 
require_once './models/Usuario.php';
require_once './middlewares/AutentificadorJWT.php';
require_once './interfaces/IApiUsable.php';
require_once './models/Arma.php';
require_once './models/VentaArmas.php';


class VentaArmasController extends VentaArmas implements IApiUsable
{

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
       
        $cantidad = $parametros['cantidad'];
        $idArma = $parametros['idArma'];
        $arma= Arma:: ObtenerArma($parametros['idArma']);
        $usuario= AutentificadorJWT:: ObtenerData($parametros['token']);
        $VentaArma= new VentaArmas();
        $VentaArma->fecha= date("Y-m-d");
        $VentaArma->precio=$cantidad*$arma->precio ;
        $VentaArma->cantidad= $cantidad;
        $VentaArma->idArma= $idArma;
        $VentaArma->idUsuario= $usuario->id;

        $VentaArma->Crear();

        if(isset($_FILES['foto'])){
            $foto= $_FILES['foto'];
            $ruta= 'FotosArma2023/' . $arma->nombre . "-" . $usuario->usuario . "-" . date("y-m-d") . ".png";
            move_uploaded_file($foto['tmp_name'],$ruta);
        }

        $payload = json_encode(array("mensaje" => "Venta del Arma realizada con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function TraerTodos($request, $response, $args)
    {
      $lista = VentaArmas::obtenerTodos();
      $payload = json_encode(array("Lista de Ventas" => $lista));

      $response->getBody()->write($payload);
      return $response
        ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $id = $args['id'];
        $venta = VentaArmas::ObtenerPorId($id);
        $payload = json_encode($venta);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    


    public function TraerTodosFiltradoPorNacionalidadYFecha($request, $response, $args)
{
    $primerFecha = $args['primerFecha'];
    $segundaFecha = $args['segundaFecha'];
    $nacionalidad = $args['nacionalidad'];
   

    $consulta = "SELECT *
                 FROM ventas
                 INNER JOIN arma ON ventas.idArma = arma.id
                 WHERE arma.nacionalidad = :nacionalidad
                 AND ventas.fecha BETWEEN :fechaInicio AND :fechaFin";

    $objAccesoDatos = AccesoDatos::obtenerInstancia();
    $stmt = $objAccesoDatos->prepararConsulta($consulta);
    $stmt->bindParam(':nacionalidad', $nacionalidad, PDO::PARAM_STR);
    $stmt->bindParam(':fechaInicio', $primerFecha, PDO::PARAM_STR);
    $stmt->bindParam(':fechaFin', $segundaFecha, PDO::PARAM_STR);
    $stmt->execute();

    $listaVentas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $payload = json_encode(array("Lista de ventas" => $listaVentas));

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
}

    public function TraerUsuariosPorArma($request, $response, $args)
    {
        $nombreArma = $args['nombreArma'];
    
        $consulta = "SELECT DISTINCT usuario.id, usuario.nombre, usuario.apellido 
                     FROM ventaArmas 
                     INNER JOIN usuario ON ventaArmas.idUsuario = usuario.id 
                     INNER JOIN arma ON ventaArmas.idArma = arma.id 
                     WHERE arma.nombre = :nombreArma";
    
        $objAccesoDatos = AccesoDatos::obtenerInstancia();
        $stmt = $objAccesoDatos->prepararConsulta($consulta);
        $stmt->bindParam(':nombreArma', $nombreArma, PDO::PARAM_STR);
        $stmt->execute();
    
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        $payload = json_encode(array("Usuarios que compraron el arma" => $usuarios));
    
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

}
