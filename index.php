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
require_once './middlewares/ConToken.php';
require_once './controllers/LoginController.php';
require_once './controllers/ArmaController.php';
require_once './controllers/UsuarioController.php';
require_once './controllers/VentaArmasController.php';
 require_once './middlewares/SoloAdmin.php';
 require_once './middlewares/Validaciones.php';
// Load ENV
//$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
//$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Add error middleware
$app->addErrorMiddleware(true, true, true);


//$app->post('/usuario', \UsuarioController::class . ':CargarUno');



$app->post('/login', \LoginController::class . ':GenerarToken');
  
  $app->group('/usuario', function (RouteCollectorProxy $group) {
      $group->get('[/]', \UsuarioController::class . ':TraerTodos');
      $group->post('[/]', \UsuarioController::class . ':CargarUno');
   } );
 
  
    $app->group('/arma', function (RouteCollectorProxy $group) {
    $group->post('[/]', \ArmaController::class . ':CargarUno') 
    ->add(\Validaciones::class . ':ValidarAdmin') ;
    $group->get('/{nacionalidad}', \ArmaController::class . ':TraerFiltradoPorNacionalidad');
    $group->get('/traer/{id}', \ArmaController::class . ':TraerFiltradoId')
    ->add(\Validaciones::class . ':ValidarJWT') ;
    $group->get('[/]', \ArmaController::class . ':TraerTodos') 
    ->add(\Validaciones::class . ':ValidarJWT') ;
    // $group->put('/', \ArmaController::class . ':ModificarUno')->add(new SoloAdmin());
  $group->delete('/{id}', \ArmaController::class . ':BorrarUno') 
  ->add(\Validaciones::class . ':ValidarAdmin') ;
  $group->get('/csv/', \ArmaController::class . ':ExportarArma');
  });
   
  
  $app->group('/ventaArmas', function (RouteCollectorProxy $group){
    $group->post('[/]', \VentaArmasController::class . ':CargarUno') 
    ->add(\Validaciones::class . ':ValidarJWT') ; 
    $group->get('[/]', \VentaArmasController::class . ':TraerTodos') ->add(new SoloAdmin()) ;
    $group->get('/{primerFecha}/{segundaFecha}/{nacionalidad}', \VentaArmasController::class . ':TraerTodosPorNacionalidadYFecha')  
    ->add(\Validaciones::class . ':ValidarJWT') ; 
    
    $group->get('/{nombre}', \VentaArmasController::class . ':TraerFiltrado');
    
  });
   
  
  
  $app->put("/arma", \ArmaController::class. ":ModificarUno");
  // $app->post("/usuario", \UsuarioController::class. ":CargarUno");
  //  $app->post("/usuario", \UsuarioController::class. ":CargarUno");
  $app->run();

?>