<?php

namespace Farejador\FarejadorConfigurador\Configurador;

use Farejador\Exceptions\FarejadorConfiguracaoException;
use Farejador\Farejador;
use Farejador\FarejadorConfigurador\FarejadorConfiguradorInterface;

class FarejadorConfiguradorPhpCsStandard implements FarejadorConfiguradorInterface
{
    const CHAVE_DA_CONFIG = '--standard';
    const CHAVE_DA_CONFIG_PT = '--padrao';
    const CHAVES_DA_CONFIG = [
        self::CHAVE_DA_CONFIG,
        self::CHAVE_DA_CONFIG_PT
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
        $this->validarPadraoParaPhpCs($padrao);
    }

    private function validarPadraoParaPhpCs($padrao)
    {
        $padroesPhpCs = $this->farejador->getPhpCsComando()->getOpcoesDePadrao();

        if (!in_array($padrao, $padroesPhpCs)) {
            throw new FarejadorConfiguracaoException('O padrao (' . $padrao . ') informado nao esta disponivel. Padroes aceitos: ' . implode(", " , $padroesPhpCs));
        }
    }

    public function configurar($chave, $valor)
    {
        if (!in_array($chave, self::CHAVES_DA_CONFIG)) {
            return;
        }

        $this->definirPadraoParaPhpCs($valor);
    }
}
