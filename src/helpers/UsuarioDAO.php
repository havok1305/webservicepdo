<?php

class UsuarioDAO extends AbstractDAO
{
    protected $codcliente;
    protected $table = 'OUSUARIOS';
    protected $primaryKey = 'CODUSUARIO';
    protected $columns = array(
        'CODUSUARIO', 'CODPESSOA',
        'CODCOLIGADA', 'CODPERFIL',
        'DATAHORACRIACAO', 'RESPCRIACAO',
        'LOGIN', 'SENHA',
        'DATASENHA', 'RESPSENHA',
        'ULTIMOACESSO', 'TIPO',
        'ALTERALOGIN', 'DATAHORALOGIN',
        'ATIVO', 'EXCLUIDO',
        'DATAHORAEXCLUSAO', 'EXCLUIDOPOR'
    );
    protected $columns_not_null = array(
        'CODPESSOA','CODCOLIGADA',
        'DATAHORACRIACAO', 'LOGIN',
        'LOGIN', 'SENHA',
        'TIPO', 'ALTERALOGIN'
    );

    public function login($user, $pass) {

        $this->setMessage('select_ok', 'Busca de usuário realizada com sucesso.');
        $this->setMessage('select_empty', 'Usuário ou senha inválidos.');

        $sql = "SELECT CODUSUARIO, LOGIN FROM {$this->table} WHERE LOGIN = ? AND SENHA = PASSWORD(?)";
        return $this->rawQuery($sql, [$user, $pass]);
    }


}