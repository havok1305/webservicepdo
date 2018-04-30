<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';

$config = [
    'settings' => [
        'displayErrorDetails' => true
    ],
    'secretkey'=>'698ee85b52b7b65dde71e42705f3aa3aa276b173'
];

$app = new \Slim\App($config);
$app->get('/token-generate/{palavra}', function (Request $request, Response $response, array $args) {
    $palavra = $args['palavra'];
    $helperToken = new HelperToken($this->get('secretkey'));
    $token = $helperToken->generate($palavra);
    $response->getBody()->write("Token: $token");

    return $response;
});

$app->get('/token-validate/{token}', function (Request $request, Response $response) {
    $token = $request->getAttribute('token');
    $helperToken = new HelperToken($this->get('secretkey'));
    if($helperToken->validate($token)){
        $response->getBody()->write("Token válido");
    } else {
        $response->getBody()->write("Token inválido");
    }

    return $response;
});

$app->get('/testedb', function (Request $request, Response $response) {
    $database = new Database();
    if($database->getPdo()) {
        $response->getBody()->write('Foi');
    } else {
        $response->getBody()->write('Erro');
    }
    return $response;
});

$app->run();