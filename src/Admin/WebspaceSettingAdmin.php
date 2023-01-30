<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\WebspaceSetting;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\PageBundle\Admin\PageAdmin;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Sulu\Component\Webspace\Webspace;

class WebspaceSettingAdmin extends Admin
{
    public const TAB_VIEW = 'app.webspace_settings';
    public const FORM_VIEW = 'app.webspace_settings.form';
    public const FORM_KEY = 'webspace_setting_details';


    public function __construct(
        private WebspaceManagerInterface $webspaceManager,
        private ViewBuilderFactoryInterface $viewBuilderFactory,
        private SecurityCheckerInterface $securityChecker
    ) {
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        if ($this->hasSomeWebspaceSettingPermission()) {
            $viewCollection->add(
                // sulu will only load the existing entity if the path of the form includes an id attribute
                $this->viewBuilderFactory->createResourceTabViewBuilder(static::TAB_VIEW, '/webspace-settings/:id')
                    ->setResourceKey(WebspaceSetting::RESOURCE_KEY)
                    ->setTabTitle('app.webspace_settings')
                    ->setParent(PageAdmin::WEBSPACE_TABS_VIEW)
                    ->setOption('routerAttributesToFormRequest', ['webspace'])
                    ->setTabOrder(8096)
                    ->setAttributeDefault('id', '-')
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createFormViewBuilder(static::FORM_VIEW, '/details')
                    ->setResourceKey(WebspaceSetting::RESOURCE_KEY)
                    ->setFormKey(self::FORM_KEY)
                    ->setTabTitle('sulu_admin.details')
                    ->addToolbarActions([new ToolbarAction('sulu_admin.save')])
                    ->addRouterAttributesToFormRequest(['webspace'])
                    ->setParent(static::TAB_VIEW)
            );
        }
    }

    public function getSecurityContexts(): array
    {
        $webspaceContexts = [];
        /* @var Webspace $webspace */
        foreach ($this->webspaceManager->getWebspaceCollection() as $webspace) {
            $securityContextKey = self::getWebspaceSettingSecurityContext($webspace->getKey());
            $webspaceContexts[$securityContextKey] = $this->getSecurityContextPermissions();
        }

        return [
            self::SULU_ADMIN_SECURITY_SYSTEM => [
                'Webspaces' => $webspaceContexts,
            ],
        ];
    }

    public function getSecurityContextsWithPlaceholder(): array
    {
        return [
            self::SULU_ADMIN_SECURITY_SYSTEM => [
                'Webspaces' => [
                    self::getWebspaceSettingSecurityContext('#webspace#') => $this->getSecurityContextPermissions(),
                ],
            ],
        ];
    }

    /**
     * Returns security context for settings in given webspace.
     *
     * @final
     *
     * @param string $webspaceKey
     *
     * @return string
     */
    public static function getWebspaceSettingSecurityContext($webspaceKey): string
    {
        return \sprintf('%s%s.%s', PageAdmin::SECURITY_CONTEXT_PREFIX, $webspaceKey, 'settings');
    }

    /**
     * @return string[]
     */
    private function getSecurityContextPermissions(): array
    {
        return [
            PermissionTypes::VIEW,
            PermissionTypes::EDIT,
        ];
    }

    private function hasSomeWebspaceSettingPermission(): bool
    {
        foreach ($this->webspaceManager->getWebspaceCollection()->getWebspaces() as $webspace) {
            $hasWebspaceAnalyticsPermission = $this->securityChecker->hasPermission(
                self::getWebspaceSettingSecurityContext($webspace->getKey()),
                PermissionTypes::EDIT
            );

            if ($hasWebspaceAnalyticsPermission) {
                return true;
            }
        }

        return false;
    }
}
