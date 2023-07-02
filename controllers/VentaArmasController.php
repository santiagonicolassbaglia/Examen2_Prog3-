<?php
require_once './models/VentaArmas.php';
require_once './models/Arma.php';
 

class VentaArmasController extends VentaArma  
{
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        $requiredParams = ['idUsuario', 'idArma', 'cantidad', 'fecha'];
    
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
    
        $cantidad = $parametros['cantidad'];
        $fecha = $parametros['fecha'];

          // Procesar el archivo de imagen
          $uploadedFiles = $request->getUploadedFiles();
          if (isset($uploadedFiles['foto'])) {
              $foto = $uploadedFiles['foto'];
              if ($foto->getError() === UPLOAD_ERR_OK) {
                  // Obtener información del archivo
                  $nombreArchivo = $foto->getClientFilename();
                  $tipoArchivo = $foto->getClientMediaType();
                  $ubicacionTemporal = $foto->getStream()->getMetadata('uri');
     
                  $arma = Arma::ObtenerArma( $idArma );
                $usuario = Usuario::obtenerUsuarioPorId( $idUsuario );
            
                  $nuevaUbicacion = './FotosArma2023/' . $arma->nombre . $usuario->usuario  . date( 'Y-M-D' ).'.jpg';
           
 
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
      
        
        $usu = new VentaArma();
        $usu->idUsuario = $idUsuario;
        $usu->idArma = $idArma;
       // $usu->foto = $nuevaUbicacion;
        $usu->cantidad = $cantidad;
        $usu->fecha = $fecha;
        $usu->crearVentaArma();

      
    
    
        $payload = json_encode(array("mensaje" => "El usuario ah sido creado" ));
    
        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
    
   
    
    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }


    public function TraerTodosPorNacionalidadYFecha($request, $response, $args)
    {$nacionalidad= $args['nacionalidad'];
        $primerFecha= $args['primerFecha'];
        $segundaFecha= $args['segundaFecha'];
        $lista =  VentaArma::obtenerTodosNacionalidadYFechas($nacionalidad,$primerFecha,$segundaFecha);
        $payload = json_encode(array("Lista de ventaarmas" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerFiltrado($request, $response, $args){
        $nombre= $args['nombre'];
        $lista = VentaArma::obtenerTodosNombre( $nombre);
        
        $response->getBody()->write(json_encode($lista));
        return $response;
    }
    
}
