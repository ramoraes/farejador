<?php

namespace Farejador;

use Farejador\Opcoes\FarejadorOpcoes;

abstract class FarejadorComando
{
    const RESULTADO_KEY = 'resultado';
    const RETORNO_KEY = 'retorno';

    protected $diretorio;

    public function __construct($diretorio)
    {
        $this->diretorio = $diretorio;
    }

    abstract public function validarExecucao();

    public function executarComando($comando)
    {
        return $this->executarComandoPegandoORetornoEResultado($comando)[self::RESULTADO_KEY];
    }

    public function executarComandoPegandoOResultado($comando)
    {
        return $this->executarComando($comando);
    }

    public function executarComandoPegandoORetorno($comando)
    {
        return $this->executarComandoPegandoORetornoEResultado($comando)[self::RETORNO_KEY];
    }

    public function executarComandoPegandoORetornoEResultado($comando)
    {
        $resultado = null;
        $retorno = null;

//        exec("cd " . $this->diretorio, $resultado, $retorno);
        exec($comando, $resultado, $retorno);

        return [
            self::RESULTADO_KEY => $resultado,
            self::RETORNO_KEY => $retorno
        ];
    }
}
