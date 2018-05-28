<?php

$container = $app->getContainer();

//TODO Testar CORS
$container['EnableCors'] = function ($c) {
    return function ($request, $response, $next) use ($c) {
        $response = $next($request, $response);
        $customHeader = $c->get('customHeader');
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Headers', "X-Requested-With, Content-Type, Accept, Origin, Authorization, {$customHeader}")
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    };
};

// Realiza a autenticacao usando o token
$container['JwtAuthentication'] = function ($c) {
    return new \Slim\Middleware\JwtAuthentication([
        "rules" => [
            new Slim\Middleware\JwtAuthentication\RequestPathRule([
                "path" => "/",
                "passthrough" => ["/auth"],//caminhos que nao precisam de token
            ]),
            new Slim\Middleware\JwtAuthentication\RequestMethodRule([
                "ignore" => ["OPTIONS"]//metodos HTTP que nao exigem token
            ])
        ],
        "secure" => false, //permite uso sem HTTPS no servidor
        "secret" => getenv("SECRETKEY"),
        "error" => function ($request, $response, $arguments) {
            $data["status"] = false;
            $data["message"] = $arguments["message"];
            return $response->withJson($data);
        }
    ]);
};

// Lê o arquivo de configurações config.ini para determinar qual o cliente que está fazendo a requisição
$container['ParseConfig'] = function ($c) {
    return function ($request, $response, $next) use ($c) {
        $header = $request->getHeader($c->get('customHeader'));
        //verifica se o cabecalho customizado foi informado o id do cliente
        if(empty($header)) {
            $response = $response->withJson(array('status'=>false, 'message' => 'Necessário informar o ID do Cliente no cabeçalho.'));
        } else {
            $idcliente = $header[0];

            $config_file = parse_ini_file(__DIR__ . '/../../config/config.ini', true)['cliente_'.$idcliente];

            //verifica se existe o arquivo de configuracao no servidor
            if(empty($config_file)) {
                $response = $response->withJson(array('status'=>false, 'message' => 'Arquivo de configuração ausente no servidor para o cliente informado.'));
            } else {
                //parametros que devem ser informados no arquivo para cada cliente
                $array_params = array(
                    'db_user',
                    'db_password',
                    'db_host',
                    'db_name',
                    'secretkey'
                );
                $param_ausente = false;
                foreach($array_params as $param) {
                    //verifica se todos os parametros estao no arquivo
                    if(!array_key_exists($param, $config_file)) {
                        $response = $response->withJson(array('status'=>false, 'message' => "O parâmetro " . strtoupper($param) . " não foi informado no arquivo de configuração."));
                        $param_ausente = true;
                    }
                }
                //se todos os parametros existirem, coloca-se todos seus valores nas variaveis de ambiente
                if(!$param_ausente) {
                    foreach($array_params as $param) {
                        putenv(strtoupper($param ) . "=" . $config_file[$param]);
                    }
                    putenv("CLIENTE=".$idcliente);
                    $response = $next($request, $response, $next);
                }
            }
        }
        return $response;
    };
};

// Converte os campos do corpo da requisição para maiusculo. Necessário para o banco de dados
$container['FieldsToUpperCase'] = function ($c) {
    return function ($request, $response, $next) use ($c) {
        if($request->isPost()) {
            $body = $request->getParsedBody();
            $fields = array_keys($body);
            $newBody = array();
            foreach($fields as $field) {
                $temp = $body[$field];
                $newBody[strtoupper($field)] = $temp;
            }
            $request = $request->withParsedBody($newBody);

            $response = $next($request, $response, $next);
//            return $response->getParsedBody();
        } else {
            $response = $next($request, $response, $next);
        }
        return $response;
    };
};

// Ordem de execução do middleware: LIFE (Last In First Executed)
// 1º EnableCors
// 2º ParseConfig
// 3º JwtAuthentication
// 4º FieldsToUpperCase

$app->add('FieldsToUpperCase');
$app->add('JwtAuthentication');
$app->add('ParseConfig');
$app->add('EnableCors');


