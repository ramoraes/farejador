<?php

namespace Farejador;

class Comando {
    public static function executarComando($comando)
    {
        $resultado = null;

        exec($comando, $resultado);

        return $resultado;
    }
}

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

class Farejador {

    private $arquivosParaFarejar = [];
    private $situacoesFarejadas = [];

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

$farejador = New Farejador();
$farejador->farejar();
