<?php

class HelperBiblioteca extends HelperPDO
{
    protected $table = 'biblioteca';
    protected $primaryKey = 'id';
    protected $columns = array(
        'id', 'nome', 'excluido'
    );
    protected $columns_not_null = array(
        'nome'
    );

}