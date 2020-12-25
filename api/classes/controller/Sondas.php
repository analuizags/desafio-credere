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
        $this->validarID($id);

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
        $this->validarID($id);

        $cordenada = $this->verificarPosicao($id);
        $resultado = $this->fazerMovimentos($cordenada, $movimento);
        
        $dados = array(
            'id'        => (int) $id,
            'eixoX'     => $resultado['eixoX'],
            'eixoY'     => $resultado['eixoY'],
            'direcao'   => $resultado['direcao']
        );

        $sonda    = new Sonda();
        $resposta = $sonda->atualizar($dados);

        if (!$resposta) throw new Exception('Não foi possível movimentar sua sonda', 1);

        http_response_code(200);
        return array('x' => $resultado['eixoX'], 'y' => $resultado['eixoY']);
    }

    public function verificarPosicao($id)
    {
        $this->validarID($id);

        $sonda    = new Sonda();
        $resposta = $sonda->listar($id, array('eixoX', 'eixoY', 'direcao'));

        if (!$resposta) throw new Exception('Sonda não encontrada', 1);

        http_response_code(200);
        return $resposta;
    }

    protected function fazerMovimentos($cordenada, $movimento)
    {
        if (empty($movimento)) throw new Exception('Precisamos de pelo menos um comando para validar seu movimento', 1);
        
        $graus   = array('D' => 0, 'C' => 90, 'E' => 180, 'B' => 270);
        $eixoX   = (int) $cordenada['eixoX'];
        $eixoY   = (int) $cordenada['eixoY'];
        $direcao = $graus[$cordenada['direcao']];
        
        foreach ($movimento as $comando) {
            if (trim($comando) == 'M') {
                switch (abs($direcao%360)) {
                    case 270:
                        $eixoY--;
                        break;
                    case 180:
                        $eixoX--;
                        break;
                    case 90:
                        $eixoY++;
                        break;
                    default:
                        $eixoX++;
                        break;
                }
            } else if (trim($comando) == 'GE') {
                $direcao -= 90;
            } else if (trim($comando) == 'GD'){
                $direcao += 90;
            } else {
                throw new Exception('Comando inválido! Nossa sonda ainda não está preparada para esse comando', 1);
            }

            if ($eixoX < 0 || $eixoY < 0 || $eixoX > 4 || $eixoY > 4) {
                throw new Exception('Movimento inválido! Por favor, não tente levar nossa sonda para onde não podemos visualiza-la', 1);
            }
        }
        
        $direcao = array_search(abs($direcao%360), $graus); 
        return array('eixoX' => $eixoX, 'eixoY' => $eixoY, 'direcao' => $direcao);
    }

    public function validarID($id)
    {
        if (empty($id) || is_null($id)) throw new Exception("Sonda inválida", 1);

        $sonda    = new Sonda();
        $resposta = $sonda->listar($id, array('id'));

        if (!$resposta) throw new Exception('Sonda inexistente', 1);

        return true;
    }    
}
    