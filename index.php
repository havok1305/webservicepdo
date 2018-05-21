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
    $database = Database::getInstance();
    if($database) {
        $response->getBody()->write('Foi');
    } else {
        $response->getBody()->write('Erro');
    }
    return $response;
});

$app->get('/biblioteca', function (Request $request, Response $response) {
    $params = $request->getQueryParams();
    $helperBiblioteca = new HelperBiblioteca();
    return $response->withJson($helperBiblioteca->query($params));
});

$app->get('/biblioteca/{id}', function (Request $request, Response $response) {
    $helperBiblioteca = new HelperBiblioteca();
    $id = $request->getAttribute('id');
    return $response->withJson($helperBiblioteca->getByPrimaryKey($id));
});
$app->post('/biblioteca', function (Request $request, Response $response) {
    $helperBiblioteca = new HelperBiblioteca();
    $body = $request->getParsedBody();
    $result = $helperBiblioteca->insert($body);
    return $response->withJson($result);
});

$app->put('/biblioteca/{id}', function (Request $request, Response $response) {
    $helperBiblioteca = new HelperBiblioteca();
    $body = $request->getParsedBody();
    $result = $helperBiblioteca->update($body, array('id'=>1));
    return $response->withJson($result);
});

$app->get('/', function (Request $request, Response $response){
    $response->getBody()->write('Webservice OK');
    return $response;
});

$app->run();