<?php

class LancamentoDAO extends AbstractDAO
{
    protected $codcliente;
    protected $table = 'filancamentos';
    protected $primaryKey = 'codlancamentos';
    protected $columns = array(
        'codlancamentos', 'datahorainclusao',
        'respinclusao','datahoraalteracao',
        'respalteracao','codempresa',
        'valororiginal','datavencimento',
        'parcela','plano',
        'valorbaixado','databaixa',
        'desconto','percdesconto',
        'acrescimos','multa',
        'juros','msgboleto',
        'situacao','codigobarra',
        'nossonumero','responsavelbaixa',
        'obs','codbaixa',
        'datacancela','justcancela',
        'respcancela','datahoraestorno',
        'justestorno','estornadopor',
        'excluido','datahoraexclusao',
        'excluidopor'
    );

    protected $columns_not_null = array(
        'datahorainclusao','respinclusao',
        'codempresa'
    );

    public function __construct($codcliente = null)
    {
        parent::__construct();
        if(!empty($codcliente)) {
            $this->codcliente = $codcliente;
        }
    }
}