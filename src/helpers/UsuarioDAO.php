<?php

class UsuarioDAO extends AbstractDAO
{
    protected $service = 'usuarios';

    public function login($user, $pass) {

        $this->setMessage('select_ok', 'Busca de usuário realizada com sucesso.');
        $this->setMessage('select_empty', 'Usuário ou senha inválidos.');

        $sql = "SELECT CODUSUARIO, LOGIN FROM {$this->table} WHERE LOGIN = ? AND SENHA = PASSWORD(?)";
        return $this->rawQuery($sql, [$user, $pass]);
    }
}