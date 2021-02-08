<?php

namespace Farejador;

class ArquivoParaFarejar {

    private $arquivoNovo = false;

    private $localizacaoDoArquivo = '';

    /**
     * ArquivoParaFarejar constructor.
     * @param string $localizacaoDoArquivo
     * @param bool $arquivoNovo
     */
    public function __construct($localizacaoDoArquivo, $arquivoNovo)
    {
        $this->localizacaoDoArquivo = $localizacaoDoArquivo;
        $this->arquivoNovo = (bool) $arquivoNovo;
    }

    private function obterLinhasAlteradasDoArquivoNovo()
    {
        return [];
    }

    private function obterLinhasAlteradasDoArquivoExistente()
    {
        $comando = 'git diff ' . $this->localizacaoDoArquivo;

        $linhasDoDiff = Comando::executarComando($comando);

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

    public function obterLinhasAlteradasDoArquivo()
    {
        if ($this->arquivoNovo) {
            return $this->obterLinhasAlteradasDoArquivoNovo();
        } else {
            return $this->obterLinhasAlteradasDoArquivoExistente();
        }
    }

    /**
     * @return bool
     */
    public function isArquivoNovo()
    {
        return $this->arquivoNovo;
    }

    /**
     * @return string
     */
    public function getLocalizacaoDoArquivo()
    {
        return $this->localizacaoDoArquivo;
    }
}
