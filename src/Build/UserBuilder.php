<?php

declare(strict_types=1);

namespace App\Build;

use Doctrine\Persistence\ObjectManager;
use Massive\Bundle\BuildBundle\Build\BuilderContext;
use Massive\Bundle\BuildBundle\Build\BuilderInterface;
use Sulu\Bundle\SecurityBundle\Build\UserBuilder as SuluUserBuilder;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Sulu\Bundle\SecurityBundle\Entity\UserSetting;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserBuilder implements BuilderInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function __construct(private readonly SuluUserBuilder $decoratedUserBuilder, ContainerInterface $container = null)
    {
        $this->setContainer($container);
    }

    public function getName()
    {
        return $this->decoratedUserBuilder->getName();
    }

    /**
     * @return string[]
     */
    public function getDependencies()
    {
        return $this->decoratedUserBuilder->getDependencies();
    }

    public function build(): void
    {
        $this->decoratedUserBuilder->build();

        $manager = $this->getManager();
        $userRepository = $manager->getRepository(User::class);
        $userSettingRepository = $manager->getRepository(UserSetting::class);

        $user = $userRepository->findOneBy(['username' => 'admin']);

        if (!$user instanceof User) {
            return;
        }

        $userSetting = $userSettingRepository->findOneBy([
            'key' => 'sulu_admin.application.navigation_pinned',
            'user' => $user->getId(),
        ]);

        if (!$userSetting instanceof UserSetting) {
            $userSetting = new UserSetting();
            $userSetting->setKey('sulu_admin.application.navigation_pinned');
            $userSetting->setUser($user);
        }

        $userSetting->setValue('true');

        $manager->persist($userSetting);
        $manager->flush();
    }

    public function setContext(BuilderContext $context): void
    {
        $this->decoratedUserBuilder->setContext($context);
    }

    private function getManager(): ObjectManager
    {
        return $this->container->get('doctrine')->getManager();
    }

    /**
     * Sets the container.
     */
    public function setContainer(ContainerInterface $container = null): void
    {
        if ($container instanceof ContainerInterface) {
            $this->container = $container;
            $this->decoratedUserBuilder->setContainer($container);
        }
    }
}
