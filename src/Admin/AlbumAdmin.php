<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Album;
use Sulu\Bundle\ActivityBundle\Infrastructure\Sulu\Admin\View\ActivityViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;

class AlbumAdmin extends Admin
{
    public const LIST_VIEW = 'app.album.list';
    public const ADD_FORM_VIEW = 'app.album.add_form';
    public const ADD_FORM_DETAILS_VIEW = 'app.album.add_form.details';
    public const EDIT_FORM_VIEW = 'app.album.edit_form';
    public const EDIT_FORM_DETAILS_VIEW = 'app.album.edit_form.details';

    private ViewBuilderFactoryInterface $viewBuilderFactory;
    private ActivityViewBuilderFactoryInterface $activityViewBuilderFactory;
    private SecurityCheckerInterface $securityChecker;

    public function __construct(
        ViewBuilderFactoryInterface $viewBuilderFactory,
        ActivityViewBuilderFactoryInterface $activityViewBuilderFactory,
        SecurityCheckerInterface $securityChecker
    ) {
        $this->viewBuilderFactory = $viewBuilderFactory;
        $this->activityViewBuilderFactory = $activityViewBuilderFactory;
        $this->securityChecker = $securityChecker;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if ($this->securityChecker->hasPermission(Album::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $rootNavigationItem = new NavigationItem('app.music');
            $rootNavigationItem->setIcon('fa-music');
            $rootNavigationItem->setPosition(25);

            $navigationItem = new NavigationItem('app.albums');
            $navigationItem->setView(static::LIST_VIEW);

            $rootNavigationItem->addChild($navigationItem);
            $navigationItemCollection->add($rootNavigationItem);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $formToolbarActions = [];
        $listToolbarActions = [];

        if ($this->securityChecker->hasPermission(Album::SECURITY_CONTEXT, PermissionTypes::ADD)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.add');
        }

        if ($this->securityChecker->hasPermission(Album::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.save');
        }

        if ($this->securityChecker->hasPermission(Album::SECURITY_CONTEXT, PermissionTypes::DELETE)) {
            $formToolbarActions[] = new ToolbarAction('sulu_admin.delete');
            $listToolbarActions[] = new ToolbarAction('sulu_admin.delete');
        }

        if ($this->securityChecker->hasPermission(Album::SECURITY_CONTEXT, PermissionTypes::VIEW)) {
            $listToolbarActions[] = new ToolbarAction('sulu_admin.export');
        }

        if ($this->securityChecker->hasPermission(Album::SECURITY_CONTEXT, PermissionTypes::EDIT)) {
            $viewCollection->add(
                $this->viewBuilderFactory->createListViewBuilder(static::LIST_VIEW, '/albums')
                    ->setResourceKey(Album::RESOURCE_KEY)
                    ->setListKey(Album::LIST_KEY)
                    ->setTitle('app.albums')
                    ->addListAdapters(['table'])
                    ->setAddView(static::ADD_FORM_VIEW)
                    ->setEditView(static::EDIT_FORM_VIEW)
                    ->addToolbarActions($listToolbarActions)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(static::ADD_FORM_VIEW, '/albums/add')
                    ->setResourceKey(Album::RESOURCE_KEY)
                    ->setBackView(static::LIST_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createFormViewBuilder(static::ADD_FORM_DETAILS_VIEW, '/details')
                    ->setResourceKey(Album::RESOURCE_KEY)
                    ->setFormKey(Album::FORM_KEY)
                    ->setTabTitle('sulu_admin.details')
                    ->setEditView(static::EDIT_FORM_VIEW)
                    ->addToolbarActions($formToolbarActions)
                    ->setParent(static::ADD_FORM_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(static::EDIT_FORM_VIEW, '/albums/:id')
                    ->setResourceKey(Album::RESOURCE_KEY)
                    ->setBackView(static::LIST_VIEW)
                    ->setTitleProperty('title')
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createFormViewBuilder(static::EDIT_FORM_DETAILS_VIEW, '/details')
                    ->setResourceKey(Album::RESOURCE_KEY)
                    ->setFormKey(Album::FORM_KEY)
                    ->setTabTitle('sulu_admin.details')
                    ->addToolbarActions($formToolbarActions)
                    ->setParent(static::EDIT_FORM_VIEW)
            );

            if ($this->activityViewBuilderFactory->hasActivityListPermission()) {
                $viewCollection->add(
                    $this->activityViewBuilderFactory
                        ->createActivityListViewBuilder(
                            static::EDIT_FORM_VIEW . '.activity',
                            '/activity',
                            Album::RESOURCE_KEY
                        )
                        ->setParent(static::EDIT_FORM_VIEW)
                );
            }
        }
    }

    /**
     * @return mixed[]
     */
    public function getSecurityContexts(): array
    {
        return [
            self::SULU_ADMIN_SECURITY_SYSTEM => [
                'Music' => [
                    Album::SECURITY_CONTEXT => [
                        PermissionTypes::VIEW,
                        PermissionTypes::ADD,
                        PermissionTypes::EDIT,
                        PermissionTypes::DELETE,
                    ],
                ],
            ],
        ];
    }
}
