<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/lancamentos', function (Request $request, Response $response) {
    $params = $request->getQueryParams();
    return $response->withJson($params);
});

