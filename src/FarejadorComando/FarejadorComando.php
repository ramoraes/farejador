<?php

namespace Farejador\FarejadorComando;

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

        exec($comando, $resultado, $retorno);

        return [
            self::RESULTADO_KEY => $resultado,
            self::RETORNO_KEY => $retorno
        ];
    }

    public function getDiretorio()
    {
        return $this->diretorio;
    }

    public function setDiretorio($diretorio)
    {
        $this->diretorio = $diretorio;
    }
}
