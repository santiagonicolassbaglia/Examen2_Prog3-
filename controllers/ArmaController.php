<?php
require_once './models/Arma.php';
 
require_once './interfaces/IApiUsable.php';
require_once './db/AccesoDatos.php';


 

class ArmaController extends Arma implements IApiUsable
{ 
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        $requiredParams = ['nombre', 'precio', 'foto', 'nacionalidad','stock'];
    
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
     
        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $foto = $parametros['foto'];
        $nacionalidad = $parametros['nacionalidad'];
        $stock = $parametros['stock'];

    
        $producto = new Arma();
        $producto->nombre= $nombre;
        $producto->precio= $precio;
        $producto->foto= $foto;
        $producto->nacionalidad= $nacionalidad;
        $producto->stock= $stock;
        $producto->CrearArma();

        $payload = json_encode(array("mensaje" => "Arma creado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');


    }

    public function TraerTodos($request, $response, $args)
    {
      $lista = Arma::obtenerTodos();
      $payload = json_encode(array("Lista de Armas" => $lista));
  
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
        $producto = new Arma();
        $producto->id = $id;
        $producto->borrarArma($id);
        $payload = json_encode(array("mensaje" => "Arma borrado con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerFiltradoId($request, $response, $args){
        $id= $args['id'];
        $lista = Arma::obtenerArma($id);
        
        $response->getBody()->write(json_encode($lista));
        return $response;
    }


    public function TraerUno( $request,  $response,  $args) {
        $id = $args['id'];
        $producto = new Arma();
        $producto->id = $id;
        $producto->obtenerArma($id);
        $payload = json_encode($producto);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}