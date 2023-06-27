<?php
 
 use Firebase\JWT\JWT;

class AutentificadorJWT
  { 
private static $claveSecreta = 'JWT';
    private static $tipoEncriptacion = ['HS256'];
    private static $aud = null;
    
    public static function CrearToken($datos)
    {
        $ahora = time();
        $payload = array(
            'iat'=>$ahora,
            'exp' => $ahora + (500000000),
            'aud' => self::Aud(),
            'data' => $datos,
            'app'=> "JWT"
        );
     
        return JWT::encode($payload, self::$claveSecreta);
    }
    
    public static function VerificarToken($token)
    {
        if (empty($token)) 
        {
            throw new Exception("El token está vacío.");
        }
    
        try 
        {
            // Se decodifica el token JWT utilizando la clave secreta y el tipo de encriptación
            $decodificado = JWT::decode($token, self::$claveSecreta, self::$tipoEncriptacion);
        } 
        catch (Exception $e) 
        {
            // Si ocurre una excepción durante la decodificación, se lanza nuevamente para que sea manejada por el código que llamó a este método
               ;throw $e;
          
        }
    
        if ($decodificado->aud !== self::Aud()) 
        {
            // Si el campo "aud" del token no coincide con el resultado de la función Aud(), se lanza una excepción indicando que el usuario no está autorizado
            throw new Exception("Usuario no autorizado");
        }
    
        // Se retorna el token decodificado
        return $decodificado;
    }
    
   
     public static function ObtenerPayLoad($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        );
    }
     public static function ObtenerData($token)
    {
        return JWT::decode(
            $token,
            self::$claveSecreta,
            self::$tipoEncriptacion
        )->data;
    }
    private static function Aud()
    {
        $aud = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }
        
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();
        
        return sha1($aud);
   }
 }





