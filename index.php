<?php

session_start();

require 'vendor/autoload.php';
require 'libs/db.php';

$app = new \Slim\App();

// Register with container
$container = $app->getContainer();
$container['csrf'] = function ($c) {
    $guard = new \Slim\Csrf\Guard();
    $guard->setFailureCallable(function ($request, $response, $next) {
        $request = $request->withAttribute('csrf_status', false);
        return $next($request, $response);
    });
    return $guard;
};

// Register middleware for all routes
// If you are implementing per-route checks you must not add this
$app->add($container->get('csrf'));

$app->any('/test', function() use ($app) {
	//GET variable
	$paramValue = $app->request->get('name');
	print_r($paramValue);
	echo '<br>param: 1';
	
	//POST variable
	$paramValue = $app->request->post('name');
	print_r($paramValue);
	echo '<br>param: 2';
	
	//PUT variable
	$paramValue = $app->request->put('name');
	print_r($paramValue);
	echo '<br>param: 3';
	
	$body = $app->request->getBody();
	$input = json_decode($body);
	print_r($input);
	echo '<br>param: 4';
});

$app->any('/auth', function($request, $response, $args) {
	//GET variable
	//$paramValue = $request->get('name');
	//print_r($paramValue);
	//echo '<br>param: 1';
	
	//POST variable
	//$paramValue = $request->post('name');
	//print_r($paramValue);
	//echo '<br>param: 2';
	
	//PUT variable
	//$paramValue = $request->put('name');
	//print_r($paramValue);
	//echo '<br>param: 3';
	
	//$body = $request->getBody();
	//$input = json_decode($body);
	//print_r($input);
	//echo '<br>param: 4';
	
	//GET
	$allGetVars = $request->getQueryParams();
	print_r($allGetVars);
	foreach($allGetVars as $key => $param){
	   //GET parameters list
	}

	//POST or PUT
	$allPostPutVars = $request->getParsedBody();
	print_r($allPostPutVars);
	foreach($allPostPutVars as $key => $param){
	   //POST or PUT parameters list
	}
	
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
});

$app->get('/get', function($request, $response, $args) {
	if (false === $request->getAttribute('csrf_result')) {
        return json_encode(array('success' => false));
    } else {
        return json_encode(array('success' => true);
    }
});

$app->post('/post', function($request, $response, $args) {
    if (false === $request->getAttribute('csrf_result')) {
        return json_encode(array('success' => false));
    } else {
		return json_encode(array('success' => true);
    }
});

$app->run();
