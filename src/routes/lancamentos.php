<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Busca todos os lancamentos do cliente informado
$app->get('/lancamentos', function (Request $request, Response $response) {
    $params = $request->getQueryParams();
    $lancamentoDAO = new LancamentoDAO(getenv("CLIENTE"));
    $lancamentos = $lancamentoDAO->query($params);

    return $response->withJson($lancamentos);
});

//Busca pelo codigo de lancamento
$app->get('/lancamentos/{codlancamento}', function (Request $request, Response $response) {
    $lancamentoDAO = new LancamentoDAO(getenv("CLIENTE"));
    $codlancamento = $request->getAttribute('codlancamento');
    $lancamento = $lancamentoDAO->getByPrimaryKey($codlancamento);
    return $response->withJson($lancamento);
});

//Cria novo lancamento
$app->post('/lancamentos', function (Request $request, Response $response) {
    $lancamentoDAO = new LancamentoDAO(getenv("CLIENTE"));
    $body = $request->getParsedBody();
    $result = $lancamentoDAO->insert($body);
    return $response->withJson($result);
});

//Atualiza um lancamento
$app->put('/lancamentos/{codlancamento}', function (Request $request, Response $response) {
    $lancamentoDAO = new LancamentoDAO(getenv("CLIENTE"));
    $id = $request->getAttribute('codlancamento');
    $body = $request->getParsedBody();
    $result = $lancamentoDAO->update($body, array('CODLANCAMENTOS'=>$id));
    return $response->withJson($result);
});

$app->delete('/lancamentos', function (Request $request, Response $response) {
    $lancamentoDAO = new LancamentoDAO(getenv("CLIENTE"));
    $body = $request->getParsedBody();
    $result = $lancamentoDAO->deleteUpdate($body);
    return $response->withJson($result);
});

