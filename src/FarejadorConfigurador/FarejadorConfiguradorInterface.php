<?php


namespace Farejador\FarejadorConfigurador;


use Farejador\Farejador;

interface FarejadorConfiguradorInterface
{
    public function __construct(Farejador $farejador);

    public function configurar($chave, $valor);
}
