<?php

namespace Farejador;

use Farejador\Exceptions\FarejadorDependenciaException;

class Farejador {

    private $arquivosParaFarejar = [];
    private $situacoesFarejadas = [];

    private $gitComando = null;
    private $phpCsComando = null;

    public function __construct(GitComando $gitComando, PhpCsComando $phpCsComando)
    {
        $this->gitComando = $gitComando;
        $this->phpCsComando = $phpCsComando;

        $this->validarDependencias();
    }

    private function validarDependencias()
    {
        $this->gitComando->validarExecucao();
        $this->phpCsComando->validarExecucao();
    }

    private function carregarArquivosParaFarejar()
    {
        $this->arquivosParaFarejar = $this->gitComando->carregarArquivosAlterados();
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
        $linhasAlteradas = $this->gitComando->obterLinhasAlteradasDoArquivo($arquivoParaFarejar);
        $situacoesFarejadas = $this->phpCsComando->obterJsonDoPHPCodeSnifer($arquivoParaFarejar);

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
