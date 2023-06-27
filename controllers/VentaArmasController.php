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

        $requiredParams = ['idUsuario', 'idArma', 'precio', 'cantidad', 'fecha'];

        $missingParams = [];
        foreach ($requiredParams as $param) {
            if (!isset($parametros[$param])) {
                $missingParams[] = $param;
            }
        }

        if (!empty($missingParams)) {
            $payload = json_encode(array("error" => "Falta el campo: " . implode(', ', $missingParams)));
            $response->getBody()->write($payload);
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }

        $idUsuario = $parametros['idUsuario'];
        $idArma = $parametros['idArma'];
        $precio = $parametros['precio'];
        $cantidad = $parametros['cantidad'];
        $fecha = $parametros['fecha'];

        $venta = new VentaArmas();
        $venta->idUsuario = $idUsuario;
        $venta->idArma = $idArma;
        $venta->precio = $precio;
        $venta->cantidad = $cantidad;
        $venta->fecha = $fecha;

        $ventaId = $venta->Crear();
 
        $uploadedFiles = $request->getUploadedFiles();
        if (isset($uploadedFiles['imagen'])) {
            $imagen = $uploadedFiles['imagen'];
            if ($imagen->getError() === UPLOAD_ERR_OK) {
                $extension = pathinfo($imagen->getClientFilename(), PATHINFO_EXTENSION);
                $nombreArchivo = $venta->idArma . '_' . $venta->idUsuario . '_' . date('Ymd') . '.' . $extension;
                $rutaImagen = './FotosArma2023/' . $nombreArchivo;
                $imagen->moveTo($rutaImagen);
            }
        }

        $payload = json_encode(array("mensaje" => "VentaArmas creada con éxito", "idVenta" => $ventaId));

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
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
                 FROM ventasArmas
                 INNER JOIN arma ON ventasArmas.idArma = arma.id
                 WHERE arma.nacionalidad = :nacionalidad
                 AND ventasArmas.fecha BETWEEN :fechaInicio AND :fechaFin";

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
