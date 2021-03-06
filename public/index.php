<?php 
//Fromt controler

//iniciamos como se mostraran los errores
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once '../vendor/autoload.php';
//Inicialializamos la sesion 
session_start(); 

//Utilizamos variables de entorno
$dotenv = new Dotenv\Dotenv(__DIR__ . '/..');
$dotenv->load();

//Conexion con la base de datos 
use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_NAME'),
    'username'  => getenv('DB_USER'),
    'password'  => getenv('DB_PASS'),
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();

// Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
$capsule->bootEloquent();

$baseDir = str_replace(basename($_SERVER['SCRIPT_NAME']),'',$_SERVER['SCRIPT_NAME']);
$baseUrl = 'http://'.$_SERVER['HTTP_HOST'].$baseDir;

define('BASE_URL',$baseUrl);

$route = $_GET['route'] ?? '/';

/* Uso de rutas 42 */
use Phroute\Phroute\RouteCollector;

$router = new RouteCollector();

//Filtros
$router->filter('auth', function(){
    if(!isset($_SESSION['userId'])){
        header('Location: '. BASE_URL.'auth/login');
        return false;
    }
});


//Ruta principal
$router->controller('/', app\controllers\IndexController::class);//::class devuelve el nombre de la clase
//Ruta Autentificacion 
$router->controller('/auth', app\controllers\AuthController::class);
//Ruta detalle
$router->controller('/detalle', app\controllers\DetalleController::class);

//Grupo de filtros inicial - en estas rutas se verifica el user login
$router->group(['before' => 'auth'], function($router){
    //Ruta Administrador
    $router->controller('/admin', app\controllers\admin\IndexController::class);
    //Ruta Insert
    $router->controller('/admin/posts', app\controllers\admin\PostController::class); 
    $router->controller('/admin/users', app\controllers\admin\UserController::class); 
});

$dispatcher = new Phroute\Phroute\Dispatcher($router->getData());
$response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'],$route);

echo $response;

?>