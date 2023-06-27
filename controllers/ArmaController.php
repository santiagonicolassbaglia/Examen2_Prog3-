<?php
require_once './models/Arma.php';
 
require_once './interfaces/IApiUsable.php';
require_once './db/AccesoDatos.php';
 

 

class ArmaController extends Arma implements IApiUsable
{ 
    // public function CargarUno($request, $response, $args)
    // {
    //     $parametros = $request->getParsedBody();
    
    //     $requiredParams = ['nombre', 'precio', 'foto', 'nacionalidad','stock'];
    
    //     $missingParams = [];
    //     foreach ($requiredParams as $param) {
    //         if (!isset($parametros[$param])) {
    //             $missingParams[] = $param;
    //         }
    //     }
       
    //     if (!empty($missingParams)) {
    //         $payload = json_encode(array("error" => "Falta el campo: " . implode(', ', $missingParams)));
    //         $response->getBody()->write($payload);
    //         return $response
    //             ->withStatus(400)
    //             ->withHeader('Content-Type', 'application/json');
    //     }
     
    //     $nombre = $parametros['nombre'];
    //     $precio = $parametros['precio'];
    //     $foto = $parametros['foto'];
    //     $nacionalidad = $parametros['nacionalidad'];
    //     $stock = $parametros['stock'];
    //     var_dump($foto);
    
    //     $producto = new Arma();
    //     $producto->nombre= $nombre;
    //     $producto->precio= $precio;
    //     $producto->foto= $foto;
    //     $producto->nacionalidad= $nacionalidad;
    //     $producto->stock= $stock;
    //     $producto->CrearArma();

    //     $payload = json_encode(array("mensaje" => "Arma creado con exito"));

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');


    // }





    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        
        $requiredParams = ['nombre', 'precio', 'nacionalidad', 'stock'];
        
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
        $nacionalidad = $parametros['nacionalidad'];
        $stock = $parametros['stock'];
    
        // Procesar el archivo de imagen
        $uploadedFiles = $request->getUploadedFiles();
        if (isset($uploadedFiles['foto'])) {
            $foto = $uploadedFiles['foto'];
            if ($foto->getError() === UPLOAD_ERR_OK) {
                // Obtener información del archivo
                $nombreArchivo = $foto->getClientFilename();
                $tipoArchivo = $foto->getClientMediaType();
                $ubicacionTemporal = $foto->getStream()->getMetadata('uri');
    
               
                $nuevaUbicacion = './FotosArma2023' . $nombreArchivo;
                $foto->moveTo($nuevaUbicacion);
            } else {
                // Manejar el error de carga del archivo
                $error = $foto->getError();
                $payload = json_encode(array("error" => "Error al cargar el archivo: " . $error));
                $response->getBody()->write($payload);
                return $response
                    ->withStatus(400)
                    ->withHeader('Content-Type', 'application/json');
            }
        } else {
            // Manejar el caso en el que no se haya proporcionado el campo "foto"
            $payload = json_encode(array("error" => "Falta el campo: foto"));
            $response->getBody()->write($payload);
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }
    
        $producto = new Arma();
        $producto->nombre = $nombre;
        $producto->precio = $precio;
        $producto->foto = $nombreArchivo; 
        $producto->nacionalidad = $nacionalidad;
        $producto->stock = $stock;
        $producto->CrearArma();
    
        $payload = json_encode(array("mensaje" => "Arma creado con éxito"));
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }







    // public function CargarUno($request, $response, $args)
    // {
    //     $parametros = $request->getParsedBody();
        

    //     $nombre = $parametros['nombre'];
    //     $precio = $parametros['precio'];
    //     $foto = $parametros['foto'];
    //     $nacionalidad = $parametros['nacionalidad'];
    //     $stock = $parametros['stock'];
    //     var_dump($foto);

    //     $usuario = new Arma();
    //     $usuario->nombre= $nombre;
    //     $usuario->precio= $precio;
    //     $usuario->foto= $foto;
    //     $usuario->nacionalidad= $nacionalidad;
    //     $usuario->stock= $stock;



    //     $usuario->CrearArma();

    //     $payload = json_encode(array("mensaje" => "Cripto creada con exito"));

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }

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