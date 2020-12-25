<?php

require_once DIR_PROJETO . DS . 'classes/DB/Database.php';

class Sonda
{
    public function adicionar($dados)
    {
        $db      = new Database();
        $conexao = $db->conectar();

        $query = "INSERT INTO sonda (" . implode(', ', array_keys($dados)) . ") 
                  VALUES (:". implode(', :', array_keys($dados)). ");";

        try {
            $statement = $conexao->prepare($query);
            $statement->execute($dados);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), 1);
        }

        if (!$statement->rowCount()) throw new Exception('Não foi possível criar sua sonda', 1);

        return $conexao->lastInsertId();
    }

    public function listar($id, $campos)
    {
        $db      = new Database();
        $conexao = $db->conectar();

        $query = "SELECT " . implode(', ', $campos) . " FROM sonda WHERE id = :id;";

        try {
            $statement = $conexao->prepare($query);
            $statement->execute(array('id' => (int) $id));
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), 1);
        }

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

    public function atualizar($dados)
    {
        $db      = new Database();
        $conexao = $db->conectar();

        $query   = "UPDATE sonda SET ";
        
        foreach ($dados as $key => $value) {
            $query .= $key . " = :" . $key . ", ";
        }

        $query  = substr($query, 0, -2);
        $query .= " WHERE id = :id;";

        try {
            $statement = $conexao->prepare($query);
            $statement->execute($dados);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage(), 1);
        }

        return $statement->rowCount();
    }
}
