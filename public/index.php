<?php

// Inclure l'autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Import des classes
use App\config\Config;
use App\utils\Response; 

// Charger nos variable d'environnement
Config::load();

// Démarrer une séssion ou reprendre la séssion existante
session_start();


// Définir des routes avec la bibliothèque FastRoute
$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r){

    /* --------------------Route gérer par HomeController----------------------------- */
    $r->addRoute('GET', '/', [App\controller\HomeController::class, 'index']);
    $r->addRoute('GET', '/mentionLegal', [App\controller\HomeController::class, 'mentionLegal']);

    /* --------------------Route gérer par ProfileController----------------------------- */
    $r->addRoute('GET', '/profile', [App\controller\ProfileController::class, 'profile']);
    $r->addRoute('POST', '/addCar', [App\controller\ProfileController::class, 'addCar']);

    /* --------------------Route gérer par ContactController----------------------------- */
    $r->addRoute('GET', '/contact', [App\controller\ContactController::class, 'showContact']);
    $r->addRoute('POST', '/contact', [App\controller\ContactController::class, 'handleSubmitEmail']);

    /* --------------------Route gérer par AuthController----------------------------- */
    $r->addRoute('GET', '/login', [App\controller\AuthController::class, 'showLogin']);
    $r->addRoute('POST', '/login', [App\controller\AuthController::class, 'login']);
    $r->addRoute('GET', '/register', [App\controller\AuthController::class, 'showRegister']);
    $r->addRoute('POST', '/register', [App\controller\AuthController::class, 'register']);
    $r->addRoute('POST', '/logout', [App\controller\AuthController::class, 'logout']);
    
    /* --------------------Route gérer par RidesharingController----------------------------- */
    $r->addRoute('GET', '/search', [App\controller\RidesharingController::class, 'showSearchRidesharing']);    
    $r->addRoute('POST', '/search', [App\controller\RidesharingController::class, 'searchRidesharing']);    
    $r->addRoute('GET', '/ridesharingDetail/{id:\d+}', [App\controller\RidesharingController::class, 'showRidesharingDetail']);
    $r->addRoute('GET', '/myRidesharing', [App\controller\RidesharingController::class, 'myRidesharing']);
    $r->addRoute('POST', '/cancelRide/{id:\d+}', [App\controller\RidesharingController::class, 'cancelRidesharing']);
    $r->addRoute('POST', '/startRide/{id:\d+}', [App\controller\RidesharingController::class, 'startRidesharing']);
    $r->addRoute('POST', '/completeRide/{id:\d+}', [App\controller\RidesharingController::class, 'completeRide']);
    $r->addRoute('GET', '/showCreateRidesharing', [App\controller\RidesharingController::class, 'showCreateRidesharing']);
    $r->addRoute('POST', '/createRidesharing', [App\controller\RidesharingController::class, 'createRidesharing']);

    /* --------------------Route gérer par ParticipateController----------------------------- */
    $r->addRoute('POST', '/participate', [App\controller\ParticipateController::class, 'participateToRidesharing']);
    $r->addRoute('POST', '/cancelParticipation/{id:\d+}', [App\controller\ParticipateController::class, 'cancelParticipation']);

    /* --------------------Route gérer par ReviewController----------------------------- */
    $r->addRoute('POST', '/letReview', [App\controller\ReviewController::class, 'letReview']);

    /* --------------------Route gérer par EmployeeController----------------------------- */
    $r->addRoute('GET', '/employeeSpace', [App\controller\EmployeeController::class, 'showEmployeeSpace']);
    $r->addRoute('POST', '/approvedReview', [App\controller\EmployeeController::class, 'approvedReview']);
    $r->addRoute('POST', '/rejectReview', [App\controller\EmployeeController::class, 'rejectReview']);

    /* --------------------Route gérer par AdminController----------------------------- */
    $r->addRoute('GET', '/adminSpace', [App\controller\AdminController::class, 'showAdminSpace']);
    $r->addRoute('POST', '/getParticipationInfoPerWeek', [App\controller\AdminController::class, 'getParticipationInfoPerWeek']);
    $r->addRoute('POST', '/getCreditInfoPerWeek', [App\controller\AdminController::class, 'getCreditInfoPerWeek']);
    $r->addRoute('POST', '/createEmployee', [App\controller\AdminController::class, 'createEmployee']);
    $r->addRoute('POST', '/suspendUser', [App\controller\AdminController::class, 'suspendUser']);
});

// Traitement de la requête
// 1. Récupérer la méthode HTTP (GET, POST, PUT, PATCH) et l'URI(/login, /car/1)
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Nettoyer l'URI pour enlever le préfixe /public
if (strpos($uri, '/public') === 0) {
    $uri = substr($uri, 7); // Enlève '/public' (7 caractères)
}

// Si l'URI devient vide après nettoyage, mettre '/'
if (empty($uri)) {
    $uri = '/';
}

// 2. Dispatcher FastRoute
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
$response = new Response();

// 3. Annalyse du résultat du Dispatching
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        $response->error('404 - Page non trouvée', 404);
        break;
    
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $response->error('405 - Méthode non autorisée', 405);
        break;

    case FastRoute\Dispatcher::FOUND:
        [$controllerClass, $method] = $routeInfo[1];
        $vars = $routeInfo[2];
        try{
            $controller = new $controllerClass();
            $controller->$method(...array_values($vars));
        }catch(\Exception $e){
            if(Config::get('APP_DEBUG') === 'true'){
                $response->error("Erreur 500 : " . $e->getMessage() . " dans " . $e->getFile() . ":" . $e->getLine(), 500);
            }else{
                (new \App\utils\Logger())->log('ERROR', 'Erreur Serveur :' . $e->getMessage());
                $response->error("Une erreur interne est survenue.", 500);
            }
        }
        break;
}

