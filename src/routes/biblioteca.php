<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


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
    $id = $request->getAttribute('id');
    $body = $request->getParsedBody();
    $result = $helperBiblioteca->update($body, array('id'=>$id));
    return $response->withJson($result);
});

$app->delete('/biblioteca/{id}', function (Request $request, Response $response) {
    $helperBiblioteca = new HelperBiblioteca();
    $id = $request->getAttribute('id');
//    $result = $helperBiblioteca->delete(array('id'=>$id));
    $result = $helperBiblioteca->deleteUpdate(array('id'=>$id));
    return $response->withJson($result);
});