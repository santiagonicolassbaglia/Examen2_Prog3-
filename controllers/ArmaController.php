<?php
require_once './models/Arma.php';
 
require_once './interfaces/IApiUsable.php';
require_once './db/AccesoDatos.php';
 require_once'./models/archivosCSVoPDF.php';


 

class ArmaController extends Arma implements IApiUsable
{ 
      
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
    
             
                $nuevaUbicacion = './FotosArma/' . $nombreArchivo;
              
                
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


    


    public function TraerTodos($request, $response, $args)
    {
      $lista = Arma::obtenerTodos();
      $payload = json_encode(array("Lista de Armas" => $lista));
  
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }
////////////////////////////////////////////

public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
        $arma = new Arma();
        $arma->id = $parametros['id'];
        $arma->nombre = $parametros['nombre'];
        $arma->precio = $parametros['precio'];
        $arma->nacionalidad = $parametros['nacionalidad'];
        $arma->stock = $parametros['stock'];
        $arma->foto = $this->MoverFoto($parametros['foto']);
     
        Arma::ModificarArma($arma);
        $payload = json_encode(array("mensaje" => "Arma modificada con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }



    public function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
        $producto = new Arma();
        $producto->id = $id;
        $producto->cargarLog(1, $id);
        $producto->borrarArma($id);
        $payload = json_encode(array("mensaje" => "Arma borrado con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    private function MoverFoto($nombre)
    { 
         $carpetaBackup = ".".DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR."Backup_2023".DIRECTORY_SEPARATOR;
        $carpeta = ".".DIRECTORY_SEPARATOR."img".DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR;
      
        $nombreFoto = $carpeta."fotoArma".$nombre.".jpg";
        if(file_exists($nombreFoto))
        {
            if(!file_exists($carpetaBackup))
            {
                mkdir($carpetaBackup, 0777, true);
            }
            copy($nombreFoto, $carpetaBackup."fotoArma".$nombre.".jpg");
        }
        else
        {
            echo "La foto que desea adjuntar no existe.";
        }
        return $nombreFoto;
    }





    public static function ExportarCSV($path)
    {
        $listaProductos = Arma::obtenerTodos();
        $file = fopen($path, "w");
        foreach($listaProductos as $producto)
        {
            $separado= implode(",", (array)$producto);  
            if($file)
            {
                fwrite($file, $separado.",\r\n"); 
            }                           
        }
        fclose($file);  
        return $path;     
    }





    public function ExportarArma($request, $response, $args)
    {
        try
        {
            $archivo = ArchivosCSVoPDF::ExportarCSV("./csv/Armas.csv"); 
            if(file_exists($archivo) && filesize($archivo) > 0)
            {
                $payload = json_encode(array("Archivo creado:" => $archivo));
            }
            else
            {
                $payload = json_encode(array("Error" => "Datos ingresados invalidos."));
            }
            $response->getBody()->write($payload);
        }
        catch(Exception $e)
        {
            echo $e;
        }
        finally
        {
            return $response->withHeader('Content-Type', 'text/csv');
        }    
    }





///////////////////////////////////////////////

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

    public function TraerFiltradoPorNacionalidad( $request,  $response,  $args) {
        $nacionalidad = $args['nacionalidad'];

        $armas=  Arma::ObtenerArmaPorNacionalidad($nacionalidad);
 
        $payload = json_encode($armas);
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}