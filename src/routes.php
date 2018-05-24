<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/', function (Request $request, Response $response){
    $response->getBody()->write('Webservice OK');
    return $response;
});

$app->get('/auth/{user}', function (Request $request, Response $response){
    $user = $request->getAttribute('user');
    $token = new Token($this->get('issuer'), $this->get('secretkey'));
    $token->setExpirationTime(1);
//    echo $this->get('secretkey');exit;
    $t = $token->generateToken($user);
    $response->getBody()->write($t);
    return $response;
});


$app->get('/validate', function (Request $request, Response $response){
//    $token = new Token($this->get('issuer'), $this->get('secretkey'));
    $t = $request->getAttribute('token');
    return $response->withJson($t);
});

require __DIR__ . '/../src/routes/lancamentos.php';
require __DIR__ . '/../src/routes/biblioteca.php';
