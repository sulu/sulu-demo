<?php

declare(strict_types=1);

namespace App\Admin;

use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Bundle\ContactBundle\Admin\ContactAdmin;

class AdditionalContactDataAdmin extends Admin
{
    /**
     * @var ViewBuilderFactoryInterface
     */
    private $viewBuilderFactory;

    public function __construct(
        ViewBuilderFactoryInterface $viewBuilderFactory
    ) {
        $this->viewBuilderFactory = $viewBuilderFactory;
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        if ($viewCollection->has('sulu_contact.contact_edit_form.details')) {
            $contactDetailsFormView = $viewCollection->get('sulu_contact.contact_edit_form.details')->getView();

            $viewCollection->add(
                $this->viewBuilderFactory
                    ->createFormViewBuilder('app.additional_contact_data_form', '/additional-data')
                    ->setResourceKey('additional_contact_data')
                    ->setFormKey('additional_contact_data')
                    ->setTabTitle('app.additional_data')
                    ->addToolbarActions([new ToolbarAction('sulu_admin.save')])
                    ->setTabOrder($contactDetailsFormView->getOption('tabOrder') + 1)
                    ->setParent(ContactAdmin::CONTACT_EDIT_FORM_VIEW)
            );
        }
    }

    public static function getPriority(): int
    {
        return ContactAdmin::getPriority() - 1;
    }
}
