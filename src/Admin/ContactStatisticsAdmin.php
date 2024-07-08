<?php

declare(strict_types=1);

namespace App\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\ContactBundle\Admin\ContactAdmin;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

class ContactStatisticsAdmin extends Admin
{
    public const CONTACT_STATISTICS_VIEW = 'app.contact_statistics';

    private ViewBuilderFactoryInterface $viewBuilderFactory;
    private SecurityCheckerInterface $securityChecker;

    public function __construct(
        ViewBuilderFactoryInterface $viewBuilderFactory,
        SecurityCheckerInterface $securityChecker
    ) {
        $this->viewBuilderFactory = $viewBuilderFactory;
        $this->securityChecker = $securityChecker;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if (!$navigationItemCollection->has('sulu_contact.contacts')) {
            return;
        }

        if ($this->securityChecker->hasPermission(ContactAdmin::CONTACT_SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $contactStatisticsNavigationItem = new NavigationItem('app.contact_statistics');
            $contactStatisticsNavigationItem->setPosition(30);
            $contactStatisticsNavigationItem->setView(static::CONTACT_STATISTICS_VIEW);

            $contactsNavigationItem = $navigationItemCollection->get('sulu_contact.contacts');
            $contactsNavigationItem->addChild($contactStatisticsNavigationItem);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        if ($this->securityChecker->hasPermission(ContactAdmin::CONTACT_SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $viewCollection->add(
                $this->viewBuilderFactory->createViewBuilder(self::CONTACT_STATISTICS_VIEW, '/contact-statistics', 'app.contact_statistics')
                    ->setOption('contactListView', ContactAdmin::CONTACT_LIST_VIEW)
                    ->setOption('contactResourceKey', 'contacts')
            );
        }
    }

    public static function getPriority(): int
    {
        return ContactAdmin::getPriority() - 1;
    }
}
