<?php

class LancamentoDAO extends AbstractDAO
{
    protected $service = 'lancamentos';

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