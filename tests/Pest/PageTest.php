<?php

use App\Tests\Pest\DemoTestCase;

uses(DemoTestCase::class);

it('is homepage page correctly rendered', function () {
    $client = $this->getClient();

    $client->request('GET', '/en');
    $response = $client->getResponse();

    expect($response->getStatusCode())->toEqual(200);
    expect($response->getContent())
        ->toContain('Homepage')
        ->toContain('International Talents was founded 1998');
});

it('is blog page correctly rendered', function () {
    $client = $this->getClient();

    $client->request('GET', '/en/blog');
    $response = $client->getResponse();

    expect($response->getStatusCode())->toEqual(200);
    expect($response->getContent())
        ->toContain('A week on the road with Civil Literature')
        ->toContain('A great song will win')
        ->toContain('Behind the scenes of our creative directors')
        ->toContain('Legend behind the Mix')
        ->toContain('Drop Big Beats');
});
