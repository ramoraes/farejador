<?php

namespace Farejador\FarejadorConfigurador\Configurador;

use Farejador\Farejador;
use Farejador\FarejadorConfigurador\FarejadorConfiguradorInterface;

class FarejadorConfiguradorPhpCsStandard implements FarejadorConfiguradorInterface
{
    const CHAVE_DA_CONFIG = '--standard';
    const CHAVE_DA_CONFIG_PT = '--padrao';
    const CHAVES_DA_CONFIG = [
        self::CHAVE_DA_CONFIG
    ];
    const PADRAO_INICIAL = 'PSR1';

    private $farejador;

    public function __construct(Farejador $farejador)
    {
        $this->farejador = $farejador;

        $this->definirPadraoParaPhpCs(self::PADRAO_INICIAL);
    }

    private function definirPadraoParaPhpCs($padrao)
    {

    }

    private function validarPadraoParaPhpCs($padrao)
    {

    }

    public function configurar($chave, $valor)
    {
        if (!in_array($chave, self::CHAVES_DA_CONFIG)) {
            return;
        }

        $this->definirPadraoParaPhpCs($valor);
    }
}
