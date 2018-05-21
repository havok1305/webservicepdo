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
    $helperBiblioteca = new HelperBiblioteca();
    var_dump($helperBiblioteca->query());
});

$app->get('/biblioteca/{id}', function (Request $request, Response $response) {
    $helperBiblioteca = new HelperBiblioteca();
    $id = $request->getAttribute('id');
    var_dump($helperBiblioteca->getByPrimaryKey($id));
});
$app->post('/biblioteca', function (Request $request, Response $response) {
    $helperBiblioteca = new HelperBiblioteca();
    $body = $request->getParsedBody();
    $fields = array_keys($body);
    $values = array_values($body);
    $result = $helperBiblioteca->insert($fields, $values);
    if($result) {
        print_r($result);
    } else {
        print_r("ERRO");
    }
});
$app->get('/', function (Request $request, Response $response){
    $response->getBody()->write('Foi');
    return $response;
});

$app->run();