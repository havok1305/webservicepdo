<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/', function (Request $request, Response $response){
    $response->getBody()->write('Webservice OK');
    return $response;
});

$app->get('/auth', function (Request $request, Response $response){
    $header = $request->getHeader('X-Life-Sistemas-Id-Cliente');
    $cliente = $header[0];

    $token = new Token($this->get('issuer'), getenv("SECRETKEY"));
//    $token->setExpirationTime(1);
    $t = $token->generateToken($cliente);
    $response->getBody()->write($t);
    return $response;
});

$app->get('/teste', function (Request $request, Response $response) {
    return $response->withJson(array('oi'=>'mundo'));
});

$app->get('/validate', function (Request $request, Response $response){
//    $token = new Token($this->get('issuer'), $this->get('secretkey'));
    $t = $request->getAttribute('token');
    return $response->withJson($t);
});

require __DIR__ . '/../src/routes/lancamentos.php';
require __DIR__ . '/../src/routes/biblioteca.php';
