<?php

/*
 * This file is part of Sulu.
 *
 * (c) MASSIVE ART WebServices GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\Functional;

/**
 * Website test case.
 */
abstract class WebsiteTestCase extends AbstractTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getKernel(array $options = [])
    {
        $kernel = new \WebsiteKernel('test', true);
        $kernel->boot();
        $this->kernelStack[] = $kernel;

        return $kernel;
    }
}
