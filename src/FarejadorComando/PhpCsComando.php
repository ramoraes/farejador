<?php

namespace Farejador\FarejadorComando;

use Farejador\Exceptions\FarejadorDependenciaException;
use Farejador\ArquivoParaFarejar;

class PhpCsComando extends FarejadorComando
{
    public function validarExecucao()
    {
        $retornoPhpCs = $this->executarComandoPegandoORetorno('phpcs --version');

        if ($retornoPhpCs !== 0) {
            throw new FarejadorDependenciaException('Nao foi possivel executar o phpcs.');
        }
    }

    public function obterJsonDoPHPCodeSnifer(ArquivoParaFarejar $arquivoParaFarejar)
    {
        $resultado = $this->executarComando('phpcs --standard=PSR1 --report=json ' . $arquivoParaFarejar->getLocalizacaoDoArquivo());

        $resultadoSniffer = [];

        if ($resultado && $resultado[0]) {
            $resultadoSnifferCompleto = json_decode($resultado[0], true);

            $resultadosDosArquivos = array_slice($resultadoSnifferCompleto['files'], 0, 1);
            $resultadoDoArquivo = reset($resultadosDosArquivos);

            $resultadoSniffer = $resultadoDoArquivo['messages'];
        }

        return $resultadoSniffer;
    }
}
