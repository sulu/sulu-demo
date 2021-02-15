<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sulu\Bundle\CategoryBundle\Category\CategoryManagerInterface;
use Sulu\Bundle\CategoryBundle\Controller\CategoryController as SuluCategoryController;
use Sulu\Bundle\CategoryBundle\Entity\CategoryRepositoryInterface;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactoryInterface;
use Sulu\Component\Rest\ListBuilder\Metadata\FieldDescriptorFactoryInterface;
use Sulu\Component\Rest\RestHelperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryController extends SuluCategoryController
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        ViewHandlerInterface $viewHandler,
        CategoryRepositoryInterface $categoryRepository,
        TranslatorInterface $translator,
        RestHelperInterface $restHelper,
        DoctrineListBuilderFactoryInterface $listBuilderFactory,
        FieldDescriptorFactoryInterface $fieldDescriptorFactory,
        CategoryManagerInterface $categoryManager,
        string $categoryClass,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct(
            $viewHandler,
            $categoryRepository,
            $translator,
            $restHelper,
            $listBuilderFactory,
            $fieldDescriptorFactory,
            $categoryManager,
            $categoryClass,
        );

        $this->entityManager = $entityManager;
    }

    public function getAction($id, Request $request)
    {
        $response = parent::getAction($id, $request);

        $category = $this->getEntityForResponse($response);
        if (!$category) {
            return $response;
        }

        return $this->addAdditionalDataToResponse($category, $response);
    }

    public function postAction(Request $request)
    {
        $response = parent::postAction($request);

        $category = $this->getEntityForResponse($response);
        if (!$category) {
            return $response;
        }

        $this->mapAdditionalDataToEntity($request->request->all(), $category);
        $this->entityManager->flush();

        return $this->addAdditionalDataToResponse($category, $response);
    }

    public function putAction($id, Request $request)
    {
        $response = parent::putAction($id, $request);

        $category = $this->getEntityForResponse($response);
        if (!$category) {
            return $response;
        }

        $this->mapAdditionalDataToEntity($request->request->all(), $category);
        $this->entityManager->flush();

        return $this->addAdditionalDataToResponse($category, $response);
    }

    protected function getEntityForResponse(Response $response): ?Category
    {
        $id = json_decode($response->getContent(), true)['id'] ?? null;

        return $id ? $this->entityManager->getRepository(Category::class)->find($id) : null;
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function mapAdditionalDataToEntity(array $data, Category $entity): void
    {
        $entity->setColor($data['color']);
    }

    protected function addAdditionalDataToResponse(Category $entity, Response $response): Response
    {
        $responseData = json_decode($response->getContent(), true);
        $responseData['color'] = $entity->getColor();

        return $this->handleView($this->view(
            $responseData,
            $response->getStatusCode(),
            $response->headers->all()
        ));
    }
}
