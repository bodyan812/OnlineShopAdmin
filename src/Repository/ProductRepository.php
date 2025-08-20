<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findPaginated(int $page, int $limit, ?int $categoryId = null, ?string $name = null): Paginator
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->addSelect('c', 'm')
            ->leftJoin('p.category', 'c')
            ->leftJoin('p.media', 'm');

        if ($categoryId) {
            $queryBuilder
                ->andWhere('p.category = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        if ($name) {
            $queryBuilder
                ->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $name . '%');
        }

        $queryBuilder->orderBy('p.id', 'ASC');
        $query = $queryBuilder->getQuery();

        $paginator = new Paginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return $paginator;
    }
}
