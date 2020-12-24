<?php
    
require_once 'classes/DB/Database.php';

class Sonda
{
    public function criar()
    {
        $db      = new Database();
        $conexao = $db->conectar();

        $query = "INSERT INTO sonda (eixoX, eixoY, direcao) VALUES (:eixoX, :eixoY, :direcao);";

        try {
            $statement = $conexao->prepare($query);
            $statement->execute(array(
                'eixoX'     => 0,
                'eixoY'     => 0,
                'direcao'   => 'D',
            ));
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }

        if ($statement->rowCount()) {
            http_response_code(201);
            return array('id' => $conexao->lastInsertId());
        } else {
            throw new Exception('Não foi possível criar sua sonda');
        }
    }

    public function reposicionar($id)
    {
        $this->verificarID($id);

        $db      = new Database();
        $conexao = $db->conectar();

        $query = "UPDATE sonda SET eixoX = :eixoX, eixoY = :eixoY, direcao = :direcao WHERE id = :id;";

        try {
            $statement = $conexao->prepare($query);
            $statement->execute(array(
                'id'        => (int) $id,
                'eixoX'     => 0,
                'eixoY'     => 0,
                'direcao'   => 'D',
            ));

            $statement->rowCount();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        
        http_response_code(200);
        return array('Sucesso' => 'Sonda enviada para nas cordenadas iniciais (0,0).');
    }

    public function movimentar($id, $movimento)
    {
        $this->verificarID($id);

        $db      = new Database();
        $conexao = $db->conectar();
        
        $cordenada = $this->verificarPosicao($id);
        $resultado = $this->fazerMovimentos($cordenada, $movimento);

        $query = "UPDATE sonda SET eixoX = :eixoX, eixoY = :eixoY, direcao = :direcao WHERE id = :id;";

        try {
            $statement = $conexao->prepare($query);
            $statement->execute(array(
                'id'        => (int) $id,
                'eixoX'     => $resultado['eixoX'],
                'eixoY'     => $resultado['eixoY'],
                'direcao'   => $resultado['direcao'],
            ));

            $statement->rowCount();
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }
        
        $resposta = array('x' => $resultado['eixoX'], 'y' => $resultado['eixoY']);

        http_response_code(200);
        return $resposta;
    }

    public function verificarPosicao($id)
    {
        $this->verificarID($id);

        $db      = new Database();
        $conexao = $db->conectar();

        $query = "SELECT eixoX, eixoY, direcao FROM sonda WHERE id = :id;";

        try {
            $statement = $conexao->prepare($query);
            $statement->execute(array('id' => (int) $id));
            $resposta = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }

        http_response_code(200);
        return $resposta;
    }

    protected function fazerMovimentos($cordenada, $movimento)
    {
        if (empty($movimento)) throw new Exception("Precisamos de pelo menos um comando para validar seu movimento", 1);
        
        // cordenadas e direção inicial da sonda
        $eixoX  = (int) $cordenada['eixoX'];
        $eixoY  = (int) $cordenada['eixoY'];
        $direcao = $cordenada['direcao'];
        
        foreach ($movimento as $comando) {
            // otimizar esse calculo
            if ($comando == 'M') {
                switch ($direcao) {
                    case 'D':
                        $eixoX++;
                        break;
                    case 'E':
                        $eixoX--;
                        break;
                    case 'C':
                        $eixoY++;
                        break;
                    default:
                        $eixoY--;
                        break;
                }
            } else if ($comando == 'GE') {
                switch ($direcao) {
                    case 'D':
                        $direcao = 'C';
                        break;
                    case 'E':
                        $direcao = 'B';
                        break;
                    case 'C':
                        $direcao = 'E';
                        break;
                    default:
                        $direcao = 'D';
                        break;
                }
            } else if ($comando == 'GD'){
                switch ($direcao) {
                    case 'D':
                        $direcao = 'B';
                        break;
                    case 'E':
                        $direcao = 'C';
                        break;
                    case 'C':
                        $direcao = 'D';
                        break;
                    default:
                        $direcao = 'E';
                        break;
                }
            } else {
                throw new Exception("Comando inválido! Nossa sonda ainda não está preparada para esse comando", 1);
            }

            if ($eixoX < 0 || $eixoY < 0 || $eixoX > 4 || $eixoY > 4) {
                throw new Exception("Movimento inválido! Por favor, não tente levar nossa sonda 
                                    para onde não podemos visualiza-la", 1);
            }
        }

        return array('eixoX' => $eixoX, 'eixoY' => $eixoY, 'direcao' => $direcao);
    }

    public function verificarID($id)
    {
        if (empty($id) || is_null($id)) {
            throw new Exception("Sonda inexistente", 1);
        }

        $db      = new Database();
        $conexao = $db->conectar();

        $query = "SELECT id FROM sonda WHERE id = :id;";

        try {
            $statement = $conexao->prepare($query);
            $statement->execute(array('id' => (int) $id));
            $resposta = $statement->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception($e->getMessage());
        }

        if (!$resposta) {
            throw new Exception("Sonda inexistente", 1);
        }

        return true;
    }
    
}
    