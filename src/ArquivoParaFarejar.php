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
