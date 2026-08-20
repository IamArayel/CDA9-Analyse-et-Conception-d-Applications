<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @return list<string> An array of allowed values for APP_ENV
     */
    private function getAllowedEnvs(): array
    {
        // `demo` sert la seule commande ti-baleine:demontrer-le-parcours. Elle y
        // trouve une horloge réglable et deux ports simulés, ce que ni `dev` ni
        // `prod` ne doivent offrir : cf. le bloc when@demo de services.yaml.
        return ['prod', 'dev', 'test', 'demo'];
    }
}
