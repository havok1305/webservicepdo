<?php
$app->add(new \Slim\Middleware\JwtAuthentication([
    "rules" => [
        new Slim\Middleware\JwtAuthentication\RequestPathRule([
            "path" => "/",
            "passthrough" => ["/biblioteca", "/auth"],//caminhos que nao precisam de token
        ]),
        new Slim\Middleware\JwtAuthentication\RequestMethodRule([
            "ignore" => ["OPTIONS"]//metodos HTTP que nao exigem token
        ])
    ],
    "secret" => "698ee85b52b7b65dde71e42705f3aa3aa276b173",
    "secure" => false, //permite uso sem HTTPS no servidor
    "error" => function ($request, $response, $arguments) {
        $data["status"] = false;
        $data["message"] = $arguments["message"];
        return $response->withJson($data);
    }
]));