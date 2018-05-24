<?php
$app->add(function($request, $response, $next){
    $header = $request->getHeader('X-Life-Sistemas-Id-Cliente');
    //verifica se o cabecalho customizado foi informado o id do cliente
    if(empty($header)) {
        $response = $response->withJson(array('status'=>false, 'Necessário informar o ID do Cliente no cabeçalho.'));
    } else {
        $idcliente = $header[0];

        $config_file = parse_ini_file(__DIR__ . '/../../config/config.ini', true)['cliente_'.$idcliente];

        //verifica se existe o arquivo de configuracao no servidor
        if(empty($config_file)) {
            $response = $response->withJson(array('status'=>false, 'Arquivo de configuração ausente no servidor para o cliente informado.'));
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
                    $response = $response->withJson(array('status'=>false, "O parâmetro " . strtoupper($param) . " não foi informado no arquivo de configuração."));
                    $param_ausente = true;
                }
            }
            //se todos os parametros existirem, coloca-se todos seus valores nas variaveis de ambiente
            if(!$param_ausente) {
                foreach($array_params as $param) {
                    putenv(strtoupper($param ) . "=" . $config_file[$param]);
                }
                $response = $next($request, $response);
            }
        }
    }
    return $response;
});

$app->add(new \Slim\Middleware\JwtAuthentication([
    "rules" => [
        new Slim\Middleware\JwtAuthentication\RequestPathRule([
            "path" => "/",
            "passthrough" => ["/", "/auth"],//caminhos que nao precisam de token
        ]),
        new Slim\Middleware\JwtAuthentication\RequestMethodRule([
            "ignore" => ["OPTIONS"]//metodos HTTP que nao exigem token
        ])
    ],
    "secure" => false, //permite uso sem HTTPS no servidor
    "error" => function ($request, $response, $arguments) {
        $data["status"] = false;
        $data["message"] = $arguments["message"];
        return $response->withJson($data);
    }
]));