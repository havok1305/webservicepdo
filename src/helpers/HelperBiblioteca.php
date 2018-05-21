<?php

class HelperBiblioteca extends HelperPDO
{
    protected $table = 'biblioteca';
    protected $primaryKey = 'id';
    protected $columns = array(
        'id', 'nome', 'excluido'
    );
}