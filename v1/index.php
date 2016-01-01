<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

session_start();

require '../vendor/autoload.php';

$app = new \Slim\App();

// Register with container
$container = $app->getContainer();
$container['csrf'] = function ($c) {
    $guard = new \Slim\Csrf\Guard();
    $guard->setFailureCallable(function ($request, $response, $next) {
        $request = $request->withAttribute('csrf_status', false);
		var_dump($request);
        return $next($request, $response);
    });
    return $guard;
};

// Register middleware for all routes
// If you are implementing per-route checks you must not add this
$app->add($container->get('csrf'));

$app->get('/', function ($request, $response, $args) {
    $response->write("Welcome to Slim!");
    return $response;
});

$app->get('/hello[/{name}]', function ($request, $response, $args) {
    $response->write("Hello, " . $args['name']);
    return $response;
})->setArgument('name', 'World!');

$app->any('/test', function() use ($app) {
	//GET
	$allGetVars = $request->getQueryParams();
	print_r($allGetVars);
	foreach($allGetVars as $key => $param){
	   //GET parameters list
	}

	//POST or PUT
	$allPostPutVars = $request->getParsedBody();
	print_r($allPostPutVars);
	if (!empty($allPostPutVars)) {
		foreach($allPostPutVars as $key => $param){
		   //POST or PUT parameters list
		}
	}
});

$app->any('/auth', function($request, $response, $args) 
{
	// GET, POST or PUT
	$params = getParams($request);
	
	// Check for required params
	verifyRequired(array('email', 'password'), $params);
	
	// Validating email address
	validateEmail($params['email']);
	
	if ($params['email'] == 'admin@admin.com' && $params['password'] == '123456') {	
		// CSRF token name and value
		$nameKey  = $this->csrf->getTokenNameKey();
		$valueKey = $this->csrf->getTokenValueKey();
		$name     = $request->getAttribute($nameKey);
		$value    = $request->getAttribute($valueKey);

		$tokenArray = [
			$nameKey => $name,
			$valueKey => $value
		];
		
		return json_encode(array('success' => true, 'token' => $tokenArray));
	} else {
		return json_encode(array('success' => false, 'message' => 'Email o password invalid.'));
	}
});

$app->get('/get', function($request, $response, $args) 
{
	$csrf_result = $request->getAttribute('csrf_result');
	if (null === $csrf_result) {
        return json_encode(array('success' => false));
    } else {
        return json_encode(array('success' => true));
    }
});

$app->post('/post', function($request, $response, $args) 
{
	$csrf_result = $request->getAttribute('csrf_result');
    if (null === $csrf_result) {
        return json_encode(array('success' => false));
    } else {
		return json_encode(array('success' => true));
    }
});

$app->run();

/**
 * Request params
 */
function getParams($request)
{
	// POST or PUT
	$params = $request->getParsedBody();

	// GET
	if (empty($params)) {
		$params = $request->getQueryParams();
	}
	
	return $params;
}

/**
 * Verifying required params posted or not
 */
function verifyRequired($required_fields = array(), $params = array()) 
{
	// Var error
    $error = false;
    $error_fields = '';
	
	foreach ($required_fields as $field) {
        if (!isset($params[$field]) || strlen(trim($params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

	// Required field(s) are missing or empty
	// echo error json and stop the app
    if ($error) {
        $message = array('success' => false, 'message' => 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty');
        setResponse(400, $message);
    }
}

/**
 * Validating email address
 */
function validateEmail($email = null) 
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = array('success' => false, 'message' => 'Email address is not valid');
        setResponse(400, $message);
    }
}

/**
 * Echoing json response to client
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function setResponse($status_code = 200, $message = array()) 
{
	// App
	$app = new \Slim\App();

	// Register with container
	$container = $app->getContainer();
	$response = $container->get('response');
	
    // Http Status - ContentType
	$response->withStatus($status_code)->withHeader('Content-Type', 'application/json');
	
    echo json_encode($message);
	
	exit;
}
