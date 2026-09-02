<?php

declare(strict_types=1);

namespace App\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\View\FormViewBuilderInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\ContactBundle\Admin\ContactAdmin;

class AppAdmin extends Admin
{
    public function configureViews(ViewCollection $viewCollection): void
    {
        if ($viewCollection->has('sulu_contact.contact_add_form.details')) {
            /** @var FormViewBuilderInterface $contactAddFormViewBuilder */
            $contactAddFormViewBuilder = $viewCollection->get('sulu_contact.contact_add_form.details');
            $contactAddFormViewBuilder->addToolbarActions([
                new ToolbarAction('app.generate_name', ['allow_overwrite' => true]),
            ]);
        }
    }

    public static function getPriority(): int
    {
        return ContactAdmin::getPriority() - 1;
    }
}
