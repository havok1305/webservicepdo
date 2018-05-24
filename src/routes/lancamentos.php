<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

//Busca todos os lancamentos do cliente informado
$app->get('/{idcliente}/lancamentos', function (Request $request, Response $response) {
    $idcliente = $request->getAttribute('idcliente');
//    $params = $request->getQueryParams();
    $params = array($idcliente);
    return $response->withJson($params);
});

$app->get('/{idcliente}/lancamentos/{codlancamento}', function (Request $request, Response $response) {
    $idcliente = $request->getAttribute('idcliente');
    $lancamentoDAO = new LancamentoDAO($idcliente);
    $codlancamento = $request->getAttribute('codlancamento');
    $lancamento = $lancamentoDAO->getByPrimaryKey($codlancamento);
    return $response->withJson($lancamento);
});

$app->post('/{idcliente}/lancamentos/', function (Request $request, Response $response) {

});

$app->put('/{idcliente}/lancamentos', function (Request $request, Response $response) {

});

$app->delete('/{idcliente}/lancamentos', function (Request $request, Response $response) {

});

