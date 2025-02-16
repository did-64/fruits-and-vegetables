<?php

namespace App\Collection;

use App\Entity\FoodItem;
use App\Entity\Fruit;
use App\Exception\CustomHttpException;
use App\Repository\FruitRepository;
use Doctrine\ORM\EntityManagerInterface;

class FruitCollection implements FoodCollectionInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FruitRepository $fruitRepository
    ) {}

    public function add(FoodItem $item): void
    {
        if (!$item instanceof Fruit) {
            throw new CustomHttpException('Item must be a Fruit');
        }
        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    public function remove(int $id): bool
    {
        $fruit = $this->fruitRepository->find($id);
        if ($fruit) {
            $this->entityManager->remove($fruit);
            $this->entityManager->flush();
        }
        return $fruit instanceof Fruit;
    }

    public function list(?string $query): array
    {
        if (!$query) {
            return $this->fruitRepository->findAll();
        }else{
            return $this->fruitRepository->createQueryBuilder('f')
                ->where('f.name LIKE :query')
                ->setParameter('query', '%' . $query . '%')
                ->getQuery()
                ->getResult();
        }
    }
}