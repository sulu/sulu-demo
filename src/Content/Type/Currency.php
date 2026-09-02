<?php

declare(strict_types=1);

namespace App\Content\Type;

use Sulu\Component\Content\SimpleContentType;

class Currency extends SimpleContentType
{
    public function __construct()
    {
        parent::__construct('currency');
    }
}
