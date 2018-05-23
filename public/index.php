<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

$config = [
    'settings' => [
        'displayErrorDetails' => true
    ],
    'secretkey'=>'698ee85b52b7b65dde71e42705f3aa3aa276b173'
];

$app = new \Slim\App($config);

$app->add(new \Slim\Middleware\JwtAuthentication([
    "rules" => [
        new Slim\Middleware\JwtAuthentication\RequestPathRule([
            "path" => "/",
            "passthrough" => ["/biblioteca", "/admin/ping"],
        ]),
        new Slim\Middleware\JwtAuthentication\RequestMethodRule([
            "ignore" => ["OPTIONS"]
        ])
    ],
    "secret" => "supersecretkeyyoushouldnotcommittogithub",
    "secure" => false, //permite uso em HTTP
]));


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


$app->get('/', function (Request $request, Response $response){
    $response->getBody()->write('Webservice OK');
    $token = $request->getAttribute("token");
    print_r($token);
    return $response;
});

require_once '../src/routes/lancamentos.php';
require_once '../src/routes/biblioteca.php';

$app->run();
