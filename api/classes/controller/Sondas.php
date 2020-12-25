<?php

require_once DIR_PROJETO . DS . 'classes/model/Sonda.php';


class Sondas
{
    public function criar()
    {
        $dados = array(
            'eixoX'   => 0,
            'eixoY'   => 0,
            'direcao' => 'D',
        );
        
        $sonda   = new Sonda();
        $idSonda = $sonda->adicionar($dados);

        http_response_code(201);
        return array('id' => $idSonda);
    }

    public function reposicionar($id)
    {
        $this->verificarID($id);

        $dados = array(
            'id'      => (int) $id,
            'eixoX'   => 0,
            'eixoY'   => 0,
            'direcao' => 'D',
        );

        $sonda    = new Sonda();
        $resposta = $sonda->atualizar($dados);

        if (!$resposta) throw new Exception('Não foi possível reposicionar sua sonda', 1);

        http_response_code(200);
        return array('Sucesso' => 'Sonda enviada para nas cordenadas iniciais (0,0).');        
    }

    public function movimentar($id, $movimento)
    {
        $this->verificarID($id);

        $cordenada = $this->verificarPosicao($id);
        $resultado = $this->fazerMovimentos($cordenada, $movimento);
        
        $dados = array(
            'id'        => (int) $id,
            'eixoX'     => $resultado['eixoX'],
            'eixoY'     => $resultado['eixoY'],
            'direcao'   => $resultado['direcao'],
        );

        $sonda    = new Sonda();
        $resposta = $sonda->atualizar($dados);

        if (!$resposta) throw new Exception('Não foi possível movimentar sua sonda', 1);

        http_response_code(200);
        return array('x' => $resultado['eixoX'], 'y' => $resultado['eixoY']);
    }

    public function verificarPosicao($id)
    {
        $this->verificarID($id);

        $sonda    = new Sonda();
        $resposta = $sonda->listar($id, array('eixoX', 'eixoY', 'direcao'));

        if (!$resposta) throw new Exception('Sonda não encontrada', 1);

        http_response_code(200);
        return $resposta;
    }

    protected function fazerMovimentos($cordenada, $movimento)
    {
        if (empty($movimento)) throw new Exception('Precisamos de pelo menos um comando para validar seu movimento', 1);
        
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
                throw new Exception('Comando inválido! Nossa sonda ainda não está preparada para esse comando', 1);
            }

            if ($eixoX < 0 || $eixoY < 0 || $eixoX > 4 || $eixoY > 4) {
                throw new Exception('Movimento inválido! Por favor, não tente levar nossa sonda para onde não podemos visualiza-la', 1);
            }
        }

        return array('eixoX' => $eixoX, 'eixoY' => $eixoY, 'direcao' => $direcao);
    }

    public function verificarID($id)
    {
        if (empty($id) || is_null($id)) throw new Exception("Sonda inválida", 1);

        $sonda    = new Sonda();
        $resposta = $sonda->listar($id, array('id'));

        if (!$resposta) throw new Exception('Sonda inexistente', 1);

        return true;
    }
    
}
    