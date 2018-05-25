<?php

class UsuarioDAO extends AbstractDAO
{
    protected $codcliente;
    protected $table = 'filancamentos';
    protected $primaryKey = 'codlancamentos';
    protected $columns = array();
    protected $columns_not_null = array(
        'datahorainclusao','respinclusao',
        'codempresa'
    );


}