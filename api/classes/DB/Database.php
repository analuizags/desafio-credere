<?php
    
class Database {

    private $conexao = null;

    public function __construct()
    {
        $dadosBD = @json_decode(file_get_contents(DIR_APP . DS . 'config/config-dev.json'));
        $dados   = isset($dadosBD->database) ? $dadosBD->database : null;

        if (!is_null($dados)) {
            $host    = $dados->host;
            $db      = $dados->db;
            $usuario = $dados->user;
            $senha   = $dados->password;

            try {
                $this->conexao = new PDO(
                    "mysql:host=$host;dbname=$db", $usuario, $senha
                );
            } catch (PDOException $e) {
                throw new Exception($e->getMessage());
            }
        } else {
            throw new Exception("Dados inválidos para conexão!");
        }
    }

    public function conectar()
    {
        return $this->conexao;
    }
}