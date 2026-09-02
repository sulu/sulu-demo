<?php

declare(strict_types=1);

namespace App\Tests\Functional\Entity;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Sulu\Bundle\ContactBundle\Entity\Contact;
use Sulu\Bundle\SecurityBundle\Entity\Role;
use Sulu\Bundle\SecurityBundle\Entity\UserRole;
use Sulu\Bundle\TestBundle\Testing\SuluTestCase;

class UserTest extends SuluTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->purgeDatabase();
        $this->entityManager = $this->getEntityManager();
    }

    public function testLifecycleCallbackGeneratesExternalIdOnPersist(): void
    {
        $user = $this->createUser('testuser');

        // External ID should be null before persistence
        $this->assertNull($user->getExternalId());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        // External ID should be generated after persistence
        $externalId = $user->getExternalId();
        $this->assertNotNull($externalId);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $externalId,
            'Generated external ID should be a valid UUID v7',
        );
    }

    public function testLifecycleCallbackGeneratesExternalIdOnUpdate(): void
    {
        $user = $this->createUser('testuser');
        $user->setExternalId('manually-set-id');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertSame('manually-set-id', $user->getExternalId());

        // Clear the external ID and update
        $user->setExternalId(null);
        $user->setEmail('updated@example.com');

        $this->entityManager->flush();

        // External ID should be regenerated on update
        $externalId = $user->getExternalId();
        $this->assertNotNull($externalId);
        $this->assertNotSame('manually-set-id', $externalId);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $externalId,
        );
    }

    public function testExternalIdIsUniqueAcrossUsers(): void
    {
        $user1 = $this->createUser('user1');
        $user2 = $this->createUser('user2');

        $this->entityManager->persist($user1);
        $this->entityManager->persist($user2);
        $this->entityManager->flush();

        $this->assertNotNull($user1->getExternalId());
        $this->assertNotNull($user2->getExternalId());
        $this->assertNotSame(
            $user1->getExternalId(),
            $user2->getExternalId(),
            'Each user should have a unique external ID',
        );
    }

    public function testExternalIdIsPersistedToDatabase(): void
    {
        $user = $this->createUser('testuser');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $externalId = $user->getExternalId();
        $userId = $user->getId();

        // Clear entity manager to force database reload
        $this->entityManager->clear();

        /** @var User|null $reloadedUser */
        $reloadedUser = $this->entityManager->getRepository(User::class)->find($userId);

        $this->assertNotNull($reloadedUser);
        $this->assertSame($externalId, $reloadedUser->getExternalId());
    }

    public function testManuallySetExternalIdIsNotOverwritten(): void
    {
        $manualId = '01933e3f-8d6e-7a3e-a9e4-1234567890ab';
        $user = $this->createUser('testuser');
        $user->setExternalId($manualId);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->assertSame($manualId, $user->getExternalId());

        // Update the user
        $user->setEmail('updated@example.com');
        $this->entityManager->flush();

        // Manual ID should still be preserved
        $this->assertSame($manualId, $user->getExternalId());
    }

    private function createUser(string $username): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($username . '@example.com');
        $user->setPassword('password');
        $user->setLocale('en');
        $user->setSalt('salt');

        // Create required contact
        $contact = new Contact();
        $contact->setFirstName('Test');
        $contact->setLastName('User');
        $this->entityManager->persist($contact);

        $user->setContact($contact);

        // Find or create required role
        $role = $this->entityManager->getRepository(Role::class)->findOneBy([]);
        if (!$role instanceof Role) {
            $role = new Role();
            $role->setName('TestRole');
            $role->setSystem('Sulu');
            $this->entityManager->persist($role);
            $this->entityManager->flush();
        }

        $userRole = new UserRole();
        $userRole->setRole($role);
        $userRole->setUser($user);
        $userRole->setLocale('["en"]');
        $this->entityManager->persist($userRole);

        $user->addUserRole($userRole);

        return $user;
    }
}
