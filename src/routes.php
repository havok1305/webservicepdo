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

$app->post('/auth', function (Request $request, Response $response) {
    $header = $request->getHeader('X-Life-Sistemas-Id-Cliente');
    $cliente = $header[0];

    $body = $request->getParsedBody();

    if(isset($body['LOGIN']) && isset($body['SENHA'])) {
        $usuarioDAO = new UsuarioDAO();
        $resultado = $usuarioDAO->login($body['LOGIN'], $body['SENHA']);
        if($resultado['status']) {
            $token = new Token($this->get('issuer'), getenv("SECRETKEY"));

            $t = $token->generateToken($cliente);
            $resultado['token'] = $t;
        }
        return $response->withJson($resultado);
    }
    return $response->withJson(array('status'=>false, 'message'=>'Não foi possível autenticar usuário.'));
});

require __DIR__ . '/../src/routes/lancamentos.php';
