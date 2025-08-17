<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function getTree(): array
    {
        $categories = $this->findBy([], ['name' => 'ASC']);
        $tree = [];

        foreach ($categories as $category) {
            if (!$category->getParent()) {
                $tree[] = $this->buildTree($category, $categories);
            }
        }

        return $tree;
    }

    private function buildTree(Category $parent, array $categories): array
    {
        $branch = [
            'id' => $parent->getId(),
            'name' => $parent->getName(),
            'image' => $parent->getImagePath(),
            'children' => []
        ];

        foreach ($categories as $category) {
            if ($category->getParent() && $category->getParent()->getId() === $parent->getId()) {
                $branch['children'][] = $this->buildTree($category, $categories);
            }
        }

        return $branch;
    }
}
