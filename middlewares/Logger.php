<?php
 
require_once('./models/Usuario.php');
require_once ('./middlewares/AutentificadorJWT.php');
                                                                                      
use Slim\Psr7\Response as ResponseMW;

class Logger
{
    public function GenerarToken($request, $handler): ResponseMW {
		$parametros= $request->getParsedBody();
		$response= new ResponseMW();

		if($request->getMethod()=="GET")
		{
		 $response->getBody()->write('<p>NO necesita credenciales para los get </p>');
         $response= $handler->handle($request);
		}else{
			$parametros = $request->getParsedBody();
			 $usuario=  Usuario::obtenerUsuario($parametros['usuario']);

             if( $usuario->clave == $parametros['clave'] )
             { 
                $usuario->usuario= $parametros['usuario'];
			$usuario->clave= $parametros['clave'];
			$usuario->tipo= $parametros['tipo'];
			$datos = array('usuario' => $usuario->usuario,'perfil' => $usuario->tipo);
            
			$token= AutentificadorJWT::CrearToken($datos);
		
             }
             
           

			
			
       			
		//$response= new ResponseMW();
			//echo "Token: " .  $token;
			//$response->getBody()->write($token);
			// $response= $handler->handle($request);
            $response->getBody()->write($token);
		}
		return $response;   
	}
    // public function GenerarToken($request, $handler): ResponseMW {
	// 	$parametros= $request->getParsedBody();
	// 	$response= new ResponseMW();

	// 	if($request->getMethod()=="GET")
	// 	{
	// 	 $response->getBody()->write('<p>NO necesita credenciales para los get </p>');
    //      $response= $handler->handle($request);
	// 	}else{
	// 		$parametros = $request->getParsedBody();
	// 		$usuario= new Usuario();
	// 		$usuario->usuario= $parametros['usuario'];
	// 		$usuario->clave= $parametros['clave'];
	// 		$usuario->tipo= $parametros['tipo'];
	// 		$datos = array('usuario' => $usuario->usuario,'perfil' => $usuario->tipo);
			
	// 		$token= AutentificadorJWT::CrearToken($datos);
					
	// 	//$response= new ResponseMW();
	// 		echo "Token: " .  $token;
	// 		//$response->getBody()->write($token);
	// 		$response= $handler->handle($request);
	// 	}
	// 	return $response;   
	// }

	public function Loguin($request, $handler): ResponseMW {
        $parametros = $request->getParsedBody();
        $response = new ResponseMW();
    
        if (isset($parametros['mail']) && isset($parametros['clave'])) {
            $usuario = Usuario::obtenerUsuario($parametros['mail']);
            if ($usuario) {
                $datos = array(
                    'id' => $usuario->id,
                    'usuario' => $usuario->usuario,
                    'perfil' => $usuario->tipo
                );
                $token = AutentificadorJWT::CrearToken($datos);
                $response->getBody()->write("Inicio de sesión exitoso. Su token es: \n" . $token);
            } else {
                $response->getBody()->write("No existe una cuenta con ese usuario.");
            }
        } else {
            $response->getBody()->write("No se ingresaron las credenciales de inicio de sesión.");
        }
    
        return $response;
    }

    public function VerificarToken($request, $handler): ResponseMW {
		$objDelaRespuesta= new stdclass();
		$seccion= self::Prueba($_SERVER['REQUEST_URI']);
		$objDelaRespuesta->respuesta="";
		$parametros= $request->getParsedBody();
		$response= new ResponseMW();
		$token="";
		if($request->getMethod()=="GET")
			{
			$response->getBody()->write('<p>NO necesita credenciales para los get </p>');
			$response= $handler->handle($request);
			return $response;
			}else{
		try {
			if(isset($parametros['token'])){
				$token= $parametros['token'];
				AutentificadorJWT::verificarToken($token);
				$objDelaRespuesta->esValido=true;   
			}  else $objDelaRespuesta->esValido=false;
		}
		catch (Exception $e) {      
			$objDelaRespuesta->excepcion=$e->getMessage();
			$objDelaRespuesta->esValido=false;     
		}
	}

		if($objDelaRespuesta->esValido)
		{		
			$payload=AutentificadorJWT::ObtenerData($token);
			switch($seccion)	{
				case 'ventaArmas':
					$response = $handler->handle($request);
					break;
				default:
				if($payload->perfil=="admin")
				{
					$response = $handler->handle($request);
				}		           	
				else
				{	
					$objDelaRespuesta->respuesta="Solo administradores";
				}
				break;
			}			
				          
		}    
		else
		{
			$objDelaRespuesta->respuesta="Solo usuarios registrados";
			$objDelaRespuesta->elToken=$token;

		}  				
	$response->getBody()->write($objDelaRespuesta->respuesta);
	return $response;				
	}

	private function Prueba($string){
		$array=explode("/",$string);

		return $array[3];
	}

	public function VerificarTokenGet($request, $handler){
		$objDelaRespuesta= new stdclass();
		$objDelaRespuesta->respuesta="";
		$seccion= self::Prueba($_SERVER['REQUEST_URI']);
		$parametros= $request->getQueryParams();
		$response= new ResponseMW();
		$token="";
		try 
		{
			if(isset($parametros['token'])){
				$token= $parametros['token'];
				AutentificadorJWT::verificarToken($token);
				$objDelaRespuesta->esValido=true;   
			}  else $objDelaRespuesta->esValido=false;
		}
		catch (Exception $e) {      
			$objDelaRespuesta->excepcion=$e->getMessage();
			$objDelaRespuesta->esValido=false;     
		}
		if($objDelaRespuesta->esValido)
		{		
			$payload=AutentificadorJWT::ObtenerData($token);
			switch($seccion){
				case 'ventas':
					if($payload->tipo == 'admin'){
						$response = $handler->handle($request);		
						break;
					}else {
						$objDelaRespuesta->respuesta="Solo admins";
						break;
					}
				default:
				$response = $handler->handle($request);		
				break;
			}     
		}    
		else
		{
			$objDelaRespuesta->respuesta="Solo usuarios registrados";
			$objDelaRespuesta->elToken=$token;

		}  				
	$response->getBody()->write($objDelaRespuesta->respuesta);
	return $response;				
	}

}