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
    private WebspaceManagerInterface $webspaceManager;
    private RouteRepositoryInterface $routeRepository;
    private TemplateAttributeResolverInterface $templateAttributeResolver;

    public function __construct(
        WebspaceManagerInterface $webspaceManager,
        RouteRepositoryInterface $routeRepository,
        TemplateAttributeResolverInterface $templateAttributeResolver
    ) {
        $this->webspaceManager = $webspaceManager;
        $this->routeRepository = $routeRepository;
        $this->templateAttributeResolver = $templateAttributeResolver;
    }

    public function indexAction(Event $event): Response
    {
        $parameters = $this->templateAttributeResolver->resolve([
            'event' => $event,
            'localizations' => $this->getLocalizationsArrayForEntity($event),
        ]);

        return $this->render('events/event.html.twig', $parameters);
    }

    /**
     * @return array<string, array{locale: string, url:string|null}>
     */
    protected function getLocalizationsArrayForEntity(Event $entity): array
    {
        $routes = $this->routeRepository->findAllByEntity(Event::class, (string) $entity->getId());

        $localizations = [];
        foreach ($routes as $route) {
            $url = $this->webspaceManager->findUrlByResourceLocator(
                $route->getPath(),
                null,
                $route->getLocale()
            );

            $localizations[$route->getLocale()] = ['locale' => $route->getLocale(), 'url' => $url];
        }

        return $localizations;
    }
}
