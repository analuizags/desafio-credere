<?php
    
class Database 
{
    private $conexao = null;

    public function __construct()
    {
        $senha   = $_ENV['DB_PASS'];
        $usuario = $_ENV['DB_USER'];
        $host    = $_ENV['DB_HOST'];
        $db      = $_ENV['DB_DATABASE'];

        try {
            $this->conexao = new PDO(
                "mysql:host=$host;dbname=$db", $usuario, $senha
            );
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function conectar()
    {
        return $this->conexao;
    }
}