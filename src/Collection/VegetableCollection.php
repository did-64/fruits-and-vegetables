<?php

namespace App\Collection;

use App\Entity\FoodItem;
use App\Entity\Vegetable;
use App\Exception\CustomHttpException;
use App\Repository\VegetableRepository;
use Doctrine\ORM\EntityManagerInterface;

class VegetableCollection implements FoodCollectionInterface
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private VegetableRepository $vegetableRepository
    ) {}

    public function add(FoodItem $item): void
    {
        if (!$item instanceof Vegetable) {
            throw new CustomHttpException('Item must be a Vegetable');
        }

        $this->entityManager->persist($item);
        $this->entityManager->flush();
    }

    public function remove(int $id): void
    {
        $vegetable = $this->vegetableRepository->find($id);
        if ($vegetable) {
            $this->entityManager->remove($vegetable);
            $this->entityManager->flush();
        }
    }

    public function list(?string $query): array
    {
        if (!$query) {
            return $this->vegetableRepository->findAll();
        }
        return $this->vegetableRepository->createQueryBuilder('v')
            ->where('v.name LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

    }


}