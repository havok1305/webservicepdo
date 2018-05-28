<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/', function (Request $request, Response $response){
    $response->getBody()->write('Webservice OK');
    return $response;
});

$app->post('/auth', function (Request $request, Response $response) {
    $header = $request->getHeader($this->get('customHeader'));
    $cliente = $header[0];

    $body = $request->getParsedBody();

    if(isset($body['LOGIN']) && isset($body['SENHA'])) {
        $login = $body['LOGIN'];
        $senha = $body['SENHA'];
        $usuarioDAO = new UsuarioDAO(getenv("CLIENTE"));
        $resultado = $usuarioDAO->login($login, $senha);
        if($resultado['status']) {
            $token = new Token($this->get('issuer'), getenv("SECRETKEY"));
            $data = array(
                'sub' => $cliente,
                'user' => $login
            );

            $t = $token->generateToken($data);
            $resultado['token'] = $t;
        }
        return $response->withJson($resultado);
    }
    return $response->withJson(array('status'=>false, 'message'=>'Não foi possível autenticar usuário.'));
});

require __DIR__ . '/../src/routes/lancamentos.php';
