<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Busca todos os lancamentos do cliente informado
$app->get('/lancamentos', function (Request $request, Response $response) {
    $params = $request->getQueryParams();
    $lancamentoDAO = new LancamentoDAO();
    $lancamentos = $lancamentoDAO->query($params);
    $lancamentoDAO->destroyPdo();
    return $response->withJson($lancamentos);
});

//Busca pelo codigo de lancamento
$app->get('/lancamentos/{codlancamento}', function (Request $request, Response $response) {
    $lancamentoDAO = new LancamentoDAO();

    $codlancamento = $request->getAttribute('codlancamento');
    $lancamento = $lancamentoDAO->getByPrimaryKey($codlancamento);
    return $response->withJson($lancamento);
});

//Cria novo lancamento
$app->post('/lancamentos', function (Request $request, Response $response) {

});

//Atualiza um lancamento
$app->put('/lancamentos/{codlancamento}', function (Request $request, Response $response) {

});

$app->delete('/lancamentos', function (Request $request, Response $response) {

});

