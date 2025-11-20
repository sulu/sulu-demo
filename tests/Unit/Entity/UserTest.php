<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User();
    }

    public function testExternalIdGetterAndSetter(): void
    {
        $this->assertNull($this->user->getExternalId());

        $externalId = '01933e3f-8d6e-7a3e-a9e4-1234567890ab';
        $this->user->setExternalId($externalId);

        $this->assertSame($externalId, $this->user->getExternalId());
    }

    public function testExternalIdCanBeSetToNull(): void
    {
        $this->user->setExternalId('01933e3f-8d6e-7a3e-a9e4-1234567890ab');
        $this->assertNotNull($this->user->getExternalId());

        $this->user->setExternalId(null);
        $this->assertNull($this->user->getExternalId());
    }

    public function testGenerateExternalIdIfEmptyCreatesUuid(): void
    {
        $this->assertNull($this->user->getExternalId());

        $this->user->generateExternalIdIfEmpty();

        $externalId = $this->user->getExternalId();
        $this->assertNotNull($externalId);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $externalId,
            'Generated external ID should be a valid UUID v7',
        );
    }

    public function testGenerateExternalIdIfEmptyDoesNotOverwriteExistingId(): void
    {
        $originalId = '01933e3f-8d6e-7a3e-a9e4-1234567890ab';
        $this->user->setExternalId($originalId);

        $this->user->generateExternalIdIfEmpty();

        $this->assertSame($originalId, $this->user->getExternalId());
    }

    public function testGenerateExternalIdIfEmptyReplacesEmptyString(): void
    {
        $this->user->setExternalId('');

        $this->user->generateExternalIdIfEmpty();

        $externalId = $this->user->getExternalId();
        $this->assertNotNull($externalId);
        $this->assertNotSame('', $externalId);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $externalId,
        );
    }

    public function testUserExtendsFromSuluUser(): void
    {
        $this->assertInstanceOf(\Sulu\Bundle\SecurityBundle\Entity\User::class, $this->user);
    }

    public function testMultipleCallsGenerateDifferentUuids(): void
    {
        $user1 = new User();
        $user1->generateExternalIdIfEmpty();

        $user2 = new User();
        $user2->generateExternalIdIfEmpty();

        $this->assertNotSame(
            $user1->getExternalId(),
            $user2->getExternalId(),
            'Each user should get a unique external ID',
        );
    }
}
