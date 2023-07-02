<?php
 
 use Firebase\JWT\JWT;

class AutentificadorJWT
  { 
    private static $miClaveSecreta = "tioSanti"; //Clave Secreta
    private static $algoritmoDeCodificacion = ['HS256'];  
    private static $aud = null;

    public static function NuevoToken($data)
    {
        $ahora = time();
        $payload = array(
            'iat' => $ahora,
            // 'exp' => $ahora + 60+60*24, // 1 mes
            'aud' => self::Aud(),
            'data' => $data
        );
        return JWT::encode($payload, self::$miClaveSecreta);
    }

    public static function ValidarToken($token)
    {
        if($token == "" || empty($token))
        {
            throw new Exception("El token esta vacio!");
        }
        try
        {
            $payload = AutentificadorJWT::ObtenerPayLoad($token);
        }
        catch (Exception $excepcion)
        {
            throw $excepcion;
        }
        if($payload->aud !== self::Aud())
        {
            throw new Exception("Usuario o contraseña no validos!");
        }
    }

    private static function Aud()
    {
        $aud = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
        {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } 
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
        {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } 
        else 
        {
            $aud = $_SERVER['REMOTE_ADDR'];
        }
        
        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();
        
        return sha1($aud);
    }

    public static function ObtenerPayLoad($token)
    {
        return JWT::decode(
            $token,
            self::$miClaveSecreta,
            self::$algoritmoDeCodificacion
        );
    }
    
    public static function ObtenerData($token)
    { 
        return JWT::decode(
            $token,
            self::$miClaveSecreta,
            self::$algoritmoDeCodificacion
        )->data;
    }
   }
?>





