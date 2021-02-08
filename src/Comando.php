<?php

namespace Farejador;

class Comando {

    const RESULTADO_KEY = 'resultado';
    const RETORNO_KEY = 'retorno';

    public static function executarComando($comando)
    {
        return self::executarComandoPegandoORetornoEResultado($comando)[self::RESULTADO_KEY];
    }

    public static function executarComandoPegandoOResultado($comando)
    {
        return self::executarComando($comando);
    }

    public static function executarComandoPegandoORetorno($comando)
    {
        return self::executarComandoPegandoORetornoEResultado($comando)[self::RETORNO_KEY];
    }

    public static function executarComandoPegandoORetornoEResultado($comando)
    {
        $resultado = null;
        $retorno = null;

        exec($comando, $resultado, $retorno);

        return [
            self::RESULTADO_KEY => $resultado,
            self::RETORNO_KEY => $retorno
        ];
    }

}
