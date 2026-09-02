<?php

declare(strict_types=1);

namespace App\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\View\ListItemAction;
use Sulu\Bundle\AdminBundle\Admin\View\ListViewBuilderInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\ContactBundle\Admin\ContactAdmin;

class AppAdmin extends Admin
{
    public function configureViews(ViewCollection $viewCollection): void
    {
        if ($viewCollection->has(ContactAdmin::CONTACT_LIST_VIEW)) {
            /** @var ListViewBuilderInterface $contactListViewBuilder */
            $contactListViewBuilder = $viewCollection->get(ContactAdmin::CONTACT_LIST_VIEW);
            $contactListViewBuilder->addItemActions([
                new ListItemAction('app.alert_name', ['disabled_ids' => [1]]),
            ]);
        }
    }

    public static function getPriority(): int
    {
        return ContactAdmin::getPriority() - 1;
    }
}
