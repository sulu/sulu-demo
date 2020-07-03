<?php

declare(strict_types=1);

namespace App\Controller\Website;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HealthController
{
    /**
     * @Route("/_health/check", name="app.health_check", defaults={"_requestAnalyzer": false})
     */
    public function healthAction(): Response
    {
        return new Response();
    }
}
