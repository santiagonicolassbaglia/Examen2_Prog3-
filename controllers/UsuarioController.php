<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';
require_once './db/AccesoDatos.php';
 

class UsuarioController extends Usuario implements IApiUsable
{
    // public function CargarUno($request, $response, $args)
    // {
    //     $parametros = $request->getParsedBody();

    //     $usuario = $parametros['usuario'];
    //     $clave = $parametros['clave'];

    //     // Creamos el usuario
    //     $usr = new Usuario();
    //     $usr->usuario = $usuario;
    //     $usr->clave = $clave;
    //     $usr->crearUsuario();

    //     $payload = json_encode(array("mensaje" => "Usuario creado con exito"));

    //     $response->getBody()->write($payload);
    //     return $response
    //       ->withHeader('Content-Type', 'application/json');
    // }

    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();
    
        $requiredParams = ['usuario', 'clave', 'mail', 'tipo'];
    
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
     
        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $mail = $parametros['mail'];
        $tipo = $parametros['tipo'];

    
        $usu = new Usuario();
        $usu->usuario= $usuario;
        $usu->clave= $clave;
        $usu->mail= $mail;
        $usu->tipo= $tipo;

    
    
        //  //CrearToken
        //   $datos = array('usuario' => $usuario, 'clave' => $clave);
        //   $token = AutentificadorJWT::CrearTokenUsuario($datos);
        //   $payload = json_encode(array('usuario' => $usuario, 'jwt' => $token));
        //   $response->getBody()->write($payload);
       
            
       
        $usu -> crearUsuario();
    
    
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



    public function TraerUno($request, $response, $args)
    {
        // Buscamos usuario por nombre
        $usr = $args['usuario'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $id = $args['id'];
        $producto = new Usuario();
        $producto->id = $id;
        $producto->borrarUsuario($id);
        $payload = json_encode(array("mensaje" => "Arma borrado con exito"));
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
  
}
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 
 ?>