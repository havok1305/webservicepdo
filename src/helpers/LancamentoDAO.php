<?php

class LancamentoDAO extends AbstractDAO
{
    protected $codcliente;
    protected $table = 'FILANCAMENTOS';
    protected $primaryKey = 'CODLANCAMENTOS';
    protected $columns = array(
        'CODLANCAMENTOS', 'DATAHORAINCLUSAO',
        'RESPINCLUSAO','DATAHORAALTERACAO',
        'RESPALTERACAO','CODTIPODOCUMENTO',
        'CODDADOSCONTA','GRA_CODCURSO',
        'GRA_CODALUNOCURSO','GRA_CODPERIODOLETIVO',
        'GRA_CODRESPFINANCEIRO','POS_CODCURSO',
        'POS_CODALUNOCURSO','POS_CODPERIODOLETIVO',
        'POS_CODRESPFINANCEIRO',
        'TEC_CODCURSO','TEC_CODALUNOCURSO',
        'TEC_CODPERIODOLETIVO','TEC_CODRESPFINANCEIRO',
        'MATRICULA','CPF',
        'TIPOCURSO','VALORORIGINAL',
        'DATAVENCIMENTO','PARCELA',
        'VALORBAIXADO','DATABAIXA',
        'DESCONTO','DESCONTOBOLSA',
        'PERCDESCONTO','ACRESCIMOS',
        'TIPOPROTESTO','DIASPROTESTO',
        'MSGBOLETO','MENSAGEM1',
        'MENSAGEM2','MENSAGEM3',
        'MENSAGEM4','MENSAGEM5',
        'SITUACAO','REGISTRADO',
        'IPTE','CODIGOBARRA',
        'NOSSONUMERO','CODIGOTIPOBAIXA',
        'LIBERADODIFPAGAMENTO','RESPONSAVELBAIXA',
        'OBS','DATACANCELA',
        'JUSTCANCELA','RESPCANCELA',
        'DATADESCANCELA','JUSTDESCANCELA',
        'ESTORNADOPOR','DATAHORAESTORNO',
        'JUSTESTORNO','TEMPOBAIXADO',
        'RESPDESCANCELA','EXCLUIDO',
        'DATAHORAEXCLUSAO',
        'EXCLUIDOPOR','JUSTEXCLUSAO'
    );

    protected $columns_not_null = array(
        'DATAHORAINCLUSAO', 'RESPINCLUSAO',
        'CODTIPODOCUMENTO', 'CODDADOSCONTA',
        'MATRICULA', 'TIPOCURSO'
    );
    public function insert($params)
    {
        if(!isset($params['DATAHORAINCLUSAO'])) {
            $params['DATAHORAINCLUSAO'] = self::getDateTimeNow();
        }

        return parent::insert($params);
    }

    public function update($params, $params_where)
    {
        if(!isset($params['DATAHORAALTERACAO'])) {
            $params['DATAHORAALTERACAO'] = self::getDateTimeNow();
        }
        return parent::update($params, $params_where);
    }
}