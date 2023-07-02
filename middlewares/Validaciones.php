<?php
 
 use Firebase\JWT\JWT;

 use Psr\Http\Message\ServerRequestInterface as Request;
 use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
 use Slim\Psr7\Response;
class Validaciones
  { 
    public function ValidarJWT( $request,  $handler) 
    {

        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
      
        $response = new Response();

        try
        {
          
              AutentificadorJWT::ValidarToken($token);
                $response= $handler->handle($request);
         
         }
         catch(Exception $excepcion)
        {
            $response->getBody()->write(json_encode(array("Error" => $excepcion->getMessage())));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }


    public function ValidarAdmin( $request,  $handler) 
    {

        $header = $request->getHeaderLine(("Authorization"));
        //$token = trim(explode("Bearer", $header)[1]);
        sscanf($header, 'Bearer %s', $token);
        $response = new Response();
var_dump($token);
        try
        {
          
          if($token != 'null')
          { 
             $data = AutentificadorJWT::ObtenerData($token); 

          
            if($data->tipo == "admin")
            {
                $response= $handler->handle($request);
            }
            else
            {       
                $response->getBody()->write(json_encode(array('Error' => "Accion reservada solamente para los administradores.")));
            }
        }else
        {
            $response->getBody()->write(json_encode(array('Error' => "Token invalido.")));
        }
    }
         catch(Exception $excepcion)
        {
            $response->getBody()->write(json_encode(array("Error" => $excepcion->getMessage())));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ValidarVendedor( $request,  $handler) 
    {

        $header = $request->getHeaderLine(("Authorization"));
        $token = trim(explode("Bearer", $header)[1]);
        $response = new Response();

        try
        {
          
          if($token != 'null')
          { 
             $data = AutentificadorJWT::ObtenerData($token); 

          
            if($data->tipo == "vendedor")
            {
                $response= $handler->handle($request);
            }
            else
            {       
                $response->getBody()->write(json_encode(array('Error' => "Accion reservada solamente para los administradores.")));
            }
        }else
        {
            $response->getBody()->write(json_encode(array('Error' => "Token invalido.")));
        }
    }
         catch(Exception $excepcion)
        {
            $response->getBody()->write(json_encode(array("Error" => $excepcion->getMessage())));
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
   }
?>





