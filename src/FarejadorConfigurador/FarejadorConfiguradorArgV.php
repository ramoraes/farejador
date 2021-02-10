<?php

namespace Farejador\FarejadorConfigurador;

use Farejador\Farejador;
use Farejador\FarejadorConfigurador\Configurador\FarejadorConfiguradorDiretorio;

class FarejadorConfiguradorArgV
{
    private $farejador;
    private $configuradores = [];

    public function __construct(Farejador $farejador)
    {
        $this->farejador = $farejador;
        $this->carregarConfiguradores();
    }

    private function carregarConfiguradores()
    {
        $this->configuradores[] = new FarejadorConfiguradorDiretorio($this->farejador);
        $this->configuradores[] = new FarejadorConfiguradorPhpCsStandard($this->farejador);
//        .
//        .
//        .
    }

    public function configurar(array $argV)
    {
        foreach ($argV as $itemDoArgV) {
            $this->configurarAPartirDeItemDoArgV($itemDoArgV);
        }
    }

    private function configurarAPartirDeItemDoArgV($itemDoArgV)
    {
        if (!preg_match('/^(\-){1,2}[a-zA-Z]+\=.*/', $itemDoArgV)) {
            return;
        }

        $chaveValor = explode("=", $itemDoArgV);
        $this->invocarConfiguradores($chaveValor[0], $chaveValor[1]);
    }

    private function invocarConfiguradores($chave, $valor)
    {
        /** @var FarejadorConfiguradorInterface $configurador */
        foreach ($this->configuradores as $configurador) {
            $configurador->configurar($chave, $valor);
        }
    }
}
