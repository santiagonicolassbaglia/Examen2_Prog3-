<?php
error_reporting(-1);
ini_set('display_errors', 1);
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;





require_once './db/AccesoDatos.php';
require __DIR__ . "./vendor/autoload.php";
 require_once './middlewares/AutentificadorJWT.php';
require_once './middlewares/Logger.php';

require_once './controllers/ArmaController.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/VentaArmasController.php';
 

// Load ENV
//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
//$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);


//$app->post('/usuario', \UsuarioController::class . ':CargarUno');


// JWT
// $app->group('/autentificacion', function (RouteCollectorProxy $group) {

//     $group->post('/crearToken', function (Request $request, Response $response) {    
//       $parametros = $request->getParsedBody();
     
//       $usuario = $parametros['usuario'];
//       $contraseña = $parametros['clave'];
  
//       $datos = array('usuario' => $usuario, 'clave' => $contraseña);
  
//       try 
//       {
//         $token = AutentificadorJWT::CrearTokenEmpleado($datos);
//         $payload = json_encode(array('usuario' => $usuario, 'jwt' => $token));
//       } 
//       catch (Exception $e) 
//       {
//         $payload = json_encode(array('error' => $e->getMessage()));
//       }
  
//       $response->getBody()->write($payload);
//       return $response
//         ->withHeader('Content-Type', 'application/json');
//     });
//   });

 
  // $app->group('/login', function (RouteCollectorProxy $group){
  //   $group->post('[/]', \Logger::class . ':Login');
  // });
  
  // $app->group('/usuario', function (RouteCollectorProxy $group) {
  //     $group->get('[/]', \UsuarioController2::class . ':TraerTodos');
  //     $group->post('[/]', \UsuarioController2::class . ':CargarUno');
   // });//->add(\Logger::class . ':GenerarToken')->add(\Logger::class . ':VerificarToken');
  
  // $app->group('/arma', function (RouteCollectorProxy $group) {
  //   $group->post('[/]', \ArmaController::class . ':CargarUno');
    // $group->get('/{nacionalidad}', \ArmaController::class . ':TraerFiltrado');
    // $group->get('/unico/{id}', \ArmaController::class . ':TraerFiltradoId');//->add(\Logger::class . ':VerificarTokenGet');
    // $group->get('[/]', \ArmaController::class . ':TraerTodos');
  
  // })->add(\Logger::class . ':VerificarToken');
  
  // $app->group('/venta', function (RouteCollectorProxy $group){
  //   $group->post('[/]', \VentaArmasController::class . ':CargarUno');
  //   $group->get('/{primerFecha}/{segundaFecha}', \VentaArmasController::class . ':TraerTodos');//->add(\Logger::class . ':VerificarTokenGet');
  //   $group->get('/{nombre}', \VentaArmasController::class . ':TraerFiltrado');//->add(\Logger::class . ':VerificarTokenGet');
  // })->add(\Logger::class . ':VerificarToken');
  
  
  $app->post("/arma", \ArmaController::class. ":CargarUno");
  $app->post("/usuario", \UsuarioController::class. ":CargarUno");
  $app->run();

?>