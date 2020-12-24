<?php

header('Content-Type: application/json; charset=UTF-8');

require_once '../config.php';
require_once 'classes/Sonda.php';

class Rest
{
    public static function open($requisicao)
    {
        $url    = explode('/', $requisicao['url']);
        $classe = isset($url[0]) ? ucfirst($url[0]) : '';

        if (isset($url[1])) {
            if (count($url) == 2) {
                $id     = '';
                $metodo = $url[1];
            } elseif (count($url) == 3) {
                $id     = $url[1];
                $metodo = $url[2];
            }                
        } else {
            $metodo = '';
        }

        $parametros = json_decode(file_get_contents('php://input'), true);

        try {
            if (class_exists($classe)) {
                if (method_exists($classe, $metodo)) {
                    $retorno = call_user_func_array(array(new $classe, $metodo), array($id, $parametros['movimentos']));
                    
                    return json_encode($retorno);
                } else {
                    http_response_code(404);
                    return json_encode(array('Erro' => 'Método inexistente'));
                }
            } else {
                http_response_code(404);
                return json_encode(array('Erro' => 'Classe inexistente'));
            }
        } catch (Exception $e) {
            http_response_code(500);
            return json_encode(array('Erro' => $e->getMessage()));
        }

    }
}


if (isset($_REQUEST) && !empty($_REQUEST)) {
    echo Rest::open($_REQUEST);
} else {
    http_response_code(400);
    echo json_encode(array('Erro' => 'URL inválida'));
}