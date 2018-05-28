<?php

class Tabelas
{
    protected $cliente;
    protected $tabelas = array();
    protected $lancamentos = array(
        'faditu' => array(
            'nome_tabela' => 'FILANCAMENTOS',
            'primary_key' => 'CODLANCAMENTOS',
            'colunas' => array(
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
            ),
            'colunas_not_null' => array(
                'CODPESSOA','CODCOLIGADA',
                'DATAHORACRIACAO', 'LOGIN',
                'LOGIN', 'SENHA',
                'TIPO', 'ALTERALOGIN'
            )
        )
    );
    protected $usuarios = array(
        'faditu' => array(
            'nome_tabela' => 'OUSUARIOS',
            'primary_key' => 'CODUSUARIO',
            'colunas' => array(
                'CODUSUARIO', 'CODPESSOA',
                'CODCOLIGADA', 'CODPERFIL',
                'DATAHORACRIACAO', 'RESPCRIACAO',
                'LOGIN', 'SENHA',
                'DATASENHA', 'RESPSENHA',
                'ULTIMOACESSO', 'TIPO',
                'ALTERALOGIN', 'DATAHORALOGIN',
                'ATIVO', 'EXCLUIDO',
                'DATAHORAEXCLUSAO', 'EXCLUIDOPOR'
            ),
            'colunas_not_null' => array(
                'CODPESSOA','CODCOLIGADA',
                'DATAHORACRIACAO', 'LOGIN',
                'LOGIN', 'SENHA',
                'TIPO', 'ALTERALOGIN'
            )
        )
    );
    public function __construct($cliente)
    {
        $this->cliente = $cliente;
        $this->tabelas['lancamentos'] = $this->lancamentos;
        $this->tabelas['usuarios'] = $this->usuarios;
    }

    public function getNomeTabela($servico)
    {
        return $this->tabelas[$servico][$this->cliente]['nome_tabela'];
    }

    public function getPrimaryKey($servico)
    {
        return $this->tabelas[$servico][$this->cliente]['primary_key'];
    }

    public function getColunas($servico)
    {
        return $this->tabelas[$servico][$this->cliente]['colunas'];
    }

    public function getColunasNotNull($servico)
    {
        return $this->tabelas[$servico][$this->cliente]['colunas_not_null'];
    }
}