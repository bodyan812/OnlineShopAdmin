<?php

namespace App\Controller\Api;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/categories', name: 'api_categories_')]
class CategoryController extends AbstractController
{
    #[Route('/tree', name: 'tree', methods: ['GET'])]
    public function tree(CategoryRepository $repository): JsonResponse
    {
        return $this->json([
            'categories' => $repository->getTree()
        ]);
    }
}
