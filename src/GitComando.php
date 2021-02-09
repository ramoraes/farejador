<?php

namespace Farejador;

use Farejador\Exceptions\FarejadorDependenciaException;

class GitComando extends FarejadorComando
{
    public function validarExecucao()
    {
        $retornoGit = $this->executarComandoPegandoORetorno('git --version');

        if ($retornoGit !== 0) {
            throw new FarejadorDependenciaException('Nao foi possivel executar o git.');
        }
    }

    public function carregarArquivosAlterados()
    {
        $listaArquivosAlterados = array_merge(
            $this->carregarArquivosAlteradosParaFarejar(),
            $this->carregarArquivosNovosParaFarejar()
        );

        return $listaArquivosAlterados;
    }

    private function carregarArquivosAlteradosParaFarejar()
    {
        $listaCaminhoArquivos = $this->executarComando('git -C ' . $this->diretorio . ' diff --name-only | grep .php$');

        $arquivosAlterados = [];

        foreach ($listaCaminhoArquivos as $caminhoArquivo) {
            $arquivosAlterados[] = new ArquivoParaFarejar($this->obterCaminhoDoArquivoComDiretorio($caminhoArquivo), false);
        }

        return $arquivosAlterados;
    }

    private function carregarArquivosNovosParaFarejar()
    {
        $listaCaminhoArquivos = $this->executarComando('git -C ' . $this->diretorio . ' status -s | grep "??" | grep .php$');

        $arquivosNovos = [];

        foreach ($listaCaminhoArquivos as $caminhoArquivo) {
            $arquivosNovos[] = new ArquivoParaFarejar(
                $this->obterCaminhoDoArquivoComDiretorio(str_replace('?? ', '', $caminhoArquivo)),
                true
            );
        }

        return $arquivosNovos;
    }

    private function obterCaminhoDoArquivoComDiretorio($caminhoArquivo)
    {
        return $this->diretorio . "/" . $caminhoArquivo;
    }

    public function obterLinhasAlteradasDoArquivo(ArquivoParaFarejar $arquivoParaFarejar)
    {
        if ($arquivoParaFarejar->isArquivoNovo()) {
            return [];
        }

        $comando = 'git diff ' . $arquivoParaFarejar->getLocalizacaoDoArquivo();

        $linhasDoDiff = $this->executarComando($comando);

        $linhasAlteradas = [];
        $linhaAdicionadaContador = null;

        foreach ($linhasDoDiff as $linhaDoDiff) {
            if (preg_match("/^@@[,\-\+0-9\s]+@@/", $linhaDoDiff)) {
                $dadosLinhasAdicionadas = [];
                $dadosLinhasRemovidas = [];

                preg_match("/\+[,0-9]+/", $linhaDoDiff, $dadosLinhasAdicionadas);
                preg_match("/\-[,0-9]+/", $linhaDoDiff, $dadosLinhasRemovidas);

                $partesLinhasRemovidas = explode(',', $dadosLinhasRemovidas[0]);
                $linhasRemovidasInicio = (int) str_replace('-', '', $partesLinhasRemovidas[0]);
                $linhasRemovidasTotal = (int) $partesLinhasRemovidas[1];

                $partesLinhasAdicionadas = explode(',', $dadosLinhasAdicionadas[0]);
                $linhasAdicionadasInicio = (int) str_replace('+', '', $partesLinhasAdicionadas[0]);
                $linhasAdicionadasTotal = (int) $partesLinhasAdicionadas[1];

                $linhaAdicionadaContador = $linhasAdicionadasInicio;

                continue;
            }

            if (!is_null($linhaAdicionadaContador)) {
                if (preg_match("/^\+/", $linhaDoDiff)) {
                    $linhasAlteradas[] = $linhaAdicionadaContador;
                }

                if (!preg_match("/^\-/", $linhaDoDiff)) {
                    $linhaAdicionadaContador++;
                }
            }
        }

        return $linhasAlteradas;
    }
}
