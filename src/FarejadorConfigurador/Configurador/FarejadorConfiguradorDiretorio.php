<?php

namespace Farejador\FarejadorConfigurador\Configurador;

use Farejador\Exceptions\FarejadorConfiguracaoException;
use Farejador\Farejador;
use Farejador\FarejadorConfigurador\FarejadorConfiguradorInterface;

class FarejadorConfiguradorDiretorio implements FarejadorConfiguradorInterface
{
    const CHAVE_CURTA_DA_CONFIG = '-d';
    const CHAVE_DA_CONFIG = '-directory';
    const CHAVE_DA_CONFIG_PT = '-diretorio';
    const CHAVES_DA_CONFIG = [
        self::CHAVE_DA_CONFIG,
        self::CHAVE_CURTA_DA_CONFIG,
        self::CHAVE_DA_CONFIG_PT
    ];

    private $farejador;

    public function __construct(Farejador $farejador)
    {
        $this->farejador = $farejador;

        $diretorioPadrao = $this->getDiretorioPadrao();
        $this->definirDiretorioParaFarejamento($diretorioPadrao);
    }

    private function getDiretorioPadrao()
    {
        return getcwd();
    }

    private function definirDiretorioParaFarejamento($diretorio)
    {
        $this->validarDiretorioParaFarejamento($diretorio);

        $this->farejador->getGitComando()->setDiretorio($diretorio);
        $this->farejador->getPhpCsComando()->setDiretorio($diretorio);
    }

    private function validarDiretorioParaFarejamento($diretorio)
    {
        if (!is_dir($diretorio)) {
            throw new FarejadorConfiguracaoException('O valor informado para o diretorio parece nao ser um diretorio: ' . $diretorio);
        }
    }

    public function configurar($chave, $valor)
    {
        if (!in_array($chave, self::CHAVES_DA_CONFIG)) {
            return;
        }

        if ($this->verificarSeValorEhDeDiretorioRelativo($valor)) {
            $this->definirDiretorioRelativoParaFarejamento($valor);
        } else {
            $this->definirDiretorioParaFarejamento($valor);
        }
    }

    private function verificarSeValorEhDeDiretorioRelativo($valor)
    {
        return preg_match('/^(\.){1,2}/', $valor);
    }

    private function definirDiretorioRelativoParaFarejamento($diretorioRelativo)
    {
        $diretorioPadrao = $this->getDiretorioPadrao();

        $diretorioParaFarejamento = $diretorioPadrao . '/' . $diretorioRelativo;

        $this->definirDiretorioParaFarejamento($diretorioParaFarejamento);
    }
}
