<?php

declare(strict_types=1);

namespace App\Controller\Website;

use App\Entity\Event;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Bundle\WebsiteBundle\Resolver\TemplateAttributeResolverInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class EventController extends AbstractController
{
    public function indexAction(Event $event): Response
    {
        $parameters = $this->get('sulu_website.resolver.template_attribute')->resolve([
            'event' => $event,
            'localizations' => $this->getLocalizationsArrayForEntity($event),
        ]);

        return $this->render('events/event.html.twig', $parameters);
    }

    /**
     * @return array<string, array>
     */
    protected function getLocalizationsArrayForEntity(Event $entity): array
    {
        $routes = $this->get('sulu.repository.route')->findAllByEntity(Event::class, (string) $entity->getId());

        $localizations = [];
        foreach ($routes as $route) {
            $url = $this->get('sulu_core.webspace.webspace_manager')->findUrlByResourceLocator(
                $route->getPath(),
                null,
                $route->getLocale()
            );

            $localizations[$route->getLocale()] = ['locale' => $route->getLocale(), 'url' => $url];
        }

        return $localizations;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedServices()
    {
        $subscribedServices = parent::getSubscribedServices();

        $subscribedServices['sulu_core.webspace.webspace_manager'] = WebspaceManagerInterface::class;
        $subscribedServices['sulu.repository.route'] = RouteRepositoryInterface::class;
        $subscribedServices['sulu_website.resolver.template_attribute'] = TemplateAttributeResolverInterface::class;

        return $subscribedServices;
    }
}
