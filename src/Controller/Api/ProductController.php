<?php
// src/Controller/Api/ProductController.php
namespace App\Controller\Api;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    #[Route('/api/products', name: 'api_products_collection', methods: ['GET'])]
    public function index(Request $request, ProductRepository $repository): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = 10;

        $paginator = $repository->findPaginated($page, $limit);
        $products = [];

        foreach ($paginator as $product) {
            $products[] = $this->normalizeProduct($product);
        }

        return $this->json([
            'items' => $products,
            'total' => $paginator->count(),
            'page' => $page,
            'pages' => ceil($paginator->count() / $limit)
        ]);
    }

    private function normalizeProduct($product): array
    {
        $mainImage = null;
        if (!$product->getMedia()->isEmpty()) {
            $firstMedia = $product->getMedia()->first();
            $mainImage = '/uploads/media/'.$firstMedia->getFilePath();
        }

        return [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'price' => $product->getPrice(),
            'description' => $product->getDescription(),
            'main_image' => $mainImage,
            'category' => $product->getCategory() ? [
                'id' => $product->getCategory()->getId(),
                'name' => $product->getCategory()->getName()
            ] : null
        ];
    }
}
