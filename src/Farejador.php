<?php

namespace Farejador;

use Farejador\Exceptions\FarejadorDependenciaException;

class Farejador {

    private $arquivosParaFarejar = [];
    private $situacoesFarejadas = [];

    public function __construct()
    {
        $this->validarDependencias();
    }

    private function validarDependencias()
    {
        $retornoGit = Comando::executarComandoPegandoORetorno('git --version');

        if ($retornoGit !== 0) {
            throw new FarejadorDependenciaException('Nao foi possivel executar o git.');
        }

        $retornoPhpCs = Comando::executarComandoPegandoORetorno('phpcs --version');

        if ($retornoPhpCs !== 0) {
            throw new FarejadorDependenciaException('Nao foi possivel executar o phpcs.');
        }
    }

    private function carregarArquivosParaFarejar()
    {
        $this->carregarArquivosAlteradosParaFarejar();
        $this->carregarArquivosNovosParaFarejar();
    }

    private function carregarArquivosAlteradosParaFarejar()
    {
        $listaCaminhoArquivos = Comando::executarComando('git diff --name-only | grep .php$');

        foreach ($listaCaminhoArquivos as $caminhoArquivo) {
            $this->arquivosParaFarejar[] = new ArquivoParaFarejar($caminhoArquivo, false);
        }
    }

    private function carregarArquivosNovosParaFarejar()
    {
        $listaCaminhoArquivos = Comando::executarComando('git status -s | grep "??" | grep .php$');

        foreach ($listaCaminhoArquivos as $caminhoArquivo) {
            $this->arquivosParaFarejar[] = new ArquivoParaFarejar(
                str_replace('?? ', '', $caminhoArquivo),
                true
            );
        }
    }

    private function farejarArquivosCarregados()
    {
        /** @var $arquivoParaFarejar ArquivoParaFarejar */
        foreach ($this->arquivosParaFarejar as $arquivoParaFarejar) {
            $this->farejarLinhasAlteradasNoArquivo($arquivoParaFarejar);
        }
    }

    private function farejarLinhasAlteradasNoArquivo(ArquivoParaFarejar $arquivoParaFarejar)
    {
        $linhasAlteradas = $arquivoParaFarejar->obterLinhasAlteradasDoArquivo();
        $situacoesFarejadas = $this->obterJsonDoPHPCodeSnifer($arquivoParaFarejar);

        $situacoesFarejadasNasLinhasAlteradas = [
            'file' => $arquivoParaFarejar->getLocalizacaoDoArquivo(),
            'messages' => []
        ];

        foreach ($situacoesFarejadas as $situacaoFarejada) {
            if ($arquivoParaFarejar->isArquivoNovo() || in_array($situacaoFarejada['line'], $linhasAlteradas)) {
                $situacoesFarejadasNasLinhasAlteradas['messages'][] = $situacaoFarejada;
            }
        }

        $this->situacoesFarejadas[] = $situacoesFarejadasNasLinhasAlteradas;

        return $situacoesFarejadasNasLinhasAlteradas;
    }

    private function obterJsonDoPHPCodeSnifer(ArquivoParaFarejar $arquivoParaFarejar)
    {
        $resultado = Comando::executarComando('phpcs --standard=PSR1 --report=json ' . $arquivoParaFarejar->getLocalizacaoDoArquivo());

        $resultadoSniffer = [];

        if ($resultado && $resultado[0]) {
            $resultadoSnifferCompleto = json_decode($resultado[0], true);

            $resultadosDosArquivos = array_slice($resultadoSnifferCompleto['files'], 0, 1);
            $resultadoDoArquivo = reset($resultadosDosArquivos);

            $resultadoSniffer = $resultadoDoArquivo['messages'];
        }

        return $resultadoSniffer;
    }

    private function imprimirSituacoesFarejadas()
    {
        print_r($this->situacoesFarejadas);
    }

    public function farejar()
    {
        $this->carregarArquivosParaFarejar();
        $this->farejarArquivosCarregados();
        $this->imprimirSituacoesFarejadas();
    }
}

//$farejador = New Farejador();
//$farejador->farejar();
