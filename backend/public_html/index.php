<?php
/**
 * @author Sam Hoque
 */
use App\Controllers\Authentication;
use App\Models\User;
use Illuminate\Database\Capsule\Manager as Capsule;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

session_set_cookie_params(604800);
session_name("Nuxt-Auth");
session_start();
require __DIR__ . '/vendor/autoload.php';


$app = AppFactory::create();


/**
 * Add Error Middleware
 *
 * @param bool $displayErrorDetails -> Should be set to false in production
 * @param bool $logErrors -> Parameter is passed to the default ErrorHandler
 * @param bool $logErrorDetails -> Display error details in error log
 * @param LoggerInterface|null $logger -> Optional PSR-3 Logger
 *
 * Note: This middleware should be added last. It will not handle any exceptions/errors
 * for middleware added after it.
 */
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

/**
 * Database
 */
$capsule = new Capsule;

$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'mysql',
    'database' => 'nuxt-auth',
    'username' => 'root',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);


// Make this Capsule instance available globally via static methods... (optional)
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($container) use ($capsule) {
    return $capsule;
};

$schema = $capsule->getDatabaseManager()->getSchemaBuilder();

/**
 * Init db
 */
if (!$schema->hasTable('users')) {
    $schema->create('users', function ($table) {
        $table->increments('id');
        $table->string('username', 36);
        $table->string('pass_hash');
        $table->timestamps();
    });
}


// Setting up CORS.
// https://www.slimframework.com/docs/cookbook/enable-cors.html
$app->addBodyParsingMiddleware();

$app->add(function(ServerRequestInterface $request, RequestHandlerInterface $handler) {
    $response = $handler->handle($request);
    $response = $response->withHeader('Access-Control-Allow-Origin', 'http://localhost');
    $response = $response->withHeader('Access-Control-Allow-Methods', "POST, GET, OPTIONS, PUT, DELETE");
    $response = $response->withHeader('Access-Control-Allow-Headers', "X-Requested-With, Content-Type, X-Auth-Token, Origin, Authorization");

    // Optional: Allow Ajax CORS requests with Authorization header
    $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
    return $response;
});

/**
 * The routing middleware should be added earlier than the ErrorMiddleware
 * Otherwise exceptions thrown from it will not be handled by the middleware
 */
$app->addRoutingMiddleware();

$app->get('/', function (Request $request, Response $response, $args) {
    if(isset($_SESSION['user'])) {
        $usr = User::where('id', $_SESSION['user'])->first();
        $response->getBody()->write('Welcome back, ' . $usr->username);
    } else {
        $response->getBody()->write("Not Logged In");
    }
    return $response;
});

$app->post('/login', [Authentication::class, 'login']);
$app->post('/register', [Authentication::class, 'register']);
$app->get('/logout', [Authentication::class, 'logout']);

// Catch-all route to serve a 404 Not Found page if none of the routes match
// NOTE: make sure this route is defined last
$app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function($req, $res) {
    $handler = $this->notFoundHandler; // handle using the default Slim page not found handler
    return $handler($req, $res);
});
$app->run();