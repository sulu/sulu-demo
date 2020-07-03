<?php

namespace App\Twig;

use Symfony\Component\Intl\Countries;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * This is just an example Twig Extension use `intl_country` instead from sulu/web-twig.
 */
class CountryTwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('country', [$this, 'getCountryByCode']),
        ];
    }

    public function getCountryByCode(?string $countryCode): string
    {
        if (!$countryCode) {
            return '';
        }

        return Countries::getName($countryCode);
    }
}
